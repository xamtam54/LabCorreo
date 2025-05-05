<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoSolicitud extends Model
{

    protected $table = 'estado_solicitud';
    public $timestamps = false;

    protected $fillable = [
        'nombre','descripcion'
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'estadoId');
    }
}
