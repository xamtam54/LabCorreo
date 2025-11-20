<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'documento';

    protected $fillable = [
        'editor_id', 'nombre_archivo', 'tamano_mb', 'ruta'
    ];

    public function solicitudes()
    {
        return $this->belongsToMany(\App\Models\Solicitud::class, 'solicitud_documento')
                    ->withPivot('orden')
                    ->withTimestamps();
    }
    
    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'editor_id');
    }

    public function eliminarArchivo()
    {
        if (empty($this->ruta)) {
            return;
        }

        if (Storage::exists($this->ruta)) {
            Storage::delete($this->ruta);
        } else {
        }

        $this->delete();
    }
}
