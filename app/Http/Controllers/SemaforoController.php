<?php

namespace App\Http\Controllers;

use App\Models\Semaforo;
use Illuminate\Http\Request;

class SemaforoController extends Controller
{
    public function index() {
        return Semaforo::with('solicitud')->get();
    }
    public function store(Request $request) {
        return Semaforo::create($request->validate([
            'estado' => 'required|string',
            'tiempo_restante_horas' => 'required|integer',
            'plazo_horas' => 'required|integer',
            'solicitud_id' => 'required|exists:solicitud,id'
        ]));
    }
}
