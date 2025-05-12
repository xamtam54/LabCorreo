<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = false; // el id viene de user

    protected $fillable = [
        'id','nombres', 'apellidos', 'fecha_creacion', 'fecha_ultima_sesion', 'rol_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_usuario')
                    ->withPivot('es_administrador', 'bloqueado')
                    ->withTimestamps();
    }


    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
