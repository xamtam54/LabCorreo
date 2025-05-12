<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Grupo extends Model
{
    use SoftDeletes;

    protected $table = 'grupos';

    protected $fillable = ['nombre', 'descripcion', 'contrasena', 'codigo', 'creador_id'];


    protected static function booted()
    {
        static::creating(function ($grupo) {
            do {
                $codigo = strtoupper(Str::random(15));
            } while (Grupo::where('codigo', $codigo)->exists());
            $grupo->codigo = $codigo;
        });
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'grupo_usuario')
                    ->withPivot('es_administrador', 'bloqueado')
                    ->withTimestamps();
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }
}
