<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * El rol Administrador pasa todas las comprobaciones de permisos.
     *
     * Gate::before se ejecuta antes que cualquier can() o middleware
     * permission:, así que el administrador entra a todas las vistas sin
     * necesidad de tener asignado cada permiso ver.<modulo> uno por uno.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrador') ? true : null;
        });
    }
}
