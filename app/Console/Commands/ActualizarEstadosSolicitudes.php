<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Solicitud;
use Carbon\Carbon;

class ActualizarEstadosSolicitudes extends Command
{
    protected $signature = 'solicitudes:actualizar-estados';
    protected $description = 'Actualizar estados de solicitudes según días hábiles transcurridos';

    public function handle()
    {
        $now = Carbon::now();

        // Solicitudes que NO están ni en Respondida ni en Cerrada
        $solicitudes = Solicitud::whereHas('estado', function($q) {
            $q->whereNotIn('nombre', ['Respondida', 'Cerrada']);
        })->get();

        foreach ($solicitudes as $solicitud) {
            $created = $solicitud->created_at;
            $businessDaysPassed = $this->countBusinessDays($created, $now);

            // Reglas según días hábiles
            if ($businessDaysPassed > 15 && $solicitud->estado->nombre !== 'Expirada') {
                $solicitud->estado_id = 6; // Expirada
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a Expirada");
            } elseif ($businessDaysPassed > 10 && $solicitud->estado->nombre !== 'Por Vencer') {
                $solicitud->estado_id = 3; // Por Vencer
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a Por Vencer");
            } elseif ($businessDaysPassed > 5 && $solicitud->estado->nombre !== 'En Revisión') {
                $solicitud->estado_id = 2; // En Revisión
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a En Revisión");
            } elseif ($businessDaysPassed <= 5 && $solicitud->estado->nombre !== 'Nueva') {
                $solicitud->estado_id = 1; // Nueva
                $solicitud->save();
                $this->info("Solicitud {$solicitud->id} actualizada a Nueva");
            }
        }

        $this->info('Actualización de estados completada.');
    }

    /**
     * Cuenta los días hábiles entre dos fechas (excluye sábados y domingos)
     */
    protected function countBusinessDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $date = $startDate->copy();

        while ($date->lessThanOrEqualTo($endDate)) {
            if (!in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $days++;
            }
            $date->addDay();
        }

        return $days;
    }
}
