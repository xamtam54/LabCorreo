<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Solicitud;
use Carbon\Carbon;

class ActualizarEstadosSolicitudes extends Command
{
    protected $signature = 'solicitudes:actualizar-estados';
    protected $description = 'Actualizar estados de solicitudes según reglas de tiempo';

    public function handle()
    {
        $now = Carbon::now();

        // Obtiene solicitudes no cerradas
        $solicitudes = Solicitud::whereHas('estado', function($q) {
            $q->where('nombre', '!=', 'Cerrada');
        })->get();

        foreach ($solicitudes as $solicitud) {
            $created = $solicitud->created_at;
            $daysPassed = $created->diffInDays($now);

            // Ejemplo de reglas
            if ($daysPassed > 25 && $solicitud->estado->nombre != 'Por Vencer') {
                $solicitud->estado_id = 3; // Por Vencer (ajusta el id)
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a Por Vencer");
            } elseif ($daysPassed > 10 && $solicitud->estado->nombre != 'En Revisión') {
                $solicitud->estado_id = 2; // En Revisión
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a En Revisión");
            } elseif ($daysPassed <= 10 && $solicitud->estado->nombre != 'Recibida') {
                $solicitud->estado_id = 1; // Recibida
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a Recibida");
            }
        }

        $this->info('Actualización de estados completada.');
    }
}

