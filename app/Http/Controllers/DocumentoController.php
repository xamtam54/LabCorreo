<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index() { return Documento::with(['editor', 'tipoDocumento'])->get(); }

    public function store(Request $request) {
        return Documento::create($request->validate([
            'editor_id' => 'required|exists:usuarios,id',
            'nombre_archivo' => 'required|string|max:255',
            'tamano_mb' => 'required|numeric',
            'ruta' => 'required|string|max:255'
        ]));
    }

    public function descargar($id)
    {
        $documento = Documento::findOrFail($id);

        $ruta = $documento->ruta; // La ruta del archivo en Storage

        if (!Storage::exists($ruta)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Retorna la descarga del archivo
        return Storage::download($ruta, $documento->nombre_archivo);
    }
    public function ver($id)
    {
        $documento = Documento::findOrFail($id);

        $ruta = $documento->ruta;

        if (!Storage::exists($ruta)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Retornar archivo para visualización en navegador (inline)
        return response()->file(Storage::path($ruta));
    }
}
