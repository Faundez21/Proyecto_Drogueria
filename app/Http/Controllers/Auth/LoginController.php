<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function show()
    {
        // Direccion donde esta la vista del login
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validacion de Iniciar Sesion
        return back();
    }
}