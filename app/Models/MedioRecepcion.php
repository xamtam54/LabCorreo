<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedioRecepcion extends Model
{
    protected $table = 'medio_recepcion';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'medio_recepcion_id');
    }
}
