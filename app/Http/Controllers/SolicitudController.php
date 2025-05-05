<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\MedioRecepcion;
use App\Models\EstadoSolicitud;


use Illuminate\Http\Request;

class SolicitudController extends Controller
{

    public function index(Request $request)
    {
        $query = Solicitud::with(['tipoSolicitud', 'estado']);

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

        return view('solicitudes.index', compact('solicitudes', 'tipos', 'estados'));
    }



    public function create()
    {
        // Puedes cargar catálogos si los necesitas (tipoSolicitud, medioRecepcion, etc.)
        return view('solicitudes.create');
    }

    public function store(Request $request)
    {

        try{
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('Error de validación:');
            logger($e->errors());
            throw $e;
        }

        $data['usuario_id'] = auth()->guard('web')->user()->id;

        Solicitud::create($data);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada con éxito.');
    }

    public function edit(Solicitud $solicitud)
    {
        $tiposSolicitud = TipoSolicitud::pluck('nombre', 'id');
        $mediosRecepcion = MedioRecepcion::pluck('nombre', 'id');
        $estados = EstadoSolicitud::pluck('nombre', 'id');

        return view('solicitudes.edit', compact('solicitud', 'tiposSolicitud', 'mediosRecepcion', 'estados'));
    }


    public function update(Request $request, Solicitud $solicitud)
    {

        try {

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('Error de validación:');
            logger($e->errors());
            throw $e;
        }
        $data['firma_digital'] = $request->has('firma_digital') ? true : false;


        logger($data);

        $solicitud->update($data);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada.');
    }

    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada.');
    }

    public function overview()
    {
        $solicitudes = Solicitud::with('tipoSolicitud', 'estado')->get();

        $porTipo = $solicitudes->groupBy('tipoSolicitud.nombre')->map->count();
        $porEstado = $solicitudes->groupBy('estado.nombre')->map->count();

        return view('solicitudes.overview', compact('porTipo', 'porEstado', 'solicitudes'));
    }

}
