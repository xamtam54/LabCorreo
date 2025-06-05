<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutorizarAdministradorGrupo
{
    public function handle(Request $request, Closure $next)
    {
        $grupo = $request->route('grupo');
        $usuario = Auth::user()?->usuario;

        if (!$usuario || !$grupo) {
            abort(403, 'No autorizado.');
        }

        // Si es el creador del grupo, permitir
        if ($grupo->creador_id === $usuario->id) {
            return $next($request);
        }

        // Verificar si está en el grupo como administrador y no está bloqueado
        $relacion = $usuario->grupos()->where('grupos.id', $grupo->id)->first();

        if ($relacion && !$relacion->pivot->bloqueado && $relacion->pivot->es_administrador) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para esta acción.');
    }
}
