<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- MUY IMPORTANTE: Importar la clase Auth

class LoginController extends Controller
{
    public function show()
    {
        // Direccion donde esta la vista del login
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validar que vengan los datos correctos del formulario
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            // Si el usuario existe y la clave es correcta, regeneramos su sesión
            $request->session()->regenerate();

            // se envia al dashboard
            return redirect()->intended('/dashboard');
        }

        //  Si falla (clave mala o usuario no existe), lo regresamos con un error
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
