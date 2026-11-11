<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetRequestController extends Controller
{
    // El usuario pide la recuperación desde la ventana del login
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'El correo electrónico no está registrado.',
        ]);

        PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => Str::random(60),
                'status' => 'pending',
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    // El administrador asigna la clave nueva a mano
    public function resetPassword(Request $request, $email)
    {
        $request->validate([
            'password' => 'required|string|min:8|max:64',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $resetToken = PasswordResetToken::where('email', $email)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $resetToken->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', "La contraseña de {$user->email} ha sido actualizada.");
    }
}
