<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSolicitud extends Model
{
    protected $table = 'tipo_solicitud';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo_solicitud_id');
    }
}
