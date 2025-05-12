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
            ['nombre' => 'Pendiente', 'descripcion' => 'Solicitud recibida pero aún no atendida.'],
            ['nombre' => 'En Proceso', 'descripcion' => 'La solicitud está siendo gestionada.'],
            ['nombre' => 'Respondido', 'descripcion' => 'Se ha dado una respuesta al solicitante.'],
            ['nombre' => 'Cerrado', 'descripcion' => 'El caso fue cerrado definitivamente.']
        ]);
    }
}
