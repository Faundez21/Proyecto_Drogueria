@extends('layouts.app')

@section('title', 'Usuarios y Permisos')

@section('actions')
    <button type="button" onclick="openRoles()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
        Roles y Accesos
    </button>
    <button type="button" onclick="openCreate()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-[#0f968c] hover:bg-teal-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nuevo Usuario
    </button>
@endsection

@section('content')
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span>Configuración</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Usuarios y Permisos</span>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

        <div class="p-4 border-b border-slate-200 bg-white flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div>
                <h3 class="text-[15px] font-bold text-slate-800">Listado de Usuarios</h3>
                <p class="text-[12px] text-slate-500 mt-0.5">Gestiona accesos, roles y atiende las solicitudes de recuperación.</p>
            </div>

            @if (count($passwordRequests) > 0)
                <button type="button" onclick="openRecoveryModal()"
                    class="relative inline-flex items-center justify-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-semibold py-2 px-4 rounded-lg transition-all shadow-sm cursor-pointer text-[12.5px]">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    Solicitudes de recuperación
                    <span class="bg-amber-200 text-amber-900 text-[11px] px-1.5 py-0.5 rounded-full font-bold">{{ count($passwordRequests) }}</span>
                </button>
            @endif
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr class="text-[12px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 whitespace-nowrap">Usuario</th>
                        <th class="px-6 py-4 whitespace-nowrap">Rol Asignado</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Estado</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]" id="mainTableBody">

                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-white text-[12px] shadow-sm shrink-0"
                                        style="background-color: var(--accent);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 truncate">{{ $user->name }} {{ $user->last_name }}</div>
                                        <div class="text-slate-500 text-[12px] truncate">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if ($user->roles->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($user->roles as $rol)
                                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-full text-[11px] border border-indigo-100 whitespace-nowrap">
                                                {{ $rol->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 font-bold rounded-full text-[11px] border border-slate-200">Sin Rol</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-2 text-emerald-600 font-medium text-[12px]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">

                                    <button type="button"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-last="{{ $user->last_name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->roles->first()->name ?? '' }}"
                                        onclick="openEdit(this)"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                        title="Editar usuario">
                                        <svg class="w-4.5 h-4.5 w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <button class="p-2 text-slate-300 cursor-not-allowed rounded-lg" title="No puedes eliminar tu propia cuenta" disabled>
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    @else
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event, '{{ addslashes($user->name) }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                                title="Eliminar usuario">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 text-[12px] text-slate-500 font-medium">
            Total: {{ $users->count() }} usuario(s) · {{ $roles->count() }} rol(es) configurado(s)
        </div>
    </div>

    <!-- Modal Roles y Accesos -->
    <div id="RolesModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Roles y Accesos por Vista</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Define qué vistas puede ver cada rol. El rol Administrador siempre tiene acceso total.</p>
                    </div>
                </div>
                <button type="button" onclick="closeRoles()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Listado de roles -->
            <div class="space-y-3 mb-6">
                @foreach ($roles as $rol)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold text-slate-800">{{ $rol->name }}</p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $rol->name === 'Administrador' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $rol->name === 'Administrador' ? 'Acceso total' : $rol->permissions->count() . ' vista(s)' }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @forelse ($rol->permissions as $perm)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ str_replace('ver.', '', $perm->name) }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-slate-400 italic">Sin accesos definidos</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button"
                                onclick='openRoleForm({{ json_encode(["id" => $rol->id, "name" => $rol->name, "modulos" => $rol->permissions->map(fn ($p) => str_replace("ver.", "", $p->name))->values()->all()]) }})'
                                class="px-3 py-1.5 text-[11.5px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors cursor-pointer">
                                Editar accesos
                            </button>
                            @if (! in_array($rol->name, ['Administrador', 'Usuario']))
                                <form action="{{ route('roles.destroy', $rol->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este rol?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-[11.5px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors cursor-pointer">
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Formulario de rol (crear/editar) -->
            <div id="roleFormBox" class="hidden border-t border-slate-200 pt-5">
                <h3 id="roleFormTitle" class="text-sm font-bold text-slate-800 mb-3">Nuevo Rol</h3>
                <form id="roleForm" method="POST" action="{{ route('roles.store') }}">
                    @csrf
                    <div id="roleMethod"></div>

                    <div class="mb-4">
                        <label for="role_name" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre del Rol <span class="text-rose-500">*</span></label>
                        <input type="text" id="role_name" name="name" required placeholder="Ej. Bodeguero, Auditor..."
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-[13px]">
                    </div>

                    <label class="block text-[12px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Acceso a Vistas</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-4">
                        @foreach ($modulos as $key => $label)
                            <label class="flex items-center gap-2.5 bg-white border border-slate-200 rounded-lg px-3 py-2.5 cursor-pointer hover:border-indigo-300 transition-colors shadow-sm">
                                <input type="checkbox" name="modulos[]" value="{{ $key }}" data-modulo="{{ $key }}"
                                    class="w-4 h-4 accent-indigo-600 rounded role-modulo">
                                <span class="text-[12px] font-semibold text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" onclick="cancelRoleForm()"
                            class="px-4 py-2 text-[12.5px] font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-[12.5px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors cursor-pointer">
                            <span id="roleSubmitText">Crear Rol</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Botón para crear rol nuevo -->
            <button type="button" id="btnNewRole" onclick="openRoleForm(null)"
                class="w-full py-3 border-2 border-dashed border-indigo-200 hover:border-indigo-400 hover:bg-indigo-50/40 text-indigo-600 font-bold text-[13px] rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear nuevo rol
            </button>

            <div class="flex justify-end pt-4 mt-2 border-t border-slate-100">
                <button type="button" onclick="closeRoles()"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Solicitudes de Recuperación -->
    <div id="RecoveryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Solicitudes de Recuperación</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Usuarios que han solicitado restablecer su contraseña de acceso</p>
                    </div>
                </div>
                <button type="button" onclick="closeRecoveryModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="assignPasswordForm" method="POST" action="">
                @csrf
                <input type="hidden" name="password" id="hidden_password_input">
            </form>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse ($passwordRequests as $req)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-amber-50/50 border border-amber-200/60 rounded-xl gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $req->email }}</p>
                            <span class="text-xs text-amber-700 font-medium">
                                Solicitado: {{ \Carbon\Carbon::parse($req->created_at)->locale('es')->diffForHumans() }}
                            </span>
                        </div>

                        <button type="button" data-email="{{ $req->email }}" onclick="promptNewPassword(this.dataset.email)"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                            Asignar nueva clave
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">No hay solicitudes pendientes de restablecimiento.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-6 mt-4 border-t border-slate-100">
                <button type="button" onclick="closeRecoveryModal()"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Crear / Editar Usuario -->
    <div id="UserModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h1 id="userModalTitle" class="text-xl font-bold text-slate-800">Crear Nuevo Usuario</h1>
                    <p class="text-sm text-slate-500 mt-0.5">El rol define las vistas a las que tendrá acceso.</p>
                </div>
                <button type="button" onclick="closeUserModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="userModalForm" method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div id="userModalMethod"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Ej. Juan"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div>
                        <label for="last_name" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Apellido <span class="text-rose-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Ej. Pérez"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Correo Electrónico <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="ejemplo@drogueriadas.com"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div class="sm:col-span-2" id="passwordField">
                        <label for="password" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Contraseña temporal <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="password" minlength="8" required placeholder="Mínimo 8 caracteres"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="role" class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Rol del Usuario
                            <span class="font-normal normal-case text-slate-400">(define las vistas a las que accede)</span>
                        </label>
                        <select id="role" name="role"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px] bg-white">
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}">
                                    {{ $rol->name }} — {{ $rol->name === 'Administrador' ? 'acceso total' : $rol->permissions->count() . ' vista(s)' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Para crear o ajustar los accesos de un rol, usa el botón "Roles y Accesos".</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeUserModal()"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <span id="userModalSubmitText">Guardar Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script>
        function openRecoveryModal() { document.getElementById('RecoveryModal').classList.remove('hidden'); }
        function closeRecoveryModal() { document.getElementById('RecoveryModal').classList.add('hidden'); }

        function openRoles() { document.getElementById('RolesModal').classList.remove('hidden'); }
        function closeRoles() { document.getElementById('RolesModal').classList.add('hidden'); }

        function openCreate() {
            const form = document.getElementById('userModalForm');
            form.action = "{{ route('users.store') }}";
            form.reset();
            document.getElementById('userModalMethod').innerHTML = '';
            document.getElementById('passwordField').style.display = '';
            document.getElementById('password').required = true;
            document.getElementById('userModalTitle').textContent = 'Crear Nuevo Usuario';
            document.getElementById('userModalSubmitText').textContent = 'Guardar Usuario';
            document.getElementById('UserModal').classList.remove('hidden');
        }

        function openEdit(button) {
            const d = button.dataset;
            const form = document.getElementById('userModalForm');
            form.action = "{{ url('users') }}/" + d.id;
            form.reset();
            document.getElementById('userModalMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('passwordField').style.display = 'none';
            document.getElementById('password').required = false;

            document.getElementById('name').value = d.name ?? '';
            document.getElementById('last_name').value = d.last ?? '';
            document.getElementById('email').value = d.email ?? '';
            if (d.role) document.getElementById('role').value = d.role;

            document.getElementById('userModalTitle').textContent = 'Editar Usuario';
            document.getElementById('userModalSubmitText').textContent = 'Guardar Cambios';
            document.getElementById('UserModal').classList.remove('hidden');
        }

        function closeUserModal() { document.getElementById('UserModal').classList.add('hidden'); }

        function openRoleForm(rol) {
            const box = document.getElementById('roleFormBox');
            const form = document.getElementById('roleForm');
            form.reset();

            document.querySelectorAll('.role-modulo').forEach(cb => cb.checked = false);

            if (rol) {
                document.getElementById('roleFormTitle').textContent = 'Editar Rol: ' + rol.name;
                document.getElementById('roleSubmitText').textContent = 'Guardar Cambios';
                form.action = "{{ url('roles') }}/" + rol.id;
                document.getElementById('roleMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('role_name').value = rol.name;
                (rol.modulos ?? []).forEach(m => {
                    const cb = document.querySelector('.role-modulo[data-modulo="' + m + '"]');
                    if (cb) cb.checked = true;
                });
            } else {
                document.getElementById('roleFormTitle').textContent = 'Nuevo Rol';
                document.getElementById('roleSubmitText').textContent = 'Crear Rol';
                form.action = "{{ route('roles.store') }}";
                document.getElementById('roleMethod').innerHTML = '';
            }

            document.getElementById('btnNewRole').classList.add('hidden');
            box.classList.remove('hidden');
            box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function cancelRoleForm() {
            document.getElementById('roleFormBox').classList.add('hidden');
            document.getElementById('btnNewRole').classList.remove('hidden');
        }

        function confirmDelete(event, nombre) {
            if (!confirm('¿Eliminar al usuario ' + nombre + '? Esta acción no se puede deshacer.')) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        function promptNewPassword(email) {
            Swal.fire({
                title: 'Asignar Nueva Contraseña',
                text: `Ingresa la nueva clave para ${email}:`,
                input: 'password',
                inputPlaceholder: 'Mínimo 8 caracteres',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Guardar Contraseña',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) return '¡Debes ingresar una contraseña!';
                    if (value.length < 8) return 'La contraseña debe tener al menos 8 caracteres.';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('assignPasswordForm');
                    form.action = "{{ url('users/reset-password') }}/" + encodeURIComponent(email);
                    document.getElementById('hidden_password_input').value = result.value;
                    form.submit();
                }
            });
        }
    </script>
@endsection
