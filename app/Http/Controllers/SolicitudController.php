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
use App\Services\BusinessDaysCalculator;

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

    public function store(Request $request, Grupo $grupo, BusinessDaysCalculator $calculator)
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

        // Crear la solicitud
        $solicitud = Solicitud::create($data);

        // Calcular estado según días hábiles
        $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles($calculator);
        if ($estadoCalculado) {
            $solicitud->estado_id = $estadoCalculado;
            $solicitud->save();
        }

        return redirect()->route('grupos.solicitudes.index', $grupo)
                        ->with('success', 'Solicitud creada con éxito.');
    }

    public function update(Request $request, Grupo $grupo, Solicitud $solicitud, BusinessDaysCalculator $calculator)
    {
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

        if ($request->boolean('firma_digital')) {
            $rules['archivo'] = 'nullable|file|max:10240';
        }

        $data = $request->validate($rules);
        $data['usuario_id'] = Auth::user()->id;

        // Cargar relación documento
        $solicitud->load('documento');

        if ($request->boolean('firma_digital')) {
            if ($request->hasFile('archivo')) {

                if ($solicitud->documento) {
                    $solicitud->documento->eliminarArchivo();
                } else {
                    Log::info('No hay documento anterior para eliminar.');
                }

                $path = $request->file('archivo')->store('documentos');

                $documento = Documento::create([
                    'editor_id' => Auth::user()->id,
                    'nombre_archivo' => $request->file('archivo')->getClientOriginalName(),
                    'tamano_mb' => round($request->file('archivo')->getSize() / 1048576, 2),
                    'ruta' => $path,
                ]);
                $data['documento_adjunto_id'] = $documento->id;
            } else {
                $data['documento_adjunto_id'] = $solicitud->documento_adjunto_id;
            }
        } else {
            if ($solicitud->documento) {
                $solicitud->documento->eliminarArchivo();
            } else {
            }

            $data['documento_adjunto_id'] = null;
        }

        $solicitud->update($data);

        // Recalcular estado
        if ($solicitud->completada) {
            $solicitud->estado_id = $solicitud->determinarEstadoFinal();
        } else {
            $estadoCalculado = $solicitud->calcularEstadoSegunDiasHabiles($calculator);
            if ($estadoCalculado) {
                $solicitud->estado_id = $estadoCalculado;
            }
        }

        $solicitud->save();

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
        $usuario = $user->usuario;

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

    // definir si la solicitud se completo
    public function completar(Grupo $grupo, Solicitud $solicitud)
    {

        $solicitud->estado_id = $solicitud->determinarEstadoFinal();
        $solicitud->completada = true;
        $solicitud->save();

        return redirect()
            ->route('grupos.solicitudes.index', $grupo)
            ->with('success', 'La solicitud fue marcada como completada y su estado actualizado.');
    }

    // revertir el proceso de completado de la solicitud
    public function revertir(Grupo $grupo, Solicitud $solicitud, BusinessDaysCalculator $calculator)
    {
        // Recalcula el estado dinámicamente según días hábiles usando el servicio
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


}
