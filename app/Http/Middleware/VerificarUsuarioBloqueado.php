<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerificarUsuarioBloqueado
{
    public function handle($request, Closure $next)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect('/login');
        }

        // Solo aplica bloqueo a Admin o Gestor_grupos
        if (
            $usuario->bloqueado &&
            in_array($usuario->rol->nombre, ['Administrador', 'Gestor_grupos'])
        ) {
            abort(403, 'Tu cuenta está bloqueada.');
        }

        return $next($request);
    }
}
