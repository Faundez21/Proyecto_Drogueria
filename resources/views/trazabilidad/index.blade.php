@extends('layouts.app')

@section('title', 'Trazabilidad')
@section('subtitle', 'Seguimiento detallado de lotes, movimientos y ciclo de vida de productos.')

@section('actions')
    <button type="button"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Exportar Reporte
    </button>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">04</span>
            <span>Control</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-800">Trazabilidad</span>
        </div>

        <!-- Tarjetas KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Movimientos Trazados</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">2,480</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100">Registrados en el sistema este mes</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Lotes Bajo Seguimiento</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">346</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100"><span class="text-emerald-600 font-medium">100%</span> con cadena verificada</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Alertas de Lote</p>
                        <p class="font-mono-das text-[28px] font-bold text-slate-800 mt-1">3</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-3 pt-3 border-t border-slate-100">Requieren revisión de proximidad</p>
            </div>
        </div>

        <!-- Contenedor principal: Tabla de Trazabilidad -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- Barra de búsqueda y filtros -->
            <div class="p-4 flex flex-col lg:flex-row justify-between gap-4 border-b border-slate-200 bg-white">
                <div class="relative w-full lg:w-[400px] shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Buscar por código de lote, producto o RUT..."
                        class="block w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <select class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 w-full sm:w-48">
                        <option value="">Tipo de movimiento</option>
                        <option value="recepcion">Recepción</option>
                        <option value="almacenamiento">Almacenamiento</option>
                        <option value="despacho">Despacho</option>
                        <option value="distribucion">Distribución</option>
                    </select>

                    <select class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 w-full sm:w-40">
                        <option value="">Estado de lote</option>
                        <option value="vigente">Vigente</option>
                        <option value="cuarentena">Cuarentena</option>
                        <option value="vencido">Vencido</option>
                    </select>
                </div>
            </div>

            <!-- Tabla responsive -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="text-[12px] font-semibold uppercase tracking-wider text-slate-400 bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3.5 whitespace-nowrap">Lote / Producto</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Movimiento</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Origen / Destino</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Fecha y Hora</th>
                            <th class="px-6 py-3.5 text-center whitespace-nowrap">Estado Lote</th>
                            <th class="px-6 py-3.5 text-right whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px]">

                        <!-- Fila 1 -->
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-mono-das font-bold text-[11px] text-teal-700 bg-teal-50 shrink-0">L-489</div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 truncate">Paracetamol 500mg Comprimidos</p>
                                        <p class="font-mono-das text-[11.5px] text-slate-400 mt-0.5">Lote: L-489392 · Venc: 12/2027</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[12px] font-medium bg-blue-50 text-blue-700 border border-blue-200 whitespace-nowrap">
                                    Recepción de Proveedor
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-700 font-medium">Laboratorios Andinos S.A.</span>
                                    <span class="text-[11.5px] text-slate-400">Bodega Central A</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono-das text-[12px] text-slate-600">
                                14/09/2026 - 10:15
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Vigente
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right relative">
                                <button class="btn-menu p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors inline-flex items-center justify-center" data-target="menu-1">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                                <div id="menu-1" class="dropdown-menu hidden absolute right-6 top-12 w-52 rounded-lg py-1 z-20 text-left bg-white border border-slate-200 shadow-xl">
                                    <button type="button" class="js-view-lineage w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Ver línea de tiempo
                                    </button>
                                    <button type="button" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Descargar certificado
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Fila 2 -->
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-mono-das font-bold text-[11px] text-amber-700 bg-amber-50 shrink-0">J-512</div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 truncate">Jeringa Descartable 5ml</p>
                                        <p class="font-mono-das text-[11.5px] text-slate-400 mt-0.5">Lote: J-51288 · Venc: 05/2028</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[12px] font-medium bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                                    Despacho a CESFAM
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-700 font-medium">Bodega Central B</span>
                                    <span class="text-[11.5px] text-slate-400">CESFAM Penco</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono-das text-[12px] text-slate-600">
                                14/09/2026 - 08:30
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Vigente
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right relative">
                                <button class="btn-menu p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors inline-flex items-center justify-center" data-target="menu-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                                <div id="menu-2" class="dropdown-menu hidden absolute right-6 top-12 w-52 rounded-lg py-1 z-20 text-left bg-white border border-slate-200 shadow-xl">
                                    <button type="button" class="js-view-lineage w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Ver línea de tiempo
                                    </button>
                                    <button type="button" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Descargar certificado
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación Responsive -->
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white">
                <span class="text-[12.5px] text-slate-500 text-center sm:text-left">Mostrando <span class="font-mono-das font-semibold text-slate-700">1–10</span> de <span class="font-mono-das font-semibold text-slate-700">2,480</span> registros</span>
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

    <!-- Modal: Línea de tiempo / Ruta de Trazabilidad (Visual) -->
    <div id="modal-lineage" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm js-modal-backdrop" data-close="modal-lineage"></div>
        <div class="relative w-full max-w-lg rounded-xl overflow-hidden opacity-0 scale-95 transition-all duration-150 max-h-[90vh] flex flex-col bg-white border border-slate-200 shadow-2xl"
            id="modal-lineage-panel">

            <div class="flex items-start justify-between p-5 border-b border-slate-100 bg-slate-50/50 shrink-0">
                <div>
                    <h3 class="text-[16px] font-bold text-slate-800">Línea de Trazabilidad del Lote</h3>
                    <p class="font-mono-das text-[12px] text-slate-500 mt-0.5">Lote: L-489392 · Paracetamol 500mg</p>
                </div>
                <button type="button" class="js-modal-close p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" data-close="modal-lineage">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Contenido del Timeline -->
            <div class="p-6 space-y-6 overflow-y-auto">
                <div class="relative pl-6 border-l-2 border-teal-500 space-y-6">
                    
                    <!-- Hito 3 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-teal-600 border-2 border-white ring-4 ring-teal-50"></div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-teal-50 text-teal-700 mb-1">Distribución</span>
                            <h4 class="text-[13.5px] font-bold text-slate-800">Despacho a CESFAM Penco</h4>
                            <p class="text-[12px] text-slate-500 mt-0.5">Entrega completada conforme a guía de despacho.</p>
                            <p class="font-mono-das text-[11px] text-slate-400 mt-1">14/09/2026 - 15:40 hrs</p>
                        </div>
                    </div>

                    <!-- Hito 2 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-teal-600 border-2 border-white ring-4 ring-teal-50"></div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 mb-1">Almacenamiento</span>
                            <h4 class="text-[13.5px] font-bold text-slate-800">Ingreso a Bodega Central A</h4>
                            <p class="text-[12px] text-slate-500 mt-0.5">Ubicación asignada: Estante 4, Nivel 2.</p>
                            <p class="font-mono-das text-[11px] text-slate-400 mt-1">14/09/2026 - 11:00 hrs</p>
                        </div>
                    </div>

                    <!-- Hito 1 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-teal-600 border-2 border-white ring-4 ring-teal-50"></div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 mb-1">Recepción</span>
                            <h4 class="text-[13.5px] font-bold text-slate-800">Recepción desde Proveedor</h4>
                            <p class="text-[12px] text-slate-500 mt-0.5">Proveedor: Laboratorios Andinos S.A. (Factura #847593)</p>
                            <p class="font-mono-das text-[11px] text-slate-400 mt-1">14/09/2026 - 10:15 hrs</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-end p-4 border-t border-slate-100 bg-slate-50 shrink-0">
                <button type="button" class="js-modal-close px-4 py-2 rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-200/60 border border-slate-200 transition-colors" data-close="modal-lineage">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeMenu = null;

            // Menús desplegables (tres puntos)
            document.querySelectorAll('.btn-menu').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const targetMenu = document.getElementById(button.getAttribute('data-target'));
                    
                    if (window.innerWidth < 640) {
                        targetMenu.classList.remove('right-6');
                        targetMenu.classList.add('right-0');
                    } else {
                        targetMenu.classList.add('right-6');
                        targetMenu.classList.remove('right-0');
                    }

                    if (activeMenu && activeMenu !== targetMenu) activeMenu.classList.add('hidden');
                    targetMenu.classList.toggle('hidden');
                    activeMenu = targetMenu.classList.contains('hidden') ? null : targetMenu;
                });
            });

            document.addEventListener('click', () => {
                if (activeMenu) { activeMenu.classList.add('hidden'); activeMenu = null; }
            });

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', (e) => e.stopPropagation());
            });

            // Control visual de modales
            function openModal(id) {
                const overlay = document.getElementById(id);
                const panel = document.getElementById(id + '-panel');
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                });
            }

            function closeModal(id) {
                const overlay = document.getElementById(id);
                const panel = document.getElementById(id + '-panel');
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');
                setTimeout(() => overlay.classList.add('hidden'), 150);
            }

            document.querySelectorAll('[data-close]').forEach(el => {
                el.addEventListener('click', () => closeModal(el.getAttribute('data-close')));
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!document.getElementById('modal-lineage').classList.contains('hidden')) {
                        closeModal('modal-lineage');
                    }
                }
            });

            // Abrir Modal de Línea de Trazabilidad
            document.querySelectorAll('.js-view-lineage').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (activeMenu) { activeMenu.classList.add('hidden'); activeMenu = null; }
                    openModal('modal-lineage');
                });
            });
        });
    </script>
@endsection