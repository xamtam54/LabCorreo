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
        $query = Solicitud::with('tipoSolicitud', 'estado');

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
            ->addRow(['Número de Radicado', 'Asunto', 'Tipo', 'Estado', 'Fecha de Creación'])
            ->addRows($solicitudes->map(function ($s) {
                return [
                    $s->numero_radicado,
                    $s->asunto,
                    $s->tipoSolicitud->nombre ?? 'No definido',
                    $s->estado->nombre ?? 'Sin estado',
                    optional($s->created_at)->format('Y-m-d H:i'),
                ];
            }))
            ->toBrowser();
    }


    public function exportToExcel()
    {
        $solicitudes = $this->filteredSolicitudes();

        return SimpleExcelWriter::streamDownload('solicitudes.xlsx')
            ->addRow(['Número de Radicado', 'Asunto', 'Tipo', 'Estado', 'Fecha de Creación'])
            ->addRows($solicitudes->map(function ($s) {
                return [
                    $s->numero_radicado,
                    $s->asunto,
                    $s->tipoSolicitud->nombre ?? 'No definido',
                    $s->estado->nombre ?? 'Sin estado',
                    optional($s->created_at)->format('Y-m-d H:i'),
                ];
            }))
            ->toBrowser();
    }

}
