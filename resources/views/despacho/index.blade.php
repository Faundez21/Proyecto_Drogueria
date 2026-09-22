@extends('layouts.app')

@section('title', 'Despacho')
@section('header', 'Despacho de Pedidos')

@section('content')
    <!-- Notificaciones -->
    @if(session('error'))
        <div class="mx-4 sm:mx-6 mt-4 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center gap-3">
            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('success'))
        <div class="mx-4 sm:mx-6 mt-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="w-full p-4 sm:p-6 space-y-6">

        <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500">
                    <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-400 font-semibold">03</span>
                    <span>Salida</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="font-medium text-slate-800">Despacho</span>
                </div>

                <button type="button" onclick="openBandeja()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm hover:shadow transition-all duration-200 w-full sm:w-auto cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Bandeja de Despachos e Historial
                </button>
            </div>
        </div>

        <!-- KPIs SUPERIORES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Despachos Hoy</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ collect($dispatches)->where('fecha_despacho', date('Y-m-d'))->count() }}</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Histórico Total</p>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ collect($dispatches)->count() }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
        </div>

        <!-- Form despacho  -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-[15px] font-bold text-slate-800 tracking-tight">Emisión de Guía de Despacho</h2>
            </div>

            <form action="{{ route('despacho.store') }}" method="POST" id="despachoForm">
                @csrf
                
                <!-- DATOS DEL REMITENTE -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-100">
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Datos del Remitente</h4>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl shadow-sm">
                            <p class="text-[14px] font-extrabold text-slate-800">I. Municipalidad de Talcahuano</p>
                            <p class="text-[12px] font-bold text-teal-700">Droguería Comunal Central</p>
                            <p class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Blanco Encalada 450, Talcahuano
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center items-end">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 w-full md:w-auto text-left md:text-right">N° Correlativo Automático</h4>
                        <input type="text" name="correlativo_despacho" value="{{ $correlativo }}" readonly class="w-full md:w-48 bg-slate-100 border border-slate-300 text-slate-800 text-lg font-mono font-extrabold rounded-xl text-center py-2 focus:outline-none cursor-default shadow-inner">
                    </div>
                </div>

                <!-- DESTINO Y LOGÍSTICA -->
                <div class="p-6 bg-white border-b border-slate-100">
                    <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide mb-4">1. Destino y Logística</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                        <div class="md:col-span-1">
                            <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Salida <span class="text-rose-500">*</span></label>
                            <input type="date" name="fecha_despacho" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Liberación <span class="text-rose-500">*</span></label>
                            <select name="estado_liberacion" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                                <option value="Liberada">Liberada / Aprobada</option>
                                <option value="Pendiente">Pendiente de V°B°</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Establecimiento Destino <span class="text-rose-500">*</span></label>
                            <select name="establecimiento_destino" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                                <option value="" disabled selected>Seleccione el CESFAM o CECOSF...</option>
                                <option value="CESFAM Paulina Avendaño">CESFAM Paulina Avendaño</option>
                                <option value="CESFAM San Vicente">CESFAM San Vicente</option>
                                <option value="CESFAM Leocán Portus">CESFAM Leocán Portus</option>
                                <option value="CECOSF 8 de Mayo">CECOSF 8 de Mayo</option>
                                <option value="CECOSF Los Lobos">CECOSF Los Lobos</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Dirección Destino</label>
                            <input type="text" name="direccion_destino" placeholder="Automático o ingresar manual..." class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Condiciones Transporte</label>
                            <input type="text" name="condiciones_transporte" placeholder="Ej: Cadena de Frío (2° a 8°C)" class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>
                </div>

                <!-- DETALLE DE PRODUCTOS -->
                <div class="p-6 bg-slate-50/30">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                        <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide">2. Detalle de Extracción (Lotes en Stock)</h3>
                        
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="openScanner()" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-[12px] font-bold hover:bg-teal-700 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2m4 0h2m4 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-4 0h-2m-4 0H5a2 2 0 01-2-2v-2m4-10h2v2H7V7zm8 0h2v2h-2V7zM7 15h2v2H7v-2zm8-2h2v4h-2v-4zm-4-2h2v2h-2v-2z"></path></svg>
                                Escanear Etiqueta QR
                            </button>
                            <button type="button" onclick="addRow()" class="px-4 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg text-[12px] font-bold hover:bg-blue-50 transition-colors flex items-center gap-2 shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Añadir Fila Manual
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm bg-white">
                        <table class="w-full text-left text-[13px] whitespace-nowrap" id="productsTable">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[10px]">
                                <tr>
                                    <th class="px-4 py-3.5 w-[35%]">Seleccionar Producto en Stock</th>
                                    <th class="px-4 py-3.5 w-[15%]">Lote Extraído</th>
                                    <th class="px-4 py-3.5 w-[15%]">Vencimiento (FEFO)</th>
                                    <th class="px-4 py-3.5 w-[15%]">Programa</th>
                                    <th class="px-4 py-3.5 w-[15%] text-center">Cantidad Rebajar</th>
                                    <th class="px-4 py-3.5 w-[5%] text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="productsBody">
                                <!-- Filas JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-5 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors text-center shadow-sm cursor-pointer">
                        Limpiar Formulario
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 text-white rounded-xl text-[13px] font-bold hover:bg-slate-900 flex items-center justify-center gap-2 transition-all shadow-md hover:-translate-y-0.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Confirmar Despacho y Descontar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- TEMPLATE OCULTO PARA NUEVAS FILAS (Garantiza Funcionar) -->
    <!-- ======================================================= -->
    <template id="rowTemplate">
        <tr class="bg-white hover:bg-slate-50/80 transition-colors group">
            <td class="px-3 py-2 border-r border-slate-100">
                <select name="productos[]" required onchange="llenarDatosFila(this)" class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[12px] font-bold text-slate-800 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="" disabled selected>Seleccione producto del inventario...</option>
                    @foreach($inventarioDisponible as $inv)
                        <option value="{{ $inv->producto }}" 
                                data-lote="{{ $inv->lote ?? 'S/L' }}" 
                                data-vence="{{ $inv->fecha_vencimiento ?? '' }}" 
                                data-stock="{{ $inv->cantidad ?? 0 }}">
                            {{ $inv->producto }} - Lote: {{ $inv->lote ?? 'S/L' }} (Disp: {{ $inv->cantidad ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-3 py-2 border-r border-slate-100">
                <input type="text" name="lotes[]" readonly placeholder="Auto..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] font-mono font-bold text-slate-500 outline-none shadow-inner cursor-not-allowed">
            </td>
            <td class="px-3 py-2 border-r border-slate-100 relative">
                <input type="date" name="vencimientos[]" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] text-slate-500 outline-none shadow-inner cursor-not-allowed">
                <div class="fefo-container"></div>
            </td>
            <td class="px-3 py-2 border-r border-slate-100">
                <select name="programas[]" required class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[12px] font-medium text-slate-700 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="Morbilidad General" selected>Morbilidad General</option>
                    <option value="Cardiovascular">Cardiovascular</option>
                    <option value="IRA/ERA">Respiratorio (IRA/ERA)</option>
                </select>
            </td>
            <td class="px-3 py-2 border-r border-slate-100 relative pb-6">
                <input type="number" name="cantidades[]" placeholder="0" min="1" oninput="validarStock(this)" required class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[14px] font-black text-center text-slate-800 outline-none shadow-sm transition-colors">
                <div class="stock-container absolute bottom-1 left-0 w-full text-center text-[10px] font-extrabold text-blue-600 hidden">Máx: <span class="stock-max-badge">0</span></div>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 bg-white text-slate-400 hover:text-white hover:bg-rose-600 rounded-lg transition-colors cursor-pointer shadow-sm border border-slate-200" title="Quitar Fila">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        </tr>
    </template>

    <!-- MODAL 1: BANDEJA DE DESPACHOS CON BOTÓN PDF -->
    <div id="BandejaModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-5xl flex flex-col overflow-hidden">
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Bandeja Histórica de Despachos</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">Trazabilidad de guías emitidas y stock rebajado</p>
                </div>
                <button type="button" onclick="closeBandeja()" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-x-auto max-h-[65vh] bg-slate-50">
                <table class="w-full text-left text-[13px] whitespace-nowrap">
                    <thead class="bg-white text-slate-500 uppercase text-[11px] font-extrabold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                        <tr>
                            <th class="px-6 py-4 w-12">Detalle</th>
                            <th class="px-6 py-4">Correlativo GD</th>
                            <th class="px-6 py-4">Fecha Emisión</th>
                            <th class="px-6 py-4">Destino</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Documento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dispatches as $despacho)
                            @php $dKey = 'desp_' . $despacho->id; @endphp
                            <tr class="bg-white hover:bg-slate-50 transition-colors cursor-pointer group" onclick="toggleItems('{{ $dKey }}')">
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200" id="icon-{{ $dKey }}">
                                        <svg class="w-4 h-4 transform transition-transform duration-300" id="arrow-{{ $dKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4 font-mono font-extrabold text-slate-800 text-[14px]">
                                    {{ $despacho->correlativo }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    {{ \Carbon\Carbon::parse($despacho->fecha_despacho)->format('d-m-Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $despacho->establecimiento_destino }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($despacho->estado_liberacion === 'Liberada' || $despacho->estado_liberacion === 'liberada')
                                        <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase">Aprobada</span>
                                    @else
                                        <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase">{{ $despacho->estado_liberacion }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation();">
                                    <a href="{{ route('despacho.pdf', $despacho->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 rounded-lg text-[11px] font-bold transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        VER PDF
                                    </a>
                                </td>
                            </tr>
                            <tr id="items-{{ $dKey }}" class="hidden bg-slate-100/50 shadow-inner">
                                <td colspan="6" class="p-0 border-b border-slate-300">
                                    <div class="py-4 pr-6 pl-14">
                                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                            <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    Productos Rebajados en esta Guía
                                                </span>
                                            </div>
                                            <table class="w-full text-left text-[12px] whitespace-nowrap">
                                                <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200 text-[10px]">
                                                    <tr>
                                                        <th class="px-4 py-2.5">Producto</th>
                                                        <th class="px-4 py-2.5">Lote</th>
                                                        <th class="px-4 py-2.5">Vencimiento</th>
                                                        <th class="px-4 py-2.5 text-center">Cant. Extraída</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                                    @foreach($despacho->items as $item)
                                                        <tr class="hover:bg-slate-50">
                                                            <td class="px-4 py-2 font-bold">{{ $item->producto }}</td>
                                                            <td class="px-4 py-2 font-mono font-bold">{{ $item->lote }}</td>
                                                            <td class="px-4 py-2">{{ $item->fecha_vencimiento ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</td>
                                                            <td class="px-4 py-2 text-center font-extrabold text-blue-700">{{ number_format($item->cantidad, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No hay guías de despacho emitidas en el sistema.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeBandeja()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cerrar Historial</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ESCÁNER QR -->
    <div id="scannerModal" class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Escanear Etiqueta QR</h2>
                    <p class="text-[11px] text-slate-300 mt-0.5 uppercase tracking-wider font-semibold">Validación automática de Stock</p>
                </div>
                <button type="button" onclick="closeScanner()" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50 flex flex-col items-center">
                <div class="bg-white border-[3px] border-slate-200 border-dashed rounded-2xl p-2 w-full max-w-sm flex flex-col items-center">
                    <div id="reader" class="w-full rounded-xl overflow-hidden shadow-inner bg-black"></div>
                </div>
                <p class="text-[12px] font-medium text-slate-500 mt-5 text-center leading-relaxed max-w-sm">
                    Apunte la cámara a la etiqueta del producto. El sistema leerá el Lote, seleccionará el producto en la tabla y establecerá el tope máximo de extracción.
                </p>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeScanner()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">Cancelar Escaneo</button>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        // Modal Bandeja Histórica
        function openBandeja() { document.getElementById('BandejaModal').classList.remove('hidden'); }
        function closeBandeja() { document.getElementById('BandejaModal').classList.add('hidden'); }

        function toggleItems(key) {
            const row = document.getElementById('items-' + key);
            const arrow = document.getElementById('arrow-' + key);
            const iconContainer = document.getElementById('icon-' + key);
            if (row) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    if(arrow) arrow.classList.add('rotate-90');
                    if(iconContainer) {
                        iconContainer.classList.remove('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'border-blue-600');
                    }
                } else {
                    row.classList.add('hidden');
                    if(arrow) arrow.classList.remove('rotate-90');
                    if(iconContainer) {
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'border-blue-600');
                    }
                }
            }
        }

        // --- LÓGICA DE TABLA ---
        const tbody = document.getElementById('productsBody');

        // Renderizar la primera fila usando el Template al cargar
        document.addEventListener("DOMContentLoaded", () => { 
            addRow(); 
        });

        function addNewRowFromTemplate() {
            const template = document.getElementById('rowTemplate');
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
            return tbody.lastElementChild;
        }

        function addRow(data = null) {
            let tr = addNewRowFromTemplate();
            if (data) {
                let select = tr.querySelector('select[name="productos[]"]');
                let options = Array.from(select.options);
                let targetOption = options.find(opt => opt.value === data.producto && opt.getAttribute('data-lote') === data.lote);
                
                if (targetOption) {
                    select.value = targetOption.value;
                    llenarDatosFila(select);
                }
            }
            return tr;
        }

        function llenarDatosFila(selectElement) {
            let option = selectElement.options[selectElement.selectedIndex];
            if (!option || option.disabled) return; 

            let tr = selectElement.closest('tr');
            let loteInput = tr.querySelector('input[name="lotes[]"]');
            let venceInput = tr.querySelector('input[name="vencimientos[]"]');
            let cantInput = tr.querySelector('input[name="cantidades[]"]');
            
            let stockBadge = tr.querySelector('.stock-max-badge');
            let stockContainer = tr.querySelector('.stock-container');
            let fefoContainer = tr.querySelector('.fefo-container');

            loteInput.value = option.getAttribute('data-lote');
            venceInput.value = option.getAttribute('data-vence');
            
            let stock = parseInt(option.getAttribute('data-stock'));
            cantInput.setAttribute('max', stock);
            stockBadge.textContent = stock;
            stockContainer.classList.remove('hidden');
            
            // Lógica FEFO
            fefoContainer.innerHTML = '';
            venceInput.classList.remove('text-rose-700', 'font-extrabold', 'bg-rose-50');
            
            if(venceInput.value) {
                try {
                    let fVence = new Date(venceInput.value);
                    let hoy = new Date();
                    let mesesRestantes = (fVence.getFullYear() - hoy.getFullYear()) * 12 + (fVence.getMonth() - hoy.getMonth());
                    
                    if(mesesRestantes <= 6) {
                        venceInput.classList.add('text-rose-700', 'font-extrabold', 'bg-rose-50');
                        fefoContainer.innerHTML = '<span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] bg-rose-500 text-white font-bold px-1.5 py-0.5 rounded shadow-sm">PRONTO A VENCER</span>';
                    }
                } catch(e) {}
            }
            
            cantInput.focus();
        }

        function removeRow(button) {
            const tr = button.closest('tr');
            if (tbody.children.length > 1) {
                tr.remove();
            } else {
                let select = tr.querySelector('select');
                select.selectedIndex = 0;
                tr.querySelectorAll('input').forEach(i => i.value = '');
                tr.querySelector('.stock-container').classList.add('hidden');
                tr.querySelector('.fefo-container').innerHTML = '';
                tr.querySelector('input[name="cantidades[]"]').removeAttribute('max');
                tr.querySelector('input[name="vencimientos[]"]').classList.remove('text-rose-700', 'font-extrabold', 'bg-rose-50');
            }
        }

        function validarStock(input) {
            let maxVal = parseInt(input.getAttribute('max'));
            let currentVal = parseInt(input.value);
            
            if(!isNaN(maxVal) && !isNaN(currentVal)) {
                if(currentVal > maxVal) {
                    input.value = maxVal; 
                    input.classList.add('border-rose-500', 'bg-rose-50', 'text-rose-700', 'ring-2', 'ring-rose-500/20');
                    setTimeout(() => {
                        input.classList.remove('border-rose-500', 'bg-rose-50', 'text-rose-700', 'ring-2', 'ring-rose-500/20');
                    }, 600);
                }
            }
        }

        // --- Lógica Escáner QR ---
        let html5QrcodeScanner = null;

        function openScanner() {
            // Verificar si estamos en entorno seguro o localhost
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                alert("Advertencia: Para usar la cámara necesitas acceder mediante 'localhost', '127.0.0.1' o una conexión segura HTTPS.");
            }

            document.getElementById('scannerModal').classList.remove('hidden');
            html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1.0 }, false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function closeScanner() {
            if(html5QrcodeScanner) { html5QrcodeScanner.clear().catch(e => console.error(e)); html5QrcodeScanner = null; }
            document.getElementById('scannerModal').classList.add('hidden');
        }

        function onScanSuccess(decodedText) {
            closeScanner();
            
            let parsedData = {};
            let chunks = decodedText.split('|');
            chunks.forEach(chunk => {
                let [key, val] = chunk.split(':');
                if(key && val) parsedData[key.trim()] = val.trim();
            });

            if(!parsedData.PROD || !parsedData.STOCK) {
                alert("QR no compatible con el sistema.");
                return;
            }

            let rowData = {
                producto: parsedData.PROD.trim(),
                lote: parsedData.LOTE ? parsedData.LOTE.trim() : 'S/L',
                vencimiento: parsedData.VENCE ? parsedData.VENCE.trim() : 'N/A',
                stock: parseInt(parsedData.STOCK)
            };

            let emptySelect = Array.from(tbody.querySelectorAll('select[name="productos[]"]')).find(sel => sel.value === '');
            let tr;
            
            if(emptySelect) {
                tr = emptySelect.closest('tr');
            } else {
                tr = addRow();
            }

            let select = tr.querySelector('select[name="productos[]"]');
            let options = Array.from(select.options);
            let targetOption = options.find(opt => opt.value === rowData.producto && opt.getAttribute('data-lote') === rowData.lote);
            
            if (targetOption) {
                select.value = targetOption.value;
                llenarDatosFila(select);
                tr.querySelector('input[name="cantidades[]"]').focus();
            } else {
                alert("El producto o lote escaneado ya no tiene stock disponible en la bodega.");
                if (!emptySelect && tbody.children.length > 1) tr.remove(); 
            }
        }

        function onScanFailure(error) {}
    </script>
@endsection