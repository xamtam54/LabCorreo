<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semaforo extends Model
{
    protected $table = 'semaforo';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'tiempo_restante_horas',
        'plazo_horas',
        'solicitud_id',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function calcularTiempoRestante()
    {
    }

    public function estaVencido()
    {
    }
}
