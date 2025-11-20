<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{
    public function index()
    {
        return Documento::with(['editor', 'solicitudes'])->get();
    }

    /**
     * Almacenar múltiples documentos para una solicitud
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'solicitud_id' => 'required|exists:solicitud,id',
            'documentos' => 'required|array',
            'documentos.*.editor_id' => 'required|exists:usuarios,id',
            'documentos.*.nombre_archivo' => 'required|string|max:255',
            'documentos.*.tamano_mb' => 'required|numeric',
            'documentos.*.ruta' => 'required|string|max:255',
            'documentos.*.orden' => 'nullable|integer'
        ]);

        $solicitud = Solicitud::findOrFail($validated['solicitud_id']);
        $documentosCreados = [];

        DB::beginTransaction();
        try {
            foreach ($validated['documentos'] as $index => $docData) {
                // Crear el documento
                $documento = Documento::create([
                    'editor_id' => $docData['editor_id'],
                    'nombre_archivo' => $docData['nombre_archivo'],
                    'tamano_mb' => $docData['tamano_mb'],
                    'ruta' => $docData['ruta']
                ]);

                // Asociar con la solicitud usando la tabla pivote
                $orden = $docData['orden'] ?? ($index + 1);
                $solicitud->documentos()->attach($documento->id, ['orden' => $orden]);

                $documentosCreados[] = $documento;
            }

            DB::commit();
            return response()->json([
                'message' => 'Documentos creados exitosamente',
                'documentos' => $documentosCreados
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear documentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Almacenar un solo documento (método simplificado)
     */
    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'solicitud_id' => 'required|exists:solicitud,id',
            'editor_id' => 'required|exists:usuarios,id',
            'nombre_archivo' => 'required|string|max:255',
            'tamano_mb' => 'required|numeric',
            'ruta' => 'required|string|max:255',
            'orden' => 'nullable|integer'
        ]);

        $solicitud = Solicitud::findOrFail($validated['solicitud_id']);

        DB::beginTransaction();
        try {
            $documento = Documento::create([
                'editor_id' => $validated['editor_id'],
                'nombre_archivo' => $validated['nombre_archivo'],
                'tamano_mb' => $validated['tamano_mb'],
                'ruta' => $validated['ruta']
            ]);

            // Obtener el siguiente orden si no se especifica
            $orden = $validated['orden'] ?? ($solicitud->documentos()->count() + 1);
            $solicitud->documentos()->attach($documento->id, ['orden' => $orden]);

            DB::commit();
            return response()->json([
                'message' => 'Documento creado exitosamente',
                'documento' => $documento
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener documentos de una solicitud específica
     */
    public function porSolicitud($grupo, $solicitudId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);
        return $solicitud->documentos()
            ->withPivot('orden')
            ->orderBy('solicitud_documento.orden')
            ->get();
    }

    public function descargar($grupo, Documento $documento)
    {
        $ruta = $documento->ruta;

        if (empty($ruta)) {
            abort(404, 'Ruta no encontrada.');
        }

        if (!Storage::disk('public')->exists($ruta)) {
            abort(404, 'Archivo no encontrado en el disco público.');
        }

        // Ruta correcta usando el disco public
        $fullPath = Storage::disk('public')->path($ruta);

        // Forzar descarga con nombre original
        return response()->download($fullPath, $documento->nombre_archivo, [
            'Content-Type' => 'application/octet-stream'
        ]);
    }


    public function ver($grupo, Documento $documento)
    {
        $ruta = $documento->ruta;

        if (empty($ruta)) {
            abort(404, 'Ruta no encontrada.');
        }

        if (!Storage::disk('public')->exists($ruta)) {
            abort(404, 'Archivo no encontrado en el disco público.');
        }

        // Opción A: servir el archivo desde el filesystem (inline)
        $fullPath = Storage::disk('public')->path($ruta);
        return response()->file($fullPath);

        // Opción B (alternativa, más simple para abrir en nueva pestaña):
        // return redirect(Storage::disk('public')->url($ruta));
    }

    public function eliminar($grupo, $id)
    {
        $documento = Documento::findOrFail($id);

        // La relación con solicitudes se elimina automáticamente si está configurada
        // la tabla pivote con onDelete('cascade')
        $documento->eliminarArchivo();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }

    /**
     * Actualizar el orden de documentos de una solicitud
     */
    public function actualizarOrden(Request $request, $grupo, $solicitudId)
    {
        $validated = $request->validate([
            'documentos' => 'required|array',
            'documentos.*.id' => 'required|exists:documento,id',
            'documentos.*.orden' => 'required|integer'
        ]);

        $solicitud = Solicitud::findOrFail($solicitudId);

        DB::beginTransaction();
        try {
            foreach ($validated['documentos'] as $doc) {
                $solicitud->documentos()->updateExistingPivot($doc['id'], [
                    'orden' => $doc['orden']
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Orden actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar orden',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desvincular documento de una solicitud (sin eliminarlo)
     */
    public function desvincular($grupo, $solicitudId, $documentoId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);
        $solicitud->documentos()->detach($documentoId);

        return redirect()->back()->with('success', 'Documento desvinculado de la solicitud.');
    }
}
