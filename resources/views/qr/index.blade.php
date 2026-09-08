@extends('layouts.app')
@section('title', 'Generador de QR - Droguería')

@section('content')

    {{-- 1. CABECERA Y ACCIONES --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Generador de QR - Medicamentos</h1>
            <p class="text-sm text-slate-500">Trazabilidad de lotes, fechas de caducidad y control de farmacovigilancia</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-50 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Importar Lotes (Excel)
            </button>
        </div>
    </div>

    {{-- 2. ÁREA PRINCIPAL: FORMULARIO Y VISTA PREVIA MEJORADA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Columna Izquierda: Formulario Farmacéutico -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-800 mb-5">Datos del Medicamento y Lote</h2>
            
            <form action="#" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Tipo de Empaque -->
                    <div class="md:col-span-2 relative">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nivel de Empaque</label>
                        <select class="w-full appearance-none py-2.5 pl-4 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            <option value="unidad">💊 Unitaria (Blíster / Frasco Individual)</option>
                            <option value="caja">📦 Caja Master / Estuche Comercial</option>
                            <option value="pallet">🚜 Pallet de Distribución</option>
                        </select>
                        <div class="pointer-events-none absolute bottom-0 right-0 top-[28px] flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Medicamento / SKU -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Medicamento (Principio Activo / SKU)</label>
                        <select class="w-full appearance-none py-2.5 pl-4 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            <option value="">Seleccione medicamento...</option>
                            <option value="MED-501">Paracetamol 500mg - Caja x 100 tab</option>
                            <option value="MED-502">Ibuprofeno 400mg - Caja x 30 tab</option>
                            <option value="MED-503">Amoxicilina 500mg - Frasco Suspensión</option>
                        </select>
                        <div class="pointer-events-none absolute bottom-0 right-0 top-[28px] flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Número de Lote -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Número de Lote</label>
                        <input type="text" placeholder="Ej: L-2026-F09" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>

                    <!-- Fecha de Caducidad -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha de Caducidad (Vencimiento)</label>
                        <input type="date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>

                    <!-- Registro Invima / Sanitario -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Registro Sanitario / Invima</label>
                        <input type="text" placeholder="Ej: INVIMA 2024M-0012345" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="button" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">Limpiar</button>
                    <button type="button" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Generar QR Trazable
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna Derecha: Vista Previa Etiqueta Farmacéutica Mejorada -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
            <h2 class="text-base font-bold text-slate-800 mb-5">Vista Previa Etiqueta</h2>
            
            <div class="flex-1 flex flex-col items-center justify-center p-4 bg-slate-100 rounded-xl border border-slate-200 border-dashed mb-5">
                <!-- Etiqueta con diseño térmico/profesional limpio -->
                <div class="bg-white p-4 rounded-xl shadow-md border border-slate-200 w-full max-w-[260px] text-left relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-emerald-500"></div>
                    
                    <div class="flex justify-between items-start mb-3 pt-1">
                        <div>
                            <h3 class="text-[11px] font-extrabold text-slate-800 tracking-tight">DROGUERÍA CENTRAL PHARMA</h3>
                            <p class="text-[9px] text-slate-400 font-medium">Control Logístico y Trazabilidad</p>
                        </div>
                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 font-mono text-[9px] font-bold rounded border border-blue-100">CAJA</span>
                    </div>

                    <!-- Código QR vectorizado realista -->
                    <div class="w-36 h-36 mx-auto bg-white border-2 border-slate-100 rounded-xl flex items-center justify-center p-2 mb-3 shadow-sm">
                        <svg class="w-full h-full text-slate-900" viewBox="0 0 33 33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <!-- Marcadores de posición (Esquinas) -->
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 3h9v9H3V3zm2 2v5h5V5H5zm1 1h3v3H6V6z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21 3h9v9h-9V3zm2 2v5h5V5h-5zm1 1h3v3h-3V6z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 21h9v9H3v-9zm2 2v5h5v-5H5zm1 1h3v3H6v-3z"/>
                            <!-- Patrones internos detallados -->
                            <path d="M14 3h2v2h-2V3zM18 3h2v2h-2V3zM14 7h2v2h-2V7zM16 5h2v2h-2V5zM18 7h2v2h-2V7zM14 9h2v2h-2V9zM18 9h2v2h-2V9zM20 11h2v2h-2v-2zM22 13h2v2h-2v-2zM20 15h2v2h-2v-2zM24 11h2v2h-2v-2zM26 13h2v2h-2v-2zM28 15h2v2h-2v-2zM28 11h2v2h-2v-2zM14 13h2v2h-2v-2zM16 11h2v2h-2v-2zM18 13h2v2h-2v-2zM14 17h2v2h-2v-2zM18 17h2v2h-2v-2zM16 15h2v2h-2v-2zM14 21h2v2h-2v-2zM16 23h2v2h-2v-2zM14 25h2v2h-2v-2zM18 21h2v2h-2v-2zM20 23h2v2h-2v-2zM18 25h2v2h-2v-2zM22 21h2v2h-2v-2zM26 21h2v2h-2v-2zM24 23h2v2h-2v-2zM28 23h2v2h-2v-2zM22 25h2v2h-2v-2zM26 25h2v2h-2v-2zM24 27h2v2h-2v-2zM28 27h2v2h-2v-2zM20 27h2v2h-2v-2zM22 29h2v2h-2v-2zM26 29h2v2h-2v-2zM3 15h2v2H3v-2zM5 13h2v2H5v-2zM7 15h2v2H7v-2zM9 13h2v2H9v-2zM11 15h2v2h-2v-2zM3 17h2v2H3v-2zM7 17h2v2H7v-2zM11 17h2v2h-2v-2zM11 7h2v2h-2V7zM11 3h2v2h-2V3zM11 27h2v2h-2v-2zM11 23h2v2h-2v-2zM16 19h14v2H16v-2zM3 19h10v2H3v-2zM14 14h2v2h-2v-2z"/>
                        </svg>
                    </div>

                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-1">
                        <p class="text-[11px] font-bold text-slate-900 truncate">Paracetamol 500mg (100 tab)</p>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-slate-500">Lote: <strong class="text-slate-700">L-2026-F09</strong></span>
                            <span class="text-red-600 font-semibold bg-red-50 px-1 rounded border border-red-100">Exp: 12/28</span>
                        </div>
                        <p class="text-[9px] text-slate-400 font-mono truncate pt-0.5 border-t border-slate-200/60 mt-1">INVIMA: 2024M-0012345</p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="grid grid-cols-2 gap-2">
                <button class="w-full py-2 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir
                </button>
                <button class="w-full py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Descargar
                </button>
            </div>
        </div>
    </div>

    {{-- 3. HISTORIAL DE LOTES GENERADOS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">Lotes y Códigos QR Recientes</h2>
            <button class="text-xs font-medium text-blue-600 hover:text-blue-700">Ver registro completo &rarr;</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 font-medium">Hash / Código</th>
                        <th class="px-5 py-3 font-medium">Medicamento</th>
                        <th class="px-5 py-3 font-medium">Lote / Invima</th>
                        <th class="px-5 py-3 font-medium">Vencimiento</th>
                        <th class="px-5 py-3 font-medium text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">QR-MED-901</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800 text-xs">Paracetamol 500mg</p>
                            <p class="text-[10px] text-slate-400">Caja x 100 tab</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-700">L-2026-F09</p>
                            <p class="text-[10px] text-slate-400">Invima: 2024M-001</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">12/12/2028</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button title="Reimprimir Etiqueta" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">QR-MED-902</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800 text-xs">Amoxicilina 500mg</p>
                            <p class="text-[10px] text-slate-400">Frasco Suspensión</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-700">L-AMX-442</p>
                            <p class="text-[10px] text-slate-400">Invima: 2023M-008</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-full">05/08/2026</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button title="Reimprimir Etiqueta" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

@endsection