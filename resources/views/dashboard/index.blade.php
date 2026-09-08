@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Cabecera del Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-500">Resumen general del sistema</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-50 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Escanear QR
            </button>
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva operación
            </button>
        </div>
    </div>

    <!-- 1. Tarjetas de Métricas -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Tarjeta 1 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-50 rounded-md text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Valor inventario</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">$ 11,557.86</h3>
            <p class="text-[10px] text-emerald-600 font-medium mt-1">+2.4% vs ayer</p>
        </div>
        <!-- Tarjeta 2 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-slate-50 rounded-md text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Órdenes hoy</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">156</h3>
            <p class="text-[10px] text-slate-400 mt-1">Total despachos</p>
        </div>
        <!-- Tarjeta 3 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-emerald-50 rounded-md text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Recepciones hoy</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">28</h3>
            <p class="text-[10px] text-slate-400 mt-1">Total ingresos</p>
        </div>
        <!-- Tarjeta 4 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm border-l-2 border-l-amber-500">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-amber-50 rounded-md text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Alertas activas</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">7</h3>
            <p class="text-[10px] text-amber-600 font-medium mt-1">Requieren atención</p>
        </div>
        <!-- Tarjeta 5 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-50 rounded-md text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Productos</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">1,256</h3>
            <p class="text-[10px] text-slate-400 mt-1">En catálogo</p>
        </div>
        <!-- Tarjeta 6 -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm border-l-2 border-l-red-500">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-red-50 rounded-md text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">Próximos a vencer</p>
            </div>
            <h3 class="text-lg font-bold text-slate-800">23</h3>
            <p class="text-[10px] text-red-500 font-medium mt-1">En 30 días</p>
        </div>
    </div>

    <!-- 2. Sección Principal: Accesos, Trazabilidad y Cuarentena -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Accesos Rápidos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4">Accesos Rápidos</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <span class="text-xs font-medium text-slate-700">Recepción</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                    <span class="text-xs font-medium text-slate-700">Despacho</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition-colors">
                    <svg class="w-6 h-6 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-xs font-medium text-slate-700">Cuarentena</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-emerald-50 hover:border-emerald-200 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span class="text-xs font-medium text-slate-700">Trazabilidad</span>
                </a>
            </div>
        </div>

        <!-- Trazabilidad Reciente -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-bold text-slate-800">Trazabilidad Reciente</h2>
                <a href="#" class="text-xs text-blue-600 hover:underline">Ver historial</a>
            </div>
            
            <div class="space-y-4">
                <!-- Item Trazabilidad -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-full mt-1.5"></div>
                        <div class="w-px h-full bg-slate-200 my-1"></div>
                    </div>
                    <div class="pb-2">
                        <p class="text-sm font-medium text-slate-800">Despacho a Clínica Santa María</p>
                        <p class="text-xs text-slate-500">Paracetamol 500mg - Lote: LOT-12345</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">16/05/2026 - 10:15 AM</p>
                    </div>
                </div>
                <!-- Item Trazabilidad -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full mt-1.5"></div>
                        <div class="w-px h-full bg-slate-200 my-1"></div>
                    </div>
                    <div class="pb-2">
                        <p class="text-sm font-medium text-slate-800">Recepción de Laboratorios Andinos</p>
                        <p class="text-xs text-slate-500">Amoxicilina 500mg - Lote: LOT-99887</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">15/05/2026 - 08:30 AM</p>
                    </div>
                </div>
                <!-- Item Trazabilidad -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-slate-300 rounded-full mt-1.5"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Almacenamiento Bodega Principal</p>
                        <p class="text-xs text-slate-500">Amoxicilina 500mg - Pasillo 3</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">15/05/2026 - 09:00 AM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Panel Inferior: Sistema QR y Lotes en Cuarentena -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Panel Sistema QR -->
        <div class="bg-[#0f172a] p-6 rounded-2xl shadow-md text-white flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                <h2 class="text-sm font-bold text-slate-300 mb-4">Sistema QR</h2>
                <!-- Simulador de código QR -->
                <div class="w-28 h-28 bg-white p-2 rounded-lg mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=LOT-12345" alt="QR Code" class="w-full h-full object-cover rounded-md opacity-90">
                </div>
                <button class="w-full py-1.5 bg-blue-600 hover:bg-blue-700 rounded-md text-xs font-medium transition-colors">
                    Imprimir etiqueta
                </button>
            </div>
            <div class="flex-1">
                <div class="border-b border-slate-700 pb-3 mb-3">
                    <h3 class="font-bold text-lg text-white">Paracetamol 500mg</h3>
                    <p class="text-xs text-slate-400">Lote: LOT-12345</p>
                </div>
                <div class="grid grid-cols-2 gap-y-3 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs">Vencimiento</p>
                        <p class="font-medium">30/06/2026</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Ubicación</p>
                        <p class="font-medium">B-02-01</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Stock actual</p>
                        <p class="font-medium text-blue-400">350 uds</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Estado</p>
                        <p class="font-medium text-emerald-400">Disponible</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lotes en Cuarentena -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-bold text-slate-800">Lotes en Cuarentena</h2>
                <a href="#" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="text-xs uppercase text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="pb-2 font-medium">Producto</th>
                            <th class="pb-2 font-medium">Lote</th>
                            <th class="pb-2 font-medium">Motivo</th>
                            <th class="pb-2 font-medium">Días</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Cefalexina 500mg</td>
                            <td class="py-3 text-xs">LOT-3333</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] rounded-full">Doc. pendiente</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">2</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Losartán 50mg</td>
                            <td class="py-3 text-xs">LOT-4444</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[10px] rounded-full">Calidad</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">5</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Azitromicina</td>
                            <td class="py-3 text-xs">LOT-5555</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] rounded-full">Análisis</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">4</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection