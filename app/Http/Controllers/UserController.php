<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public const MODULOS = [
        'dashboard' => 'Panel Principal',
        'proveedores' => 'Proveedores',
        'recepcion' => 'Recepción',
        'inventario' => 'Inventario',
        'cuarentena' => 'Cuarentena',
        'despacho' => 'Despacho Normal',
        'despacho_controlado' => 'Despacho Controlado',
        'distribucion' => 'Distribución',
        'solicitudes' => 'Solicitudes',
        'trazabilidad' => 'Trazabilidad',
        'reportes' => 'Reportes',
        'usuarios' => 'Usuarios y Roles',
    ];

    public function index()
    {
        $users = User::with('roles')->get();

        $passwordRequests = PasswordResetToken::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('users.index', [
            'users' => $users,
            'passwordRequests' => $passwordRequests,
            'roles' => $roles,
            'modulos' => self::MODULOS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email:rfc',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],
            'password' => 'required|string|min:8|max:64',
            'role' => 'nullable|string|exists:roles,name',
            'status' => 'nullable|in:activo,inactivo',
        ], [
            'email.regex' => 'El correo debe tener una extensión válida (ej. .com, .cl).',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'] ?? 'activo',
        ]);

        $user->assignRole($validated['role'] ?? 'Usuario');

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email:rfc',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email,'.$user->id,
            ],
            'role' => 'nullable|string|exists:roles,name',
            'status' => 'nullable|in:activo,inactivo',
        ], [
            'email.regex' => 'El correo debe tener una extensión válida (ej. .com, .cl).',
        ]);

        $userData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        if (isset($validated['status'])) {
            $userData['status'] = $validated['status'];
        }

        $user->update($userData);

        if (! empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes cambiar el estado de tu propia cuenta.');
        }

        // Alternar el estado
        $user->status = ($user->status === 'activo') ? 'inactivo' : 'activo';
        $user->save();

        $mensaje = ($user->status === 'activo')
            ? "El usuario {$user->name} ha sido activado."
            : "El usuario {$user->name} ha sido desactivado.";

        return redirect()->route('users.index')->with('success', $mensaje);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|in:'.implode(',', array_keys(self::MODULOS)),
        ]);

        $rol = Role::create(['name' => $validated['name']]);
        $this->sincronizarAccesos($rol, $validated['modulos'] ?? []);

        return redirect()->route('users.index')->with('success', "Rol '{$rol->name}' creado con sus accesos.");
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|in:'.implode(',', array_keys(self::MODULOS)),
        ]);

        $role->update(['name' => $validated['name']]);
        $this->sincronizarAccesos($role, $validated['modulos'] ?? []);

        return redirect()->route('users.index')->with('success', "Rol '{$role->name}' actualizado correctamente.");
    }

    public function destroyRole(Role $role)
    {
        if (in_array($role->name, ['Administrador', 'Usuario'])) {
            return redirect()->route('users.index')->with('error', 'Los roles del sistema no se pueden eliminar.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('users.index')->with('error', "El rol '{$role->name}' tiene usuarios asignados. Reasígnalos primero.");
        }

        $role->delete();

        return redirect()->route('users.index')->with('success', 'Rol eliminado correctamente.');
    }

    private function sincronizarAccesos(Role $rol, array $modulos): void
    {
        $permisos = [];

        foreach ($modulos as $modulo) {
            if (array_key_exists($modulo, self::MODULOS)) {
                $permisos[] = Permission::firstOrCreate(['name' => 'ver.'.$modulo])->name;
            }
        }

        $rol->syncPermissions($permisos);
    }
}
