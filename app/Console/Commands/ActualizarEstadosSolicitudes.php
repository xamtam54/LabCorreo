<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Solicitud;
use App\Services\BusinessDaysCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActualizarEstadosSolicitudes extends Command
{
    protected $signature = 'solicitudes:actualizar-estados';
    protected $description = 'Actualizar estados de solicitudes según días hábiles transcurridos';

    protected BusinessDaysCalculator $calculator;

    public function __construct(BusinessDaysCalculator $calculator)
    {
        parent::__construct();
        $this->calculator = $calculator;
        Log::info('ActualizarEstadosSolicitudes: Constructor ejecutado y servicio BusinessDaysCalculator inyectado.');
    }

    public function handle()
    {
        $now = Carbon::now();
        Log::info("ActualizarEstadosSolicitudes: Inicio de ejecución a las $now");

        $solicitudes = Solicitud::whereHas('estado', function ($q) {
            $q->whereNotIn('nombre', ['Respondida', 'Cerrada']);
        })->get();

        Log::info("ActualizarEstadosSolicitudes: Se encontraron " . $solicitudes->count() . " solicitudes para evaluar.");

        // Definir el orden lógico de estados
        $orden_estados = [
            'Nueva' => 1,
            'En Revisión' => 2,
            'Por Vencer' => 3,
            'Expirada' => 4,
            'Respondida' => 5,
            'Cerrada' => 6,
        ];

        foreach ($solicitudes as $solicitud) {
            $created = Carbon::parse($solicitud->fecha_ingreso);
            Log::info("Procesando solicitud ID {$solicitud->id} con fecha_ingreso {$created}");

            $businessDaysPassed = $this->calculator->countBusinessDays($created, $now);
            Log::info("Días hábiles transcurridos para solicitud {$solicitud->id}: {$businessDaysPassed}");

            $estado_actual = $solicitud->estado->nombre;
            $prioridad_actual = $orden_estados[$estado_actual] ?? 0;

            $nuevo_estado_id = null;
            $nuevo_estado_nombre = null;
            $prioridad_nueva = 0;

            if ($businessDaysPassed > 15) {
                $nuevo_estado_id = 6; // Expirada (si ese ID corresponde)
                $nuevo_estado_nombre = 'Expirada';
                $prioridad_nueva = $orden_estados[$nuevo_estado_nombre];
            } elseif ($businessDaysPassed > 10) {
                $nuevo_estado_id = 3; // Por Vencer
                $nuevo_estado_nombre = 'Por Vencer';
                $prioridad_nueva = $orden_estados[$nuevo_estado_nombre];
            } elseif ($businessDaysPassed > 5) {
                $nuevo_estado_id = 2; // En Revisión
                $nuevo_estado_nombre = 'En Revisión';
                $prioridad_nueva = $orden_estados[$nuevo_estado_nombre];
            } elseif ($businessDaysPassed <= 5) {
                $nuevo_estado_id = 1; // Nueva
                $nuevo_estado_nombre = 'Nueva';
                $prioridad_nueva = $orden_estados[$nuevo_estado_nombre];
            }

            // Solo actualizar si el nuevo estado tiene mayor prioridad que el actual
            if ($nuevo_estado_id && $prioridad_nueva > $prioridad_actual) {
                $estado_anterior = $estado_actual;
                $solicitud->estado_id = $nuevo_estado_id;
                $solicitud->save();

                $solicitud->refresh();

                Log::info("Solicitud {$solicitud->id} actualizada de estado '{$estado_anterior}' a '{$nuevo_estado_nombre}'");
            } else {
                Log::info("Solicitud {$solicitud->id} mantiene estado '{$estado_actual}'");
            }
        }

        Log::info('ActualizarEstadosSolicitudes: Actualización de estados completada.');
        $this->info('Actualización de estados completada.');
    }

}
