<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;


class RolSeeder extends Seeder
{
    public function run()
    {
        Rol::create(['nombre' => 'Administrador']); // Tiene acceso total a toda la aplicación.
                                                    // Puede crear, editar y eliminar usuarios y grupos.
                                                    // Configura opciones generales del sistema.
                                                    // Visualiza y gestiona reportes y estadísticas.
                                                    // Asigna roles a otros usuarios.
        Rol::create(['nombre' => 'Gestor_grupos']); // Puede crear y administrar grupos.
                                                    // Puede agregar y bloquear usuarios en los grupos.
                                                    // No puede eliminar usuarios.
                                                    // Puede ver la actividad dentro de los grupos que gestiona.
        Rol::create(['nombre' => 'Miembro_grupo']); // Solo tiene acceso a los grupos a los que pertenece.
                                                    // Puede participar en las actividades del grupo (comentar, subir archivos, visualizar contenido).
                                                    // No puede crear ni administrar grupos ni gestionar usuarios.
    }
}
