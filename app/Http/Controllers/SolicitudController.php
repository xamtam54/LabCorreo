<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Grupo;
use App\Models\TipoSolicitud;
use App\Models\MedioRecepcion;
use App\Models\EstadoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;


use Illuminate\Http\Request;

class SolicitudController extends Controller
{

    public function index(Request $request, Grupo $grupo)
    {
        $solicitudes = Solicitud::with(['tipoSolicitud', 'estado'])
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
        return view('grupos.solicitudes.create', compact('grupo'));
    }

    public function store(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'numero_radicado' => 'required|string|unique:solicitud',
            'tipo_solicitud_id' => 'required|exists:tipo_solicitud,id',
            'remitente' => 'required|string|max:255',
            'asunto' => 'nullable|string',
            'contenido' => 'nullable|string',
            'medio_recepcion_id' => 'required|exists:medio_recepcion,id',
            'fecha_ingreso' => 'required|date',
            'documento_adjunto_id' => 'nullable|exists:documento,id',
            'fecha_vencimiento' => 'nullable|date',
            'estado_id' => 'required|exists:estado_solicitud,id',
            'firma_digital' => 'boolean',
        ]);


        $data['usuario_id'] = Auth::user()->id;
        $data['grupo_id'] = $grupo->id;

        Solicitud::create($data);

        return redirect()->route('grupos.solicitudes.index', $grupo)->with('success', 'Solicitud creada con éxito.');
    }

    public function update(Request $request, Grupo $grupo, Solicitud $solicitud)
    {
        Log::info('Usuario autenticado:', ['user' => Auth::user()]);

        // Validar campos base
        $rules = [
            'tipo_solicitud_id' => 'required|exists:tipo_solicitud,id',
            'remitente' => 'required|string|max:255',
            'asunto' => 'nullable|string|max:255',
            'contenido' => 'nullable|string',
            'medio_recepcion_id' => 'required|exists:medio_recepcion,id',
            'fecha_ingreso' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_ingreso',
            'estado_id' => 'required|exists:estado_solicitud,id',
            'firma_digital' => 'boolean',
        ];

        // Si firma_digital es true, archivo puede ser obligatorio (según lógica)
        if ($request->boolean('firma_digital')) {
            $rules['archivo'] = 'nullable|file|max:10240';
        }

        $data = $request->validate($rules);
        Log::info('Datos validados para actualización:', $data);

        $data['usuario_id'] = Auth::user()->id;

        // Manejo del archivo
        if ($request->boolean('firma_digital')) {
            if ($request->hasFile('archivo')) {
                // Elimina archivo anterior si existe
                if ($solicitud->documento_adjunto) {
                    Storage::delete($solicitud->documento_adjunto->ruta);
                    $solicitud->documento_adjunto->delete();
                }

                // Guarda el nuevo archivo
                $path = $request->file('archivo')->store('documentos');
                Log::info('Archivo actualizado guardado en:', ['path' => $path]);

                $documento = Documento::create([
                    'editor_id' => Auth::user()->id,
                    'nombre_archivo' => $request->file('archivo')->getClientOriginalName(),
                    'tamano_mb' => round($request->file('archivo')->getSize() / 1048576, 2),
                    'ruta' => $path,
                ]);

                Log::info('Documento actualizado creado:', ['documento' => $documento]);

                $data['documento_adjunto_id'] = $documento->id;
            } else {
                // Mantener documento actual si no se envía uno nuevo
                $data['documento_adjunto_id'] = $solicitud->documento_adjunto_id;
            }
        } else {
            // Si ya no se requiere firma digital, se elimina documento si había
            if ($solicitud->documento_adjunto) {
                Storage::delete($solicitud->documento_adjunto->ruta);
                $solicitud->documento_adjunto->delete();
            }
            $data['documento_adjunto_id'] = null;
        }

        $solicitud->update($data);
        Log::info('Solicitud actualizada:', ['solicitud' => $solicitud]);

        return redirect()->route('grupos.solicitudes.index', $grupo)
                        ->with('success', 'Solicitud actualizada correctamente.');
    }

    public function edit(Grupo $grupo, Solicitud $solicitud)
    {
        if ($solicitud->grupo_id !== $grupo->id) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        // Cargar la relación correcta
        $solicitud->load('documento');

        $tiposSolicitud = TipoSolicitud::pluck('nombre', 'id');
        $mediosRecepcion = MedioRecepcion::pluck('nombre', 'id');
        $estados = EstadoSolicitud::pluck('nombre', 'id');

        return view('grupos.solicitudes.edit', compact('solicitud', 'tiposSolicitud', 'mediosRecepcion', 'estados', 'grupo'));
    }


    public function destroy(Grupo $grupo, Solicitud $solicitud)
    {
        if ($solicitud->grupo_id !== $grupo->id) {
        abort(403, 'No tienes permiso para eliminar esta solicitud.');
        }
        $solicitud->delete();

        return redirect()->route('grupos.solicitudes.index', $grupo)->with('success', 'Solicitud eliminada.');
    }

    public function overview(Request $request)
    {
        $query = Solicitud::with('tipoSolicitud', 'estado');

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

        $usuarios = User::all();  // Puedes optimizar esto con select('id', 'name')
        $grupos = Grupo::all();

        return view('solicitudes.overview', compact('porTipo', 'porEstado', 'solicitudes', 'usuarios', 'grupos'));
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $usuario = $user->usuario; // relación que tienes que definir en User.php

        $tipos = TipoSolicitud::all();
        $estados = EstadoSolicitud::all();

        $grupoIds = $usuario ? $usuario->grupos->pluck('id') : collect([]);
        $tieneGrupos = $grupoIds->isNotEmpty();

        $solicitudes = Solicitud::whereIn('grupo_id', $grupoIds)
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo_solicitud_id', $request->tipo))
            ->when($request->filled('estado'), fn($q) => $q->where('estado_id', $request->estado))
            ->when($request->filled('fecha'), fn($q) => $q->whereDate('fecha_ingreso', $request->fecha))
            ->when($request->orden == 'recientes', fn($q) => $q->orderBy('fecha_ingreso', 'desc'))
            ->when($request->orden == 'antiguos', fn($q) => $q->orderBy('fecha_ingreso', 'asc'))
            ->when($request->orden == 'prioridad', fn($q) => $q->orderBy('prioridad', 'desc'))
            ->with(['grupo', 'estado', 'tipoSolicitud'])
            ->get();

        $grupo = $tieneGrupos ? Grupo::find($grupoIds->first()) : null;

        return view('solicitudes.dashboard', compact('solicitudes', 'tipos', 'estados', 'tieneGrupos', 'grupo'));
    }

    public function soloPrioridad()
    {
        $solicitudes = Solicitud::with(['tipoSolicitud', 'estado:id,nombre,descripcion', 'grupo'])
            ->ordenarPor('prioridad')
            ->get();

        return view('solicitudes.dashboard', compact('solicitudes'));
    }


    public function show(Grupo $grupo, Solicitud $solicitud)
    {
        $solicitud->load('documento'); // Carga relación

        // Opcional para debug:
        // dd($solicitud->documento);

        return view('grupos.solicitudes.show', compact('grupo', 'solicitud'));
    }

    public function completar(Grupo $grupo, Solicitud $solicitud)
    {
        // Carga los estados "Cerrada" y "Respondida"
        $estados = DB::table('estado_solicitud')
            ->whereIn('nombre', ['Cerrada', 'Respondida'])
            ->get()
            ->keyBy('nombre');

        if ($solicitud->firma_digital) {
            // firma_digital = 1
            if ($solicitud->documento) {
                $solicitud->estado_id = $estados['Cerrada']->id ?? $solicitud->estado_id;
            } else {
                $solicitud->estado_id = $estados['Respondida']->id ?? $solicitud->estado_id;
            }
        } else {
            // firma_digital = 0, no importa documento
            $solicitud->estado_id = $estados['Cerrada']->id ?? $solicitud->estado_id;
        }

        $solicitud->completada = true;
        $solicitud->save();

        return redirect()->route('grupos.solicitudes.index', $grupo)
                        ->with('success', 'La solicitud fue marcada como completada y su estado actualizado.');
    }


    public function revertir(Grupo $grupo, Solicitud $solicitud)
    {
        $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles();

        if ($estadoCalculado) {
            $solicitud->estado_id = $estadoCalculado;
        }

        $solicitud->completada = false;
        $solicitud->save();

        return redirect()->route('grupos.solicitudes.index', $grupo)
                        ->with('success', 'La solicitud fue revertida a no completada y su estado recalculado correctamente.');
    }


}
