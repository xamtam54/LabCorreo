<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemitenteCatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // TIPOS DE REMITENTE
        DB::table('tipo_remitente')->insert([
            [
                'nombre' => 'Persona Natural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Anónimo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Representante de una Organización',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // TIPOS DE DOCUMENTO
        DB::table('tipo_documento_identificacion')->insert([
            [
                'nombre' => 'Cédula de Ciudadanía',
                'abreviatura' => 'CC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Identidad',
                'abreviatura' => 'TI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cédula de Extranjería',
                'abreviatura' => 'CE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Pasaporte',
                'abreviatura' => 'PA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'NIT',
                'abreviatura' => 'NIT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
