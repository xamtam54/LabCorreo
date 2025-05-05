<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'mensaje',
        'fecha_envio',
        'leida',
        'tipo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
