<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Grupo;
use App\Models\TipoSolicitud;
use App\Models\MedioRecepcion;
use App\Models\EstadoSolicitud;
use App\Models\User;
use App\Models\Documento;
use App\Models\Remitente;
use App\Models\TipoRemitente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\BusinessDaysCalculator;
use Illuminate\Support\Facades\Log;
use App\Services\RadicadoService;
use Carbon\Carbon;

class SolicitudController extends Controller
{
    public function index(Request $request, Grupo $grupo)
    {
        $solicitudes = Solicitud::with(['tipoSolicitud', 'estado', 'remitente', 'documentos'])
            ->where('grupo_id', $grupo->id)
            ->filtrarTipo($request->tipo)
            ->filtrarEstado($request->estado)
            ->filtrarFecha($request->fecha)
            ->ordenarPor($request->orden)
            ->get();

        $tipos = TipoSolicitud::all();
        $estados = EstadoSolicitud::all();

        return view('grupos.solicitudes.index', compact('solicitudes', 'tipos', 'estados', 'grupo'));
    }

    public function create(Grupo $grupo)
    {
        $tipos_remitente = TipoRemitente::orderBy('id')->get();
        $remitentes = Remitente::orderBy('nombre')->get();
        $tiposSolicitud = TipoSolicitud::all();
        $medios = MedioRecepcion::all();
        $estados = EstadoSolicitud::all();

        return view('grupos.solicitudes.create', compact(
            'grupo',
            'tipos_remitente',
            'remitentes',
            'tiposSolicitud',
            'medios',
            'estados'
        ));
    }

