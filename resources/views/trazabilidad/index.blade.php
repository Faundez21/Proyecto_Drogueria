@extends('layouts.app')

@section('title', 'Trazabilidad')
@section('header', 'Trazabilidad y Rastreo')

@section('content')
    <!-- Navegación y Acciones Superiores -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Auditoría
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Rastreo de Lotes</span>
        </div>
        <button class="w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded-md text-xs font-bold hover:bg-slate-50 flex items-center justify-center gap-2 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Exportar Historial
        </button>
    </div>

    <!-- Panel de Búsqueda y Escaneo -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5 flex flex-col md:flex-row items-center gap-3">
        <div class="flex-1 w-full relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" placeholder="Ingrese o escanee N° de Lote, Código de Barras o Serie..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors font-mono font-medium text-slate-800 placeholder:font-sans">
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button class="flex-1 md:flex-none px-4 py-2.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Escanear
            </button>
            <button class="flex-1 md:flex-none px-6 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800 transition-colors shadow-sm">
                Rastrear
            </button>
        </div>
    </div>

    <!-- Resultados del Rastreo (Layout a 2 columnas) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Ficha Técnica del Lote (Izquierda) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm h-fit overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Ficha Técnica del Lote</h3>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-widest border border-emerald-200">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Vigente
                </span>
            </div>
            
            <div class="p-0">
                <!-- Atributos -->
                <div class="divide-y divide-slate-100">
                    <div class="flex flex-col px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Producto</span>
                        <span class="text-sm font-semibold text-slate-800">Paracetamol 500mg</span>
                        <span class="text-xs text-slate-500">Caja x 20 Comprimidos</span>
                    </div>
                    
                    <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-medium text-slate-500">Código de Lote</span>
                        <span class="text-sm font-bold font-mono text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">LOT-12345</span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-medium text-slate-500">Fabricante</span>
                        <span class="text-xs font-semibold text-slate-800">Laboratorios Andinos S.A.</span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-medium text-slate-500">Stock Actual</span>
                        <span class="text-sm font-bold text-slate-800">2,450 <span class="text-xs font-normal text-slate-500">Unidades</span></span>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100">
                        <div class="px-5 py-3 hover:bg-slate-50 transition-colors">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Elaboración</span>
                            <span class="text-xs font-semibold text-slate-800">15/01/2026</span>
                        </div>
                        <div class="px-5 py-3 bg-red-50/30 hover:bg-red-50/50 transition-colors">
                            <span class="block text-[10px] font-bold text-red-400 uppercase tracking-wider mb-1">Vencimiento</span>
                            <span class="text-xs font-bold text-red-600">30/06/2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Línea de Tiempo / Audit Trail (Derecha) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Registro de Movimientos (Audit Trail)</h3>
                <span class="text-xs font-medium text-slate-500">3 Registros encontrados</span>
            </div>
            
            <div class="p-6">
                <div class="relative max-w-3xl">
                    <!-- Línea vertical central -->
                    <div class="absolute top-2 bottom-0 left-[21px] w-px bg-slate-200"></div>

                    <div class="space-y-6">
                        
                        <!-- Evento 1: Despacho (Más reciente) -->
                        <div class="relative flex gap-4 items-start group">
                            <!-- Icono / Punto -->
                            <div class="relative z-10 flex items-center justify-center w-11 h-11 rounded-full bg-amber-100 border-4 border-white shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </div>
                            <!-- Contenido -->
                            <div class="flex-1 bg-white border border-slate-100 rounded-lg p-3.5 shadow-sm group-hover:border-amber-200 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-bold text-slate-800">Despacho Completado</h4>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">16 MAY 2026 • 10:15</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Enviado a <span class="font-semibold text-slate-800">Clínica Santa María</span> (Orden <a href="#" class="text-blue-600 hover:underline">#ORD-0091</a>). Se descontaron <span class="font-bold text-slate-800">50 cajas</span> del inventario.
                                </p>
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600">JP</div>
                                    <p class="text-[10px] font-medium text-slate-500">Registrado por <span class="text-slate-700">Juan Pérez (Logística)</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Evento 2: Almacenamiento -->
                        <div class="relative flex gap-4 items-start group">
                            <div class="relative z-10 flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 border-4 border-white shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div class="flex-1 bg-white border border-slate-100 rounded-lg p-3.5 shadow-sm group-hover:border-blue-200 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-bold text-slate-800">Almacenamiento y Ubicación</h4>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">15 MAY 2026 • 09:00</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Mercadería ubicada físicamente en <span class="font-semibold text-slate-800">Bodega Principal</span>, Pasillo 3, Estante B-02.
                                </p>
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600">MT</div>
                                    <p class="text-[10px] font-medium text-slate-500">Registrado por <span class="text-slate-700">María Torres (Bodega)</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Evento 3: Recepción (Más antiguo) -->
                        <div class="relative flex gap-4 items-start group">
                            <div class="relative z-10 flex items-center justify-center w-11 h-11 rounded-full bg-emerald-100 border-4 border-white shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex-1 bg-white border border-slate-100 rounded-lg p-3.5 shadow-sm group-hover:border-emerald-200 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-bold text-slate-800">Recepción Inicial (Ingreso)</h4>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">15 MAY 2026 • 08:30</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Ingreso al sistema de <span class="font-bold text-slate-800">2,500 cajas</span> mediante Orden de Compra <a href="#" class="text-blue-600 hover:underline">#OC-2026-001</a>. Control de calidad aprobado.
                                </p>
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600">AR</div>
                                    <p class="text-[10px] font-medium text-slate-500">Registrado por <span class="text-slate-700">Admin Sistema (Recepción)</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Fin del Historial (Indicador visual) -->
                        <div class="relative flex gap-4 items-center">
                            <div class="relative z-10 flex items-center justify-center w-11 h-6 shrink-0">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Fin del historial</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection