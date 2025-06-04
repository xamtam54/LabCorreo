<?php

namespace App\Exports;

use App\Models\Solicitud;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Http\Request;


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
            'grupo'
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
                    $s->remitente ?? 'NULL',
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
        $solicitudes = $this->filteredSolicitudes();

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
                return [
                    $s->numero_radicado,
                    $s->asunto ?? 'NULL',
                    $s->remitente ?? 'NULL',
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
}
