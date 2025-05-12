<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !$user->usuario || !in_array($user->usuario->rol->nombre, $roles)) {
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
