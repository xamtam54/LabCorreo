<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remitente extends Model
{
    protected $table = 'remitente';
    protected $fillable = [
        'tipo_remitente_id',
        'tipo_documento_identificacion_id',
        'nombre',
        'numero_documento',
        'correo'
    ];

    public function tipoRemitente()
    {
        return $this->belongsTo(TipoRemitente::class, 'tipo_remitente_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoIdentificacion::class, 'tipo_documento_identificacion_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'remitente_id');
    }
}
