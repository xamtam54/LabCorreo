<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;

class VerificarMiembroGrupo
{
    public function handle(Request $request, Closure $next)
    {
        $grupoParam = $request->route('grupo');
        $grupo = is_object($grupoParam) ? $grupoParam : Grupo::find($grupoParam);
        $usuario = Auth::user();
        $usuarioApp = $usuario?->usuario;

        if (!$usuarioApp || !$grupo) {
            abort(403, 'No tienes acceso a este grupo.');
        }

        // Verificar si pertenece al grupo
        $relacion = $usuarioApp->grupos()
            ->where('grupos.id', $grupo->id)
            ->first();

        if (!$relacion) {
            abort(403, 'No perteneces a este grupo.');
        }

        // Verificar si está bloqueado en el grupo
        if ($relacion->pivot->bloqueado) {
            abort(403, 'Has sido bloqueado en este grupo.');
        }

        return $next($request);
    }
}
