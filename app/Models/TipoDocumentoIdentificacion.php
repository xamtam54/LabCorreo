<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoIdentificacion extends Model
{
    protected $table = 'tipo_documento_identificacion';
    protected $fillable = ['nombre','abreviatura'];
}
