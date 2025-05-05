<?php

namespace App\Http\Controllers;

use App\Models\TipoSolicitud;
use Illuminate\Http\Request;

class TipoSolicitudController extends Controller
{
    public function index() { return TipoSolicitud::all(); }

    public function store(Request $request) {
        return TipoSolicitud::create($request->validate(['nombre' => 'required|string|max:100']));
    }

    public function update(Request $request, TipoSolicitud $tipo) {
        $tipo->update($request->validate(['nombre' => 'required|string|max:100']));
        return $tipo;
    }
    
    public function destroy(TipoSolicitud $tipo) { $tipo->delete(); return response()->noContent(); }
}
