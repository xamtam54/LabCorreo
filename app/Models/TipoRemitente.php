<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRemitente extends Model
{
    protected $table = 'tipo_remitente';
    protected $fillable = ['nombre'];
}
