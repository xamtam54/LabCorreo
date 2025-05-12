<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('administrar-usuarios', function (User $user) {
            return $user->usuario?->rol?->nombre === 'Administrador';
        });

        Gate::define('administrar-grupos', function (User $user) {
            return in_array($user->usuario?->rol?->nombre, ['Administrador', 'Gestor_grupos']);
        });

    }
}
