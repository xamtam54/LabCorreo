<?php

namespace App\Http\Controllers;

use App\Exports\SolicitudesExport;
use Illuminate\Http\Request;

class SolicitudExportController extends Controller
{
    public function exportCSV(Request $request)
    {
        return (new SolicitudesExport($request))->exportToCSV();
    }

    public function exportExcel(Request $request)
    {
        return (new SolicitudesExport($request))->exportToExcel();
    }
}
