@extends('layouts.app')
@section('title', 'Administración de Usuarios')

@section('content')

    {{-- 1. CABECERA Y BOTONES DE ACCIÓN --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight">Gestión de Usuarios</h1>
            <p class="text-sm text-slate-500 mt-1">Administra accesos, roles, permisos y estado del personal operativo</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <!-- Botón de Permisos -->
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 hover:text-slate-900 focus:ring-4 focus:ring-slate-100 transition-all shadow-sm flex">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Matriz de Permisos
            </button>
            <!-- Botón Nuevo Usuario -->
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 hover:shadow-md hover:-translate-y-0.5 focus:ring-4 focus:ring-blue-500/20 transition-all shadow-sm flex">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Usuario
            </button>
        </div>
    </div>

    {{-- 2. TARJETAS DE MÉTRICAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div class="flex items-center gap-3 mb-3 relative">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 ring-1 ring-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Total Usuarios</p>
            </div>
            <h3 class="text-2xl font-black text-slate-800 relative">24</h3>
        </div>

        <!-- Activos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div class="flex items-center gap-3 mb-3 relative">
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 ring-1 ring-emerald-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Activos</p>
            </div>
            <h3 class="text-2xl font-black text-slate-800 relative">21</h3>
        </div>

        <!-- Inactivos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </div>
            <div class="flex items-center gap-3 mb-3 relative">
                <div class="p-2 bg-rose-50 rounded-lg text-rose-600 ring-1 ring-rose-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Inactivos</p>
            </div>
            <h3 class="text-2xl font-black text-slate-800 relative">3</h3>
        </div>

        <!-- Roles -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
            </div>
            <div class="flex items-center gap-3 mb-3 relative">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 ring-1 ring-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Roles Configurados</p>
            </div>
            <h3 class="text-2xl font-black text-slate-800 relative">4</h3>
        </div>
    </div>

    {{-- 3. FILTROS Y BÚSQUEDA --}}
    <div class="bg-white p-2.5 rounded-2xl border border-slate-200/80 shadow-sm mb-6 flex flex-col lg:flex-row gap-3 justify-between items-center">
        <!-- Buscador -->
        <div class="relative w-full lg:w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" placeholder="Buscar por nombre o correo electrónico..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
        </div>

        <!-- Desplegables -->
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Roles -->
            <div class="relative w-full sm:w-52">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <select class="w-full appearance-none py-2.5 pl-10 pr-10 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer">
                    <option value="">Todos los Roles</option>
                    <option value="admin">Administrador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="operador">Operador Bodega</option>
                    <option value="auditor">Auditor</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Estados -->
            <div class="relative w-full sm:w-48">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <select class="w-full appearance-none py-2.5 pl-10 pr-10 bg-slate-50/50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer">
                    <option value="">Todos los Estados</option>
                    <option value="1">Solo Activos</option>
                    <option value="0">Solo Inactivos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABLA PRINCIPAL DE USUARIOS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider">Usuario</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Rol Asignado</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Nivel de Acceso</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Estado</th>
                        <th class="px-6 py-4 font-bold tracking-wider text-center">Acceso Sistema</th>
                        <th class="px-6 py-4 font-bold tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Fila 1: Usuario Activo -->
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 font-bold flex items-center justify-center text-sm shadow-inner ring-2 ring-white">
                                    CR
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Carlos Rodríguez</p>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">crodriguez@empresa.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg ring-1 ring-inset ring-blue-700/10">Administrador</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-100 rounded-full h-1.5 w-24">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: 100%"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">Total (18/18)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg ring-1 ring-inset ring-emerald-600/10">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Activo
                            </span>
                        </td>
                        <!-- Toggle Activado -->
                        <td class="px-6 py-4 text-center">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-emerald-500 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button title="Editar" class="p-2 hover:bg-blue-50 rounded-lg text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button title="Permisos" class="p-2 hover:bg-indigo-50 rounded-lg text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg>
                                </button>
                                <button title="Eliminar" class="p-2 hover:bg-rose-50 rounded-lg text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Fila 2: Usuario Inactivo -->
                    <tr class="bg-slate-50/40 hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 opacity-75">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 font-bold flex items-center justify-center text-sm shadow-inner ring-2 ring-white">
                                    LG
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700">Luis Gómez</p>
                                    <p class="text-xs text-slate-400 font-medium mt-0.5">lgomez@empresa.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 opacity-75">
                            <span class="inline-flex px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg ring-1 ring-inset ring-amber-600/20">Auditor</span>
                        </td>
                        <td class="px-6 py-4 opacity-75">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-200 rounded-full h-1.5 w-24">
                                    <div class="bg-slate-400 h-1.5 rounded-full" style="width: 22%"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-500">Lectura (4/18)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 opacity-75">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg ring-1 ring-inset ring-slate-500/10">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactivo
                            </span>
                        </td>
                        <!-- Toggle Desactivado -->
                        <td class="px-6 py-4 text-center">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-slate-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                                <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button title="Editar" class="p-2 hover:bg-blue-50 rounded-lg text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button title="Permisos" class="p-2 hover:bg-indigo-50 rounded-lg text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg>
                                </button>
                                <button title="Eliminar" class="p-2 hover:bg-rose-50 rounded-lg text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
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
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h2 class="text-base font-bold text-slate-900">Jerarquía de Roles</h2>
            </div>
            
            <div class="space-y-3">
                <div class="p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-300 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Administrador</p>
                        <p class="text-xs text-slate-500 mt-0.5">Control total del sistema, usuarios y configuración global.</p>
                    </div>
                </div>
                <div class="p-3.5 bg-white border border-slate-200 rounded-xl hover:border-indigo-300 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Supervisor</p>
                        <p class="text-xs text-slate-500 mt-0.5">Gestión operativa, reportes avanzados y auditorías.</p>
                    </div>
                </div>
                <div class="p-3.5 bg-white border border-slate-200 rounded-xl hover:border-emerald-300 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Operador Bodega</p>
                        <p class="text-xs text-slate-500 mt-0.5">Tareas de piso, recepción, despacho y escáner QR.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos Básicos -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-5">
                <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                <h2 class="text-base font-bold text-slate-900">Resumen de Permisos por Módulo</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-slate-50/80 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm font-bold text-slate-800">Recepción / Despacho</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-100/50 text-emerald-700 text-xs font-semibold rounded">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Lectura y Escritura
                    </span>
                </div>

                <div class="p-4 bg-slate-50/80 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <p class="text-sm font-bold text-slate-800">Trazabilidad</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-100/50 text-blue-700 text-xs font-semibold rounded">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Solo Consulta
                    </span>
                </div>

                <div class="p-4 bg-slate-50/80 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <p class="text-sm font-bold text-slate-800">Gestión Usuarios</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-rose-100/50 text-rose-700 text-xs font-semibold rounded">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Restringido (Solo Admin)
                    </span>
                </div>
            </div>
        </div>

    </div>

@endsection