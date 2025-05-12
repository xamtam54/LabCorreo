<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use SoftDeletes;
    protected $table = 'solicitud';

    protected $fillable = [
        'numero_radicado',
        'tipo_solicitud_id',
        'remitente',
        'asunto',
        'medio_recepcion_id',
        'fecha_ingreso',
        'documento_adjunto_id',
        'fecha_vencimiento',
        'usuario_id',
        'estado_id',
        'firma_digital',
        'grupo_id', 
    ];

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function medioRecepcion()
    {
        return $this->belongsTo(MedioRecepcion::class);
    }

    public function documentoAdjunto()
    {
        return $this->belongsTo(Documento::class, 'documento_adjunto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function semaforo()
    {
        return $this->hasOne(Semaforo::class);
    }

    public function reporte()
    {
        return $this->hasMany(Reporte::class);
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
}
