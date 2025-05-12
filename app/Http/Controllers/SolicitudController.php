<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Grupo;
use App\Models\TipoSolicitud;
use App\Models\MedioRecepcion;
use App\Models\EstadoSolicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use Illuminate\Http\Request;

class SolicitudController extends Controller
{

    public function index(Request $request, Grupo $grupo)
    {
        $query = Solicitud::with(['tipoSolicitud', 'estado'])
        ->where('grupo_id', $grupo->id);


        // Filtrar por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo_solicitud_id', $request->tipo);
        }

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        // Filtrar por fecha
        if ($request->filled('fecha')) {
            $fecha = \Carbon\Carbon::parse($request->fecha)->startOfDay();
            $fin = \Carbon\Carbon::parse($request->fecha)->endOfDay();
            $query->whereBetween('created_at', [$fecha, $fin]);
        }

        // Ordenamiento
        if ($request->filled('orden')) {
            $orden = $request->orden === 'antiguos' ? 'asc' : 'desc';
            $query->orderBy('created_at', $orden);
        } else {
            $query->latest();
        }

        $solicitudes = $query->get();
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

        return view('solicitudes.edit', compact('solicitud', 'tiposSolicitud', 'mediosRecepcion', 'estados', 'grupo'));
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

    public function overview()
    {
        $solicitudes = Solicitud::with('tipoSolicitud', 'estado')->get();

        $porTipo = $solicitudes->groupBy('tipoSolicitud.nombre')->map->count();
        $porEstado = $solicitudes->groupBy('estado.nombre')->map->count();

        return view('solicitudes.overview', compact('porTipo', 'porEstado', 'solicitudes'));
    }

}
