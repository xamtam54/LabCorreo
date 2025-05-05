<?php

namespace App\Exports;

use App\Models\Solicitud;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SolicitudesExport
{
    public function exportToCSV()
    {
        return SimpleExcelWriter::streamDownload('solicitudes.csv')
            ->addRow(['Número de Radicado', 'Asunto', 'Tipo', 'Estado', 'Fecha de Creación'])
            ->addRows(Solicitud::all()->map(function ($solicitud) {
                return [
                    $solicitud->numero_radicado,
                    $solicitud->asunto,
                    $solicitud->tipoSolicitud->nombre ?? 'No definido',
                    $solicitud->estado->nombre ?? 'Sin estado',
                    $solicitud->created_at->format('Y-m-d H:i'),
                ];
            }))
            ->toBrowser();
    }


    public function exportToExcel()
    {
        return SimpleExcelWriter::streamDownload('solicitudes.xlsx')
            ->addRow(['Número de Radicado', 'Asunto', 'Tipo', 'Estado', 'Fecha de Creación'])
            ->addRows(Solicitud::all()->map(function ($solicitud) {
                return [
                    $solicitud->numero_radicado,
                    $solicitud->asunto,
                    $solicitud->tipoSolicitud->nombre ?? 'No definido',
                    $solicitud->estado->nombre ?? 'Sin estado',
                    $solicitud->created_at->format('Y-m-d H:i'),
                ];
            }))
            ->toBrowser();
    }

}
