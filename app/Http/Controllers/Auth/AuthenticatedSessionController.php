<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // display the login view
    public function create(): View
    {
        return view('auth.login');
    }

    // handle an incoming authentication request
  public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $user = Auth::user();

    if (($user->status ?? 'activo') !== 'activo') {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.',
        ]);
    }

    $request->session()->regenerate();

    // redireccion si tiene acceso a solicitudes
    if ($user->can('ver.solicitudes') || $user->can('ver.requests')) {
        return redirect()->route('requests.index');
    }

    if ($user->hasRole('Administrador') || $user->can('ver.usuarios')) {
        return redirect()->route('users.index');
    }

    if ($user->can('ver.recepcion')) return redirect()->route('reception.index');
    if ($user->can('ver.inventario')) return redirect()->route('inventory.index');

    return redirect()->route('requests.index');
}

    // destroy an authenticated session
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
