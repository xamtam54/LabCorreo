<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index() {
        return Reporte::with(['autor', 'solicitudRelacionada'])->get();
    }
    public function store(Request $request) {
        return Reporte::create($request->validate([
            'autor_id' => 'required|exists:usuarios,id',
            'tipo' => 'required|string|max:100',
            'contenido' => 'required|string',
            'solicitud_relacionada_id' => 'required|exists:solicitud,id',
            'fecha_generacion' => 'required|date'
        ]));
    }
}
