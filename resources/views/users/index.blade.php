@extends('layouts.app')

@section('title', 'Usuarios y Permisos')

@section('actions')
    <button type="button" onclick="openRoles()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
            </path>
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
                <p class="text-[12px] text-slate-500 mt-0.5">Gestiona accesos, roles y atiende las solicitudes de
                    recuperación.</p>
            </div>

            @if (count($passwordRequests) > 0)
                <button type="button" onclick="openRecoveryModal()"
                    class="relative inline-flex items-center justify-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-semibold py-2 px-4 rounded-lg transition-all shadow-sm cursor-pointer text-[12.5px]">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    Solicitudes de recuperación
                    <span
                        class="bg-amber-200 text-amber-900 text-[11px] px-1.5 py-0.5 rounded-full font-bold">{{ count($passwordRequests) }}</span>
                </button>
            @endif
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr
                        class="text-[12px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 whitespace-nowrap">Usuario</th>
                        <th class="px-6 py-4 whitespace-nowrap">Rol y Vistas de Solicitudes</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Solicitudes Asignadas (Despacho)</th>
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
                                        <div class="font-bold text-slate-800 truncate">{{ $user->name }}
                                            {{ $user->last_name }}</div>
                                        <div class="text-slate-500 text-[12px] truncate">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $userSolPermisos = $user->getAllPermissions()
                                        ->pluck('name')
                                        ->intersect(['ver.solicitudes', 'ver.solicitudes_cesfam'])
                                        ->map(fn ($p) => str_replace('ver.', '', $p))
                                        ->values()
                                        ->all();
                                @endphp
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    @if ($user->roles->count() > 0)
                                        @foreach ($user->roles as $rol)
                                            <span
                                                class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-full text-[11px] border border-indigo-100 whitespace-nowrap">
                                                {{ $rol->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-600 font-bold rounded-full text-[11px] border border-slate-200">Sin
                                            Rol</span>
                                    @endif

                                    @if ($user->hasRole('Administrador'))
                                        <span class="px-2 py-0.5 bg-teal-50 text-teal-700 font-semibold rounded-md text-[10.5px] border border-teal-200 whitespace-nowrap">
                                            Todas las vistas
                                        </span>
                                    @else
                                        @if (in_array('solicitudes', $userSolPermisos))
                                            <span class="px-2 py-0.5 bg-teal-50 text-teal-700 font-semibold rounded-md text-[10.5px] border border-teal-200 whitespace-nowrap">
                                                Solicitudes Establecimientos
                                            </span>
                                        @endif
                                        @if (in_array('solicitudes_cesfam', $userSolPermisos))
                                            <span class="px-2 py-0.5 bg-purple-50 text-purple-700 font-semibold rounded-md text-[10.5px] border border-purple-200 whitespace-nowrap">
                                                Solicitudes Cesfam
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5 flex-wrap">
                                    @if (($user->solicitudes_activas_count ?? 0) > 0)
                                        <a href="{{ route('dispatch.index') }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors"
                                            title="Ver solicitudes asignadas en Despacho">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ $user->solicitudes_activas_count }} por despachar
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Sin pendientes</span>
                                    @endif

                                    @if (($user->solicitudes_despachadas_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $user->solicitudes_despachadas_count }} despachada(s)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (($user->status ?? 'activo') === 'activo')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">

                                    <button type="button" data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        data-last="{{ $user->last_name }}" data-email="{{ $user->email }}"
                                        data-role="{{ $user->roles->first()->name ?? '' }}"
                                        data-solicitudes="{{ implode(',', $userSolPermisos) }}" onclick="openEdit(this)"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                        title="Editar usuario">
                                        <svg class="w-4.5 h-4.5 w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <button class="p-2 text-slate-300 cursor-not-allowed rounded-lg"
                                            title="No puedes deshabilitar tu propia cuenta" disabled>
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                </path>
                                            </svg>
                                        </button>
                                    @else
                                        <form id="toggle-form-{{ $user->id }}"
                                            action="{{ route('users.toggle-status', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                onclick="confirmToggleStatus('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->status ?? 'activo' }}')"
                                                class="p-2 rounded-lg transition-colors cursor-pointer {{ ($user->status ?? 'activo') === 'activo' ? 'text-slate-400 hover:text-rose-600 hover:bg-rose-50' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' }}"
                                                title="{{ ($user->status ?? 'activo') === 'activo' ? 'Desactivar usuario' : 'Activar usuario' }}">
                                                @if (($user->status ?? 'activo') === 'activo')
                                                    <!-- Icono Desactivar / Bloquear -->
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <!-- Icono Activar -->
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
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
    <div id="RolesModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden">

            <!-- Cabecera del Modal -->
            <div class="px-6 py-5 bg-slate-900 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-indigo-500/20 border border-indigo-400/30 rounded-xl flex items-center justify-center text-indigo-300 shadow-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-lg font-extrabold tracking-tight text-white">Roles y Accesos por Vista</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/30 text-indigo-200 border border-indigo-400/30">
                                {{ $roles->count() }} Roles · {{ count($modulos) }} Vistas
                            </span>
                        </div>
                        <p class="text-[12px] text-slate-300 mt-0.5">Configura qué módulos y vistas (incluyendo Solicitudes Establecimientos y Solicitudes Cesfam) puede visualizar cada rol.</p>
                    </div>
                </div>
                <button type="button" onclick="closeRoles()"
                    class="text-slate-400 hover:text-white p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Cuerpo scrolleable -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-slate-50/50">

                <!-- Listado de roles -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">Roles Configurados en el Sistema</h3>
                        <button type="button" id="btnNewRoleTop" onclick="openRoleForm(null)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nuevo Rol
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($roles as $rol)
                            @php
                                $esAdminRol = $rol->name === 'Administrador';
                                $permisosSlugs = $esAdminRol
                                    ? array_keys($modulos)
                                    : $rol->permissions->map(fn ($p) => str_replace('ver.', '', $p->name))->values()->all();
                            @endphp
                            <div
                                class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 bg-white border {{ $esAdminRol ? 'border-teal-200 ring-1 ring-teal-500/10' : 'border-slate-200' }} rounded-xl shadow-sm hover:border-indigo-200 transition-all">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $esAdminRol ? 'bg-teal-500' : 'bg-indigo-500' }}"></span>
                                        <p class="text-[14px] font-extrabold text-slate-800">{{ $rol->name }}</p>
                                        <span
                                            class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-full border {{ $esAdminRol ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                                            {{ $esAdminRol ? 'Acceso Total (' . count($modulos) . ' vistas)' : count($permisosSlugs) . ' de ' . count($modulos) . ' vista(s)' }}
                                        </span>
                                        <span class="text-[10.5px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $rol->users_count ?? 0 }} usuario(s)
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mt-3">
                                        @forelse ($permisosSlugs as $slug)
                                            @php
                                                $nombreVista = $modulos[$slug] ?? ucfirst(str_replace('_', ' ', $slug));
                                                $esVistaSolicitud = in_array($slug, ['solicitudes', 'solicitudes_cesfam']);
                                                $badgeClasses = match($slug) {
                                                    'solicitudes' => 'bg-teal-50 text-teal-800 border-teal-200',
                                                    'solicitudes_cesfam' => 'bg-purple-50 text-purple-800 border-purple-200',
                                                    'despacho_controlado' => 'bg-rose-50 text-rose-800 border-rose-200',
                                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2.5 py-1 rounded-md border {{ $badgeClasses }}">
                                                @if ($esVistaSolicitud)
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $slug === 'solicitudes_cesfam' ? 'bg-purple-500' : 'bg-teal-500' }}"></span>
                                                @endif
                                                {{ $nombreVista }}
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-slate-400 italic">Sin vistas habilitadas para este rol</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                                    <button type="button"
                                        onclick='openRoleForm({{ json_encode(['id' => $rol->id, 'name' => $rol->name, 'modulos' => $permisosSlugs]) }})'
                                        class="px-3.5 py-2 text-[12px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors cursor-pointer inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Configurar Vistas
                                    </button>
                                    @if (!in_array($rol->name, ['Administrador', 'Usuario']))
                                        <form action="{{ route('roles.destroy', $rol->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar el rol {{ addslashes($rol->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-2 text-[12px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Formulario de rol (crear/editar) -->
                <div id="roleFormBox" class="hidden bg-white border-2 border-indigo-200 rounded-2xl p-5 shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-100">
                        <div>
                            <h3 id="roleFormTitle" class="text-[15px] font-extrabold text-slate-800">Nuevo Rol</h3>
                            <p class="text-[11.5px] text-slate-500">Marca las vistas a las que tendrán acceso los usuarios con este rol.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-[11px] font-extrabold">
                                <span id="roleSelectedCount">0</span> / {{ count($modulos) }} vistas seleccionadas
                            </span>
                            <button type="button" onclick="toggleAllRoleModules(true)"
                                class="px-2.5 py-1 text-[11px] font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded-lg transition-colors cursor-pointer">
                                Marcar todas
                            </button>
                            <button type="button" onclick="toggleAllRoleModules(false)"
                                class="px-2.5 py-1 text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors cursor-pointer">
                                Desmarcar
                            </button>
                        </div>
                    </div>

                    <form id="roleForm" method="POST" action="{{ route('roles.store') }}">
                        @csrf
                        <div id="roleMethod"></div>

                        <div class="mb-5">
                            <label for="role_name"
                                class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre del
                                Rol <span class="text-rose-500">*</span></label>
                            <input type="text" id="role_name" name="name" required
                                placeholder="Ej. Bodeguero, Encargado CESFAM, Químico Farmacéutico..."
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-[13px] font-semibold">
                        </div>

                        <div class="space-y-4 mb-5">
                            @foreach ($modulosGrupos as $grupoNombre => $grupoData)
                                <div class="bg-slate-50/80 border border-slate-200 rounded-xl p-4" data-role-group="{{ $loop->index }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $grupoData['icono'] }}"></path>
                                            </svg>
                                            <span class="text-[11.5px] font-extrabold text-slate-700 uppercase tracking-wider">{{ $grupoNombre }}</span>
                                        </div>
                                        <button type="button" onclick="toggleGroupRoleModules({{ $loop->index }})"
                                            class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                            Alternar sección
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach ($grupoData['items'] as $key => $info)
                                            <label
                                                class="role-card-label flex items-start gap-3 bg-white border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-indigo-400 transition-all shadow-sm">
                                                <input type="checkbox" name="modulos[]" value="{{ $key }}"
                                                    data-modulo="{{ $key }}"
                                                    onchange="updateRoleSelectedCount()"
                                                    class="w-4 h-4 mt-0.5 accent-indigo-600 rounded role-modulo shrink-0">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="text-[12.5px] font-bold text-slate-800">{{ $info['label'] }}</span>
                                                        @if (in_array($key, ['solicitudes', 'solicitudes_cesfam']))
                                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold uppercase {{ $key === 'solicitudes_cesfam' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                                                {{ $key === 'solicitudes_cesfam' ? 'CESFAM' : 'Establecimientos' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="block text-[11px] text-slate-500 leading-snug mt-0.5">{{ $info['desc'] }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-2.5 border-t border-slate-100 pt-4">
                            <button type="button" onclick="cancelRoleForm()"
                                class="px-4 py-2.5 text-[12.5px] font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors cursor-pointer">
                                <span id="roleSubmitText">Crear Rol</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Botón para crear rol nuevo -->
                <button type="button" id="btnNewRole" onclick="openRoleForm(null)"
                    class="w-full py-3.5 border-2 border-dashed border-indigo-200 hover:border-indigo-400 hover:bg-indigo-50/40 text-indigo-600 font-bold text-[13px] rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-2 bg-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear nuevo rol personalizado
                </button>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end shrink-0">
                <button type="button" onclick="closeRoles()"
                    class="px-6 py-2.5 text-[13px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Solicitudes de Recuperación -->
    <div id="RecoveryModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Solicitudes de Recuperación</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Usuarios que han solicitado restablecer su contraseña de
                            acceso</p>
                    </div>
                </div>
                <button type="button" onclick="closeRecoveryModal()"
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="assignPasswordForm" method="POST" action="">
                @csrf
                <input type="hidden" name="password" id="hidden_password_input">
            </form>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse ($passwordRequests as $req)
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-amber-50/50 border border-amber-200/60 rounded-xl gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $req->email }}</p>
                            <span class="text-xs text-amber-700 font-medium">
                                Solicitado: {{ \Carbon\Carbon::parse($req->created_at)->locale('es')->diffForHumans() }}
                            </span>
                        </div>

                        <button type="button" data-email="{{ $req->email }}"
                            onclick="promptNewPassword(this.dataset.email)"
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
    <div id="UserModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h1 id="userModalTitle" class="text-xl font-bold text-slate-800">Crear Nuevo Usuario</h1>
                    <p class="text-sm text-slate-500 mt-0.5">El rol define las vistas a las que tendrá acceso.</p>
                </div>
                <button type="button" onclick="closeUserModal()"
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="userModalForm" method="POST" action="{{ route('users.store') }}" class="space-y-4" novalidate
                onsubmit="return validateUserEmail(event)"> @csrf
                <div id="userModalMethod"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name"
                            class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre <span
                                class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Ej. Juan"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div>
                        <label for="last_name"
                            class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Apellido
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Ej. Pérez"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email"
                            class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Correo Electrónico <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                            oninvalid="this.setCustomValidity(this.value === '' ? 'Completa este campo' : 'Ingresa un correo válido con su dominio (ej. usuario@ejemplo.com)')"
                            oninput="this.setCustomValidity('')" placeholder="ejemplo@drogueriadas.com"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>
                    <div class="sm:col-span-2" id="passwordField">
                        <label for="password"
                            class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Contraseña temporal <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" minlength="8" maxlength="64"
                            placeholder="Entre 8 y 64 caracteres"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px]">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="role"
                            class="block text-[12px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Rol del Usuario
                            <span class="font-normal normal-case text-slate-400">(define las vistas a las que
                                accede)</span>
                        </label>
                        <select id="role" name="role"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-[13px] bg-white">
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}">
                                    {{ $rol->name }} —
                                    {{ $rol->name === 'Administrador' ? 'acceso total' : $rol->permissions->count() . ' vista(s)' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Para crear o ajustar los accesos de un rol, usa el botón
                            "Roles y Accesos".</p>
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
        function openRecoveryModal() {
            document.getElementById('RecoveryModal').classList.remove('hidden');
        }

        function closeRecoveryModal() {
            document.getElementById('RecoveryModal').classList.add('hidden');
        }

        function openRoles() {
            document.getElementById('RolesModal').classList.remove('hidden');
        }

        function closeRoles() {
            document.getElementById('RolesModal').classList.add('hidden');
        }

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

        function closeUserModal() {
            document.getElementById('UserModal').classList.add('hidden');
        }

        function updateRoleSelectedCount() {
            const checkboxes = document.querySelectorAll('.role-modulo');
            let count = 0;
            checkboxes.forEach(cb => {
                const label = cb.closest('.role-card-label');
                if (cb.checked) {
                    count++;
                    if (label) {
                        label.classList.add('border-indigo-400', 'bg-indigo-50/40', 'ring-1', 'ring-indigo-500/20');
                        label.classList.remove('border-slate-200', 'bg-white');
                    }
                } else if (label) {
                    label.classList.remove('border-indigo-400', 'bg-indigo-50/40', 'ring-1', 'ring-indigo-500/20');
                    label.classList.add('border-slate-200', 'bg-white');
                }
            });
            const countEl = document.getElementById('roleSelectedCount');
            if (countEl) countEl.textContent = count;
        }

        function toggleAllRoleModules(state) {
            document.querySelectorAll('.role-modulo').forEach(cb => {
                cb.checked = !!state;
            });
            updateRoleSelectedCount();
        }

        function toggleGroupRoleModules(groupIdx) {
            const group = document.querySelector(`[data-role-group="${groupIdx}"]`);
            if (!group) return;
            const boxes = Array.from(group.querySelectorAll('.role-modulo'));
            const allChecked = boxes.every(cb => cb.checked);
            boxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            updateRoleSelectedCount();
        }

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

            updateRoleSelectedCount();
            document.getElementById('btnNewRole').classList.add('hidden');
            box.classList.remove('hidden');
            box.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
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
                html: `
            <p class="text-[13px] text-slate-600 mb-4">Ingresa la nueva clave para <strong>${email}</strong>:</p>
            <div class="relative w-full max-w-xs mx-auto">
                <input type="password" id="swal_password_input" class="swal2-input !m-0 !w-full !pr-10 text-[13px]" placeholder="Mínimo 8 caracteres" style="box-sizing: border-box;">
                <button type="button" id="toggle_swal_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                    <svg id="swal_eye_open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="swal_eye_closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.122-.363c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.022 3.829m-2.115 2.115A8 8 0 0112 15a3 3 0 01-3-3c0-.414.084-.81.236-1.168" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>
            </div>
        `,
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Guardar Contraseña',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                didOpen: () => {
                    const input = document.getElementById('swal_password_input');
                    const btn = document.getElementById('toggle_swal_password');
                    const eyeOpen = document.getElementById('swal_eye_open');
                    const eyeClosed = document.getElementById('swal_eye_closed');

                    btn.addEventListener('click', () => {
                        if (input.type === 'password') {
                            input.type = 'text';
                            eyeOpen.classList.remove('hidden');
                            eyeClosed.classList.add('hidden');
                        } else {
                            input.type = 'password';
                            eyeOpen.classList.add('hidden');
                            eyeClosed.classList.remove('hidden');
                        }
                    });
                },
                preConfirm: () => {
                    const value = document.getElementById('swal_password_input').value;
                    if (!value) {
                        Swal.showValidationMessage('¡Debes ingresar una contraseña!');
                        return false;
                    }
                    if (value.length < 8) {
                        Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres.');
                        return false;
                    }
                    return value;
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

        function validateUserEmail(event) {
            const emailInput = document.getElementById('email');
            const emailValue = emailInput.value.trim();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            // 1. Validar Correo Electrónico
            if (!emailRegex.test(emailValue)) {
                event.preventDefault();
                emailInput.setCustomValidity('Ingresa un correo válido con su dominio (ej. usuario@ejemplo.com o .cl)');
                emailInput.reportValidity();
                emailInput.focus();
                return false;
            }
            emailInput.setCustomValidity('');

            const passwordField = document.getElementById('passwordField');
            const passwordInput = document.getElementById('password');

            if (passwordField && passwordField.style.display !== 'none') {
                const passValue = passwordInput.value;

                if (passValue.length < 8) {
                    event.preventDefault();
                    passwordInput.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
                    passwordInput.reportValidity();
                    passwordInput.focus();
                    return false;
                }

                if (passValue.length > 64) {
                    event.preventDefault();
                    passwordInput.setCustomValidity('La contraseña no puede superar los 64 caracteres.');
                    passwordInput.reportValidity();
                    passwordInput.focus();
                    return false;
                }

                passwordInput.setCustomValidity('');
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    this.setCustomValidity('');
                });
            }
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    this.setCustomValidity('');
                });
            }
        });

        function confirmToggleStatus(userId, userName, currentStatus) {
            const isActivo = currentStatus === 'activo';
            const actionText = isActivo ? 'desactivar' : 'activar';
            const confirmButtonColor = isActivo ? '#e11d48' : '#059669';

            Swal.fire({
                title: `¿${isActivo ? 'Desactivar' : 'Activar'} usuario?`,
                html: `¿Estás seguro de que deseas ${actionText} a <strong>${userName}</strong>?`,
                icon: isActivo ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: `Sí, ${actionText}`,
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`toggle-form-${userId}`).submit();
                }
            });
        }
    </script>
@endsection
