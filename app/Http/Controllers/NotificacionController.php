<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index() {
        return Notificacion::with('usuario')->get();
    }
    public function store(Request $request) {
        return Notificacion::create($request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'mensaje' => 'required|string',
            'fecha_envio' => 'required|date',
            'leida' => 'required|boolean',
            'tipo' => 'required|string|max:100'
        ]));
    }
}
