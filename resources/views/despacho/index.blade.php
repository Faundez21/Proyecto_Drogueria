@extends('layouts.app')

@section('title', 'Despacho')
@section('header', 'Despacho de Pedidos')

@section('content')
    <!-- Navegación y Acciones Superiores -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Movimientos
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Generar Despacho</span>
        </div>
        <button class="w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 flex items-center justify-center gap-2 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Bandeja de Despachos
        </button>
    </div>

    <!-- Tarjetas de Resumen (KPIs Extra Compactos) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Programados Hoy</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">45</h3>
            </div>
            <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">En Picking</p>
                <h3 class="text-xl font-extrabold text-amber-600 mt-0.5">12</h3>
            </div>
            <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Despachados</p>
                <h3 class="text-xl font-extrabold text-emerald-600 mt-0.5">28</h3>
            </div>
            <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal: Guía de Despacho -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Cabecera de Documento y Toggle de Controlados -->
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50/80 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-indigo-100 text-indigo-700 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 tracking-tight">Emisión de Guía de Despacho</h2>
            </div>

            <!-- Toggle Módulo Controlados -->
            <label class="relative inline-flex items-center cursor-pointer bg-red-50 border border-red-100 px-3 py-1 rounded-md hover:bg-red-100 transition-colors">
                <input type="checkbox" class="sr-only peer">
                <div class="w-8 h-4 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[6px] after:left-[14px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-red-500"></div>
                <span class="ml-2 text-[10px] font-bold text-red-700 uppercase tracking-widest">Módulo Controlados</span>
            </label>
        </div>

        <form action="#" method="POST" class="divide-y divide-slate-100">
            
            <!-- SECCIÓN 1: Identificación del Remitente y Documento -->
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Datos Emisor (Solo lectura visual, formato membrete) -->
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Remitente</h4>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                        <p class="text-sm font-bold text-slate-800">I. Municipalidad de Talcahuano</p>
                        <p class="text-[11px] font-medium text-slate-600">Droguería Comunal Talcahuano</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Blanco Encalada 450, Talcahuano
                        </p>
                    </div>
                </div>

                <!-- Datos Documento -->
                <div class="flex flex-col justify-center items-end">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 w-full md:w-auto text-left md:text-right">N° Documento Interno</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400">GD -</span>
                        <input type="text" value="2026-0899" readonly class="w-32 bg-slate-50 border border-slate-200 text-slate-800 text-base font-mono font-bold rounded-lg text-center py-1 focus:outline-none cursor-default shadow-inner">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Información Logística -->
            <div class="p-5 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">1. Datos Logísticos y Destino</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Fecha de Despacho <span class="text-red-500">*</span></label>
                        <input type="date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block py-1.5 px-2.5 text-xs transition-colors">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Liberación <span class="text-red-500">*</span></label>
                        <select required class="w-full bg-white border border-slate-300 text-slate-900 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block py-1.5 px-2.5 text-xs transition-colors">
                            <option value="liberada">Liberada / Aprobada</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="cuarentena">En Cuarentena</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Establecimiento Destino <span class="text-red-500">*</span></label>
                        <select required class="w-full bg-white border border-slate-300 text-slate-900 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block py-1.5 px-2.5 text-xs transition-colors">
                            <option value="" disabled selected>Seleccione establecimiento...</option>
                            <option value="cesfam_paul">CESFAM Paulina Avendaño</option>
                            <option value="cesfam_sv">CESFAM San Vicente</option>
                            <option value="cesfam_la">CESFAM Leocán Portus</option>
                            <option value="cecosf_8m">CECOSF 8 de Mayo</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dirección Exacta</label>
                        <input type="text" placeholder="Ej: Calle Los Robles 123..." class="w-full bg-white border border-slate-300 text-slate-900 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block py-1.5 px-2.5 text-xs transition-colors">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Condiciones de Transporte (Frío, Luz, etc.)</label>
                        <input type="text" placeholder="Ej: Mantener 2°C - 8°C." class="w-full bg-white border border-slate-300 text-slate-900 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block py-1.5 px-2.5 text-xs transition-colors">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: Detalle de Productos -->
            <div class="p-5">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">2. Detalle de Productos</h3>
                    <button type="button" class="px-3 py-1.5 bg-white text-blue-600 border border-blue-200 rounded-md text-xs font-bold hover:bg-blue-50 transition-colors flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Añadir Línea
                    </button>
                </div>
                
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-3 py-2 w-1/3">Producto (Fármaco/Insumo)</th>
                                <th class="px-3 py-2 w-28">Lote</th>
                                <th class="px-3 py-2 w-32">Vencimiento</th>
                                <th class="px-3 py-2 w-36">Programa</th>
                                <th class="px-3 py-2 w-24 text-center">Cantidad</th>
                                <th class="px-3 py-2 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Fila 1 (Llenada) -->
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-3 py-1.5">
                                    <div class="font-bold text-slate-800">Paracetamol 500mg</div>
                                    <div class="text-[10px] text-slate-500">Cód: PRD-001</div>
                                </td>
                                <td class="px-3 py-1.5">
                                    <!-- Inputs transparentes para simular Excel -->
                                    <input type="text" value="L-90210" class="w-full bg-transparent border border-transparent hover:border-slate-300 focus:border-blue-500 focus:bg-white rounded px-2 py-1 font-mono text-slate-700 transition-all focus:outline-none">
                                </td>
                                <td class="px-3 py-1.5">
                                    <input type="date" value="2025-10-15" class="w-full bg-transparent border border-transparent hover:border-slate-300 focus:border-blue-500 focus:bg-white rounded px-2 py-1 text-slate-700 transition-all focus:outline-none">
                                </td>
                                <td class="px-3 py-1.5">
                                    <select class="w-full bg-transparent border border-transparent hover:border-slate-300 focus:border-blue-500 focus:bg-white rounded px-1 py-1 text-slate-700 transition-all focus:outline-none">
                                        <option value="cardio">Cardiovascular</option>
                                        <option value="morbilidad" selected>Morbilidad</option>
                                    </select>
                                </td>
                                <td class="px-3 py-1.5">
                                    <input type="number" value="5000" min="1" class="w-full bg-transparent border border-transparent hover:border-slate-300 focus:border-blue-500 focus:bg-white rounded px-2 py-1 font-bold text-center text-slate-800 transition-all focus:outline-none">
                                </td>
                                <td class="px-3 py-1.5 text-center">
                                    <button type="button" class="p-1 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded transition-colors opacity-0 group-hover:opacity-100" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Fila 2 (Vacía para ingresar) -->
                            <tr class="bg-blue-50/10 hover:bg-slate-50/50 transition-colors group">
                                <td class="px-3 py-1.5">
                                    <input type="text" placeholder="Buscar producto..." class="w-full bg-white border border-slate-300 focus:border-blue-500 rounded px-2 py-1 text-slate-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-1.5">
                                    <input type="text" placeholder="Lote" class="w-full bg-white border border-slate-300 focus:border-blue-500 rounded px-2 py-1 font-mono text-slate-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-1.5">
                                    <input type="date" class="w-full bg-white border border-slate-300 focus:border-blue-500 rounded px-2 py-1 text-slate-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-1.5">
                                    <select class="w-full bg-white border border-slate-300 focus:border-blue-500 rounded px-1 py-1 text-slate-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="ira">IRA / ERA</option>
                                        <option value="morbilidad">Morbilidad</option>
                                    </select>
                                </td>
                                <td class="px-3 py-1.5">
                                    <input type="number" placeholder="0" min="1" class="w-full bg-white border border-slate-300 focus:border-blue-500 rounded px-2 py-1 font-bold text-center text-slate-800 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-1.5 text-center">
                                    <button type="button" class="p-1 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Eliminar fila">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botones de Acción (Footer) -->
            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                <button type="button" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-md text-xs font-bold hover:bg-slate-100 transition-colors text-center shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 text-white rounded-md text-xs font-bold hover:bg-slate-800 flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Confirmar e Imprimir
                </button>
            </div>
        </form>
    </div>
@endsection