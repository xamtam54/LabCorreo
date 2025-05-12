<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // relaciones con usuarios
    protected static function booted()
    {
        static::created(function ($user) {
            Usuario::create([
                'id' => $user->id,
                'nombres' => $user->name ?? 'No definido',
                'apellidos' => 'No definido',
                'fecha_creacion' => now(),
                'fecha_ultima_sesion' => now(),
            ]);
        });
    }

    public function assignRole($rolId)
    {
        $rol = Rol::find($rolId);

        if ($rol) {
            $this->usuario->rol()->associate($rol);
            $this->usuario->save();
        }
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id');
    }

}
