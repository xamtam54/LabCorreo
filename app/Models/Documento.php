<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'documento';

    protected $fillable = [
        'editor_id', 'nombre_archivo', 'tamano_mb', 'ruta'
    ];

    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'editor_id');
    }


}
