<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index() { return Documento::with(['editor', 'tipoDocumento'])->get(); }

    public function store(Request $request) {
        return Documento::create($request->validate([
            'editor_id' => 'required|exists:usuarios,id',
            'nombre_archivo' => 'required|string|max:255',
            'tipo_documento_id' => 'required|exists:tipo_documento,id',
            'tamano_mb' => 'required|numeric',
            'ruta' => 'required|string|max:255'
        ]));
    }
}
