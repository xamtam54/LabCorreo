<?php

namespace App\Exports;

use App\Models\Solicitud;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SolicitudesExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function filteredSolicitudes()
    {
        $query = Solicitud::with([
            'tipoSolicitud',
            'estado',
            'medioRecepcion',
            'usuario',
            'grupo',
            'remitente'
        ]);

        if ($this->request->filled('fecha_inicio')) {
            $query->whereDate('fecha_ingreso', '>=', $this->request->fecha_inicio);
        }

        if ($this->request->filled('fecha_fin')) {
            $query->whereDate('fecha_ingreso', '<=', $this->request->fecha_fin);
        }

        if ($this->request->filled('usuario_id')) {
            $query->where('usuario_id', $this->request->usuario_id);
        }

        if ($this->request->filled('grupo_id')) {
            $query->where('grupo_id', $this->request->grupo_id);
        }

        return $query->get();
    }

    public function exportToCSV()
    {
        $solicitudes = $this->filteredSolicitudes();

        return SimpleExcelWriter::streamDownload('solicitudes.csv')
            ->noHeaderRow()
            ->addRow([
                'Número de Radicado',
                'Asunto',
                'Remitente',
                'Contenido',
                'Tipo de Solicitud',
                'Medio de Recepción',
                'Estado',
                'Fecha de Ingreso',
                'Fecha de Vencimiento',
                'Usuario Responsable',
                'Grupo',
                'Firma Digital',
                'Completada',
                'Fecha de Creación',
                'Hora de Creación'

            ])
            ->addRows($solicitudes->map(function ($s) {
                return [
                    $s->numero_radicado,
                    $s->asunto ?? 'NULL',
                    $s->remitente_id->nombre ?? 'NULL',
                    $s->contenido ?? 'NULL',
                    $s->tipoSolicitud->nombre ?? 'No definido',
                    $s->medioRecepcion->nombre ?? 'No definido',
                    $s->estado->nombre ?? 'Sin estado',
                    $s->fecha_ingreso ? \Carbon\Carbon::parse($s->fecha_ingreso)->format('Y-m-d') : 'NULL',
                    $s->fecha_vencimiento ? \Carbon\Carbon::parse($s->fecha_vencimiento)->format('Y-m-d') : 'NULL',
                    $s->usuario->nombres ?? 'Desconocido',
                    $s->grupo->nombre ?? 'N/A',
                    $s->firma_digital ? 'Sí' : 'No',
                    $s->completada ? 'Sí' : 'No',
                    optional($s->created_at)->format('Y-m-d'),
                    optional($s->created_at)->format('H:i:s'),
                ];
            }))
            ->toBrowser();
    }

public function exportToExcel()
{
    Log::info('ExportToExcel iniciado');

    try {
        $solicitudes = $this->filteredSolicitudes();

        Log::info('Solicitudes filtradas', [
            'total' => $solicitudes->count()
        ]);

        return SimpleExcelWriter::streamDownload('solicitudes.xlsx')
            ->noHeaderRow()
            ->addRow([
                'Número de Radicado',
                'Asunto',
                'Remitente',
                'Contenido',
                'Tipo de Solicitud',
                'Medio de Recepción',
                'Estado',
                'Fecha de Ingreso',
                'Fecha de Vencimiento',
                'Usuario Responsable',
                'Grupo',
                'Firma Digital',
                'Completada',
                'Fecha de Creación',
                'Hora de Creación'
            ])
            ->addRows($solicitudes->map(function ($s) {

                Log::debug('Procesando solicitud', [
                    'id' => $s->id,
                    'numero_radicado' => $s->numero_radicado
                ]);

                // Log si falta alguna relación
                if (!$s->remitente_id) {
                    Log::warning("Solicitud {$s->id}: remitente NULL");
                }
                if (!$s->tipoSolicitud) {
                    Log::warning("Solicitud {$s->id}: tipoSolicitud NULL");
                }
                if (!$s->medioRecepcion) {
                    Log::warning("Solicitud {$s->id}: medioRecepcion NULL");
                }
                if (!$s->estado) {
                    Log::warning("Solicitud {$s->id}: estado NULL");
                }
                if (!$s->usuario) {
                    Log::warning("Solicitud {$s->id}: usuario NULL");
                }
                if (!$s->grupo) {
                    Log::warning("Solicitud {$s->id}: grupo NULL");
                }

                return [
                    $s->numero_radicado,
                    $s->asunto ?? 'NULL',
                    $s->remitente->nombre ?? 'NULL', // ← FIX
                    $s->contenido ?? 'NULL',
                    $s->tipoSolicitud->nombre ?? 'No definido',
                    $s->medioRecepcion->nombre ?? 'No definido',
                    $s->estado->nombre ?? 'Sin estado',
                    $s->fecha_ingreso ? \Carbon\Carbon::parse($s->fecha_ingreso)->format('Y-m-d') : 'NULL',
                    $s->fecha_vencimiento ? \Carbon\Carbon::parse($s->fecha_vencimiento)->format('Y-m-d') : 'NULL',
                    $s->usuario->nombres ?? 'Desconocido',
                    $s->grupo->nombre ?? 'N/A',
                    $s->firma_digital ? 'Sí' : 'No',
                    $s->completada ? 'Sí' : 'No',
                    optional($s->created_at)->format('Y-m-d'),
                    optional($s->created_at)->format('H:i:s'),
                ];
            }))
            ->toBrowser();

    } catch (\Throwable $e) {

        Log::error('Error en exportToExcel', [
            'mensaje' => $e->getMessage(),
            'linea'   => $e->getLine(),
            'archivo' => $e->getFile(),
        ]);

        return back()->with('error', 'Ocurrió un error al generar el Excel');
    }
}

}
