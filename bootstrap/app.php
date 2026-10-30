<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Carga la sesión para la gestión global de excepciones y normalización de inputs
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);

        // Alias de middlewares para usar en web.php
        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Redirección cuando un usuario no tiene los permisos necesarios
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            $urlAnterior = url()->previous();
            $urlActual = $request->url();
            $user = auth()->user();

            // Evita el bucle infinito si ingresan la URL a mano o provienen de login
            if ($urlAnterior === $urlActual || str_contains($urlAnterior, '/login') || empty($urlAnterior)) {
                if ($user) {
                    if ($user->hasRole('Administrador') || $user->can('ver.dashboard')) {
                        return redirect()->route('dashboard.index');
                    }

                    // Redirigir a la primera vista permitida para este usuario
                    $rutasModulos = [
                        'ver.usuarios' => 'users.index',
                        'ver.inventario' => 'inventory.index',
                        'ver.recepcion' => 'reception.index',
                        'ver.cuarentena' => 'quarantine.index',
                        'ver.despacho' => 'dispatch.index',
                        'ver.despacho_controlado' => 'controlled_dispatch.index',
                        'ver.distribucion' => 'distribution.index',
                        'ver.solicitudes' => 'requests.index',
                        'ver.solicitudes_cesfam' => 'requests.cesfam',
                        'ver.proveedores' => 'providers.index',
                        'ver.trazabilidad' => 'traceability.index',
                        'ver.reportes' => 'reports.index',
                    ];

                    foreach ($rutasModulos as $perm => $ruta) {
                        if ($user->can($perm)) {
                            return redirect()->route($ruta);
                        }
                    }

                    // Si no tiene ninguna vista habilitada, cerramos la sesión de forma limpia
                    \Illuminate\Support\Facades\Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Tu usuario no tiene ninguna vista asignada en el sistema. Contacta a un administrador para que habilite tus accesos.',
                    ]);
                }

                return redirect()->route('login');
            }

            // Devuelve al usuario a la pantalla anterior
            return redirect($urlAnterior);
        });
    })->create();
