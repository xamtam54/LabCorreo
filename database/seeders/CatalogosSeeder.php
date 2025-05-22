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
        DB::table('tipo_solicitud')->insert([
            ['nombre' => 'Petición'],
            ['nombre' => 'Queja'],
            ['nombre' => 'Reclamo'],
            ['nombre' => 'Sugerencia']
        ]);

        // Medios de recepción
        DB::table('medio_recepcion')->insert([
            ['nombre' => 'Correo'],
            ['nombre' => 'Web'],
            ['nombre' => 'Teléfono'],
            ['nombre' => 'Presencial']
        ]);

        // Estados de la solicitud
        DB::table('estado_solicitud')->insert([
            ['nombre' => 'Recibida', 'descripcion' => 'La solicitud ha ingresado y aún no ha sido procesada.'],
            ['nombre' => 'En Revisión', 'descripcion' => 'La solicitud está siendo gestionada; quedan más de 10 días para completarla.'],
            ['nombre' => 'Por Vencer', 'descripcion' => 'La solicitud está próxima a su vencimiento (quedan 5 días).'],
            ['nombre' => 'Respondida', 'descripcion' => 'La solicitud fue atendida pero falta adjuntar el documento requerido.'],
            ['nombre' => 'Cerrada', 'descripcion' => 'La solicitud fue respondida y el documento fue adjuntado de ser necesario.'],
        ]);

    }
}
