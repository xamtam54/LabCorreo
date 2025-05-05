<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;
    protected $table = 'documento';

    protected $fillable = [
        'editorId', 'nombreArchivo', 'tipoDocumentoId', 'tamanoMB', 'ruta'
    ];

    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'editorId');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipoDocumentoId');
    }
}
