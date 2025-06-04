<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Services\BusinessDaysCalculator;

Route::get('/calcular-fecha', function (Request $request) {
    $fecha = $request->query('fecha');

    if (!$fecha) {
        return response()->json(['error' => 'Fecha no proporcionada'], 400);
    }

    try {
        $calculator = new BusinessDaysCalculator();
        $fechaFinal = $calculator->addBusinessDays(Carbon::parse($fecha), 15);

        return response()->json(['fecha_resultado' => $fechaFinal->format('Y-m-d')]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Error al calcular la fecha',
            'message' => $e->getMessage()
        ], 500);
    }
});
