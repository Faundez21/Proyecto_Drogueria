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
        'distribucion' => 'Distribución',
        'solicitudes' => 'Solicitudes Establecimientos',
        'solicitudes_cesfam' => 'Solicitudes Cesfam',
        'despacho' => 'Despacho Normal',
        'despacho_controlado' => 'Despacho Controlado',
        'trazabilidad' => 'Trazabilidad',
        'reportes' => 'Reportes',
    ];

    public const MODULOS_GRUPOS = [
        'General' => [
            'icono' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM14 11a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z',
            'color' => 'slate',
            'items' => [
                'dashboard' => ['label' => 'Panel Principal (Dashboard)', 'desc' => 'KPIs operativos, establecimientos, CESFAM y escáner QR'],
            ],
        ],
        '01 · Abastecimiento' => [
            'icono' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'color' => 'emerald',
            'items' => [
                'proveedores' => ['label' => 'Proveedores', 'desc' => 'Directorio y estado de laboratorios y distribuidores'],
                'recepcion' => ['label' => 'Recepción', 'desc' => 'Ingreso de lotes, actas de recepción y precios netos'],
            ],
        ],
        '02 · Almacenamiento' => [
            'icono' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            'color' => 'amber',
            'items' => [
                'inventario' => ['label' => 'Inventario de Droguería', 'desc' => 'Control físico de lotes, stock mínimo y vencimientos'],
                'cuarentena' => ['label' => 'Cuarentena', 'desc' => 'Gestión de lotes retenidos, liberación o baja sanitaria'],
                'distribucion' => ['label' => 'Distribución', 'desc' => 'Mapa de bodega, pasillos, estantes, niveles y posiciones'],
            ],
        ],
        '03 · Salida (Solicitudes y Despachos)' => [
            'icono' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'color' => 'teal',
            'items' => [
                'solicitudes' => ['label' => 'Solicitudes Establecimientos', 'desc' => 'Pedidos de medicamentos e insumos (no estupefacientes)'],
                'solicitudes_cesfam' => ['label' => 'Solicitudes Cesfam', 'desc' => 'Pedidos de medicamentos, insumos y estupefacientes para CESFAM'],
                'despacho' => ['label' => 'Despacho Normal', 'desc' => 'Emisión de guías de despacho FEFO y atención de solicitudes'],
                'despacho_controlado' => ['label' => 'Despacho Controlado', 'desc' => 'Despacho exclusivo de fármacos controlados y estupefacientes'],
            ],
        ],
        '04 · Control y Configuración' => [
            'icono' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            'color' => 'indigo',
            'items' => [
                'trazabilidad' => ['label' => 'Trazabilidad', 'desc' => 'Kardex histórico y línea de tiempo por lote'],
                'reportes' => ['label' => 'Reportes', 'desc' => 'Exportación de informes en Excel y PDF'],
            ],
        ],
    ];

    public function index()
    {
        foreach (array_keys(self::MODULOS) as $modKey) {
            Permission::firstOrCreate(['name' => 'ver.' . $modKey]);
        }

        $users = User::with(['roles.permissions', 'permissions'])
            ->withCount([
                'assignedRequests as solicitudes_activas_count' => fn ($q) => $q->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial']),
                'assignedRequests as solicitudes_despachadas_count' => fn ($q) => $q->where('estado', 'Despachado'),
            ])
            ->get();

        $passwordRequests = PasswordResetToken::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();

        return view('users.index', [
            'users' => $users,
            'passwordRequests' => $passwordRequests,
            'roles' => $roles,
            'modulos' => self::MODULOS,
            'modulosGrupos' => self::MODULOS_GRUPOS,
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
            'role' => 'required|string|exists:roles,name',
            'status' => 'nullable|in:activo,inactivo',
        ], [
            'email.regex' => 'El correo debe tener una extensión válida (ej. .com, .cl).',
            'role.required' => 'Debes seleccionar un rol para el usuario.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role && $role->name !== 'Administrador' && $role->permissions()->count() === 0) {
            return back()->withInput()->withErrors([
                'role' => "El rol '{$role->name}' no tiene ninguna vista asignada. Asigna al menos 1 vista a este rol antes de asignarlo a un usuario."
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'] ?? 'activo',
        ]);

        $user->assignRole($validated['role']);

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
            'role' => 'required|string|exists:roles,name',
            'status' => 'nullable|in:activo,inactivo',
        ], [
            'email.regex' => 'El correo debe tener una extensión válida (ej. .com, .cl).',
            'role.required' => 'Debes seleccionar un rol para el usuario.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role && $role->name !== 'Administrador' && $role->permissions()->count() === 0) {
            return back()->withInput()->withErrors([
                'role' => "El rol '{$role->name}' no tiene ninguna vista asignada. Asigna al menos 1 vista a este rol antes de asignarlo a un usuario."
            ]);
        }

        $userData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        if (isset($validated['status'])) {
            $userData['status'] = $validated['status'];
        }

        $user->update($userData);

        $user->syncRoles([$validated['role']]);

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
            'modulos' => 'required|array|min:1',
            'modulos.*' => 'string|in:'.implode(',', array_keys(self::MODULOS)),
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'modulos.required' => 'Debes asignar al menos 1 vista al rol.',
            'modulos.min' => 'Debes asignar al menos 1 vista al rol.',
            'modulos.*.in' => 'Una de las vistas seleccionadas no es válida.',
        ]);

        $rol = Role::create(['name' => $validated['name']]);
        $this->sincronizarAccesos($rol, $validated['modulos']);

        return redirect()->route('users.index')->with('success', "Rol '{$rol->name}' creado con sus accesos.");
    }

    public function updateRole(Request $request, Role $role)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'modulos' => 'required|array|min:1',
            'modulos.*' => 'string|in:'.implode(',', array_keys(self::MODULOS)),
        ];

        // Si es el rol Administrador, siempre tiene todas las vistas
        if ($role->name === 'Administrador') {
            $rules['modulos'] = 'nullable|array';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'modulos.required' => 'Debes asignar al menos 1 vista al rol.',
            'modulos.min' => 'Debes asignar al menos 1 vista al rol.',
            'modulos.*.in' => 'Una de las vistas seleccionadas no es válida.',
        ]);

        $role->update(['name' => $validated['name']]);
        
        $modulos = $role->name === 'Administrador'
            ? array_keys(self::MODULOS)
            : ($validated['modulos'] ?? []);

        $this->sincronizarAccesos($role, $modulos);

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
