<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Grupo;
use App\Models\TipoSolicitud;
use App\Models\MedioRecepcion;
use App\Models\EstadoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    public function edit(Grupo $grupo, Solicitud $solicitud)
    {

        if ($solicitud->grupo_id !== $grupo->id) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        $tiposSolicitud = TipoSolicitud::pluck('nombre', 'id');
        $mediosRecepcion = MedioRecepcion::pluck('nombre', 'id');
        $estados = EstadoSolicitud::pluck('nombre', 'id');

        return view('grupos.solicitudes.edit', compact('solicitud', 'tiposSolicitud', 'mediosRecepcion', 'estados', 'grupo'));
    }


    public function update(Request $request, Grupo $grupo, Solicitud $solicitud)
    {

        // Verificar que la solicitud pertenece al grupo
        if ($solicitud->grupo_id !== $grupo->id) {
            abort(403, 'No tienes permiso para modificar esta solicitud.');
        }

        $data = $request->validate([
            'numero_radicado' => 'required|string|unique:solicitud,numero_radicado,' . $solicitud->id,
            'tipo_solicitud_id' => 'required|exists:tipo_solicitud,id',
            'remitente' => 'required|string|max:255',
            'asunto' => 'nullable|string',
            'medio_recepcion_id' => 'required|exists:medio_recepcion,id',
            'fecha_ingreso' => 'required|date',
            'documento_adjunto_id' => 'nullable|exists:documento,id',
            'fecha_vencimiento' => 'nullable|date',
            'estado_id' => 'required|exists:estado_solicitud,id',
            'firma_digital' => 'nullable|boolean',
        ]);

        $data['firma_digital'] = $request->has('firma_digital') ? true : false;

        $solicitud->update($data);

        return redirect()->route('grupos.solicitudes.index', $grupo)->with('success', 'Solicitud actualizada.');
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
        $solicitudes = Solicitud::with(['tipoSolicitud', 'estado', 'grupo'])
            ->ordenarPor('prioridad')
            ->get();

        return view('solicitudes.dashboard', compact('solicitudes'));
    }



}
