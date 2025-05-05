<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index() { return TipoDocumento::all(); }
    public function store(Request $request) {
        return TipoDocumento::create($request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]));
    }
    public function update(Request $request, TipoDocumento $tipo) {
        $tipo->update($request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string'
        ]));
        return $tipo;
    }
    public function destroy(TipoDocumento $tipo) { $tipo->delete(); return response()->noContent(); }
}
