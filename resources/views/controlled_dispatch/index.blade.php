@extends('layouts.app')
@section('title', 'Controlados')
@section('subtitle', 'Directorio y gestión de guías de despacho de fármacos controlados.')

@section('actions')
    <button type="button" onclick="openBandeja()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
            </path>
        </svg>
        Bandeja de Controlados
    </button>
@endsection

@section('content')
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">03</span>
        <span>Salida</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Fármacos Controlados</span>
    </div>

    <!-- notificaciones de alerta -->
    @if (session('error'))
        <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center gap-3">
            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @php
        $totalActivasCtrl = collect($solicitudes)->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count();
        $totalMiasCtrl = isset($misSolicitudesAsignadas) ? count($misSolicitudesAsignadas) : 0;
    @endphp
    <!-- indicadores de rendimiento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Despachos Hoy</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">
                    {{ collect($dispatches)->where('fecha_despacho', date('Y-m-d'))->count() }}</h3>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Solicitudes por Despachar</p>
                <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $totalActivasCtrl }}</h3>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Histórico Total</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ collect($dispatches)->count() }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
    </div>
    <!-- formulario principal de despacho -->
    <div class="bg-white rounded-2xl border border-rose-200 shadow-sm overflow-hidden flex flex-col">

        <!-- cabecera de la tarjeta del formulario -->
        <div class="px-6 py-5 border-b border-rose-100 bg-rose-50 flex items-center gap-4">
            <div class="w-10 h-10 bg-rose-600/10 text-rose-600 rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-[16px] font-bold text-slate-800 tracking-tight">Despacho de Fármacos Controlados</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Acceso restringido unicamente a estupefacientes y talonarios de
                    recetas cheque. Solo solicitudes de CESFAM.</p>
            </div>
            <button type="button" onclick="openSolicitudesControlado()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2zM13 3v4h4"></path>
                </svg>
                Ver Solicitudes (Estupefacientes)
                @if ($totalActivasCtrl > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-white/20 text-white">
                        {{ $totalActivasCtrl }}
                    </span>
                @endif
                @if ($totalMiasCtrl > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ $totalMiasCtrl }} asignada(s) a ti
                    </span>
                @endif
            </button>
        </div>

        <form action="{{ route('controlled_dispatch.store') }}" method="POST" id="despachoControladoForm">
            @csrf
            <input type="hidden" name="establishment_request_id" id="establishment_request_id" value="">

            <!-- Banner informativo cuando se carga una solicitud -->
            <div id="banner_solicitud_activa" class="hidden mx-6 mt-5 p-4 bg-rose-50 border border-rose-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        SOL
                    </div>
                    <div>
                        <p id="banner_solicitud_titulo" class="text-[13px] font-extrabold text-rose-900"></p>
                        <p id="banner_solicitud_subtitulo" class="text-[11.5px] text-rose-700"></p>
                    </div>
                </div>
                <button type="button" onclick="desvincularSolicitudActivaControlado()"
                    class="text-[11.5px] font-bold text-rose-800 hover:text-rose-900 bg-white px-3 py-1.5 rounded-lg border border-rose-200 transition-colors cursor-pointer shrink-0">
                    Desvincular solicitud
                </button>
            </div>

            <!-- seccion de datos del remitente y correlativo -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-100">
                <div>
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Datos del Remitente</h4>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl shadow-sm">
                        <p class="text-[14px] font-extrabold text-slate-800">I. Municipalidad de Talcahuano</p>
                        <p class="text-[12px] font-bold text-teal-700">Droguería Comunal Central</p>
                        <p class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Blanco Encalada 450, Talcahuano
                        </p>
                    </div>
                </div>

                <div class="flex flex-col justify-center items-end">
                    <h4
                        class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 w-full md:w-auto text-left md:text-right">
                        N° Correlativo Automatico</h4>
                    <input type="text" name="correlativo_despacho" value="{{ $correlativo }}" readonly
                        class="w-full md:w-48 bg-slate-100 border border-slate-300 text-rose-700 text-lg font-mono font-extrabold rounded-xl text-center py-2 focus:outline-none cursor-default shadow-inner">
                </div>
            </div>

            <!-- seccion de destino y logistica con clasificacion -->
            <div class="p-6 bg-white border-b border-slate-100">
                <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide mb-4">1. Tipo, Destino y
                    Logistica</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                    <!-- clasificacion interno/externo -->
                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Clasificación del Despacho <span
                                class="text-rose-500">*</span></label>
                        <select name="clasificacion" id="clasificacion_select" onchange="toggleClasificacion()" required
                            class="w-full bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione la clasificación...</option>
                            <option value="Interno">Interno (Nivel Comunal / CESFAM)</option>
                            <option value="Externo">Externo (Traspasos, Préstamos, Donaciones, etc.)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Salida <span
                                class="text-rose-500">*</span></label>
                        <input type="date" name="fecha_despacho" value="{{ date('Y-m-d') }}" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Liberación <span
                                class="text-rose-500">*</span></label>
                        <select name="estado_liberacion" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="Liberada">Liberada / Aprobada</option>
                            <option value="Pendiente">Pendiente de V°B°</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino interno -->
                    <div id="div_interno" class="md:col-span-2 hidden">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Establecimiento Destino (Interno)
                            <span class="text-rose-500">*</span></label>
                        <select id="establecimiento_interno" onchange="actualizarDireccionInterna()"
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione el Establecimiento...</option>
                            <option value="CESFAM LOS CERROS"
                                data-direccion="Calle El Galgo, esquina Calle Coliumo, Villa Badaran, Talcahuano">CESFAM
                                LOS CERROS</option>
                            <option value="CESFAM PAULINA AVENDAÑO PEREDA"
                                data-direccion="Calle Carlos Dittborn 4100, Villa San Martín, Talcahuano">CESFAM PAULINA
                                AVENDAÑO PEREDA</option>
                            <option value="CESFAM SAN VICENTE" data-direccion="Calle Brasil 360, San Vicente, Talcahuano">
                                CESFAM SAN VICENTE</option>
                            <option value="CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN"
                                data-direccion="Calle Manuel Barros Borgoño 2645, Las Salinas, Talcahuano">CESFAM ALCALDE
                                LEOCÁN PORTUS GOVINDEN</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino externo -->
                    <div id="div_externo" class="md:col-span-2 hidden">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Tipo y Destino Externo <span
                                class="text-rose-500">*</span></label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="tipo_externo"
                                class="w-full sm:w-2/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                                <option value="" disabled selected>Tipo...</option>
                                <option value="Traspaso">Traspaso</option>
                                <option value="Prestamo">Préstamo</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <input type="text" id="establecimiento_externo"
                                placeholder="Nombre institución/destino..."
                                class="w-full sm:w-3/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Dirección Destino</label>
                        <input type="text" name="direccion_destino" id="direccion_destino"
                            placeholder="Esperando clasificación..." readonly
                            class="w-full bg-slate-50 border border-slate-300 text-slate-500 font-medium rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-inner transition-all">
                    </div>

                    <!-- campo especial para recetas cheque -->
                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-rose-700 mb-1.5">Numeros de Serie - Recetas Cheque
                            (Opcional)</label>
                        <input type="text" name="series_recetas" placeholder="Ej: Serie 0001 al 0050"
                            class="w-full bg-rose-50 border border-rose-200 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Condiciones Transporte y
                            Observaciones</label>
                        <input type="text" name="condiciones_transporte" placeholder="Ej: Transporte con custodia."
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>
                </div>
            </div>

            <!-- seccion de tabla de productos y stock -->
            <div class="p-6 bg-rose-50/20 border-b border-rose-100/60">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <div>
                        <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide flex items-center gap-2">
                            2. Detalle de Extracción Controlada
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 lowercase">receta / libro oficial</span>
                        </h3>
                        <p class="text-[11.5px] text-slate-500 mt-0.5">Fármacos controlados, psicotrópicos y estupefacientes bajo custodia y registro reglamentario.</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="openScanner()"
                            class="px-3.5 py-2 text-white bg-rose-600 hover:bg-rose-700 rounded-xl text-[12px] font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer"
                            style="background-color: #e11d48 !important; color: #ffffff !important;">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <span>Escanear QR</span>
                        </button>
                        <button type="button" onclick="addRow()"
                            class="px-3.5 py-2 bg-white text-slate-700 hover:text-rose-700 border border-slate-300 hover:border-rose-400 rounded-xl text-[12px] font-bold hover:bg-rose-50/40 transition-colors flex items-center gap-2 shadow-xs cursor-pointer">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Añadir Fila
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-xs bg-white">
                    <table class="w-full text-left text-[13px] whitespace-nowrap" id="productsTable">
                        <thead
                            class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-extrabold text-[10.5px]">
                            <tr>
                                <th class="px-4 py-3.5 w-[36%]">Fármaco Controlado en Stock</th>
                                <th class="px-4 py-3.5 w-[14%]">Lote</th>
                                <th class="px-4 py-3.5 w-[15%]">Vencimiento (FEFO)</th>
                                <th class="px-4 py-3.5 w-[15%]">Programa</th>
                                <th class="px-4 py-3.5 w-[15%] text-center">Cantidad a Despachar</th>
                                <th class="px-3 py-3.5 w-[5%] text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="productsBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- seccion de firmas digitales -->
            <div class="p-6 bg-slate-50/60">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                    <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">3. Firmas de Respaldo</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- firma despacha (bodega central) -->
                    <div class="flex flex-col items-center">
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma de quien despacha (Q.F. / Encargado)</p>
                        <input type="hidden" name="firma_despacha" id="firma_despacha_input">
                        <div
                            class="border-2 border-dashed border-slate-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_despacha" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>
                        <button type="button" onclick="signatureDespacha.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar
                            Firma</button>
                    </div>

                    <!-- firma solicita/recibe (establecimiento) -->
                    <div class="flex flex-col items-center">
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma de quien recibe (Establecimiento)</p>
                        <input type="hidden" name="firma_recibe" id="firma_recibe_input">
                        <div
                            class="border-2 border-dashed border-rose-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_recibe" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>
                        <button type="button" onclick="signatureRecibe.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar
                            Firma</button>
                    </div>
                </div>
            </div>

            <!-- botones inferiores del formulario -->
            <div
                class="px-6 py-5 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3 rounded-b-2xl">
                <button type="button" id="btn_limpiar_form"
                    class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors text-center shadow-sm cursor-pointer">
                    Limpiar Formulario
                </button>
                <button type="submit" name="action" value="save_and_download"
                    class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[13px] font-bold hover:bg-blue-700 flex items-center justify-center gap-2 transition-all shadow-md hover:-translate-y-0.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Guardar y Descargar PDF
                </button>
                <button type="submit" name="action" value="save_only"
                    class="w-full sm:w-auto px-6 py-2.5 bg-rose-600 text-white rounded-xl text-[13px] font-bold hover:bg-rose-700 flex items-center justify-center gap-2 transition-all shadow-md hover:-translate-y-0.5 cursor-pointer">
                    <svg class="w-4 h-4 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirmar Despacho Controlado
                </button>
            </div>
        </form>
    </div>

    <!-- plantilla oculta para generar nuevas filas de productos -->
    <template id="rowTemplate">
        <tr class="bg-white hover:bg-slate-50/80 transition-colors group">
            <td data-label="Producto" class="col-producto px-3.5 py-2.5 border-r border-slate-100 align-middle">
                <select name="productos[]" required onchange="llenarDatosFila(this)"
                    class="w-full bg-white border border-slate-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/10 rounded-lg px-2.5 py-2 text-[12.5px] font-bold text-slate-800 outline-none shadow-xs cursor-pointer transition-colors">
                    <option value="" disabled selected>Seleccione estupefaciente...</option>
                    @foreach ($inventarioDisponible as $inv)
                        <option value="{{ $inv->producto }}"
                            title="{{ $inv->producto }} - Lote: {{ $inv->lote ?? 'S/L' }} (Disp: {{ $inv->cantidad ?? 0 }})"
                            data-lote="{{ $inv->lote ?? 'S/L' }}"
                            data-vence="{{ $inv->fecha_vencimiento ?? '' }}" data-stock="{{ $inv->cantidad ?? 0 }}"
                            data-programa="{{ $inv->programa ?? '' }}">
                            {{ $inv->producto }} - Lote: {{ $inv->lote ?? 'S/L' }} (Disp: {{ $inv->cantidad ?? 0 }})
                        </option>
                    @endforeach
                </select>
                <div class="producto-nombre-movil"></div>
            </td>
            <td data-label="Lote" class="col-lote px-3.5 py-2.5 border-r border-slate-100 align-middle">
                <input type="text" name="lotes[]" readonly placeholder="Auto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] font-mono font-bold text-slate-600 outline-none shadow-inner cursor-not-allowed text-center">
            </td>
            <td data-label="Vencimiento (FEFO)" class="col-vence px-3.5 py-2.5 border-r border-slate-100 align-middle relative">
                <input type="date" name="vencimientos[]" readonly
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] text-slate-600 outline-none shadow-inner cursor-not-allowed">
                <div class="fefo-container"></div>
            </td>
            <td data-label="Programa" class="col-programa px-3.5 py-2.5 border-r border-slate-100 align-middle">
                <select name="programas[]" required
                    class="w-full bg-white border border-slate-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/10 rounded-lg px-2.5 py-2 text-[12px] font-semibold text-slate-700 outline-none shadow-xs cursor-pointer transition-colors">
                    @foreach ($programasLista ?? [] as $idxProg => $progOpt)
                        <option value="{{ $progOpt }}" {{ $idxProg === 0 ? 'selected' : '' }}>{{ $progOpt }}</option>
                    @endforeach
                </select>
            </td>
            <td data-label="Cantidad" class="col-cantidad px-3.5 py-2.5 border-r border-slate-100 align-middle">
                <div class="space-y-1">
                    <input type="number" name="cantidades[]" placeholder="0" min="1" oninput="validarStock(this)"
                        required
                        class="w-full bg-white border border-slate-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-lg px-2.5 py-2 text-[14px] font-black text-center text-slate-900 outline-none shadow-xs transition-colors">
                    <div
                        class="stock-container text-center text-[10px] font-bold text-rose-700 hidden leading-tight">
                        Disp: <span class="stock-max-badge font-mono font-black">0</span> uds.
                    </div>
                </div>
            </td>
            <td data-label="" class="col-quitar px-3.5 py-2.5 text-center align-middle">
                <button type="button" onclick="removeRow(this)"
                    class="p-2 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-lg transition-colors cursor-pointer shadow-xs"
                    title="Quitar Fila">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <!-- ventana modal para el historial de despachos controlados -->
    <div id="BandejaModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden my-auto">

            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Bandeja Histórica de Controlados</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">Trazabilidad estricta
                        de estupefacientes</p>
                </div>
                <button type="button" onclick="closeBandeja()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- cuadricula de filtros dinamicos -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Filtros de Búsqueda</span>
                    <button type="button" onclick="limpiarFiltrosBandeja()"
                        class="text-[11px] font-bold text-rose-600 hover:text-rose-700 transition-colors">Limpiar
                        Filtros</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Correlativo</label>
                        <input type="text" id="filtroCorrelativo" list="sugerencias-correlativos"
                            oninput="filtrarBandeja()" placeholder="Ej: 123"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm">
                        <datalist id="sugerencias-correlativos">
                            @foreach ($dispatches as $despacho)
                                <option value="{{ $despacho->correlativo }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fecha
                            Emisión</label>
                        <input type="date" id="filtroFecha" oninput="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm cursor-pointer">
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Clasificación</label>
                        <select id="filtroClasificacion" onchange="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todas</option>
                            <option value="interno">Interno</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Destino</label>
                        <input type="text" id="filtroDestino" oninput="filtrarBandeja()" placeholder="Ej: CESFAM"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estado</label>
                        <select id="filtroEstadoBandeja" onchange="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todos los estados</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[60vh] bg-white">
                <table class="w-full text-left text-[13px] whitespace-nowrap">
                    <thead
                        class="bg-white text-slate-500 uppercase text-[11px] font-extrabold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                        <tr>
                            <th class="px-6 py-4 w-12">Detalle</th>
                            <th class="px-6 py-4">Correlativo GD</th>
                            <th class="px-6 py-4">Fecha Emisión</th>
                            <th class="px-6 py-4">Clasificación</th>
                            <th class="px-6 py-4">Destino</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Documento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dispatches as $despacho)
                            @php $dKey = 'desp_' . $despacho->id; @endphp
                            <tr class="bg-white hover:bg-slate-50 transition-colors cursor-pointer group fila-despacho"
                                data-key="{{ $dKey }}" onclick="toggleItems('{{ $dKey }}')">
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-rose-100 group-hover:text-rose-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200"
                                        id="icon-{{ $dKey }}">
                                        <svg class="w-4 h-4 transform transition-transform duration-300"
                                            id="arrow-{{ $dKey }}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4 font-mono font-extrabold text-slate-800 text-[14px]">
                                    {{ $despacho->correlativo }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    {{ \Carbon\Carbon::parse($despacho->fecha_despacho)->format('d-m-Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-600">
                                    @if (strtolower($despacho->clasificacion) == 'interno')
                                        <span
                                            class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded text-[10px] uppercase">Interno</span>
                                    @else
                                        <span
                                            class="bg-purple-50 text-purple-700 border border-purple-200 px-2 py-1 rounded text-[10px] uppercase">{{ $despacho->clasificacion ?? 'Externo' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $despacho->establecimiento_destino }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if (strtolower($despacho->estado_liberacion) === 'liberada')
                                        <span
                                            class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase badge-estado">Aprobada</span>
                                    @else
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase badge-estado">{{ $despacho->estado_liberacion }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation();">
                                    <a href="{{ route('controlled_dispatch.pdf', $despacho->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 rounded-lg text-[11px] font-bold transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        VER PDF
                                    </a>
                                </td>
                            </tr>
                            <tr id="items-{{ $dKey }}" class="hidden bg-slate-100/50 shadow-inner">
                                <td colspan="7" class="p-0 border-b border-slate-300">
                                    <div class="py-4 pr-6 pl-14">

                                        @if ($despacho->series_recetas)
                                            <div
                                                class="mb-4 bg-yellow-50 p-3 border border-yellow-200 rounded-lg text-[12px]">
                                                <strong class="text-yellow-800">Series Recetas Cheque:</strong> <span
                                                    class="text-yellow-900">{{ $despacho->series_recetas }}</span>
                                            </div>
                                        @endif

                                        <div
                                            class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-4">
                                            <div
                                                class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                        </path>
                                                    </svg>
                                                    Productos Controlados Rebajados
                                                </span>
                                            </div>
                                            <table class="w-full text-left text-[12px] whitespace-nowrap">
                                                <thead
                                                    class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200 text-[10px]">
                                                    <tr>
                                                        <th class="px-4 py-2.5">Producto</th>
                                                        <th class="px-4 py-2.5">Lote</th>
                                                        <th class="px-4 py-2.5">Vencimiento</th>
                                                        <th class="px-4 py-2.5 text-center">Cant. Extraída</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                                    @foreach ($despacho->items as $item)
                                                        <tr class="hover:bg-slate-50">
                                                            <td class="px-4 py-2 font-bold">{{ $item->producto }}</td>
                                                            <td class="px-4 py-2 font-mono font-bold">{{ $item->lote }}
                                                            </td>
                                                            <td class="px-4 py-2">
                                                                {{ $item->fecha_vencimiento ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                            <td class="px-4 py-2 text-center font-extrabold text-blue-700">
                                                                {{ number_format($item->cantidad, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- visualizacion de firmas en el historial -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 text-center">
                                                <p
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                                                    Firma de quien despacha</p>
                                                @if (isset($despacho->firma_despacha) && $despacho->firma_despacha)
                                                    <img src="{{ $despacho->firma_despacha }}" class="mx-auto max-h-16"
                                                        alt="Firma Despacho">
                                                @else
                                                    <p class="text-xs text-slate-400 italic py-2">No registrada</p>
                                                @endif
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 text-center">
                                                <p
                                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                                                    Firma de quien solicita/recibe</p>
                                                @if (isset($despacho->firma_recibe) && $despacho->firma_recibe)
                                                    <img src="{{ $despacho->firma_recibe }}" class="mx-auto max-h-16"
                                                        alt="Firma Recepción">
                                                @else
                                                    <p class="text-xs text-slate-400 italic py-2">No registrada</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">No hay guías
                                    de despacho emitidas en la plataforma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeBandeja()"
                    class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cerrar
                    Historial</button>
            </div>
        </div>
    </div>
    <!-- modal del escaner QR del despacho controlado -->
    <div id="scannerModal" class="hidden fixed inset-0 z-[100] !m-0 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-2 sm:p-4 transition-all">
        <div class="flex min-h-full items-center justify-center">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
                <!-- cabecera del modal -->
                <div class="px-5 py-4 bg-slate-800 text-white flex justify-between items-center shrink-0 border-b border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/20 border border-rose-400/30 text-rose-300 flex items-center justify-center">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-white tracking-wide leading-tight">
                                Escanear Etiqueta QR
                            </h2>
                            <p class="text-[11px] text-rose-300 font-medium">Validación automática de Stock</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeScanner()" class="text-slate-400 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- visor de la camara -->
                <div class="p-4 sm:p-5 flex flex-col items-center bg-slate-50/70 overflow-y-auto flex-1">
                    <div class="relative w-full max-w-[300px] sm:max-w-[320px] aspect-square rounded-2xl overflow-hidden bg-black border-2 border-slate-300 shadow-md flex items-center justify-center" id="scannerVisualContainer">
                        <div id="reader-feed" class="w-full h-full object-cover"></div>

                        <!-- marco y linea de escaneo sobre la camara -->
                        <div class="pointer-events-none absolute inset-5 flex items-center justify-center" id="scannerHudOverlay">
                            <div class="absolute top-0 left-0 w-5 h-5 border-t-[3px] border-l-[3px] border-rose-400 rounded-tl-md"></div>
                            <div class="absolute top-0 right-0 w-5 h-5 border-t-[3px] border-r-[3px] border-rose-400 rounded-tr-md"></div>
                            <div class="absolute bottom-0 left-0 w-5 h-5 border-b-[3px] border-l-[3px] border-rose-400 rounded-bl-md"></div>
                            <div class="absolute bottom-0 right-0 w-5 h-5 border-b-[3px] border-r-[3px] border-rose-400 rounded-br-md"></div>

                            <div class="scan-laser-bar-rose"></div>
                        </div>

                        <div id="scannerLoadingState" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-4 text-center z-10 transition-opacity">
                            <div class="w-8 h-8 border-3 border-rose-400 border-t-transparent rounded-full animate-spin mb-2.5"></div>
                            <p class="text-[13px] font-bold text-white">Iniciando sensor de cámara...</p>
                            <p class="text-[11px] text-slate-300 mt-0.5">Conceda permiso de acceso a la cámara en el navegador.</p>
                        </div>
                    </div>

                    <div class="w-full max-w-[300px] sm:max-w-[320px] flex items-center justify-between mt-3.5 px-0.5">
                        <div class="flex items-center gap-2 text-[12px] text-slate-600 font-medium">
                            <span class="inline-block w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span id="scannerStatusTxt">Buscando código QR...</span>
                        </div>

                        <button type="button" id="btnSwitchCamera" onclick="switchScannerCamera()"
                            class="hidden px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-[11px] font-bold border border-slate-300 transition-colors items-center gap-1.5 cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Cambiar Cámara
                        </button>
                    </div>

                    <p class="text-[11.5px] text-slate-500 mt-3 text-center leading-relaxed max-w-sm">
                        Apunte la cámara a la etiqueta del producto controlado. El sistema leerá el Lote, seleccionará el estupefaciente en la tabla y validará el stock.
                    </p>
                </div>

                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
                    <button type="button" onclick="closeScanner()" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 rounded-xl text-[12.5px] font-bold shadow-xs transition-colors cursor-pointer">
                        Cancelar Escaneo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal de solicitudes con estupefacientes -->
    <div id="SolicitudesControladoModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl flex flex-col max-h-[92vh] overflow-hidden my-auto">
            <div class="px-6 py-5 bg-rose-700 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Solicitudes CESFAM con Estupefacientes</h2>
                    <p class="text-[12px] text-rose-100 mt-0.5">Asigna solicitudes a los trabajadores y despacha los fármacos controlados solicitados por los CESFAM.</p>
                </div>
                <button type="button" onclick="closeSolicitudesControlado()"
                    class="text-rose-200 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Filtros del modal controlado -->
            <div class="px-6 py-3.5 bg-slate-50 border-b border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-1">Buscar solicitud CESFAM</label>
                    <input type="text" id="filtroControladoTexto" oninput="filtrarSolicitudesControladoModal()"
                        placeholder="CESFAM, N° solicitud, responsable o estupefaciente..."
                        class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estado</label>
                    <select id="filtroControladoEstado" onchange="filtrarSolicitudesControladoModal()"
                        class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 cursor-pointer">
                        <option value="activas" selected>Activas (Por despachar)</option>
                        <option value="">Todas (Incluye Despachadas)</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="asignada">Asignada</option>
                        <option value="parcial">Parcial</option>
                        <option value="despachado">Despachado</option>
                    </select>
                </div>
            </div>

            <div class="overflow-y-auto p-5 space-y-4">
                @php $haySolicitudesControladas = false; @endphp
                @foreach ($solicitudes as $solicitud)
                    @php
                        $todosControlados = $solicitud->items->filter(
                            fn($i) => in_array($i->product->nombre ?? '', $controlados)
                        );
                        $itemsNormal = $solicitud->items->filter(
                            fn($i) => !in_array($i->product->nombre ?? '', $controlados) &&
                                $i->cantidad_solicitada - $i->cantidad_despachada > 0,
                        );
                    @endphp

                    @if ($todosControlados->isNotEmpty())
                        @php
                            $haySolicitudesControladas = true;
                            $estadoClaseCtrl = match($solicitud->estado) {
                                'Pendiente' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'Asignada' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'Parcial' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Despachado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                            $searchCtrl = mb_strtolower(
                                $solicitud->numero_solicitud . ' ' .
                                $solicitud->establecimiento . ' ' .
                                ($solicitud->responsable ?? '') . ' ' .
                                $todosControlados->map(fn($i) => $i->product->nombre ?? '')->implode(' ')
                            );
                        @endphp
                        <div class="card-solicitud-controlado border border-slate-200 rounded-xl overflow-hidden shadow-xs"
                            data-search="{{ $searchCtrl }}"
                            data-estado="{{ mb_strtolower($solicitud->estado) }}">
                            <div
                                class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col lg:flex-row justify-between lg:items-center gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-extrabold text-[13px] text-slate-900">{{ $solicitud->numero_solicitud }}</span>
                                        <span class="text-[13.5px] font-extrabold text-slate-800">{{ $solicitud->establecimiento }}</span>
                                        @if ($itemsNormal->isNotEmpty())
                                            <span class="das-badge-mixta-rose inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9.5px] font-extrabold bg-gradient-to-r from-teal-50 to-rose-50 text-rose-800 border border-rose-200" title="Contiene también productos normales">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Mixta (+{{ $itemsNormal->count() }} normales)
                                            </span>
                                        @endif
                                        <span id="badge-ctrl-estado-{{ $solicitud->id }}"
                                            class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase border {{ $estadoClaseCtrl }}">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </div>
                                    <p class="text-[11.5px] text-slate-500 mt-0.5">
                                        Fecha: {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d-m-Y') }} ·
                                        Responsable: {{ $solicitud->responsable }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2.5">
                                    @role('Administrador')
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] font-bold text-slate-500">Asignar a:</span>
                                            <select
                                                onfocus="this.dataset.prevVal = this.value"
                                                onchange="asignarSolicitudControladoUsuario({{ $solicitud->id }}, this.value, this)"
                                                class="bg-white border border-slate-300 text-slate-800 text-[12px] font-semibold rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 cursor-pointer">
                                                <option value="">— Sin asignar —</option>
                                                @foreach ($usuariosAsignables ?? [] as $usr)
                                                    <option value="{{ $usr->id }}" {{ (int) $solicitud->assigned_user_id === (int) $usr->id ? 'selected' : '' }}>
                                                        {{ $usr->name }} {{ $usr->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        @if ($solicitud->assignedUser)
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Asignado a: {{ $solicitud->assignedUser->name }} {{ $solicitud->assignedUser->last_name }}
                                            </span>
                                        @endif
                                    @endrole

                                    @if ($solicitud->estado !== 'Despachado')
                                        <button type="button" onclick="usarSolicitudControlado({{ $solicitud->id }})"
                                            class="px-4 py-1.5 text-[12px] font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-colors cursor-pointer shrink-0">
                                            Usar Solicitud
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-[12px] whitespace-nowrap">
                                    <thead class="bg-white text-slate-500 uppercase font-extrabold border-b border-slate-200 text-[10px]">
                                        <tr>
                                            <th class="px-4 py-2.5">Estupefaciente / Programa</th>
                                            <th class="px-4 py-2.5 text-center">Consumo mensual anterior</th>
                                            <th class="px-4 py-2.5 text-center">Stock Actual del establecimiento</th>
                                            <th class="px-4 py-2.5 text-center">Solicitado</th>
                                            <th class="px-4 py-2.5 text-center">Despachado / Pendiente</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($todosControlados as $item)
                                            @php
                                                $pendCtrl = max(0, (int) $item->cantidad_solicitada - (int) ($item->cantidad_despachada ?? 0));
                                            @endphp
                                            <tr class="hover:bg-rose-50/30">
                                                <td class="px-4 py-2.5 font-bold text-slate-800">
                                                    {{ $item->product->nombre }}
                                                    @if ($item->programa)
                                                        <span class="ml-1.5 px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-semibold border border-slate-200">
                                                            {{ $item->programa }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2.5 text-center font-bold text-slate-700">
                                                    {{ number_format((int) ($item->consumo_anterior ?? 0), 0, ',', '.') }} uds.
                                                </td>
                                                <td class="px-4 py-2.5 text-center font-extrabold text-blue-700">
                                                    {{ number_format((int) ($item->stock_actual ?? 0), 0, ',', '.') }} uds.
                                                </td>
                                                <td class="px-4 py-2.5 text-center font-extrabold text-rose-700">
                                                    {{ number_format((int) $item->cantidad_solicitada, 0, ',', '.') }} uds.
                                                </td>
                                                <td class="px-4 py-2.5 text-center">
                                                    @if ($pendCtrl === 0)
                                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-extrabold text-[10.5px] border border-emerald-200">
                                                            {{ $item->cantidad_despachada }} desp. (Completo)
                                                        </span>
                                                    @else
                                                        <span class="font-bold text-slate-600">{{ $item->cantidad_despachada }} desp.</span>
                                                        <span class="ml-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-extrabold text-[10.5px] border border-rose-200">
                                                            Pendiente: {{ $pendCtrl }} uds.
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($itemsNormal->isNotEmpty())
                                <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500 italic">
                                    + {{ $itemsNormal->count() }} producto(s) no controlado(s) en esta solicitud (se despachan por el módulo Despacho Normal).
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach

                @if (!$haySolicitudesControladas)
                    <div class="text-center py-12 text-slate-400">
                        <p class="text-sm font-medium">No hay solicitudes con estupefacientes registradas.</p>
                        <p class="text-[12px] mt-1">Solo las solicitudes de establecimientos CESFAM pueden contener estupefacientes.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <!-- avisos con SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let signatureDespacha = null;
        let signatureRecibe = null;
        const solicitudesControlado = @json($solicitudes);

        const esControladoJS = (nombre) => @json($controlados).some(c => c.toLowerCase() === String(nombre ?? '')
            .trim().toLowerCase());

        // Solicitudes (estupefacientes)
        function openSolicitudesControlado() {
            filtrarSolicitudesControladoModal();
            document.getElementById('SolicitudesControladoModal').classList.remove('hidden');
        }

        function closeSolicitudesControlado() {
            document.getElementById('SolicitudesControladoModal').classList.add('hidden');
        }

        function filtrarSolicitudesControladoModal() {
            const texto = (document.getElementById('filtroControladoTexto')?.value || '').trim().toLowerCase();
            const estado = (document.getElementById('filtroControladoEstado')?.value || '').toLowerCase();

            document.querySelectorAll('.card-solicitud-controlado').forEach(card => {
                const rowSearch = (card.dataset.search || '').toLowerCase();
                const rowEstado = (card.dataset.estado || '').toLowerCase();

                const matchTexto = !texto || rowSearch.includes(texto);
                let matchEstado = true;
                if (estado === 'activas') {
                    matchEstado = ['pendiente', 'asignada', 'parcial'].includes(rowEstado);
                } else if (estado) {
                    matchEstado = rowEstado === estado;
                }

                card.style.display = (matchTexto && matchEstado) ? '' : 'none';
            });
        }

        async function asignarSolicitudControladoUsuario(solicitudId, userId, selectElem) {
            const userName = selectElem && selectElem.selectedIndex >= 0 ? selectElem.options[selectElem.selectedIndex].text.trim() : '';
            const prevVal = selectElem ? (selectElem.dataset.prevVal ?? '') : '';

            const confirmTitle = userId ? '¿Asignar Solicitud Controlada?' : '¿Desasignar Solicitud?';
            const confirmHtml = userId
                ? `¿Deseas asignar la atención de esta solicitud controlada a <strong>${userName}</strong>?`
                : 'La solicitud volverá al estado <strong>Pendiente</strong> sin responsable asignado.';
            const confirmBtnText = userId ? 'Sí, asignar' : 'Sí, desasignar';

            const result = await Swal.fire({
                title: confirmTitle,
                html: confirmHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#be123c',
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            });

            if (!result.isConfirmed) {
                if (selectElem) selectElem.value = prevVal;
                return;
            }

            if (selectElem) selectElem.dataset.prevVal = userId;
            if (selectElem) selectElem.disabled = true;
            try {
                const res = await fetch(`{{ url('requests') }}/${solicitudId}/assign`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        assigned_user_id: userId ? parseInt(userId) : null
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'No se pudo asignar la solicitud');
                }

                const badge = document.getElementById('badge-ctrl-estado-' + solicitudId);
                if (badge && data.estado) {
                    badge.textContent = data.estado;
                    badge.className = 'px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase border ';
                    if (data.estado === 'Pendiente') {
                        badge.className += 'bg-amber-100 text-amber-800 border-amber-200';
                    } else if (data.estado === 'Asignada') {
                        badge.className += 'bg-indigo-100 text-indigo-800 border-indigo-200';
                    } else if (data.estado === 'Parcial') {
                        badge.className += 'bg-blue-100 text-blue-800 border-blue-200';
                    } else {
                        badge.className += 'bg-emerald-100 text-emerald-800 border-emerald-200';
                    }
                }

                const card = selectElem ? selectElem.closest('.card-solicitud-controlado') : null;
                if (card && data.estado) {
                    card.dataset.estado = data.estado.toLowerCase();
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Ocurrió un error al asignar.',
                    confirmButtonColor: '#be123c'
                });
            } finally {
                if (selectElem) selectElem.disabled = false;
            }
        }

        function desvincularSolicitudActivaControlado() {
            const requestInput = document.getElementById('establishment_request_id');
            if (requestInput) requestInput.value = '';
            const banner = document.getElementById('banner_solicitud_activa');
            if (banner) banner.classList.add('hidden');
        }

        function usarSolicitudControlado(id) {
            const solicitud = solicitudesControlado.find(s => s.id === id);
            if (!solicitud) return;

            // Helper de normalización para matching insensible a mayúsculas, tildes y símbolos
            const normText = s => (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, ' ').replace(/\s+/g, ' ').trim();

            // marcar la solicitud de origen
            const requestInput = document.getElementById('establishment_request_id');
            if (requestInput) requestInput.value = solicitud.id;

            // actualizar banner informativo
            const banner = document.getElementById('banner_solicitud_activa');
            const bannerTitulo = document.getElementById('banner_solicitud_titulo');
            const bannerSubtitulo = document.getElementById('banner_solicitud_subtitulo');
            if (banner && bannerTitulo && bannerSubtitulo) {
                bannerTitulo.textContent = `Atendiendo Solicitud ${solicitud.numero_solicitud} — ${solicitud.establecimiento}`;
                bannerSubtitulo.textContent = `Solicitante: ${solicitud.responsable || 'N/A'} · Estado actual: ${solicitud.estado} · Al confirmar el despacho se actualizará el estado automáticamente.`;
                banner.classList.remove('hidden');
            }

            // clasificacion interna + establecimiento (matching insensible a mayusculas)
            const clasificacion = document.getElementById('clasificacion_select');
            clasificacion.value = 'Interno';
            toggleClasificacion();

            const selectEst = document.getElementById('establecimiento_interno');
            const objetivoNorm = normText(solicitud.establecimiento);
            let opcionEst = Array.from(selectEst.options).find(op => {
                const valNorm = normText(op.value);
                return valNorm === objetivoNorm;
            });

            if (!opcionEst) {
                opcionEst = Array.from(selectEst.options).find(op => {
                    const valNorm = normText(op.value);
                    return valNorm && objetivoNorm && (valNorm.includes(objetivoNorm) || objetivoNorm.includes(valNorm));
                });
            }

            if (opcionEst) {
                selectEst.value = opcionEst.value;
                actualizarDireccionInterna();
            } else if (solicitud.establecimiento) {
                const selectClasif = document.getElementById('clasificacion_select');
                if (selectClasif) {
                    selectClasif.value = 'Externo';
                    toggleClasificacion();
                    const inputExt = document.getElementById('establecimiento_externo');
                    if (inputExt) inputExt.value = solicitud.establecimiento;
                }
            }

            // condiciones de transporte / observaciones
            const condiciones = document.querySelector('input[name="condiciones_transporte"]');
            if (condiciones) {
                const refTexto = `Ref: Solicitud ${solicitud.numero_solicitud}${solicitud.responsable ? ' (' + solicitud.responsable + ')' : ''}`;
                condiciones.value = solicitud.observaciones ? `${solicitud.observaciones} - ${refTexto}` : refTexto;
            }

            // limpiar filas actuales
            while (tbody.children.length > 0) tbody.children[0].remove();

            let normales = [];
            let sinStock = [];

            (solicitud.items || []).forEach(item => {
                const nombreItem = item.product?.nombre ?? ('Producto #' + item.product_id);
                const pendiente = Math.max(0, (parseInt(item.cantidad_solicitada) || 0) - (parseInt(item.cantidad_despachada) || 0));

                if (pendiente <= 0) return;

                if (!esControladoJS(nombreItem)) {
                    normales.push(nombreItem + ' (x' + pendiente + ')');
                    return;
                }

                const tr = addRow();
                const selectProducto = tr.querySelector('select[name="productos[]"]');
                const itemNorm = normText(nombreItem);

                let opcion = Array.from(selectProducto.options).find(op =>
                    normText(op.value) === itemNorm
                );

                if (!opcion) {
                    opcion = Array.from(selectProducto.options).find(op => {
                        const optNorm = normText(op.value);
                        return optNorm && itemNorm && (optNorm.includes(itemNorm) || itemNorm.includes(optNorm));
                    });
                }

                if (opcion) {
                    selectProducto.value = opcion.value;
                    llenarDatosFila(selectProducto);
                    const selectPrograma = tr.querySelector('select[name="programas[]"]');
                    if (selectPrograma && item.programa) {
                        const progNorm = normText(item.programa);
                        const optProg = Array.from(selectPrograma.options).find(o =>
                            normText(o.value) === progNorm || normText(o.text) === progNorm
                        );
                        if (optProg) selectPrograma.value = optProg.value;
                    }
                    const cantInput = tr.querySelector('input[name="cantidades[]"]');
                    if (cantInput) {
                        cantInput.value = pendiente;
                        validarStock(cantInput);
                    }
                } else {
                    tr.remove();
                    sinStock.push(nombreItem);
                }
            });

            if (tbody.children.length === 0) addRow();

            // precargar la firma del solicitante (firma de quien recibe)
            if (solicitud.firma_solicitante) {
                if (signatureRecibe) {
                    signatureRecibe.clear();
                    signatureRecibe.fromDataURL(solicitud.firma_solicitante);
                }
                const firmaInput = document.getElementById('firma_recibe_input');
                if (firmaInput) firmaInput.value = solicitud.firma_solicitante;
            } else {
                if (signatureRecibe) signatureRecibe.clear();
                const firmaInput = document.getElementById('firma_recibe_input');
                if (firmaInput) firmaInput.value = '';
            }

            closeSolicitudesControlado();

            // Notificación toast de éxito al cargar
            if (typeof Swal !== 'undefined' && normales.length === 0 && sinStock.length === 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Solicitud ${solicitud.numero_solicitud} cargada`,
                    text: 'Se completaron los datos del establecimiento y fármacos controlados solicitados.',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            }

            if (normales.length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Solicitud Mixta: Medicamentos Normales',
                    html: `
                        <div class="das-swal-container text-left text-xs">
                            <div class="das-swal-mixed-banner das-swal-mixed-banner-teal p-3.5 bg-gradient-to-r from-teal-50 to-amber-50 border border-teal-300 rounded-xl flex items-start gap-3">
                                <div class="das-swal-mixed-icon das-swal-mixed-icon-teal w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 font-extrabold text-sm">!</div>
                                <div class="flex-1 min-w-0">
                                    <p class="das-swal-mixed-title font-bold text-teal-950 text-[13px]">
                                        Solicitud ${solicitud.numero_solicitud ?? ''} contiene Productos Normales
                                    </p>
                                    <p class="das-swal-mixed-text text-slate-600 text-[11.5px] mt-1 leading-relaxed">
                                        Se cargaron los fármacos controlados en este formulario. Los siguientes productos NO son estupefacientes y deben despacharse por el módulo <strong>Despacho Normal</strong>:
                                    </p>
                                    <ul class="das-swal-pill-list mt-2.5 space-y-1.5">
                                        ${normales.map(prod => `
                                            <li class="das-swal-pill-item das-swal-pill-item-teal flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[11.5px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 shrink-0"></span>
                                                <span>${prod}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#be123c',
                    confirmButtonText: 'Entendido, continuar despacho controlado',
                    width: '540px'
                });
            } else if (sinStock.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta de Stock',
                    text: 'Sin stock Disponible de: ' + sinStock.join(', '),
                    confirmButtonColor: '#be123c'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const padDespachaElem = document.getElementById('pad_despacha');
            const padRecibeElem = document.getElementById('pad_recibe');
            const padOptions = {
                penColor: 'rgb(15, 23, 42)',
                backgroundColor: 'rgba(255, 255, 255, 1)'
            };

            if (padDespachaElem) signatureDespacha = new SignaturePad(padDespachaElem, padOptions);
            if (padRecibeElem) signatureRecibe = new SignaturePad(padRecibeElem, padOptions);

            const form = document.getElementById('despachoControladoForm');
            let despachoControladoSubmitAction = 'save_only';

            document.querySelectorAll('#despachoControladoForm button[type="submit"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    despachoControladoSubmitAction = this.value || 'save_only';
                });
            });

            function triggerSilentDownloadControlado(pdfUrl) {
                if (!pdfUrl) return;
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = pdfUrl;
                document.body.appendChild(iframe);
                setTimeout(() => {
                    if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
                }, 60000);
            }

            function showControlledDispatchSuccessModal(ctrlData) {
                const htmlDetail = `
                    <div class="das-swal-container text-left text-xs text-slate-700">
                        <div class="das-swal-summary das-swal-summary-rose p-3.5 bg-rose-50/80 border border-rose-200 rounded-xl mb-3.5 space-y-1.5">
                            <div class="das-swal-summary-header flex items-center justify-between pb-2 mb-2 border-b border-rose-200">
                                <span class="das-swal-summary-title font-bold text-rose-950 text-sm">Guía Controlada N° ${ctrlData.correlativo}</span>
                                <span class="das-swal-badge-rose px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">FÁRMACOS CONTROLADOS</span>
                            </div>
                            <p class="das-swal-meta"><strong>Destino:</strong> ${ctrlData.destino}</p>
                            <p class="das-swal-meta"><strong>Clasificación:</strong> ${ctrlData.clasificacion}</p>
                            <p class="das-swal-meta"><strong>Fecha de Emisión:</strong> ${ctrlData.fecha}</p>
                            ${ctrlData.series_recetas ? `<p class="das-swal-meta"><strong>Series Recetas:</strong> ${ctrlData.series_recetas}</p>` : ''}
                            <p class="das-swal-meta"><strong>Total Fármacos Controlados:</strong> ${Number(ctrlData.total_unidades || 0).toLocaleString('es-CL')} uds. (${ctrlData.total_items} producto${ctrlData.total_items === 1 ? '' : 's'})</p>
                        </div>
                        <p class="das-swal-section-title font-bold text-slate-700 mb-1.5 text-[11px] uppercase tracking-wide">Detalle de Fármacos Despachados:</p>
                        <div class="das-swal-table-wrap max-h-56 overflow-y-auto border border-rose-200 rounded-lg shadow-inner">
                            <table class="das-swal-table das-swal-table-rose w-full text-[11.5px] text-left">
                                <thead class="bg-rose-50 text-rose-900 font-bold sticky top-0 border-b border-rose-100">
                                    <tr>
                                        <th class="p-2.5">Fármaco Controlado</th>
                                        <th class="p-2.5 text-center">Lote FEFO</th>
                                        <th class="p-2.5 text-center">Vencimiento</th>
                                        <th class="p-2.5 text-right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100 bg-white">
                                    ${(ctrlData.items || []).map(it => `
                                        <tr class="hover:bg-rose-50/40">
                                            <td class="p-2.5 font-medium text-slate-800">${it.producto}</td>
                                            <td class="p-2.5 text-center font-mono text-[11px] font-semibold text-slate-700">${it.lote}</td>
                                            <td class="p-2.5 text-center text-slate-600">${it.vencimiento}</td>
                                            <td class="p-2.5 text-right font-bold text-rose-700 das-swal-qty-rose">${Number(it.cantidad).toLocaleString('es-CL')} u.</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                if (ctrlData.auto_download && ctrlData.pdf_url) {
                    triggerSilentDownloadControlado(ctrlData.pdf_url);
                }

                if (ctrlData.tiene_normales_pendientes && ctrlData.url_despacho_normal) {
                    Swal.fire({
                        title: '¡Despacho Controlado Emitido!',
                        html: `
                            <div class="das-swal-container text-left text-xs text-slate-700">
                                ${htmlDetail}
                                <div class="das-swal-mixed-banner das-swal-mixed-banner-teal mt-4 p-3.5 bg-gradient-to-r from-teal-50 to-amber-50 border border-teal-300 rounded-xl flex items-start gap-3 shadow-xs">
                                    <div class="das-swal-mixed-icon das-swal-mixed-icon-teal w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-xs font-extrabold text-sm">!</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="das-swal-mixed-title font-bold text-teal-950 text-[13px]">Solicitud Mixta: Productos Normales Pendientes</p>
                                        <p class="das-swal-mixed-text text-slate-600 text-[11.5px] mt-1 leading-relaxed">
                                            Esta solicitud aún tiene <strong>${ctrlData.cant_normales_pendientes} producto(s) normal(es)</strong> pendiente(s) por despachar en el módulo de Despacho Normal.
                                        </p>
                                        <p class="das-swal-mixed-cta font-semibold text-[11.5px] mt-1.5">
                                            ¿Deseas emitir el Despacho Normal ahora mismo?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        showDenyButton: Boolean(ctrlData.pdf_url),
                        confirmButtonColor: '#0f766e',
                        confirmButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg> Ir a Despacho Normal',
                        denyButtonColor: '#be123c',
                        denyButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Ver Guía Controlada PDF',
                        cancelButtonColor: '#475569',
                        cancelButtonText: 'Dejar para después',
                        allowOutsideClick: false,
                        width: '660px'
                    }).then((res) => {
                        if (res.isConfirmed) {
                            window.location.href = ctrlData.url_despacho_normal;
                        } else if (res.isDenied && ctrlData.pdf_url) {
                            window.open(ctrlData.pdf_url, '_blank');
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: '¡Despacho Realizado con Éxito!',
                    html: htmlDetail,
                    icon: 'success',
                    confirmButtonColor: '#0f766e',
                    confirmButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Aceptar',
                    showCancelButton: Boolean(ctrlData.pdf_url),
                    cancelButtonColor: '#be123c',
                    cancelButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Ver Guía Controlada PDF',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    width: '640px'
                }).then((res) => {
                    if (res.dismiss === Swal.DismissReason.cancel && ctrlData.pdf_url) {
                        window.open(ctrlData.pdf_url, '_blank');
                    }
                    window.location.reload();
                });
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    const clasif = document.getElementById('clasificacion_select')?.value || 'Interno';
                    let destinatario = '';
                    if (clasif === 'Externo') {
                        const ext = (document.getElementById('establecimiento_externo')?.value || '').trim();
                        const tipoExt = (document.getElementById('tipo_externo')?.value || '').trim();
                        destinatario = (tipoExt ? tipoExt + ' - ' : '') + (ext || 'Destino Externo');
                    } else {
                        const selInt = document.getElementById('establecimiento_interno');
                        destinatario = selInt && selInt.selectedIndex >= 0 && selInt.value
                            ? selInt.options[selInt.selectedIndex].text.trim()
                            : 'Establecimiento CESFAM';
                    }

                    const rows = document.querySelectorAll('#productsBody tr');
                    let itemsCount = 0;
                    let totalUnidades = 0;

                    rows.forEach(tr => {
                        const selProd = tr.querySelector('select[name="productos[]"]');
                        const inpCant = tr.querySelector('input[name="cantidades[]"]');
                        const cant = parseInt(inpCant?.value) || 0;
                        if (selProd && selProd.value) {
                            itemsCount++;
                            totalUnidades += cant;
                        }
                    });

                    if (itemsCount === 0 && rows.length > 0) {
                        itemsCount = rows.length;
                    }

                    const textoUnidades = totalUnidades > 0 ? ` (${totalUnidades.toLocaleString('es-CL')} unidades en total)` : '';

                    Swal.fire({
                        title: '¿Emitir Despacho de Fármacos Controlados?',
                        html: `Se emitirá la guía de despacho de estupefacientes/psicotrópicos para <strong>${destinatario}</strong> con <strong>${itemsCount}</strong> producto(s)${textoUnidades}.<br><span class="text-[12px] text-rose-600 font-bold mt-1 block">Esta acción descuenta existencias controladas y genera registro legal de trazabilidad.</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#be123c',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, emitir despacho controlado',
                        cancelButtonText: 'Revisar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const faltaFirmaDespacha = !signatureDespacha || signatureDespacha.isEmpty();
                            const faltaFirmaRecibe = (!signatureRecibe || signatureRecibe.isEmpty()) && !document.getElementById('firma_recibe_input').value;
                            if (faltaFirmaDespacha || faltaFirmaRecibe) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Faltan firmas',
                                    text: faltaFirmaDespacha
                                        ? 'Debe firmar quien despacha (bodega) antes de emitir la guía.'
                                        : 'Debe firmar quien recibe el despacho antes de emitir la guía.',
                                    confirmButtonColor: '#0f766e'
                                });
                                return;
                            }
                            if (signatureDespacha && !signatureDespacha.isEmpty()) {
                                document.getElementById('firma_despacha_input').value = signatureDespacha.toDataURL('image/png');
                            }
                            if (signatureRecibe && !signatureRecibe.isEmpty()) {
                                document.getElementById('firma_recibe_input').value = signatureRecibe.toDataURL('image/png');
                            }

                            const formData = new FormData(form);
                            formData.set('action', despachoControladoSubmitAction);

                            Swal.fire({
                                title: 'Procesando Despacho Controlado...',
                                html: '<span class="text-xs text-slate-500">Descontando existencias controladas y actualizando trazabilidad legal...</span>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(form.getAttribute('action'), {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                                },
                                body: formData
                            })
                            .then(async response => {
                                const data = await response.json().catch(() => ({}));
                                if (!response.ok) {
                                    let errorMsg = data.message || 'Error al procesar el despacho controlado.';
                                    if (data.errors) {
                                        errorMsg = Object.values(data.errors).flat().join('<br>');
                                    }
                                    throw new Error(errorMsg);
                                }
                                return data;
                            })
                            .then(data => {
                                if (data.success && data.detail) {
                                    showControlledDispatchSuccessModal(data.detail);
                                } else {
                                    throw new Error(data.message || 'Error inesperado al emitir el despacho controlado.');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'No se pudo emitir el despacho',
                                    html: `<div class="text-xs text-rose-700 bg-rose-50 p-3 rounded-lg border border-rose-200">${error.message}</div>`,
                                    confirmButtonColor: '#be123c',
                                    confirmButtonText: 'Entendido'
                                });
                            });
                        }
                    });
                });
            }

            const btnLimpiar = document.getElementById('btn_limpiar_form');
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', function() {
                    if (signatureDespacha) signatureDespacha.clear();
                    if (signatureRecibe) signatureRecibe.clear();
                    const items = document.querySelectorAll('#productsBody tr');
                    for (let i = 1; i < items.length; i++) {
                        items[i].remove();
                    }
                    window.location.reload();
                });
            }
        });

        function toggleClasificacion() {
            const val = document.getElementById('clasificacion_select').value;
            const divInt = document.getElementById('div_interno');
            const divExt = document.getElementById('div_externo');

            const selInt = document.getElementById('establecimiento_interno');
            const selExtTipo = document.getElementById('tipo_externo');
            const inpExtDest = document.getElementById('establecimiento_externo');
            const inpDir = document.getElementById('direccion_destino');

            if (val === 'Interno') {
                divInt.classList.remove('hidden');
                divExt.classList.add('hidden');

                selInt.setAttribute('name', 'establecimiento_destino');
                selInt.setAttribute('required', 'true');

                selExtTipo.removeAttribute('name');
                selExtTipo.removeAttribute('required');
                inpExtDest.removeAttribute('name');
                inpExtDest.removeAttribute('required');

                inpDir.readOnly = true;
                inpDir.classList.add('bg-slate-50', 'cursor-not-allowed');
                inpDir.classList.remove('bg-white');
                inpDir.placeholder = "Se autocompletará al seleccionar destino...";

                actualizarDireccionInterna();
            } else if (val === 'Externo') {
                divInt.classList.add('hidden');
                divExt.classList.remove('hidden');

                selInt.removeAttribute('name');
                selInt.removeAttribute('required');

                selExtTipo.setAttribute('name', 'tipo_externo_despacho');
                selExtTipo.setAttribute('required', 'true');
                inpExtDest.setAttribute('name', 'establecimiento_destino');
                inpExtDest.setAttribute('required', 'true');

                inpDir.readOnly = false;
                inpDir.classList.remove('bg-slate-50', 'cursor-not-allowed');
                inpDir.classList.add('bg-white');
                inpDir.value = '';
                inpDir.placeholder = 'Ingrese la dirección manualmente...';
            }
        }

        function actualizarDireccionInterna() {
            if (document.getElementById('clasificacion_select').value !== 'Interno') return;

            const select = document.getElementById('establecimiento_interno');
            const input = document.getElementById('direccion_destino');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.dataset.direccion) {
                input.value = selectedOption.dataset.direccion;
            } else {
                input.value = '';
            }
        }

        function openBandeja() {
            document.getElementById('BandejaModal').classList.remove('hidden');
        }

        function closeBandeja() {
            document.getElementById('BandejaModal').classList.add('hidden');
        }

        function limpiarFiltrosBandeja() {
            document.getElementById('filtroCorrelativo').value = '';
            document.getElementById('filtroFecha').value = '';
            document.getElementById('filtroClasificacion').value = '';
            document.getElementById('filtroDestino').value = '';
            document.getElementById('filtroEstadoBandeja').value = '';
            filtrarBandeja();
        }

        function filtrarBandeja() {
            let inputCorrelativo = document.getElementById('filtroCorrelativo').value.toLowerCase();
            let inputFecha = document.getElementById('filtroFecha').value;
            let selectClasificacion = document.getElementById('filtroClasificacion').value.toLowerCase();
            let inputDestino = document.getElementById('filtroDestino').value.toLowerCase();
            let selectEstado = document.getElementById('filtroEstadoBandeja').value.toLowerCase();

            let fechaFormateada = "";
            if (inputFecha) {
                let partes = inputFecha.split('-');
                if (partes.length === 3) {
                    fechaFormateada = partes[2] + '-' + partes[1] + '-' + partes[0];
                }
            }

            let filas = document.querySelectorAll('.fila-despacho');

            filas.forEach(fila => {
                let correlativoText = fila.children[1].textContent.toLowerCase();
                let fechaText = fila.children[2].textContent.trim();
                let clasificacionText = fila.children[3].textContent.toLowerCase();
                let destinoText = fila.children[4].textContent.toLowerCase();
                let estadoText = fila.children[5].textContent.toLowerCase();

                let coincideCorrelativo = correlativoText.includes(inputCorrelativo);
                let coincideFecha = fechaFormateada === "" || fechaText.includes(fechaFormateada);
                let coincideClasificacion = selectClasificacion === "" || clasificacionText.includes(
                    selectClasificacion);
                let coincideDestino = destinoText.includes(inputDestino);
                let coincideEstado = selectEstado === "" || estadoText.includes(selectEstado);

                let key = fila.getAttribute('data-key');
                let filaHija = document.getElementById('items-' + key);

                if (coincideCorrelativo && coincideFecha && coincideClasificacion && coincideDestino &&
                    coincideEstado) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                    if (filaHija) {
                        filaHija.classList.add('hidden');
                        let arrow = document.getElementById('arrow-' + key);
                        let iconContainer = document.getElementById('icon-' + key);
                        if (arrow) arrow.classList.remove('rotate-90');
                        if (iconContainer) {
                            iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                            iconContainer.classList.remove('bg-rose-600', 'text-white', 'shadow-md',
                                'border-rose-600');
                        }
                    }
                }
            });
        }

        function toggleItems(key) {
            const row = document.getElementById('items-' + key);
            const arrow = document.getElementById('arrow-' + key);
            const iconContainer = document.getElementById('icon-' + key);
            if (row) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    if (arrow) arrow.classList.add('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.add('bg-rose-600', 'text-white', 'shadow-md', 'border-rose-600');
                    }
                } else {
                    row.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.remove('bg-rose-600', 'text-white', 'shadow-md', 'border-rose-600');
                    }
                }
            }
        }

        const tbody = document.getElementById('productsBody');

        document.addEventListener("DOMContentLoaded", () => {
            addRow();
            filtrarSolicitudesControladoModal();
            const params = new URLSearchParams(window.location.search);
            const solIdParam = parseInt(params.get('solicitud_id'));
            if (solIdParam) {
                setTimeout(() => usarSolicitudControlado(solIdParam), 150);
            }
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
                let targetOption = options.find(opt => opt.value === data.producto && opt.getAttribute('data-lote') === data
                    .lote);

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
            const nombreMovil = tr.querySelector('.producto-nombre-movil');
            if (nombreMovil) nombreMovil.textContent = option.getAttribute('data-nombre-completo') || option.value || '';
            let loteInput = tr.querySelector('input[name="lotes[]"]');
            let venceInput = tr.querySelector('input[name="vencimientos[]"]');
            let cantInput = tr.querySelector('input[name="cantidades[]"]');

            let stockBadge = tr.querySelector('.stock-max-badge');
            let stockContainer = tr.querySelector('.stock-container');
            let fefoContainer = tr.querySelector('.fefo-container');

            loteInput.value = option.getAttribute('data-lote');
            venceInput.value = option.getAttribute('data-vence');

            const progSugerido = (option.getAttribute('data-programa') || '').trim();
            const progSelect = tr.querySelector('select[name="programas[]"]');
            if (progSelect && progSugerido) {
                let matchedProg = Array.from(progSelect.options).find(
                    o => o.value && o.value.toLowerCase() === progSugerido.toLowerCase()
                );
                if (!matchedProg) {
                    const newOpt = document.createElement('option');
                    newOpt.value = progSugerido;
                    newOpt.textContent = progSugerido;
                    progSelect.appendChild(newOpt);
                    matchedProg = newOpt;
                }
                progSelect.value = matchedProg.value;
            }

            let stock = parseInt(option.getAttribute('data-stock'));
            cantInput.setAttribute('max', stock);
            stockBadge.textContent = stock;
            stockContainer.classList.remove('hidden');

            fefoContainer.innerHTML = '';
            venceInput.classList.remove('text-rose-700', 'font-extrabold', 'bg-rose-50');

            if (venceInput.value) {
                try {
                    let fVence = new Date(venceInput.value);
                    let hoy = new Date();
                    let mesesRestantes = (fVence.getFullYear() - hoy.getFullYear()) * 12 + (fVence.getMonth() - hoy
                        .getMonth());

                    if (mesesRestantes <= 6) {
                        venceInput.classList.add('text-rose-700', 'font-extrabold', 'bg-rose-50');
                        fefoContainer.innerHTML =
                            '<span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] bg-rose-500 text-white font-bold px-1.5 py-0.5 rounded shadow-sm">PRONTO A VENCER</span>';
                    }
                } catch (e) {}
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
                const nombreMovilVacio = tr.querySelector('.producto-nombre-movil');
                if (nombreMovilVacio) nombreMovilVacio.textContent = '';
                tr.querySelectorAll('input').forEach(i => i.value = '');
                tr.querySelector('.stock-container').classList.add('hidden');
                tr.querySelector('.fefo-container').innerHTML = '';
                tr.querySelector('input[name="cantidades[]"]').removeAttribute('max');
                tr.querySelector('input[name="vencimientos[]"]').classList.remove('text-rose-700', 'font-extrabold',
                    'bg-rose-50');
            }
        }

        function validarStock(input) {
            let maxVal = parseInt(input.getAttribute('max'));
            let currentVal = parseInt(input.value);

            if (!isNaN(maxVal) && !isNaN(currentVal)) {
                if (currentVal > maxVal) {
                    input.value = maxVal;
                    input.classList.add('border-rose-500', 'bg-rose-50', 'text-rose-700', 'ring-2', 'ring-rose-500/20');
                    setTimeout(() => {
                        input.classList.remove('border-rose-500', 'bg-rose-50', 'text-rose-700', 'ring-2',
                            'ring-rose-500/20');
                    }, 600);
                }
            }
        }

        // Escáner QR
        let html5QrScanner = null;
        let availableCameras = [];
        let currentCameraIndex = 0;

        function playScannerBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            } catch(e) {}
        }

        async function openScanner() {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aviso de Seguridad',
                    text: 'Para usar la cámara necesitas acceder mediante localhost, 127.0.0.1 o HTTPS.',
                    confirmButtonColor: '#e11d48'
                });
            }

            const modal = document.getElementById('scannerModal');
            const loadingState = document.getElementById('scannerLoadingState');
            const statusTxt = document.getElementById('scannerStatusTxt');
            const btnSwitch = document.getElementById('btnSwitchCamera');

            modal.classList.remove('hidden');
            if (loadingState) {
                loadingState.style.display = 'flex';
                loadingState.style.opacity = '1';
            }
            if (statusTxt) statusTxt.textContent = 'Iniciando cámara...';

            try {
                if (!html5QrScanner) {
                    html5QrScanner = new Html5Qrcode("reader-feed");
                }

                try {
                    availableCameras = await Html5Qrcode.getCameras();
                    if (availableCameras && availableCameras.length > 1 && btnSwitch) {
                        btnSwitch.classList.remove('hidden');
                        btnSwitch.classList.add('inline-flex');
                    }
                } catch(camErr) {
                    availableCameras = [];
                }

                startScannerWithCurrentCamera();
            } catch (err) {
                console.error("Error al iniciar lector:", err);
                if (loadingState) {
                    loadingState.innerHTML = `
                        <div class="text-center p-4">
                            <svg class="w-10 h-10 text-rose-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-sm font-bold text-white">No se pudo acceder a la cámara</p>
                            <p class="text-xs text-slate-400 mt-1">Verifica los permisos en tu navegador o conecta una cámara.</p>
                        </div>
                    `;
                }
                if (statusTxt) statusTxt.textContent = 'Error de cámara';
            }
        }

        async function startScannerWithCurrentCamera() {
            const loadingState = document.getElementById('scannerLoadingState');
            const statusTxt = document.getElementById('scannerStatusTxt');

            let cameraConfig = { facingMode: "environment" };
            if (availableCameras.length > 0 && availableCameras[currentCameraIndex]) {
                cameraConfig = availableCameras[currentCameraIndex].id;
            }

            const scanConfig = {
                fps: 15,
                qrbox: { width: 220, height: 220 },
                aspectRatio: 1.0
            };

            if (html5QrScanner && html5QrScanner.isScanning) {
                await html5QrScanner.stop();
            }

            html5QrScanner.start(
                cameraConfig,
                scanConfig,
                onScanSuccess,
                function() {}
            ).then(() => {
                if (loadingState) {
                    loadingState.style.opacity = '0';
                    setTimeout(() => { loadingState.style.display = 'none'; }, 200);
                }
                if (statusTxt) statusTxt.textContent = 'Enfoca el código QR en el visor';
            }).catch(e => {
                console.error("Fallo al iniciar cámara:", e);
                if (statusTxt) statusTxt.textContent = 'Error al activar cámara';
            });
        }

        async function switchScannerCamera() {
            if (availableCameras.length <= 1) return;
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            const statusTxt = document.getElementById('scannerStatusTxt');
            if (statusTxt) statusTxt.textContent = 'Cambiando de cámara...';
            await startScannerWithCurrentCamera();
        }

        async function closeScanner() {
            if (html5QrScanner && html5QrScanner.isScanning) {
                try {
                    await html5QrScanner.stop();
                } catch(e) {}
            }
            document.getElementById('scannerModal').classList.add('hidden');
        }

        function onScanSuccess(decodedText) {
            playScannerBeep();
            closeScanner();

            let parsedData = {};
            let chunks = decodedText.split('|');
            chunks.forEach(chunk => {
                let idx = chunk.indexOf(':');
                if (idx > -1) {
                    let key = chunk.substring(0, idx).trim().toUpperCase();
                    let val = chunk.substring(idx + 1).trim();
                    if (key) parsedData[key] = val;
                }
            });

            const norm = s => (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, ' ').replace(/\s+/g, ' ').trim();
            const cleanLote = l => {
                let s = (l || '').toString().trim().toUpperCase();
                if (!s || s === 'S/L' || s === 'SIN LOTE' || s === 'N/A' || s === 'NULL') return 'S/L';
                return s;
            };

            let prodTarget = (parsedData.PROD || '').trim();
            let loteTarget = (parsedData.LOTE || (!prodTarget ? decodedText.trim() : '')).trim();

            if (!prodTarget && !loteTarget) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato Inválido',
                    text: 'El código QR no contiene datos compatibles de producto o lote.',
                    confirmButtonColor: '#e11d48'
                });
                return;
            }

            let emptySelect = Array.from(tbody.querySelectorAll('select[name="productos[]"]')).find(sel => sel.value === '');
            let tr = emptySelect ? emptySelect.closest('tr') : addRow();

            let select = tr.querySelector('select[name="productos[]"]');
            let options = Array.from(select.options).filter(opt => opt.value && !opt.disabled);

            let targetOption = null;

            // 1. Coincidencia exacta de Producto Y Lote
            if (prodTarget && loteTarget) {
                const targetProdNorm = norm(prodTarget);
                const targetLoteNorm = cleanLote(loteTarget);
                targetOption = options.find(opt => {
                    return norm(opt.value) === targetProdNorm && cleanLote(opt.getAttribute('data-lote')) === targetLoteNorm;
                });
            }

            // 2. Coincidencia por Lote (si el lote es específico y distinto de S/L)
            if (!targetOption && loteTarget && cleanLote(loteTarget) !== 'S/L') {
                const targetLoteNorm = cleanLote(loteTarget);
                targetOption = options.find(opt => cleanLote(opt.getAttribute('data-lote')) === targetLoteNorm);
            }

            // 3. Coincidencia por Producto (primer lote disponible)
            if (!targetOption && prodTarget) {
                const targetProdNorm = norm(prodTarget);
                targetOption = options.find(opt => norm(opt.value) === targetProdNorm);
            }

            // 4. Coincidencia parcial / difusa por Producto
            if (!targetOption && prodTarget) {
                const targetProdNorm = norm(prodTarget);
                targetOption = options.find(opt => {
                    const optNorm = norm(opt.value);
                    return optNorm && targetProdNorm && (optNorm.includes(targetProdNorm) || targetProdNorm.includes(optNorm));
                });
            }

            if (targetOption) {
                // Seleccionar índice exacto para no colisionar con productos duplicados
                targetOption.selected = true;
                select.selectedIndex = targetOption.index;
                select.value = targetOption.value;
                llenarDatosFila(select);

                const cantInput = tr.querySelector('input[name="cantidades[]"]');
                if (cantInput) {
                    cantInput.focus();
                }

                const stockDisp = parseInt(targetOption.getAttribute('data-stock')) || 0;
                const loteEncontrado = targetOption.getAttribute('data-lote') || 'S/L';

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Fármaco cargado: ${targetOption.value}`,
                    text: `Lote: ${loteEncontrado} · Stock disp: ${stockDisp} uds.`,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            } else {
                let nombreBuscado = prodTarget || loteTarget;
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin Stock en Fármacos Controlados',
                    html: `El producto o lote <strong>${nombreBuscado}</strong> no figura con stock disponible en la bodega de <strong>Fármacos Controlados</strong>.<br><br><span class="text-xs text-slate-500">Recuerda que este módulo es exclusivo para estupefacientes y recetas cheque. Si es un producto común, búscalo en el módulo de Despacho general.</span>`,
                    confirmButtonColor: '#e11d48'
                });
                if (!emptySelect && tbody.children.length > 1) tr.remove();
            }
        }

        function onScanFailure(error) {
            // Se ignora silenciosamente para no saturar la consola en cada frame fallido
        }

        @if (session('controlled_dispatch_success_detail'))
            document.addEventListener('DOMContentLoaded', function() {
                const ctrlData = @json(session('controlled_dispatch_success_detail'));
                if (typeof showControlledDispatchSuccessModal === 'function') {
                    showControlledDispatchSuccessModal(ctrlData);
                }
            });
        @endif
    </script>

    <style>
        /* ===== Detalle de extracción en celular: cada fila se muestra como tarjeta ===== */
        .producto-nombre-movil { display: none; }
        @media (max-width: 767px) {
            #productsTable { min-width: 0 !important; width: 100% !important; table-layout: auto !important; white-space: normal !important; }
            #productsTable thead { display: none; }
            #productsTable, #productsTable tbody { display: block; }
            #productsTable tbody tr { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 10px 12px; padding: 14px; border-bottom: 1px solid rgba(148, 163, 184, .35); }
            #productsTable tbody tr:last-child { border-bottom: 0; }
            #productsTable tbody td { display: block; padding: 0 !important; border: 0 !important; max-width: none !important; min-width: 0; }
            #productsTable tbody td::before { content: attr(data-label); display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: 4px; }
            #productsTable tbody td.col-producto, #productsTable tbody td.col-quitar { grid-column: 1 / -1; }
            #productsTable tbody td.col-quitar { text-align: right !important; }
            #productsTable tbody td.col-quitar::before { display: none; }
            #productsTable tbody td.col-vence .fefo-container span { top: auto !important; bottom: 6px; transform: none !important; }
            #productsTable select, #productsTable input { min-width: 0; }
            .producto-nombre-movil { display: block; font-size: 12px; font-weight: 700; line-height: 1.35; margin-top: 6px; white-space: normal; overflow-wrap: anywhere; }
            .producto-nombre-movil:empty { display: none; }
            /* Modales de solicitudes / bandeja: en celular se desplaza todo el modal, no solo la lista */
            #SolicitudesModal > div, #BandejaModal > div { overflow-y: auto !important; }
            #SolicitudesModal .max-h-\[60vh\], #BandejaModal .max-h-\[60vh\] { max-height: none !important; flex-shrink: 0; }
        }

        @keyframes scanLaserAnim {
            0% { top: 6%; opacity: 0.6; }
            50% { top: 88%; opacity: 1; }
            100% { top: 6%; opacity: 0.6; }
        }
        .scan-laser-bar-rose {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent, #f43f5e, #fb7185, #f43f5e, transparent);
            box-shadow: 0 0 12px 2px rgba(244, 63, 94, 0.9);
            animation: scanLaserAnim 2s ease-in-out infinite;
        }
        #reader-feed video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
    </style>
@endsection
