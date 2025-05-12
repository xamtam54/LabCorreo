<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarMiembroGrupo
{
    public function handle(Request $request, Closure $next)
    {
        $grupo = $request->route('grupo');
        $usuario = Auth::user();

        if (!$usuario || !$grupo || !$usuario->grupos->contains('id', $grupo->id)) {
            abort(403, 'No tienes acceso a este grupo.');
        }

        return $next($request);
    }
}
