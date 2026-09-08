@extends('layouts.app')
@section('title', 'Administración de Usuarios')

@section('content')

    {{-- 1. CABECERA Y BOTONES DE ACCIÓN --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Gestión de Usuarios</h1>
            <p class="text-sm text-slate-500">Administra accesos, roles, permisos y estado de los usuarios</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <!-- Botón de Permisos -->
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-50 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Matriz de Permisos
            </button>
            <!-- Botón Nuevo Usuario -->
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Nuevo usuario
            </button>
        </div>
    </div>

    {{-- 2. TARJETAS DE MÉTRICAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-50 rounded-md text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Total Usuarios</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">24</h3>
        </div>

        <!-- Activos -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm border-l-2 border-l-emerald-500">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-emerald-50 rounded-md text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Activos</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">21</h3>
        </div>

        <!-- Inactivos -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm border-l-2 border-l-red-500">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-red-50 rounded-md text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Inactivos</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">3</h3>
        </div>

        <!-- Roles -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm border-l-2 border-l-indigo-500">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-indigo-50 rounded-md text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Roles Configurados</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">4</h3>
        </div>
    </div>

    {{-- 3. FILTROS MEJORADOS VISUALMENTE --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col lg:flex-row gap-4 justify-between items-center">
        <!-- Buscador -->
        <div class="relative w-full lg:w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" placeholder="Buscar por nombre o correo..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
        </div>

        <!-- Desplegables de Roles y Estados (Diseño arreglado) -->
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Desplegable Roles -->
            <div class="relative w-full sm:w-48">
                <select class="w-full appearance-none py-2.5 pl-4 pr-10 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer shadow-sm">
                    <option value="">👤 Todos los Roles</option>
                    <option value="admin">Administrador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="operador">Operador Bodega</option>
                    <option value="auditor">Auditor</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Desplegable Estados -->
            <div class="relative w-full sm:w-48">
                <select class="w-full appearance-none py-2.5 pl-4 pr-10 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer shadow-sm">
                    <option value="">🔘 Todos los Estados</option>
                    <option value="1">🟢 Solo Activos</option>
                    <option value="0">🔴 Solo Inactivos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABLA PRINCIPAL DE USUARIOS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-xs uppercase bg-slate-50 text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-medium">Usuario</th>
                        <th class="px-5 py-4 font-medium">Rol</th>
                        <th class="px-5 py-4 font-medium">Permisos Básicos</th>
                        <th class="px-5 py-4 font-medium">Estado</th>
                        <th class="px-5 py-4 font-medium text-center">Acceso</th>
                        <th class="px-5 py-4 font-medium text-right">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Fila 1: Usuario Activo -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center text-xs">CR</div>
                                <div>
                                    <p class="font-medium text-slate-800">Carlos Rodríguez</p>
                                    <p class="text-xs text-slate-400">crodriguez@empresa.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-100">Administrador</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500">Acceso Total (18/18)</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-600 text-xs font-medium rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Activo
                            </span>
                        </td>
                        <!-- Toggle Activado -->
                        <td class="px-5 py-4 text-center">
                            <button type="button" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-emerald-500 transition-colors duration-200 ease-in-out">
                                <span class="translate-x-4 pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button title="Editar" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                <button title="Permisos" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Fila 2: Usuario Inactivo -->
                    <tr class="bg-slate-50/40 opacity-75 hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">LG</div>
                                <div>
                                    <p class="font-medium text-slate-800">Luis Gómez</p>
                                    <p class="text-xs text-slate-400">lgomez@empresa.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-100">Auditor</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500">Solo Lectura (4/18)</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-red-50 text-red-600 text-xs font-medium rounded-full">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Inactivo
                            </span>
                        </td>
                        <!-- Toggle Desactivado -->
                        <td class="px-5 py-4 text-center">
                            <button type="button" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-slate-300 transition-colors duration-200 ease-in-out">
                                <span class="translate-x-0 pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button title="Editar" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                <button title="Permisos" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg></button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. RESUMEN DE ROLES (INFORMATIVO) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Descripción de Roles -->
        <div class="lg:col-span-1 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4">Jerarquía de Roles</h2>
            
            <div class="space-y-3">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-800">Administrador</p>
                        <p class="text-[10px] text-slate-500">Acceso total al sistema</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-800">Supervisor</p>
                        <p class="text-[10px] text-slate-500">Gestión operativa y reportes</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-800">Operador Bodega</p>
                        <p class="text-[10px] text-slate-500">Tareas de piso y escáner QR</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos Básicos -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4">Módulos del Sistema</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                    <p class="text-xs font-bold text-slate-700">Recepción / Despacho</p>
                    <p class="text-[10px] text-emerald-600 font-medium mt-1">✓ Lectura y Escritura</p>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                    <p class="text-xs font-bold text-slate-700">Trazabilidad</p>
                    <p class="text-[10px] text-emerald-600 font-medium mt-1">✓ Solo Consulta</p>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                    <p class="text-xs font-bold text-slate-700">Gestión Usuarios</p>
                    <p class="text-[10px] text-red-500 font-medium mt-1">🔒 Solo Administrador</p>
                </div>
            </div>
        </div>

    </div>

@endsection