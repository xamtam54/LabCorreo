<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Facades\Log;

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

    public function descargar($grupo, $id)
    {
        //Log::info("Intentando descargar documento con ID: [$id]");

        $documento = Documento::findOrFail($id);

        if (empty($documento->ruta)) {
            //Log::warning("Documento sin ruta.");
            abort(404, 'Ruta no encontrada.');
        }

        $ruta = $documento->ruta;

        if (!Storage::exists($ruta)) {
            //Log::warning("Archivo no encontrado para descarga: $ruta");
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::download($ruta, $documento->nombre_archivo);
    }


    public function ver($grupo, $id)
    {
        //Log::info("Intentando ver documento con ID: [$id]");

        $documento = Documento::findOrFail($id);

        if (empty($documento->ruta)) {
            //Log::warning("Documento sin ruta.");
            abort(404, 'Ruta no encontrada.');
        }

        $ruta = $documento->ruta;

        if (!Storage::exists($ruta)) {
            //Log::warning("Archivo no encontrado: $ruta");
            abort(404, 'Archivo no encontrado.');
        }
        return response()->file(Storage::path($ruta));
    }


    public function eliminar($grupo, $id)
    {
        $documento = Documento::findOrFail($id);
        $documento->eliminarArchivo();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }


}
