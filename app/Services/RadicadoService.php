<?php

namespace App\Services;

use App\Models\Solicitud;

class RadicadoService
{
    public static function generar($tipo, $dependencia)
    {
        $año = date('Y');

        // Buscar último radicado SOLO por año
        $ultimo = Solicitud::whereYear('fecha_ingreso', $año)
            ->orderBy('id', 'desc')
            ->first();

        $codigo = $ultimo
            ? intval(substr($ultimo->numero_radicado, -6)) + 1
            : 1;

        $codigo = str_pad($codigo, 6, '0', STR_PAD_LEFT);

        return "ALC-GAMA-$año-$tipo-$dependencia-$codigo";
    }
}
