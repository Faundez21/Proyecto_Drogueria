<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticatedUser(Auth::user());
        }

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    public function authenticate(Request $request)
    {
        // Validar inputs del formulario de login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:64'],
        ], [
            'password.min' => 'La contraseña debe tener entre 8 y 64 caracteres.',
            'password.max' => 'La contraseña debe tener entre 8 y 64 caracteres.',
        ]);

        $remember = $request->boolean('remember');

        // Intentar autenticar credenciales
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (($user->status ?? 'activo') !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Su cuenta se encuentra inactiva. Contacte al administrador.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectAuthenticatedUser($user);
        }

        //  Si falla el login
        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }

    protected function redirectAuthenticatedUser($user)
    {
        // Administrador o permiso de Dashboard
        if ($user->hasRole('Administrador') || $user->can('ver.dashboard')) {
            return redirect()->route('dashboard.index');
        }

        // Inventario / Bodega
        if ($user->can('ver.inventario')) {
            return redirect()->route('inventory.index');
        }

        // Recepción
        if ($user->can('ver.recepcion') || $user->can('ver.reception')) {
            return redirect()->route('reception.index');
        }

        // Cuarentena
        if ($user->can('ver.cuarentena') || $user->can('ver.quarantine')) {
            return redirect()->route('quarantine.index');
        }

        // Despacho Normal
        if ($user->can('ver.despacho')) {
            return redirect()->route('dispatch.index');
        }

        // Despacho Controlado
        if ($user->can('ver.despacho_controlado') || $user->can('ver.controlled_dispatch')) {
            return redirect()->route('controlled_dispatch.index');
        }

        // Distribución
        if ($user->can('ver.distribucion') || $user->can('ver.distribution')) {
            return redirect()->route('distribution.index');
        }

        // Solicitudes Establecimientos
        if ($user->can('ver.solicitudes') || $user->can('ver.requests')) {
            return redirect()->route('requests.index');
        }

        // Solicitudes CESFAM
        if ($user->can('ver.solicitudes_cesfam')) {
            return redirect()->route('requests.cesfam');
        }

        // Proveedores
        if ($user->can('ver.proveedores') || $user->can('ver.providers')) {
            return redirect()->route('providers.index');
        }

        // Trazabilidad
        if ($user->can('ver.trazabilidad')) {
            return redirect()->route('traceability.index');
        }

        // Reportes
        if ($user->can('ver.reportes')) {
            return redirect()->route('reports.index');
        }

        // Si el usuario no tiene ninguna vista asignada, cerramos la sesión para evitar el bucle infinito
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Su usuario no tiene ninguna vista asignada en el sistema. Contacte a un administrador para que configure sus accesos.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
