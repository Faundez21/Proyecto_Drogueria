@extends('layouts.app')

@section('title', 'Proveedores')
@section('subtitle', 'Directorio y gestión de proveedores registrados.')

@section('actions')
    <button type="button" id="btn-new-provider"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Registrar Proveedor
    </button>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">01</span>
            <span>Abastecimiento</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-800">Proveedores</span>
        </div>

        <!-- Tarjetas KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Total registrados</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">142</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100">Todas las categorías registradas</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Proveedores activos</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">138</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100"><span class="text-emerald-600 font-medium">97%</span> del total habilitados</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Agregados este mes</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">4</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100">Septiembre 2026</p>
            </div>
        </div>

        <!-- Contenedor principal: Tabla blanca estructurada -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- Barra de búsqueda y filtros -->
            <div class="p-4 flex flex-col lg:flex-row justify-between gap-4 border-b border-slate-200 bg-white">
                <div class="relative w-full lg:w-[400px] shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Buscar por RUT, razón social..."
                        class="block w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <select class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 w-full sm:w-48">
                        <option value="">Todas las categorías</option>
                        <option value="farmacos">Fármacos y medicamentos</option>
                        <option value="insumos">Insumos clínicos</option>
                        <option value="equipos">Equipamiento mayor</option>
                    </select>

                    <select class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 w-full sm:w-40">
                        <option value="">Todos los estados</option>
                        <option value="activos">Solo activos</option>
                        <option value="inactivos">Inactivos</option>
                    </select>
                </div>
            </div>

            <!-- Tabla responsive -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-[12px] font-semibold uppercase tracking-wider text-slate-400 bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3.5 whitespace-nowrap">Proveedor</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Contacto</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Categoría</th>
                            <th class="px-6 py-3.5 text-center whitespace-nowrap">Estado</th>
                            <th class="px-6 py-3.5 text-right whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px]">

                        <!-- Fila 1 -->
                        <tr class="provider-row hover:bg-slate-50/80 transition-colors" data-name="Laboratorios Andinos S.A." data-rut="76.543.210-K" data-email="contacto@labandinos.cl" data-phone="+56 9 1234 5678" data-category="Fármacos y medicamentos" data-status="activo">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-mono-das font-bold text-[12px] text-teal-700 bg-teal-50 shrink-0">LA</div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 truncate">Laboratorios Andinos S.A.</p>
                                        <p class="font-mono-das text-[11.5px] text-slate-400 mt-0.5">76.543.210-K</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-700 font-medium">contacto@labandinos.cl</span>
                                    <span class="font-mono-das text-[11.5px] text-slate-400">+56 9 1234 5678</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[12px] font-medium bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                                    Fármacos y medicamentos
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right relative">
                                <button class="btn-menu p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors inline-flex items-center justify-center" data-target="menu-1">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                                <div id="menu-1" class="dropdown-menu hidden fixed w-48 rounded-lg py-1 z-[9999] text-left bg-white border border-slate-200 shadow-xl">
                                    <button type="button" class="js-view-detail w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Ver ficha técnica
                                    </button>
                                    <button type="button" class="js-edit-provider w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Editar proveedor
                                    </button>
                                    <div class="h-px w-full my-1 bg-slate-100"></div>
                                    <button type="button" class="js-toggle-status w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        Desactivar
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Fila 2 -->
                        <tr class="provider-row hover:bg-slate-50/80 transition-colors" data-name="Equipamiento Médico Central" data-rut="78.444.333-2" data-email="hola@equipcentral.cl" data-phone="" data-category="Equipamiento mayor" data-status="inactivo">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-mono-das font-bold text-[12px] text-slate-500 bg-slate-100 shrink-0">EQ</div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-700 truncate">Equipamiento Médico Central</p>
                                        <p class="font-mono-das text-[11.5px] text-slate-400 mt-0.5">78.444.333-2</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-600 font-medium">hola@equipcentral.cl</span>
                                    <span class="font-mono-das text-[11.5px] text-slate-400">Sin teléfono</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[12px] font-medium bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                                    Equipamiento mayor
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactivo
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right relative">
                                <button class="btn-menu p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors inline-flex items-center justify-center" data-target="menu-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                                <div id="menu-2" class="dropdown-menu hidden fixed w-48 rounded-lg py-1 z-[9999] text-left bg-white border border-slate-200 shadow-xl">
                                    <button type="button" class="js-view-detail w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Ver ficha técnica
                                    </button>
                                    <button type="button" class="js-edit-provider w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Editar proveedor
                                    </button>
                                    <div class="h-px w-full my-1 bg-slate-100"></div>
                                    <button type="button" class="js-toggle-status w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-teal-600 hover:bg-teal-50 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Reactivar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación Responsive -->
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white">
                <span class="text-[12.5px] text-slate-500 text-center sm:text-left">Mostrando <span class="font-mono-das font-semibold text-slate-700">1–10</span> de <span class="font-mono-das font-semibold text-slate-700">142</span> proveedores</span>
                <div class="flex items-center gap-1.5 w-full sm:w-auto justify-center sm:justify-end">
                    <button class="px-3 py-1.5 rounded-lg text-[12.5px] font-medium border border-slate-200 text-slate-400 cursor-not-allowed bg-slate-50 transition-colors hidden sm:block">Anterior</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg font-mono-das text-[12.5px] font-bold bg-teal-600 text-white shadow-sm transition-colors">1</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg font-mono-das text-[12.5px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">2</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg font-mono-das text-[12.5px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">3</button>
                    <span class="px-1 text-slate-400">···</span>
                    <button class="px-3 py-1.5 rounded-lg text-[12.5px] font-medium border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors hidden sm:block">Siguiente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Ficha técnica (Visual) -->
    <div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm js-modal-backdrop" data-close="modal-detail"></div>
        <div class="relative w-full max-w-md rounded-xl overflow-hidden opacity-0 scale-95 transition-all duration-150 bg-white border border-slate-200 shadow-2xl"
            id="modal-detail-panel">

            <div class="flex items-start justify-between p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center font-mono-das font-bold text-[13px] text-white bg-teal-600 shadow-sm shrink-0">LA</div>
                    <div>
                        <h3 id="detail-name" class="text-[15px] font-bold text-slate-800">Laboratorios Andinos S.A.</h3>
                        <p id="detail-rut" class="font-mono-das text-[12px] text-slate-400">RUT: 76.543.210-K</p>
                    </div>
                </div>
                <button type="button" class="js-modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" data-close="modal-detail">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[11.5px] font-medium text-slate-400">Correo</p>
                        <p id="detail-email" class="text-[13px] font-medium text-slate-700 mt-0.5 break-all">contacto@labandinos.cl</p>
                    </div>
                    <div>
                        <p class="text-[11.5px] font-medium text-slate-400">Teléfono</p>
                        <p id="detail-phone" class="font-mono-das text-[13px] font-medium text-slate-700 mt-0.5">+56 9 1234 5678</p>
                    </div>
                    <div>
                        <p class="text-[11.5px] font-medium text-slate-400">Categoría</p>
                        <p id="detail-category" class="text-[13px] font-medium text-slate-700 mt-0.5">Fármacos y medicamentos</p>
                    </div>
                    <div>
                        <p class="text-[11.5px] font-medium text-slate-400">Estado</p>
                        <span id="detail-status" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2.5 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" class="js-modal-close px-4 py-2 rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-200/60 border border-slate-200 w-full sm:w-auto transition-colors" data-close="modal-detail">
                    Cerrar
                </button>
                <button type="button" id="modal-detail-edit-btn" class="px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm w-full sm:w-auto transition-all">
                    Editar proveedor
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Formulario Registrar / Editar (Visual) -->
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm js-modal-backdrop" data-close="modal-edit"></div>
        <div class="relative w-full max-w-lg rounded-xl overflow-hidden opacity-0 scale-95 transition-all duration-150 max-h-[90vh] flex flex-col bg-white border border-slate-200 shadow-2xl"
            id="modal-edit-panel">

            <div class="flex items-start justify-between p-5 border-b border-slate-100 bg-slate-50/50 shrink-0">
                <div>
                    <h3 class="text-[16px] font-bold text-slate-800">Proveedor</h3>
                    <p class="text-[12px] text-slate-500 mt-0.5">Completa la información general de la empresa.</p>
                </div>
                <button type="button" class="js-modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" data-close="modal-edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Razón Social</label>
                    <input type="text" placeholder="Ej: Laboratorios Andinos S.A."
                        id="provider-name" class="w-full px-3.5 py-2 rounded-lg text-[13px] border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">RUT</label>
                        <input type="text" id="provider-rut" placeholder="76.543.210-K"
                            class="w-full px-3.5 py-2 rounded-lg text-[13px] font-mono-das border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Categoría Principal</label>
                        <select id="provider-category" class="w-full px-3.5 py-2 rounded-lg text-[13px] border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                            <option>Fármacos y medicamentos</option>
                            <option>Insumos clínicos</option>
                            <option>Equipamiento mayor</option>
                            <option>Dispositivos médicos</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Correo Electrónico</label>
                        <input type="email" id="provider-email" placeholder="contacto@empresa.cl"
                            class="w-full px-3.5 py-2 rounded-lg text-[13px] border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Teléfono</label>
                        <input type="text" id="provider-phone" placeholder="+56 9 1234 5678"
                            class="w-full px-3.5 py-2 rounded-lg text-[13px] font-mono-das border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Estado</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 rounded-lg p-3 border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="edit-status" value="activo" checked class="accent-teal-600">
                            <span class="text-[13px] font-medium text-slate-700">Activo</span>
                        </label>
                        <label class="flex items-center gap-2.5 rounded-lg p-3 border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="edit-status" value="inactivo" class="accent-rose-600">
                            <span class="text-[13px] font-medium text-slate-700">Inactivo</span>
                        </label>
                    </div>
                </div>
            </form>

            <div class="flex flex-col sm:flex-row justify-end gap-2.5 p-4 border-t border-slate-100 bg-slate-50 shrink-0">
                <button type="button" class="js-modal-close px-4 py-2 rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-200/60 border border-slate-200 w-full sm:w-auto transition-colors" data-close="modal-edit">
                    Cancelar
                </button>
                <button type="button" id="modal-edit-action" class="px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm w-full sm:w-auto transition-all">
                    Guardar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 3: Confirmación Desactivar/Reactivar (Visual) -->
    <div id="modal-confirm" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm js-modal-backdrop" data-close="modal-confirm"></div>
        <div class="relative w-full max-w-sm rounded-xl overflow-hidden opacity-0 scale-95 transition-all duration-150 bg-white border border-slate-200 shadow-2xl"
            id="modal-confirm-panel">

            <div class="p-6 text-center sm:text-left">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-4 mx-auto sm:mx-0 bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-[16px] font-bold text-slate-800">Cambiar estado</h3>
                <p id="confirm-message" class="text-[13px] text-slate-500 mt-2 leading-relaxed">
                    ¿Confirmas que deseas cambiar el estado operacional de este proveedor?
                </p>
            </div>
            <div class="flex flex-col sm:flex-row justify-end gap-2.5 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" class="js-modal-close px-4 py-2 rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-200/60 border border-slate-200 w-full sm:w-auto transition-colors" data-close="modal-confirm">
                    Cancelar
                </button>
                <button type="button" id="modal-confirm-action" class="px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm w-full sm:w-auto transition-all">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeMenu = null;
            let selectedRow = null;

            // Menú de acciones.
            function positionMenu(button, menu) {
                const rect = button.getBoundingClientRect();
                const menuWidth = 192;
                const menuHeight = menu.offsetHeight || 150;
                const gap = 8;

                let left = rect.right - menuWidth;
                let top = rect.bottom + gap;

                if (left < 8) {
                    left = 8;
                }

                if (left + menuWidth > window.innerWidth - 8) {
                    left = window.innerWidth - menuWidth - 8;
                }

                if (top + menuHeight > window.innerHeight - 8) {
                    top = rect.top - menuHeight - gap;
                }

                if (top < 8) {
                    top = 8;
                }

                menu.style.left = `${left}px`;
                menu.style.top = `${top}px`;
            }

            function closeActiveMenu() {
                if (!activeMenu) return;

                activeMenu.classList.add('hidden');
                activeMenu = null;
            }

            document.querySelectorAll('.btn-menu').forEach(button => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const targetMenu = document.getElementById(button.dataset.target);
                    if (!targetMenu) return;

                    if (activeMenu && activeMenu !== targetMenu) {
                        activeMenu.classList.add('hidden');
                    }

                    const isHidden = targetMenu.classList.contains('hidden');

                    if (!isHidden) {
                        targetMenu.classList.add('hidden');
                        activeMenu = null;
                        return;
                    }

                    targetMenu.classList.remove('hidden');
                    positionMenu(button, targetMenu);
                    activeMenu = targetMenu;
                });
            });

            window.addEventListener('resize', () => {
                if (!activeMenu) return;

                const button = document.querySelector(`[data-target="${activeMenu.id}"]`);

                if (button) {
                    positionMenu(button, activeMenu);
                }
            });

            window.addEventListener('scroll', () => {
                if (!activeMenu) return;

                const button = document.querySelector(`[data-target="${activeMenu.id}"]`);

                if (button) {
                    positionMenu(button, activeMenu);
                }
            }, true);

            document.addEventListener('click', () => {
                if (activeMenu) {
                    activeMenu.classList.add('hidden');
                    activeMenu = null;
                }
            });

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', event => event.stopPropagation());
            });

            function openModal(id) {
                const modal = document.getElementById(id);
                const panel = document.getElementById(`${id}-panel`);

                if (!modal || !panel) return;

                modal.classList.remove('hidden');

                requestAnimationFrame(() => {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                });
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                const panel = document.getElementById(`${id}-panel`);

                if (!modal || !panel) return;

                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');

                setTimeout(() => modal.classList.add('hidden'), 150);
            }

            function getProvider(row) {
                return {
                    name: row.dataset.name || '',
                    rut: row.dataset.rut || '',
                    email: row.dataset.email || '',
                    phone: row.dataset.phone || '',
                    category: row.dataset.category || '',
                    status: row.dataset.status || 'inactivo'
                };
            }

            function updateDetail(provider) {
                document.getElementById('detail-name').textContent = provider.name;
                document.getElementById('detail-rut').textContent = `RUT: ${provider.rut}`;
                document.getElementById('detail-email').textContent = provider.email || 'Sin correo';
                document.getElementById('detail-phone').textContent = provider.phone || 'Sin teléfono';
                document.getElementById('detail-category').textContent = provider.category;

                const status = document.getElementById('detail-status');
                status.innerHTML = `
                    <span class="w-1.5 h-1.5 rounded-full ${provider.status === 'activo' ? 'bg-emerald-500' : 'bg-slate-400'}"></span>
                    ${provider.status === 'activo' ? 'Activo' : 'Inactivo'}
                `;

                status.className = provider.status === 'activo'
                    ? 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5'
                    : 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11.5px] font-medium bg-slate-100 text-slate-600 border border-slate-200 mt-0.5';
            }

            function updateForm(provider) {
                document.getElementById('provider-name').value = provider.name;
                document.getElementById('provider-rut').value = provider.rut;
                document.getElementById('provider-email').value = provider.email;
                document.getElementById('provider-phone').value = provider.phone;
                document.getElementById('provider-category').value = provider.category;

                document.querySelectorAll('input[name="edit-status"]').forEach(input => {
                    input.checked = input.value === provider.status;
                });
            }

            function updateConfirm(provider) {
                const nextStatus = provider.status === 'activo' ? 'desactivar' : 'reactivar';

                document.getElementById('confirm-message').textContent =
                    `¿Confirmas que deseas ${nextStatus} a ${provider.name}?`;

                const action = document.getElementById('modal-confirm-action');

                action.textContent = provider.status === 'activo' ? 'Desactivar' : 'Reactivar';

                action.className = provider.status === 'activo'
                    ? 'px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm w-full sm:w-auto transition-all'
                    : 'px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm w-full sm:w-auto transition-all';
            }

            function openDetail(row) {
                selectedRow = row;
                updateDetail(getProvider(row));
                openModal('modal-detail');
            }

            function openEdit(row = null) {
                selectedRow = row;

                if (row) {
                    updateForm(getProvider(row));
                } else {
                    document.getElementById('provider-name').value = '';
                    document.getElementById('provider-rut').value = '';
                    document.getElementById('provider-email').value = '';
                    document.getElementById('provider-phone').value = '';
                    document.getElementById('provider-category').selectedIndex = 0;
                    document.querySelector('input[name="edit-status"][value="activo"]').checked = true;
                }

                openModal('modal-edit');
            }

            // Abrir y cerrar modales.
            document.querySelectorAll('[data-close]').forEach(element => {
                element.addEventListener('click', () => {
                    closeModal(element.dataset.close);
                });
            });

            document.addEventListener('keydown', event => {
                if (event.key !== 'Escape') return;

                ['modal-detail', 'modal-edit', 'modal-confirm'].forEach(id => {
                    const modal = document.getElementById(id);

                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(id);
                    }
                });
            });

            // Acción: ver ficha.
            document.querySelectorAll('.js-view-detail').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('.provider-row');
                    closeActiveMenu();

                    if (row) {
                        openDetail(row);
                    }
                });
            });

            // Acción: editar.
            document.querySelectorAll('.js-edit-provider').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('.provider-row');
                    closeActiveMenu();

                    if (row) {
                        openEdit(row);
                    }
                });
            });

            // Acción: nuevo proveedor.
            document.getElementById('btn-new-provider').addEventListener('click', () => {
                closeActiveMenu();
                openEdit();
            });

            // Desde la ficha se puede editar el mismo proveedor.
            document.getElementById('modal-detail-edit-btn').addEventListener('click', () => {
                const row = selectedRow;

                closeModal('modal-detail');

                setTimeout(() => {
                    if (row) {
                        openEdit(row);
                    }
                }, 160);
            });

            // Acción: cambiar estado.
            document.querySelectorAll('.js-toggle-status').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('.provider-row');
                    closeActiveMenu();

                    if (!row) return;

                    selectedRow = row;
                    updateConfirm(getProvider(row));
                    openModal('modal-confirm');
                });
            });

            // Confirmar cambio de estado.
            document.getElementById('modal-confirm-action').addEventListener('click', () => {
                if (!selectedRow) {
                    closeModal('modal-confirm');
                    return;
                }

                const provider = getProvider(selectedRow);
                const newStatus = provider.status === 'activo' ? 'inactivo' : 'activo';

                selectedRow.dataset.status = newStatus;

                const statusCell = selectedRow.querySelector('td:nth-child(4)');

                if (statusCell) {
                    statusCell.innerHTML = newStatus === 'activo'
                        ? `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </span>
                        `
                        : `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactivo
                            </span>
                        `;
                }

                closeModal('modal-confirm');
                updateDetail(getProvider(selectedRow));
            });

            // Guardar proveedor del formulario.
            document.getElementById('modal-edit-action').addEventListener('click', () => {
                const name = document.getElementById('provider-name').value.trim();
                const rut = document.getElementById('provider-rut').value.trim();
                const email = document.getElementById('provider-email').value.trim();
                const phone = document.getElementById('provider-phone').value.trim();
                const category = document.getElementById('provider-category').value;
                const status = document.querySelector('input[name="edit-status"]:checked')?.value || 'activo';

                if (!name || !rut || !email || !category) {
                    alert('Completa los campos obligatorios.');
                    return;
                }

                // Si es un registro existente, actualiza la fila.
                if (selectedRow) {
                    selectedRow.dataset.name = name;
                    selectedRow.dataset.rut = rut;
                    selectedRow.dataset.email = email;
                    selectedRow.dataset.phone = phone;
                    selectedRow.dataset.category = category;
                    selectedRow.dataset.status = status;

                    const nameElement = selectedRow.querySelector('td:first-child p.font-semibold');
                    const rutElement = selectedRow.querySelector('td:first-child p.font-mono-das');
                    const emailElement = selectedRow.querySelector('td:nth-child(2) span:first-child');
                    const phoneElement = selectedRow.querySelector('td:nth-child(2) span:last-child');
                    const categoryElement = selectedRow.querySelector('td:nth-child(3) span');
                    const statusCell = selectedRow.querySelector('td:nth-child(4)');

                    if (nameElement) nameElement.textContent = name;
                    if (rutElement) rutElement.textContent = rut;
                    if (emailElement) emailElement.textContent = email;
                    if (phoneElement) phoneElement.textContent = phone || 'Sin teléfono';
                    if (categoryElement) categoryElement.textContent = category;

                    if (statusCell) {
                        statusCell.innerHTML = status === 'activo'
                            ? `
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            `
                            : `
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactivo
                                </span>
                            `;
                    }

                    closeModal('modal-edit');
                    return;
                }

                // En esta versión el botón nuevo prepara el formulario.
                // La persistencia real debe hacerse en el backend.
                alert('Proveedor preparado para registrar. Conecta este formulario a tu ruta POST para guardarlo en la base de datos.');
                closeModal('modal-edit');
            });
        });
    </script>
@endsection