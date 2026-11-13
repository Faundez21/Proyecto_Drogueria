@extends('layouts.app')

@section('title', 'Usuarios y Permisos')
@section('subtitle', 'Administración de cuentas de funcionarios, roles de acceso por vista y recuperación de contraseñas.')

@section('actions')
    @if (count($passwordRequests) > 0)
        <button type="button" onclick="openRecoveryModal()"
            class="relative inline-flex items-center justify-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold py-2 px-3.5 rounded-lg transition-all shadow-2xs cursor-pointer text-[12.5px]">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>
            Recuperación de Claves
            <span class="bg-amber-500 text-white text-[11px] px-1.5 py-0.5 rounded-full font-extrabold">{{ count($passwordRequests) }}</span>
        </button>
    @endif
    <button type="button" onclick="openRoles()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-[13px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 shadow-2xs transition-all duration-200 cursor-pointer">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
            </path>
        </svg>
        Roles y Accesos
    </button>
    <button type="button" onclick="openCreate()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-[13px] font-bold text-white bg-[#0f968c] hover:bg-teal-700 shadow-sm transition-all duration-200 cursor-pointer">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nuevo Usuario
    </button>
@endsection

@section('content')
    @php
        $totalUsuarios = $users->count();
        $usuariosActivos = $users->where('status', 'activo')->count();
        $usuariosInactivos = $totalUsuarios - $usuariosActivos;
        $totalSolicitudesActivas = $users->sum('solicitudes_activas_count');

        $mapaRolesVistas = [];
        foreach ($roles as $rolItem) {
            $esAdm = $rolItem->name === 'Administrador';
            $slugs = $esAdm
                ? array_keys($modulos)
                : $rolItem->permissions->map(fn ($p) => str_replace('ver.', '', $p->name))->values()->all();
            $mapaRolesVistas[$rolItem->name] = array_values(array_map(fn ($s) => $modulos[$s] ?? $s, $slugs));
        }
    @endphp

    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">04</span>
        <span>Configuración</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Usuarios y Permisos</span>
    </div>

    <!-- alertas de sesion -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-[13px] font-bold text-emerald-900">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-xs">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-[13px] font-extrabold text-rose-900">Revise los datos ingresados:</p>
                    <ul class="mt-1 list-disc list-inside text-[12px] text-rose-700 font-medium">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- tarjetas kpi de resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Funcionarios</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalUsuarios }}</p>
                <p class="text-[11px] font-semibold text-slate-500 mt-1">Cuentas registradas en sistema</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Estado de Cuentas</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-emerald-600">{{ $usuariosActivos }}</p>
                    <span class="text-[12px] font-bold text-slate-400">activos</span>
                </div>
                <p class="text-[11px] font-semibold text-slate-500 mt-1">{{ $usuariosInactivos }} cuenta(s) inactiva(s)</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div onclick="openRoles()" class="bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-300 p-5 shadow-xs flex items-center justify-between cursor-pointer transition-all group">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 group-hover:text-indigo-600 transition-colors">Roles y Perfiles</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ $roles->count() }}</p>
                <p class="text-[11px] font-bold text-indigo-600 mt-1 underline decoration-indigo-300">Configurar {{ count($modulos) }} vistas →</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
        </div>

        <div onclick="{{ count($passwordRequests) > 0 ? 'openRecoveryModal()' : '' }}" class="bg-white rounded-2xl border {{ count($passwordRequests) > 0 ? 'border-amber-300 bg-amber-50/20 cursor-pointer hover:border-amber-400' : 'border-slate-200/80' }} p-5 shadow-xs flex items-center justify-between transition-all">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Recuperación de Clave</p>
                <p class="text-2xl font-black {{ count($passwordRequests) > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">{{ count($passwordRequests) }}</p>
                <p class="text-[11px] font-semibold {{ count($passwordRequests) > 0 ? 'text-amber-700 font-bold' : 'text-slate-500' }} mt-1">
                    {{ count($passwordRequests) > 0 ? 'Solicitudes pendientes de atención' : 'Sin solicitudes pendientes' }}
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl {{ count($passwordRequests) > 0 ? 'bg-amber-100 text-amber-600 border border-amber-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- directorio y filtros de usuarios -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">

        <!-- cabecera con buscador y filtros en vivo -->
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-[15px] font-extrabold text-slate-800">Directorio de Usuarios y Accesos</h3>
                        <span id="contadorUsuariosFiltrados" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-teal-50 text-teal-700 border border-teal-200">
                            {{ $totalUsuarios }} usuario(s)
                        </span>
                    </div>
                    <p class="text-[12px] text-slate-500 mt-0.5">Filtre y gestione los funcionarios según su rol asignado o estado de cuenta.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end pt-2 border-t border-slate-200/60">
                <div>
                    <label for="filtroBusquedaUsuario" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Buscar Funcionario</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="filtroBusquedaUsuario" oninput="filtrarTablaUsuarios()"
                            placeholder="Nombre, apellido o correo..."
                            class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-[12.5px] font-medium text-slate-700 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/15 shadow-2xs">
                    </div>
                </div>

                <div>
                    <label for="filtroRolUsuario" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rol de Usuario</label>
                    <select id="filtroRolUsuario" onchange="filtrarTablaUsuarios()"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-[12.5px] font-semibold text-slate-700 focus:outline-none focus:border-teal-500 cursor-pointer shadow-2xs">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $rol)
                            <option value="{{ strtolower($rol->name) }}">{{ $rol->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filtroEstadoUsuario" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Estado de Cuenta</label>
                    <select id="filtroEstadoUsuario" onchange="filtrarTablaUsuarios()"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-[12.5px] font-semibold text-slate-700 focus:outline-none focus:border-teal-500 cursor-pointer shadow-2xs">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>
                </div>

                <div>
                    <button type="button" onclick="limpiarFiltrosUsuarios()"
                        class="w-full px-4 py-2 bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-[12.5px] font-bold transition-all shadow-2xs hover:shadow-sm cursor-pointer flex items-center justify-center gap-1.5 h-[38px]">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[820px]">
                <thead>
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-3.5 whitespace-nowrap">Funcionario</th>
                        <th class="px-6 py-3.5 whitespace-nowrap">Rol y Vistas Habilitadas</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">Solicitudes Asignadas (Despacho)</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">Estado</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]" id="mainTableBody">

                    @forelse ($users as $user)
                        @php
                            $nombreRolPrincipal = $user->roles->first()->name ?? 'Sin Rol';
                            $esAdminUser = $user->hasRole('Administrador');
                            $userPermisosSlugs = $esAdminUser
                                ? array_keys($modulos)
                                : $user->getAllPermissions()
                                    ->pluck('name')
                                    ->map(fn ($p) => str_replace('ver.', '', $p))
                                    ->values()
                                    ->all();
                            $userSolPermisos = array_values(array_intersect($userPermisosSlugs, ['solicitudes', 'solicitudes_cesfam']));
                            $estadoUser = strtolower($user->status ?? 'activo');
                        @endphp
                        <tr class="fila-usuario hover:bg-slate-50/80 transition-colors"
                            data-search="{{ strtolower($user->name . ' ' . $user->last_name . ' ' . $user->email) }}"
                            data-role="{{ strtolower($nombreRolPrincipal) }}"
                            data-status="{{ $estadoUser }}">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-extrabold text-white text-[13px] shadow-xs shrink-0 {{ $esAdminUser ? 'bg-gradient-to-br from-teal-600 to-teal-700' : 'bg-gradient-to-br from-indigo-600 to-slate-700' }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-slate-800 truncate">{{ $user->name }} {{ $user->last_name }}</span>
                                            @if ($user->id === auth()->id())
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-50 text-teal-700 border border-teal-200">Tú</span>
                                            @endif
                                        </div>
                                        <div class="text-slate-500 text-[12px] truncate font-medium">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if ($user->roles->count() > 0)
                                            @foreach ($user->roles as $rol)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border {{ $rol->name === 'Administrador' ? 'bg-teal-50 text-teal-800 border-teal-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $rol->name === 'Administrador' ? 'bg-teal-500' : 'bg-indigo-500' }}"></span>
                                                    {{ $rol->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 font-bold rounded-full text-[11px] border border-slate-200">Sin Rol</span>
                                        @endif

                                        <span class="text-[11px] font-bold text-slate-400">
                                            · {{ $esAdminUser ? 'Acceso Total (' . count($modulos) . ' vistas)' : count($userPermisosSlugs) . ' vista(s) habilitada(s)' }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-1 items-center">
                                        @if ($esAdminUser)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded text-[10.5px] border border-slate-200">
                                                Todas las vistas del sistema
                                            </span>
                                        @else
                                            @if (in_array('solicitudes', $userSolPermisos))
                                                <span class="px-2 py-0.5 bg-teal-50 text-teal-700 font-bold rounded text-[10px] border border-teal-200">
                                                    Solicitudes Establecimientos
                                                </span>
                                            @endif
                                            @if (in_array('solicitudes_cesfam', $userSolPermisos))
                                                <span class="px-2 py-0.5 bg-purple-50 text-purple-700 font-bold rounded text-[10px] border border-purple-200">
                                                    Solicitudes CESFAM
                                                </span>
                                            @endif
                                            @php
                                                $otrasVistas = array_values(array_diff($userPermisosSlugs, ['solicitudes', 'solicitudes_cesfam']));
                                                $primerasOtras = array_slice($otrasVistas, 0, 3);
                                                $restantesCount = count($otrasVistas) - count($primerasOtras);
                                            @endphp
                                            @foreach ($primerasOtras as $vSlug)
                                                <span class="px-2 py-0.5 bg-slate-50 text-slate-600 font-semibold rounded text-[10px] border border-slate-200">
                                                    {{ $modulos[$vSlug] ?? $vSlug }}
                                                </span>
                                            @endforeach
                                            @if ($restantesCount > 0)
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px] border border-slate-200"
                                                    title="{{ implode(', ', array_map(fn($s) => $modulos[$s] ?? $s, $otrasVistas)) }}">
                                                    +{{ $restantesCount }} más
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- solicitudes asignadas -->
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5 flex-wrap">
                                    @if (($user->solicitudes_activas_count ?? 0) > 0)
                                        <a href="{{ route('dispatch.index') }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors"
                                            title="Ver solicitudes asignadas en Despacho">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ $user->solicitudes_activas_count }} por despachar
                                        </a>
                                    @else
                                        <span class="text-[11.5px] text-slate-400 font-medium">Sin pendientes</span>
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
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
                                        class="px-2.5 py-1.5 text-[11.5px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors cursor-pointer inline-flex items-center gap-1"
                                        title="Editar datos y rol del usuario">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <button class="p-1.5 text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg"
                                            title="No puede deshabilitar su propia cuenta" disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                class="p-1.5 rounded-lg border transition-colors cursor-pointer {{ ($user->status ?? 'activo') === 'activo' ? 'text-rose-600 bg-rose-50 hover:bg-rose-100 border-rose-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200' }}"
                                                title="{{ ($user->status ?? 'activo') === 'activo' ? 'Desactivar usuario' : 'Activar usuario' }}">
                                                @if (($user->status ?? 'activo') === 'activo')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[12px] text-slate-500 font-medium">
            <span>Total: <strong>{{ $users->count() }}</strong> funcionario(s) · <strong>{{ $roles->count() }}</strong> rol(es) configurado(s)</span>
            <span>Solicitudes de despacho activas asignadas: <strong class="text-slate-700">{{ $totalSolicitudesActivas }}</strong></span>
        </div>
    </div>

    <!-- modal roles y accesos -->
    <div id="RolesModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden my-auto">

            <!-- cabecera del modal -->
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
                        <p class="text-[12px] text-slate-300 mt-0.5">Configure qué módulos y vistas (incluyendo Solicitudes Establecimientos y Solicitudes CESFAM) puede visualizar cada rol.</p>
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

            <!-- cuerpo scrolleable -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-slate-50/50">

                <!-- listado de roles -->
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
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-md">
                                                <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                Sin vistas asignadas — Debe tener al menos 1 vista
                                            </span>
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
                                        <form id="delete-role-form-{{ $rol->id }}" action="{{ route('roles.destroy', $rol->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteRole('{{ $rol->id }}', '{{ addslashes($rol->name) }}')"
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

                <!-- formulario de rol (crear/editar) -->
                <div id="roleFormBox" class="hidden bg-white border-2 border-indigo-200 rounded-2xl p-5 shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-100">
                        <div>
                            <h3 id="roleFormTitle" class="text-[15px] font-extrabold text-slate-800">Nuevo Rol</h3>
                            <p class="text-[11.5px] text-slate-500">Marque las vistas a las que tendrán acceso los usuarios con este rol.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="roleCountBadge" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-[11px] font-extrabold transition-colors">
                                <span id="roleSelectedCount">0</span> / {{ count($modulos) }} vistas <span class="text-rose-500 font-black">(Mínimo 1)</span>
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

                    <form id="roleForm" method="POST" action="{{ route('roles.store') }}" onsubmit="return validateRoleForm(event)">
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

                <!-- boton para crear rol nuevo -->
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

    <!-- modal solicitudes de recuperacion -->
    <div id="RecoveryModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="px-6 py-5 bg-slate-900 text-white flex justify-between items-center">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-amber-500/20 border border-amber-400/30 rounded-xl flex items-center justify-center text-amber-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-white">Solicitudes de Recuperación de Clave</h2>
                        <p class="text-xs text-slate-300 mt-0.5">Funcionarios que han solicitado restablecer su contraseña de acceso</p>
                    </div>
                </div>
                <button type="button" onclick="closeRecoveryModal()"
                    class="text-slate-400 hover:text-white p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
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

            <div class="p-6 space-y-3 max-h-96 overflow-y-auto bg-slate-50/50">
                @forelse ($passwordRequests as $req)
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-white border border-amber-200 rounded-xl shadow-xs gap-3">
                        <div>
                            <p class="text-[13.5px] font-extrabold text-slate-800">{{ $req->email }}</p>
                            <span class="text-xs text-amber-700 font-semibold">
                                Solicitado {{ \Carbon\Carbon::parse($req->created_at)->locale('es')->diffForHumans() }}
                            </span>
                        </div>

                        <button type="button" data-email="{{ $req->email }}"
                            onclick="promptNewPassword(this.dataset.email)"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                            Asignar nueva clave
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm font-medium">No hay solicitudes pendientes de restablecimiento.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeRecoveryModal()"
                    class="px-5 py-2 text-[13px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- modal crear / editar usuario -->
    <div id="UserModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="px-6 py-5 bg-slate-900 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-teal-500/20 border border-teal-400/30 rounded-xl flex items-center justify-center text-teal-300 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 id="userModalTitle" class="text-lg font-extrabold text-white">Crear Nuevo Usuario</h1>
                        <p class="text-xs text-slate-300 mt-0.5">Complete los datos del funcionario y asigne su rol de acceso.</p>
                    </div>
                </div>
                <button type="button" onclick="closeUserModal()"
                    class="text-slate-400 hover:text-white p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="userModalForm" method="POST" action="{{ route('users.store') }}" class="p-6 space-y-5 overflow-y-auto bg-slate-50/40" novalidate
                onsubmit="return validateUserModal(event)">
                @csrf
                <div id="userModalMethod"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name"
                            class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre <span
                                class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Ej. Juan"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all text-[13px] font-medium">
                    </div>

                    <div>
                        <label for="last_name"
                            class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Apellido
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Ej. Pérez"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all text-[13px] font-medium">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email"
                            class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Correo Electrónico Institucional <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                            oninvalid="this.setCustomValidity(this.value === '' ? 'Complete este campo' : 'Ingrese un correo válido con su dominio (ej. usuario@ejemplo.com)')"
                            oninput="this.setCustomValidity('')" placeholder="ejemplo@drogueriadas.cl"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all text-[13px] font-medium">
                    </div>

                    <div class="sm:col-span-2" id="passwordField">
                        <label for="password"
                            class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Contraseña Temporal <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" minlength="8" maxlength="64"
                                placeholder="Entre 8 y 64 caracteres"
                                class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all text-[13px] font-medium">
                            <button type="button" onclick="toggleUserModalPassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="role"
                            class="block text-[11.5px] font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Rol del Usuario <span class="text-rose-500">*</span> <span class="font-normal normal-case text-slate-400">(debe incluir al menos 1 vista asignada)</span>
                        </label>
                        <select id="role" name="role" required onchange="actualizarPreviewRolModal(this.value)"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all text-[13px] font-bold text-slate-800 cursor-pointer">
                            @foreach ($roles as $rol)
                                @php
                                    $cantVistas = $rol->name === 'Administrador' ? count($modulos) : $rol->permissions->count();
                                    $sinVistas = $rol->name !== 'Administrador' && $cantVistas === 0;
                                @endphp
                                <option value="{{ $rol->name }}" class="{{ $sinVistas ? 'text-rose-600' : '' }}" {{ $sinVistas ? 'data-sin-vistas="true"' : '' }}>
                                    {{ $rol->name }} —
                                    @if ($rol->name === 'Administrador')
                                        Acceso total ({{ count($modulos) }} vistas)
                                    @elseif ($cantVistas > 0)
                                        {{ $cantVistas }} vista(s) habilitada(s)
                                    @else
                                        ⚠️ Sin vistas asignadas (No asignable)
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <!-- muestra que vistas incluye el rol que se esta editando -->
                        <div class="mt-3 p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Vistas incluidas en este rol:</span>
                                <button type="button" onclick="closeUserModal(); openRoles();"
                                    class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                    Editar accesos de roles →
                                </button>
                            </div>
                            <div id="previewVistasRolContainer" class="flex flex-wrap gap-1.5"></div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" onclick="closeUserModal()"
                        class="px-5 py-2.5 text-[13px] font-bold text-slate-600 hover:text-slate-800 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-[13px] font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <span id="userModalSubmitText">Guardar Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const MAPA_ROLES_VISTAS = @json($mapaRolesVistas);

        function actualizarPreviewRolModal(roleName) {
            const container = document.getElementById('previewVistasRolContainer');
            if (!container) return;
            container.innerHTML = '';

            const vistas = MAPA_ROLES_VISTAS[roleName] || [];
            if (roleName !== 'Administrador' && vistas.length === 0) {
                container.innerHTML = `
                    <div class="w-full p-2.5 bg-rose-50 border border-rose-200 rounded-lg flex items-center gap-2 text-rose-700">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-[11.5px] font-bold">Este rol no tiene vistas asignadas. Debe asignarle al menos 1 vista antes de poder asignarlo a un usuario.</span>
                    </div>
                `;
                return;
            }

            vistas.forEach(v => {
                const span = document.createElement('span');
                const esSol = v.toLowerCase().includes('solicitud');
                span.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold border ' +
                    (esSol ? 'bg-teal-50 text-teal-800 border-teal-200' : 'bg-slate-50 text-slate-700 border-slate-200');
                span.textContent = v;
                container.appendChild(span);
            });
        }

        function toggleUserModalPassword() {
            const inp = document.getElementById('password');
            if (!inp) return;
            inp.type = inp.type === 'password' ? 'text' : 'password';
        }

        function filtrarTablaUsuarios() {
            const q = (document.getElementById('filtroBusquedaUsuario')?.value || '').trim().toLowerCase();
            const rol = (document.getElementById('filtroRolUsuario')?.value || '').trim().toLowerCase();
            const est = (document.getElementById('filtroEstadoUsuario')?.value || '').trim().toLowerCase();

            let visibles = 0;
            document.querySelectorAll('.fila-usuario').forEach(tr => {
                const matchSearch = !q || (tr.getAttribute('data-search') || '').includes(q);
                const matchRole = !rol || (tr.getAttribute('data-role') || '') === rol;
                const matchStatus = !est || (tr.getAttribute('data-status') || '') === est;

                if (matchSearch && matchRole && matchStatus) {
                    tr.style.display = '';
                    visibles++;
                } else {
                    tr.style.display = 'none';
                }
            });

            const badge = document.getElementById('contadorUsuariosFiltrados');
            if (badge) {
                badge.textContent = `${visibles} usuario(s)`;
            }
        }

        function limpiarFiltrosUsuarios() {
            const b = document.getElementById('filtroBusquedaUsuario');
            const r = document.getElementById('filtroRolUsuario');
            const e = document.getElementById('filtroEstadoUsuario');
            if (b) b.value = '';
            if (r) r.value = '';
            if (e) e.value = '';
            filtrarTablaUsuarios();
        }

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

        let userModalSubmitConfirmed = false;

        function openCreate() {
            userModalSubmitConfirmed = false;
            const form = document.getElementById('userModalForm');
            form.action = "{{ route('users.store') }}";
            form.reset();
            document.getElementById('userModalMethod').innerHTML = '';
            document.getElementById('passwordField').style.display = '';
            document.getElementById('password').required = true;
            document.getElementById('userModalTitle').textContent = 'Crear Nuevo Usuario';
            document.getElementById('userModalSubmitText').textContent = 'Guardar Usuario';
            actualizarPreviewRolModal(document.getElementById('role').value);
            document.getElementById('UserModal').classList.remove('hidden');
        }

        function openEdit(button) {
            userModalSubmitConfirmed = false;
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

            actualizarPreviewRolModal(document.getElementById('role').value);

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

            const badge = document.getElementById('roleCountBadge');
            if (badge) {
                if (count === 0) {
                    badge.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-indigo-100');
                    badge.classList.add('bg-rose-50', 'text-rose-700', 'border-rose-200');
                } else {
                    badge.classList.remove('bg-rose-50', 'text-rose-700', 'border-rose-200');
                    badge.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-100');
                }
            }
        }

        let roleSubmitConfirmed = false;

        function validateRoleForm(event) {
            if (roleSubmitConfirmed) return true;

            const nameInput = document.getElementById('role_name');
            if (!nameInput || !nameInput.value.trim()) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Nombre obligatorio',
                    text: 'Debe ingresar un nombre para el rol.',
                    confirmButtonColor: '#4f46e5'
                });
                if (nameInput) nameInput.focus();
                return false;
            }

            const checkedCount = document.querySelectorAll('.role-modulo:checked').length;
            if (checkedCount === 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Vista obligatoria',
                    text: 'Debe asignar al menos 1 vista a este rol para que los usuarios puedan utilizarlo.',
                    confirmButtonColor: '#4f46e5'
                });
                return false;
            }

            event.preventDefault();
            const roleName = nameInput.value.trim();
            const isEditing = document.getElementById('roleMethod')?.innerHTML.includes('PUT');

            Swal.fire({
                title: isEditing ? '¿Guardar cambios del Rol?' : '¿Crear nuevo Rol?',
                html: `Se procederá a ${isEditing ? 'actualizar' : 'crear'} el rol <strong>${roleName}</strong> con <strong>${checkedCount}</strong> vista(s) asignada(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: isEditing ? 'Sí, guardar cambios' : 'Sí, crear rol',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    roleSubmitConfirmed = true;
                    document.getElementById('roleForm').submit();
                }
            });

            return false;
        }

        function confirmDeleteRole(roleId, roleName) {
            Swal.fire({
                title: '¿Eliminar Rol?',
                html: `¿Está seguro de que desea eliminar el rol <strong>${roleName}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar rol',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-role-form-${roleId}`).submit();
                }
            });
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
            roleSubmitConfirmed = false;
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

        function confirmDelete(event, nombre, formId) {
            event.preventDefault();
            Swal.fire({
                title: '¿Eliminar Usuario?',
                html: `¿Está seguro de que desea eliminar al usuario <strong>${nombre}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (formId) {
                        document.getElementById(formId).submit();
                    } else if (event.target && event.target.closest('form')) {
                        event.target.closest('form').submit();
                    }
                }
            });
            return false;
        }

        function promptNewPassword(email) {
            Swal.fire({
                title: 'Asignar Nueva Contraseña',
                html: `
            <p class="text-[13px] text-slate-600 mb-4">Ingrese la nueva clave para <strong>${email}</strong>:</p>
            <div class="relative w-full max-w-xs mx-auto">
                <input type="password" id="swal_password_input" minlength="8" maxlength="64" class="swal2-input !m-0 !w-full !pr-10 text-[13px]" placeholder="Entre 8 y 64 caracteres" style="box-sizing: border-box;">
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
                        Swal.showValidationMessage('¡Debe ingresar una contraseña!');
                        return false;
                    }
                    if (value.length < 8 || value.length > 64) {
                        Swal.showValidationMessage('La contraseña debe tener entre 8 y 64 caracteres.');
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

        function validateUserModal(event) {
            const nameInput = document.getElementById('name');
            const lastNameInput = document.getElementById('last_name');

            if (nameInput && !nameInput.value.trim()) {
                event.preventDefault();
                nameInput.setCustomValidity('Complete este campo');
                nameInput.reportValidity();
                nameInput.focus();
                return false;
            }
            if (nameInput) nameInput.setCustomValidity('');

            if (lastNameInput && !lastNameInput.value.trim()) {
                event.preventDefault();
                lastNameInput.setCustomValidity('Complete este campo');
                lastNameInput.reportValidity();
                lastNameInput.focus();
                return false;
            }
            if (lastNameInput) lastNameInput.setCustomValidity('');

            const emailInput = document.getElementById('email');
            const emailValue = emailInput ? emailInput.value.trim() : '';
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (emailInput && !emailRegex.test(emailValue)) {
                event.preventDefault();
                emailInput.setCustomValidity('Ingrese un correo válido con su dominio (ej. usuario@ejemplo.com o .cl)');
                emailInput.reportValidity();
                emailInput.focus();
                return false;
            }
            if (emailInput) emailInput.setCustomValidity('');

            const passwordField = document.getElementById('passwordField');
            const passwordInput = document.getElementById('password');

            if (passwordField && passwordField.style.display !== 'none' && passwordInput) {
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

            // validar que el rol tenga al menos 1 vista asignada
            const roleSelect = document.getElementById('role');
            const selectedRole = roleSelect ? roleSelect.value : '';

            if (!selectedRole) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Rol obligatorio',
                    text: 'Debe seleccionar un rol para el usuario.',
                    confirmButtonColor: '#0f968c'
                });
                return false;
            }

            if (selectedRole !== 'Administrador') {
                const vistas = MAPA_ROLES_VISTAS[selectedRole] || [];
                if (vistas.length === 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rol sin vistas asignadas',
                        text: `El rol "${selectedRole}" no tiene ninguna vista asignada. Asigne al menos 1 vista al rol en "Roles y Accesos" antes de asignarlo a un usuario.`,
                        confirmButtonColor: '#0f968c'
                    });
                    return false;
                }
            }

            if (userModalSubmitConfirmed) return true;

            event.preventDefault();
            const fullName = `${nameInput.value.trim()} ${lastNameInput.value.trim()}`;
            const isEditing = document.getElementById('userModalMethod')?.innerHTML.includes('PUT');

            Swal.fire({
                title: isEditing ? '¿Guardar cambios de Usuario?' : '¿Crear nuevo Usuario?',
                html: `Se procederá a ${isEditing ? 'actualizar' : 'registrar'} al usuario <strong>${fullName}</strong> con el rol <strong>${selectedRole}</strong>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f968c',
                cancelButtonColor: '#64748b',
                confirmButtonText: isEditing ? 'Sí, guardar cambios' : 'Sí, crear usuario',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    userModalSubmitConfirmed = true;
                    document.getElementById('userModalForm').submit();
                }
            });

            return false;
        }

        // compatibilidad por si se llama con el nombre anterior
        const validateUserEmail = validateUserModal;

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
                html: `¿Está seguro de que desea ${actionText} a <strong>${userName}</strong>?`,
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
