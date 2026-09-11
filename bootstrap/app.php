<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middlewares para usar en web.php
        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
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

            // Evita el bucle infinito si ingresan la URL a mano
            if ($urlAnterior === $urlActual) {
                return redirect()->route('dashboard.index');
            }

            // Devuelve al usuario a la pantalla anterior
            return redirect($urlAnterior);
        });
    })->create();
