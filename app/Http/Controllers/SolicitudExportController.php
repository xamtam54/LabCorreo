<?php

namespace App\Http\Controllers;

use App\Exports\SolicitudesExport;

class SolicitudExportController extends Controller
{
    public function exportCSV()
    {
        return (new SolicitudesExport)->exportToCSV();
    }

    public function exportExcel()
    {
        return (new SolicitudesExport)->exportToExcel();
    }
}
