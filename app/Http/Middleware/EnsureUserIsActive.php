<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea en cada petición a los usuarios desactivados.
 *
 * Si un administrador desactiva una cuenta mientras está con la sesión abierta
 * (o con "Recordarme"), en su siguiente clic se le cierra la sesión y no puede
 * volver a entrar hasta que la cuenta se reactive.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ($user->status ?? 'activo') !== 'activo') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $mensaje = 'Su cuenta se encuentra inactiva. Contacte al administrador.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $mensaje], 403);
            }

            return redirect()->route('login')->withErrors(['email' => $mensaje]);
        }

        return $next($request);
    }
}
