<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Mostrar todos los usuarios.
     */
    public function index()
    {
        $users = User::all();

        $passwordRequests = PasswordResetToken::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.index', compact('users', 'passwordRequests'));
    }

    /**
     * Mostrar formulario para crear un usuario.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Guardar un nuevo usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Mostrar un usuario especÃfico.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar un usuario.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Actualizar un usuario.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