    public function store(Request $request, Grupo $grupo, BusinessDaysCalculator $calculator)
    {
        Log::info("INICIO store() solicitud", ["request" => $request->all()]);

        $numeroRadicado = RadicadoService::generar(
            $request->tipo_radicacion,
            $request->dependencia
        );

        $tipoRem = intval($request->tipo_remitente_id);
        $remitenteId = null;

        // Caso 1: Anónimo (id = 2)
        if ($tipoRem === 2) {
            $rem = Remitente::create([
                "nombre"                          => "Anónimo",
                "numero_documento"                => null,
                "telefono"                        => null,
                "correo"                          => null,
                "tipo_remitente_id"               => 2,
                "tipo_documento_identificacion_id" => null
            ]);
            $remitenteId = $rem->id;
            Log::info("Remitente anónimo creado", ["id" => $remitenteId]);
        }
        // Caso 2: Remitente existente seleccionado
        elseif ($request->filled('remitente_id')) {
            $remitenteId = $request->remitente_id;
            Log::info("Remitente existente seleccionado", ["id" => $remitenteId]);
        }
        // Caso 3: Crear nuevo remitente (Natural o Jurídico)
        elseif ($request->filled('rem_nombre') && $request->rem_nombre !== 'Anónimo') {
            $rem = Remitente::create([
                "nombre"                          => $request->rem_nombre,
                "numero_documento"                => $request->rem_numero_documento,
                "telefono"                        => $request->rem_telefono,
                "correo"                          => $request->rem_correo,
                "tipo_remitente_id"               => $tipoRem,
                "tipo_documento_identificacion_id" => $request->rem_tipo_documento_id
            ]);
            $remitenteId = $rem->id;
            Log::info("Nuevo remitente creado", ["id" => $remitenteId, "nombre" => $rem->nombre]);
        }

        // Validar que se haya obtenido un remitente_id
        if (!$remitenteId) {
            Log::error("No se pudo determinar el remitente_id", [
                "tipo_remitente_id" => $tipoRem,
                "remitente_id" => $request->remitente_id,
                "rem_nombre" => $request->rem_nombre
            ]);

            return redirect()->back()
                ->with('error', 'Error: No se pudo asociar el remitente a la solicitud.')
                ->withInput();
        }
        DB::beginTransaction();
        try {
            $datosSolicitud = [
                "grupo_id"           => $grupo->id,
                "usuario_id"         => Auth::id(),
                "numero_radicado"    => $numeroRadicado,
                "tipo_radicacion"    => $request->tipo_radicacion,
                "dependencia"        => $request->dependencia,
                "fecha_ingreso"      => $request->fecha_ingreso,
                "fecha_vencimiento"  => $request->fecha_vencimiento,
                "estado_id"          => $request->estado_id,
                "tipo_solicitud_id"  => $request->tipo_solicitud_id,
                "tipo_remitente_id"  => $tipoRem,
                "remitente_id"       => $remitenteId,
                "es_anonimo"         => $tipoRem === 2 ? 1 : 0,
                "asunto"             => $request->asunto,
                "contenido"          => $request->contenido,
                "medio_recepcion_id" => $request->medio_recepcion_id,
                "firma_digital"      => $request->firma_digital,
                "archivo"            => null,
            ];

            $solicitud = Solicitud::create($datosSolicitud);

            // LOG: Verificar el objeto creado
            Log::info("Solicitud DESPUÉS de create()", [
                "solicitud_id" => $solicitud->id,
                "remitente_id_en_objeto" => $solicitud->remitente_id,
                "solicitud_completa" => $solicitud->toArray()
            ]);

            // Manejo de múltiples archivos
            if ($request->hasFile('archivos')) {
                $archivos = $request->file('archivos');

                foreach ($archivos as $index => $archivo) {
                    $path = $archivo->store('documentos', 'public');

                    $documento = Documento::create([
                        'editor_id'      => Auth::id(),
                        'nombre_archivo' => $archivo->getClientOriginalName(),
                        'tamano_mb'      => round($archivo->getSize() / 1048576, 2),
                        'ruta'           => $path,
                    ]);

                    $solicitud->documentos()->attach($documento->id, ['orden' => $index + 1]);
                }
            }

            // Si solo hay un archivo (compatibilidad con código anterior)
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $path = $archivo->store('documentos', 'public');

                $documento = Documento::create([
                    'editor_id'      => Auth::id(),
                    'nombre_archivo' => $archivo->getClientOriginalName(),
                    'tamano_mb'      => round($archivo->getSize() / 1048576, 2),
                    'ruta'           => $path,
                ]);

                $solicitud->documentos()->attach($documento->id, ['orden' => 1]);
            }

            $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles($calculator);
            if ($estadoCalculado) {
                $solicitud->estado_id = $estadoCalculado;
                $solicitud->save();
            }

            DB::commit();

            Log::info("Solicitud guardada exitosamente", ["solicitud_id" => $solicitud->id]);

            return redirect()
                ->route('grupos.solicitudes.index', $grupo)
                ->with('success', 'Solicitud creada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear solicitud: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear la solicitud: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Grupo $grupo, Solicitud $solicitud)
    {
        if ($solicitud->grupo_id !== $grupo->id) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        // Cargar documentos ordenados
        $solicitud->load([
            'documentos' => function($query) {
                $query->orderBy('solicitud_documento.orden');
            },
            'remitente'
        ]);

        $tipos_remitente = TipoRemitente::orderBy('id')->get();
        $remitentes = Remitente::orderBy('nombre')->get();
        $tiposSolicitud = TipoSolicitud::pluck('nombre', 'id');
        $mediosRecepcion = MedioRecepcion::pluck('nombre', 'id');
        $estados = EstadoSolicitud::pluck('nombre', 'id');

        return view('grupos.solicitudes.edit', compact(
            'solicitud',
            'grupo',
            'tipos_remitente',
            'remitentes',
            'tiposSolicitud',
            'mediosRecepcion',
            'estados'
        ));
    }

    public function update(Request $request, Grupo $grupo, Solicitud $solicitud, BusinessDaysCalculator $calculator)
    {
        $rules = [
            'tipo_solicitud_id'  => 'required|exists:tipo_solicitud,id',
            'tipo_remitente_id'  => 'required|exists:tipo_remitente,id',
            'remitente_id'       => 'nullable|exists:remitente,id',
            'asunto'             => 'nullable|string|max:255',
            'contenido'          => 'nullable|string',
            'medio_recepcion_id' => 'required|exists:medio_recepcion,id',
            'fecha_ingreso'      => 'required|date',
            'fecha_vencimiento'  => 'nullable|date|after_or_equal:fecha_ingreso',
            'estado_id'          => 'required|exists:estado_solicitud,id',
            'firma_digital'      => 'boolean',
            'archivos'           => 'nullable|array',
            'archivos.*'         => 'file|max:10240',
            'documentos_eliminar' => 'nullable|array',
            'documentos_eliminar.*' => 'exists:documento,id',
        ];

        $data = $request->validate($rules);
        $data['usuario_id'] = Auth::id();

        DB::beginTransaction();

        try {
            $tipoRem = intval($request->tipo_remitente_id);

            // Caso 1: Anónimo (id = 2)
            if ($tipoRem === 2) {
                $rem = Remitente::create([
                    "nombre"                          => "Anónimo",
                    "numero_documento"                => null,
                    "telefono"                        => null,
                    "correo"                          => null,
                    "tipo_remitente_id"               => 2,
                    "tipo_documento_identificacion_id" => null
                ]);
                $data['remitente_id'] = $rem->id;
            }
            // Caso 2: Remitente existente seleccionado
            elseif ($request->filled('remitente_id')) {
                $data['remitente_id'] = $request->remitente_id;
            }
            // Caso 3: Crear nuevo remitente
            elseif ($request->filled('rem_nombre')) {
                $rem = Remitente::create([
                    "nombre"                          => $request->rem_nombre,
                    "numero_documento"                => $request->rem_numero_documento,
                    "telefono"                        => $request->rem_telefono,
                    "correo"                          => $request->rem_correo,
                    "tipo_remitente_id"               => $tipoRem,
                    "tipo_documento_identificacion_id" => $request->rem_tipo_documento_id
                ]);
                $data['remitente_id'] = $rem->id;
            }

            $data['es_anonimo'] = $tipoRem === 2 ? 1 : 0;

            // Eliminar documentos marcados para eliminar
            if ($request->filled('documentos_eliminar')) {
                foreach ($request->documentos_eliminar as $docId) {
                    $documento = Documento::find($docId);
                    if ($documento) {
                        // Verificar que el documento pertenece a esta solicitud
                        if ($solicitud->documentos->contains($docId)) {
                            if (Storage::exists($documento->ruta)) {
                                Storage::delete($documento->ruta);
                            }
                            $solicitud->documentos()->detach($docId);
                            $documento->delete();
                        }
                    }
                }
            }

            // Manejar firma digital
            if ($request->boolean('firma_digital')) {
                // Agregar nuevos archivos
                if ($request->hasFile('archivos')) {
                    $ordenActual = $solicitud->documentos()->count();

                    foreach ($request->file('archivos') as $archivo) {
                        $path = $archivo->store('documentos', 'public');

                        $documento = Documento::create([
                            'editor_id'      => Auth::id(),
                            'nombre_archivo' => $archivo->getClientOriginalName(),
                            'tamano_mb'      => round($archivo->getSize() / 1048576, 2),
                            'ruta'           => $path,
                        ]);

                        $ordenActual++;
                        $solicitud->documentos()->attach($documento->id, ['orden' => $ordenActual]);
                    }
                }

                // Si hay un solo archivo (compatibilidad)
                if ($request->hasFile('archivo')) {
                    $archivo = $request->file('archivo');
                    $path = $archivo->store('documentos', 'public');

                    $documento = Documento::create([
                        'editor_id'      => Auth::id(),
                        'nombre_archivo' => $archivo->getClientOriginalName(),
                        'tamano_mb'      => round($archivo->getSize() / 1048576, 2),
                        'ruta'           => $path,
                    ]);

                    $ordenActual = $solicitud->documentos()->count() + 1;
                    $solicitud->documentos()->attach($documento->id, ['orden' => $ordenActual]);
                }
            } else {
                // Si firma_digital es false, eliminar todos los documentos
                foreach ($solicitud->documentos as $doc) {
                    if (Storage::exists($doc->ruta)) {
                        Storage::delete($doc->ruta);
                    }
                    $solicitud->documentos()->detach($doc->id);
                    $doc->delete();
                }
            }

            $solicitud->update($data);

            if ($solicitud->completada) {
                $solicitud->estado_id = $solicitud->determinarEstadoFinal();
            } else {
                $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles($calculator);
                if ($estadoCalculado) {
                    $solicitud->estado_id = $estadoCalculado;
                }
            }

            $solicitud->save();

            DB::commit();

            return redirect()->route('grupos.solicitudes.index', $grupo)
                             ->with('success', 'Solicitud actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar solicitud: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Error al actualizar la solicitud.')
                             ->withInput();
        }
    }

    public function destroy(Grupo $grupo, Solicitud $solicitud)
    {
        if ($solicitud->grupo_id !== $grupo->id) {
            abort(403, 'No tienes permiso para eliminar esta solicitud.');
        }

        DB::beginTransaction();
        try {
            // Eliminar documentos asociados
            foreach ($solicitud->documentos as $doc) {
                if (Storage::exists($doc->ruta)) {
                    Storage::delete($doc->ruta);
                }
                $solicitud->documentos()->detach($doc->id);
                $doc->delete();
            }

            // Eliminar la solicitud para liberar el número
            $solicitud->forceDelete();

            DB::commit();

            return redirect()->route('grupos.solicitudes.index', $grupo)
                            ->with('success', 'Solicitud eliminada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar solicitud: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'Error al eliminar la solicitud.');
        }
    }


    public function overview(Request $request)
    {
        $query = Solicitud::with('tipoSolicitud', 'estado', 'documentos');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_fin);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('grupo_id')) {
            $query->where('grupo_id', $request->grupo_id);
        }

        $solicitudes = $query->get();

        $porTipo = $solicitudes->groupBy('tipoSolicitud.nombre')->map->count();
        $porEstado = $solicitudes->groupBy('estado.nombre')->map->count();

        $usuarios = User::all();
        $grupos = Grupo::all();

        return view('solicitudes.overview', compact('porTipo', 'porEstado', 'solicitudes', 'usuarios', 'grupos'));
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $usuario = $user->usuario;

        $tipos = TipoSolicitud::all();
        $estados = EstadoSolicitud::all();

        $grupoIds = $usuario ? $usuario->grupos->pluck('id') : collect([]);
        $tieneGrupos = $grupoIds->isNotEmpty();

        $solicitudes = Solicitud::with(['documentos'])
            ->whereIn('grupo_id', $grupoIds)
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo_solicitud_id', $request->tipo))
            ->when($request->filled('estado'), fn($q) => $q->where('estado_id', $request->estado))
            ->when($request->filled('fecha'), fn($q) => $q->whereDate('fecha_ingreso', $request->fecha))
            ->when($request->orden == 'recientes', fn($q) => $q->orderBy('fecha_ingreso', 'desc'))
            ->when($request->orden == 'antiguos', fn($q) => $q->orderBy('fecha_ingreso', 'asc'))
            ->when($request->orden == 'prioridad', fn($q) => $q->ordenarPor('prioridad'))
            ->with(['grupo', 'estado', 'tipoSolicitud'])
            ->get();

        $grupo = $tieneGrupos ? Grupo::find($grupoIds->first()) : null;

        return view('solicitudes.dashboard', compact('solicitudes', 'tipos', 'estados', 'tieneGrupos', 'grupo'));
    }

    public function soloPrioridad()
    {
        $user = Auth::user();
        $usuario = $user->usuario;

        $grupoIds = $usuario ? $usuario->grupos->pluck('id') : collect([]);
        $tieneGrupos = $grupoIds->isNotEmpty();

        if (!$tieneGrupos) {
            $solicitudes = collect();
        } else {
            $solicitudes = Solicitud::with(['tipoSolicitud', 'estado:id,nombre,descripcion', 'grupo', 'documentos'])
                ->whereIn('grupo_id', $grupoIds)
                ->ordenarPor('prioridad')
                ->get();
        }

        return view('solicitudes.dashboard', compact('solicitudes'));
    }

    
    public function show(Grupo $grupo, Solicitud $solicitud)
    {
        // Cargar documentos ordenados
        $solicitud->load([
            'documentos' => function($query) {
                $query->orderBy('solicitud_documento.orden');
            },
            'remitente'
        ]);

        return view('grupos.solicitudes.show', compact('grupo', 'solicitud'));
    }

    public function completar(Grupo $grupo, Solicitud $solicitud)
    {
        $solicitud->estado_id = $solicitud->determinarEstadoFinal();
        $solicitud->completada = true;
        $solicitud->save();

        return redirect()
            ->route('grupos.solicitudes.index', $grupo)
            ->with('success', 'La solicitud fue marcada como completada y su estado actualizado.');
    }

    public function revertir(Grupo $grupo, Solicitud $solicitud, BusinessDaysCalculator $calculator)
    {
        $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles($calculator);

        if ($estadoCalculado) {
            $solicitud->estado_id = $estadoCalculado;
        }

        $solicitud->completada = false;
        $solicitud->save();

        return redirect()
            ->route('grupos.solicitudes.index', $grupo)
            ->with('success', 'La solicitud fue revertida a no completada y su estado recalculado correctamente.');
    }

    /**
     * GESTIÓN DE DOCUMENTOS
     */

    /**
     * Agregar documento a una solicitud existente
     */
    public function agregarDocumento(Request $request, Grupo $grupo, Solicitud $solicitud)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $archivo = $request->file('archivo');
            $path = $archivo->store('documentos', 'public');

            $documento = Documento::create([
                'editor_id'      => Auth::id(),
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'tamano_mb'      => round($archivo->getSize() / 1048576, 2),
                'ruta'           => $path,
            ]);

            $ordenActual = $solicitud->documentos()->count() + 1;
            $solicitud->documentos()->attach($documento->id, ['orden' => $ordenActual]);

            DB::commit();

            return redirect()->back()
                             ->with('success', 'Documento agregado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar documento: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Error al agregar el documento.');
        }
    }

    /**
     * Eliminar un documento específico de una solicitud
     */
    public function eliminarDocumento(Grupo $grupo, Solicitud $solicitud, Documento $documento)
    {
        // Verificar que el documento pertenece a esta solicitud
        if (!$solicitud->documentos->contains($documento->id)) {
            abort(403, 'Este documento no pertenece a la solicitud.');
        }

        DB::beginTransaction();
        try {
            if (Storage::exists($documento->ruta)) {
                Storage::delete($documento->ruta);
            }

            $solicitud->documentos()->detach($documento->id);
            $documento->delete();

            DB::commit();

            return redirect()->back()
                             ->with('success', 'Documento eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Error al eliminar el documento.');
        }
    }

    /**
     * Reordenar documentos de una solicitud
     */
    public function reordenarDocumentos(Request $request, Grupo $grupo, Solicitud $solicitud)
    {
        $request->validate([
            'documentos' => 'required|array',
            'documentos.*.id' => 'required|exists:documento,id',
            'documentos.*.orden' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->documentos as $doc) {
                // Verificar que el documento pertenece a esta solicitud
                if ($solicitud->documentos->contains($doc['id'])) {
                    $solicitud->documentos()->updateExistingPivot($doc['id'], [
                        'orden' => $doc['orden']
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Orden actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al reordenar documentos: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar el orden',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
