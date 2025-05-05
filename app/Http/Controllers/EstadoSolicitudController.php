<?php

namespace App\Http\Controllers;

use App\Models\EstadoSolicitud;
use Illuminate\Http\Request;

class EstadoSolicitudController extends Controller
{
    public function index() { return EstadoSolicitud::all(); }
    public function store(Request $request) {
        return EstadoSolicitud::create($request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]));
    }
    public function update(Request $request, EstadoSolicitud $estado) {
        $estado->update($request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]));
        return $estado;
    }
    public function destroy(EstadoSolicitud $estado) { $estado->delete(); return response()->noContent(); }
}
