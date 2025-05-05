<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
    use SoftDeletes;

    protected $table = 'reporte';

    protected $fillable = [
        'autor_id',
        'tipo',
        'contenido',
        'solicitud_relacionada_id',
        'fecha_generacion',
    ];

    public $timestamps = false;

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_relacionada_id');
    }
}
