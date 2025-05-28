<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
public function run()
    {
        // Tipos de solicitud: PQRS
        $tipos = [
            'Petición', 'Queja', 'Reclamo', 'Sugerencia',
            'Felicitación', 'Denuncia', 'Solicitud de Información'
        ];

        foreach ($tipos as $nombre) {
            DB::table('tipo_solicitud')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre]
            );
        }

        // Medios de recepción
        $medios = ['Correo', 'Web', 'Teléfono', 'Físico', 'Chat', 'Redes Sociales'];

        foreach ($medios as $nombre) {
            DB::table('medio_recepcion')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre]
            );
        }

        // Estados de la solicitud
        $estados = [
            ['nombre' => 'Nueva', 'descripcion' => 'Solicitud registrada, sin procesar.'],
            ['nombre' => 'En Revisión', 'descripcion' => 'Solicitud en proceso, quedan más de 10 días.'],
            ['nombre' => 'Por Vencer', 'descripcion' => 'Quedan pocos días para su vencimiento (5 o menos).'],
            ['nombre' => 'Respondida', 'descripcion' => 'Respuesta emitida, falta adjuntar documento.'],
            ['nombre' => 'Cerrada', 'descripcion' => 'Solicitud completada con respuesta y documentos.'],
            ['nombre' => 'Expirada', 'descripcion' => 'Se venció el plazo sin gestión.'],
        ];

        foreach ($estados as $estado) {
            DB::table('estado_solicitud')->updateOrInsert(
                ['nombre' => $estado['nombre']],
                ['descripcion' => $estado['descripcion']]
            );
        }
    }
}
