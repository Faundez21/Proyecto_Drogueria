@extends('layouts.app')

@section('title', 'Proveedores')
@section('header', 'Directorio de Proveedores')

@section('content')
    <!-- Barra Superior: Navegación y Acciones -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Proveedores
            </a>
        </div>
    </div>
    <!-- Tarjetas de Resumen (Métricas) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Total Registrados</p>
                    <h4 class="text-3xl font-extrabold text-slate-800">142</h4>
                </div>
                <div class="p-3 bg-blue-600 text-white rounded-xl shadow-md shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Activos -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Proveedores Activos</p>
                    <h4 class="text-3xl font-extrabold text-slate-800">138</h4>
                </div>
                <div class="p-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Nuevos -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Agregados este mes</p>
                    <h4 class="text-3xl font-extrabold text-slate-800">4</h4>
                </div>
                <div class="p-3 bg-purple-600 text-white rounded-xl shadow-md shadow-purple-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal (Tabla y Controles) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        
        <!-- Barra de Herramientas -->
        <div class="p-5 border-b border-slate-200 flex flex-col lg:flex-row justify-between items-center gap-4">
            
            <!-- Buscador y Filtros -->
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Buscar por RUT, Razón Social..." class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-slate-700">
                </div>
                
                <select class="py-2.5 px-4 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm w-full sm:w-auto">
                    <option value="">Todos los Estados</option>
                    <option value="activos">Solo Activos</option>
                    <option value="inactivos">Inactivos</option>
                </select>
            </div>
            
            <!-- Botón Acción Principal -->
            <div class="w-full lg:w-auto flex justify-end">
                <a href="{{ route('proveedores.create') }}" class="w-full lg:w-auto bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-800 flex items-center justify-center gap-2 transition-all shadow-sm focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Registrar Proveedor
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4">Información del Proveedor</th>
                        <th class="px-6 py-4">Datos de Contacto</th>
                        <th class="px-6 py-4">Categoría Principal</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    
                    <!-- Fila 1 -->
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                    LA
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Laboratorios Andinos S.A.</p>
                                    <p class="text-slate-500 text-xs mt-0.5 font-mono">RUT: 76.543.210-K</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="flex items-center gap-2 text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    contacto@labandinos.cl
                                </span>
                                <span class="flex items-center gap-2 text-slate-500 text-xs">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    +56 9 1234 5678
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                Fármacos y Medicamentos
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                Activo
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right relative">
                            <!-- Menú tres puntos -->
                            <button class="btn-menu p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" data-target="menu-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                            </button>
                            <!-- Dropdown -->
                            <div id="menu-1" class="dropdown-menu hidden absolute right-8 top-12 w-48 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 py-2 z-10 text-left">
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Ver Ficha Técnica
                                </a>
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editar Proveedor
                                </a>
                                <div class="h-px w-full bg-slate-100 my-1"></div>
                                <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    Desactivar
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Fila 2 -->
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                    IM
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Insumos Médicos Sur SpA</p>
                                    <p class="text-slate-500 text-xs mt-0.5 font-mono">RUT: 77.111.222-3</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="flex items-center gap-2 text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    ventas@imsur.cl
                                </span>
                                <span class="flex items-center gap-2 text-slate-500 text-xs">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    +56 41 222 3344
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                Insumos Clínicos
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                Activo
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right relative">
                            <!-- Menú tres puntos -->
                            <button class="btn-menu p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" data-target="menu-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                            </button>
                            <!-- Dropdown -->
                            <div id="menu-2" class="dropdown-menu hidden absolute right-8 top-12 w-48 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 py-2 z-10 text-left">
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Ver Ficha Técnica
                                </a>
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editar Proveedor
                                </a>
                                <div class="h-px w-full bg-slate-100 my-1"></div>
                                <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    Desactivar
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Fila 3 (Inactivo) -->
                    <tr class="hover:bg-slate-50/80 transition-colors opacity-75">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm border border-slate-300">
                                    EQ
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700">Equipamiento Médico Central</p>
                                    <p class="text-slate-400 text-xs mt-0.5 font-mono">RUT: 78.444.333-2</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="flex items-center gap-2 text-slate-500">
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    hola@equipcentral.cl
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Equipamiento Mayor
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-2 h-2 bg-slate-400 rounded-full"></span>
                                Inactivo
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación Moderna -->
        <div class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-slate-500">Mostrando del <span class="font-medium text-slate-900">1</span> al <span class="font-medium text-slate-900">10</span> de <span class="font-medium text-slate-900">142</span> proveedores</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed bg-slate-50">Anterior</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 font-bold border border-blue-100">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">3</button>
                <span class="px-1 text-slate-400">...</span>
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Script para los menús desplegables -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.btn-menu');
            let activeMenu = null;

            buttons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const targetId = button.getAttribute('data-target');
                    const targetMenu = document.getElementById(targetId);

                    // Cerrar el menú anterior si hay uno abierto
                    if (activeMenu && activeMenu !== targetMenu) {
                        activeMenu.classList.add('hidden');
                    }

                    // Alternar el actual
                    targetMenu.classList.toggle('hidden');
                    activeMenu = targetMenu.classList.contains('hidden') ? null : targetMenu;
                });
            });

            // Cerrar al hacer clic en cualquier parte de la pantalla outside
            document.addEventListener('click', () => {
                if (activeMenu) {
                    activeMenu.classList.add('hidden');
                    activeMenu = null;
                }
            });

            // Evitar que el clic dentro del menú lo cierre
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', (e) => e.stopPropagation());
            });
        });
    </script>
@endsection