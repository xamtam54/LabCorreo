<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Services\BusinessDaysCalculator;

class Solicitud extends Model
{
    use SoftDeletes;
    protected $table = 'solicitud';

    protected $fillable = [
        'numero_radicado',
        'tipo_solicitud_id',
        'remitente',
        'asunto',
        'contenido',
        'medio_recepcion_id',
        'fecha_ingreso',
        'documento_adjunto_id',
        'fecha_vencimiento',
        'usuario_id',
        'estado_id',
        'firma_digital',
        'completada',
        'grupo_id',
    ];


    public function scopeFiltrarTipo(Builder $query, $tipo)
    {
        if (!empty($tipo)) {
            $query->where('tipo_solicitud_id', $tipo);
        }
    }

    // Scope: Filtrar por estado
    public function scopeFiltrarEstado(Builder $query, $estado)
    {
        if (!empty($estado)) {
            $query->where('estado_id', $estado);
        }
    }

    // Scope: Filtrar por fecha
    public function scopeFiltrarFecha(Builder $query, $fecha)
    {
        if (!empty($fecha)) {
            $inicio = \Carbon\Carbon::parse($fecha)->startOfDay();
            $fin = \Carbon\Carbon::parse($fecha)->endOfDay();
            $query->whereBetween('fecha_ingreso', [$inicio, $fin]);
        }
    }

    // Scope: Ordenar por prioridad
    public function scopeOrdenarPor(Builder $query, $orden)
    {
        if ($orden === 'antiguos') {
            return $query->orderBy('created_at', 'asc');
        } elseif ($orden === 'recientes') {
            return $query->orderBy('created_at', 'desc');
        } elseif ($orden === 'prioridad') {
            $prioridades = [
                3 => 1,  // Por Vencer
                2 => 2,  // En Revisión
                1 => 3,  // Recibida
                4 => 4,  // Respondida
                5 => 5,  // Cerrada
            ];

            $cases = "CASE estado_id";
            foreach ($prioridades as $estado_id => $prio) {
                $cases .= " WHEN {$estado_id} THEN {$prio}";
            }
            $cases .= " ELSE 999 END";

            return $query->orderByRaw($cases);
        }

        return $query->latest(); // por defecto
    }


    public function calcularEstadoSegunDiasHabiles(BusinessDaysCalculator $calculator): ?int
    {
        $now = Carbon::now();
        $created = Carbon::parse($this->fecha_ingreso ?? $this->created_at);

        $businessDaysPassed = $calculator->countBusinessDays($created, $now);

        // Cargar estados posibles
        $estados = DB::table('estado_solicitud')
            ->whereIn('nombre', ['Nueva', 'En Revisión', 'Por Vencer', 'Expirada'])
            ->get()
            ->keyBy('nombre');

        return match (true) {
            $businessDaysPassed > 15 => $estados['Expirada']->id ?? null,
            $businessDaysPassed > 10 => $estados['Por Vencer']->id ?? null,
            $businessDaysPassed > 5  => $estados['En Revisión']->id ?? null,
            default                 => $estados['Nueva']->id ?? null,
        };
    }

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function medioRecepcion()
    {
        return $this->belongsTo(MedioRecepcion::class);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_adjunto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    /*
    public function semaforo()
    {
        return $this->hasOne(Semaforo::class);
    }*/

    public function reporte()
    {
        return $this->hasMany(Reporte::class);
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function determinarEstadoFinal()
    {
        $estados = DB::table('estado_solicitud')
            ->whereIn('nombre', ['Cerrada', 'Respondida'])
            ->get()
            ->keyBy('nombre');

        if ($this->firma_digital) {
            if ($this->documento) {
                return $estados['Cerrada']->id ?? $this->estado_id;
            } else {
                return $estados['Respondida']->id ?? $this->estado_id;
            }
        }

        return $estados['Cerrada']->id ?? $this->estado_id;
    }

}
