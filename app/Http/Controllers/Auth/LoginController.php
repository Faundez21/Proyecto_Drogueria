<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validar inputs del formulario de login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar credenciales
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (($user->status ?? 'activo') !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Administrador o permiso de Dashboard
            if ($user->hasRole('Administrador') || $user->can('ver.dashboard')) {
                return redirect()->route('dashboard.index');
            }

            //  Módulo de Usuarios y Roles
            if ($user->can('ver.usuarios') || $user->can('ver.users')) {
                return redirect()->route('users.index');
            }

            // Inventario / Bodega
            if ($user->can('ver.inventario')) {
                return redirect()->route('inventario.index');
            }

            //Recepción
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

            // Solicitudes
            if ($user->can('ver.solicitudes') || $user->can('ver.requests')) {
                return redirect()->route('requests.index');
            }

            // Proveedores
            if ($user->can('ver.proveedores') || $user->can('ver.providers')) {
                return redirect()->route('providers.index');
            }

            //Trazabilidad
            if ($user->can('ver.trazabilidad')) {
                return redirect()->route('traceability.index');
            }

            // Reportes
            if ($user->can('ver.reportes')) {
                return redirect()->route('reportes.index');
            }

            return redirect()->intended(route('dashboard.index'));
        }

        //  Si falla el login
        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
