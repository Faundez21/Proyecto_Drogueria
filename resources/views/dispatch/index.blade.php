@extends('layouts.app')
@section('title', 'Despacho')
@section('subtitle', 'Directorio y gestión de guías de despacho emitidas.')

@section('actions')
    <button type="button" onclick="openBandeja()"
        class="das-btn das-btn-secondary">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
            </path>
        </svg>
        Bandeja de Despachos e Historial
    </button>
@endsection

@section('content')
    @php
        $totalActivas = collect($solicitudes)->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count();
        $totalMias = isset($misSolicitudesAsignadas) ? count($misSolicitudesAsignadas) : 0;
    @endphp

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

    <!-- Solicitudes asignadas al usuario: una fila compacta con un botón "Cargar" por solicitud -->
    @if (isset($misSolicitudesAsignadas) && count($misSolicitudesAsignadas) > 0)
        <div class="mb-4 flex flex-wrap items-center gap-2 px-3 py-2.5 bg-teal-50/70 border border-teal-200 rounded-xl">
            <span class="text-[12.5px] font-semibold text-teal-900 mr-1">
                Asignadas a usted ({{ count($misSolicitudesAsignadas) }}):
            </span>
            @foreach ($misSolicitudesAsignadas as $miSol)
                @php
                    $esCesfamMiSol = \App\Services\InventoryService::esCesfam($miSol->establecimiento);
                @endphp
                <span class="das-chip"
                    title="{{ $miSol->establecimiento }} · {{ $esCesfamMiSol ? 'CESFAM' : 'Establecimiento' }} · {{ $miSol->estado }} · {{ $miSol->items->count() }} producto(s) · {{ \Carbon\Carbon::parse($miSol->fecha_solicitud)->format('d/m/Y') }}">
                    <span class="font-mono">{{ $miSol->numero_solicitud }}</span>
                    <span class="font-medium max-w-[220px] truncate">{{ $miSol->establecimiento }}</span>
                    <button type="button" onclick="usarSolicitud({{ $miSol->id }})">Cargar</button>
                </span>
            @endforeach
            <button type="button" onclick="openSolicitudes('mias')"
                class="ml-auto text-[12.5px] font-semibold text-teal-800 hover:underline cursor-pointer">
                Ver detalle de mis asignadas
            </button>
        </div>
    @endif

    <!-- indicadores: una sola fila; cada uno abre su lista -->
    <div class="das-kpi-strip">
        <div class="das-kpi">
            <span class="das-kpi-value">{{ collect($dispatches)->where('fecha_despacho', date('Y-m-d'))->count() }}</span>
            <span class="das-kpi-label">Despachos hoy</span>
        </div>
        <button type="button" onclick="openSolicitudes()" class="das-kpi {{ $totalActivas > 0 ? 'is-warn' : '' }}" title="Ver solicitudes por despachar">
            <span class="das-kpi-value">{{ $totalActivas }}</span>
            <span class="das-kpi-label">Solicitudes por despachar</span>
        </button>
        <button type="button" onclick="openBandeja()" class="das-kpi" title="Abrir bandeja e historial">
            <span class="das-kpi-value">{{ collect($dispatches)->count() }}</span>
            <span class="das-kpi-label">Guías en el historial</span>
        </button>
    </div>

    <!-- formulario principal de despacho -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">

        <!-- cabecera de la tarjeta del formulario -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 rounded-t-2xl flex flex-wrap items-center gap-4">
            <div class="w-10 h-10 bg-[#0f968c]/10 text-[#0f968c] rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-[16px] font-bold text-slate-800 tracking-tight">Registro de Despacho</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Complete los datos del documento, cliente, productos a
                    despachar y firmas.</p>
            </div>
            <button type="button" onclick="openSolicitudes()"
                class="das-btn das-btn-secondary">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2zM13 3v4h4"></path>
                </svg>
                Ver Solicitudes
                @if ($totalActivas > 0)
                    <span class="das-btn-count">
                        {{ $totalActivas }}
                    </span>
                @endif
                @if ($totalMias > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ $totalMias }} asignada(s) a usted
                    </span>
                @endif
            </button>
        </div>

        <form action="{{ route('dispatch.store') }}" method="POST" id="despachoForm">
            @csrf
            <input type="hidden" name="establishment_request_id" id="establishment_request_id" value="">

            <!-- Banner informativo cuando se carga una solicitud -->
            <div id="banner_solicitud_activa" class="hidden mx-6 mt-5 p-4 bg-teal-50 border border-teal-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        SOL
                    </div>
                    <div>
                        <p id="banner_solicitud_titulo" class="text-[13px] font-extrabold text-teal-900"></p>
                        <p id="banner_solicitud_subtitulo" class="text-[11.5px] text-teal-700"></p>
                    </div>
                </div>
                <button type="button" onclick="desvincularSolicitudActiva()"
                    class="text-[11.5px] font-bold text-teal-800 hover:text-rose-600 bg-white px-3 py-1.5 rounded-lg border border-teal-200 transition-colors cursor-pointer shrink-0">
                    Desvincular solicitud
                </button>
            </div>

            <!-- remitente (fijo) y correlativo en una sola línea -->
            <div class="px-6 py-3 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100">
                <p class="text-[12.5px] text-slate-600">
                    <span class="font-semibold text-slate-800">Remitente:</span>
                    I. Municipalidad de Talcahuano · Droguería Comunal Central · Blanco Encalada 450, Talcahuano
                </p>
                <label class="flex items-center gap-2 text-[12.5px] font-semibold text-slate-600">
                    N° correlativo
                    <input type="text" name="correlativo_despacho" value="{{ $correlativo }}" readonly
                        class="w-40 bg-slate-100 border border-slate-300 text-slate-800 text-[14px] font-mono font-bold rounded-lg text-center py-1.5 focus:outline-none cursor-default">
                </label>
            </div>

            <!-- seccion de destino y logistica con clasificacion -->
            <div class="p-6 bg-white border-b border-slate-100">
                <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide mb-4">1. Tipo, Destino y
                    Logística</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                    <!-- clasificacion interno/externo -->
                    <div class="md:col-span-2 min-w-0">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Clasificación del Despacho <span
                                class="text-rose-500">*</span></label>
                        <select name="clasificacion" id="clasificacion_select" onchange="toggleClasificacion()" required
                            class="w-full max-w-full truncate bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione la clasificación...</option>
                            <option value="Interno">Interno (Nivel Comunal / CESFAM)</option>
                            <option value="Externo">Externo (Préstamos, Donaciones, Devoluciones, etc.)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Salida <span
                                class="text-rose-500">*</span></label>
                        <input type="date" name="fecha_despacho" value="{{ date('Y-m-d') }}" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <div class="md:col-span-1 min-w-0">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Liberación <span
                                class="text-rose-500">*</span></label>
                        <select name="estado_liberacion" required
                            class="w-full max-w-full truncate bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="Liberada">Liberada / Aprobada</option>
                            <option value="Pendiente">Pendiente de V°B°</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino interno -->
                    <div id="div_interno" class="md:col-span-2 hidden min-w-0">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Establecimiento Destino (Interno)
                            <span class="text-rose-500">*</span></label>
                        <select id="establecimiento_interno" onchange="actualizarDireccionInterna()"
                            class="w-full max-w-full truncate bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione el Establecimiento...</option>
                            <option value="CESFAM LOS CERROS" data-direccion="Calle El Galgo, esquina Calle Coliumo, Villa Badaran, Talcahuano">CESFAM LOS CERROS</option>
                            <option value="CESFAM PAULINA AVENDAÑO PEREDA" data-direccion="Calle Carlos Dittborn 4100, Villa San Martín, Talcahuano">CESFAM PAULINA AVENDAÑO PEREDA</option>
                            <option value="CESFAM SAN VICENTE" data-direccion="Calle Brasil 360, San Vicente, Talcahuano">CESFAM SAN VICENTE</option>
                            <option value="CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN" data-direccion="Calle Manuel Barros Borgoño 2645, Las Salinas, Talcahuano">CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN</option>
                            <option value="CECOSF ESMERALDA" data-direccion="Calle San Miguel 546, Población Patricio Lynch, Talcahuano">CECOSF ESMERALDA</option>
                            <option value="CECOSF 8 DE MAYO" data-direccion="Calle Aysén 7370, Población Diego Portales, Talcahuano">CECOSF 8 DE MAYO</option>
                            <option value="CECOSF LIBERTAD-GAETE" data-direccion="Calle Isla San Ambrosio 509, Población Libertad, Talcahuano">CECOSF LIBERTAD-GAETE</option>
                            <option value="CECOSF LOS LOBOS-LA GLORIA" data-direccion="Calle Los Lobos 1960, Población Los Lobos, Talcahuano">CECOSF LOS LOBOS-LA GLORIA</option>
                            <option value="CECOSF VILLA CENTINELA" data-direccion="Calle Punta Peñablanca 1301, Población Centinela, Talcahuano">CECOSF VILLA CENTINELA</option>
                            <option value="POSTA RURAL TUMBES" data-direccion="Maryland s/n, Caleta Tumbes, Talcahuano">POSTA RURAL TUMBES</option>
                            <option value="SAR SAN VICENTE" data-direccion="Juan de la Cruz Tapia s/n, Talcahuano">SAR SAN VICENTE</option>
                            <option value="SAR LOS CERROS" data-direccion="Calle Coliumo S/N Nueva los Lobos, Talcahuano">SAR LOS CERROS</option>
                            <option value="UAPO" data-direccion="Calle Blanco Encalada 1142, Talcahuano">UAPO</option>
                            <option value="Dental - CESFAM SAN VICENTE" data-direccion="Brasil 360, Talcahuano">Dental - CESFAM SAN VICENTE</option>
                            <option value="Laboratorio - CESFAM SAN VICENTE" data-direccion="Brasil 360, Talcahuano">Laboratorio - CESFAM SAN VICENTE</option>
                            <option value="MÓDULO DENTAL JUNAEB" data-direccion="Calle Blanco Encalada 455, Talcahuano">MÓDULO DENTAL JUNAEB</option>
                            <option value="MÓVIL: LICEO POLIVALENTE Y LICEO COMERCIAL" data-direccion="Unidad Móvil Salud Escolar, Talcahuano">MÓVIL: LICEO POLIVALENTE Y LICEO COMERCIAL</option>
                            <option value="MÓVIL: LICEO INDUSTRIAL Y LICEO ASUNCIÓN" data-direccion="Unidad Móvil Salud Escolar, Talcahuano">MÓVIL: LICEO INDUSTRIAL Y LICEO ASUNCIÓN</option>
                            <option value="MÓVIL: LICEO A-21" data-direccion="Unidad Móvil Salud Escolar, Talcahuano">MÓVIL: LICEO A-21</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino externo -->
                    <div id="div_externo" class="md:col-span-2 hidden min-w-0">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Tipo y Destino Externo <span
                                class="text-rose-500">*</span></label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="tipo_externo"
                                class="w-full sm:w-2/5 max-w-full truncate bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                                <option value="" disabled selected>Tipo...</option>
                                <option value="Traspaso">Traspaso</option>
                                <option value="Prestamo">Préstamo</option>
                                <option value="Donacion">Donación</option>
                                <option value="Devolucion">Devolución a Proveedor</option>
                                <option value="Merma">Merma / Destrucción</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <input type="text" id="establecimiento_externo"
                                placeholder="Nombre institución/destino..."
                                class="w-full sm:w-3/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Dirección Destino</label>
                        <input type="text" name="direccion_destino" id="direccion_destino"
                            placeholder="Esperando clasificación..." readonly
                            class="w-full bg-slate-50 border border-slate-300 text-slate-500 font-medium rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-inner transition-all">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Condiciones Transporte y
                            Observaciones</label>
                        <input type="text" name="condiciones_transporte"
                            placeholder="Ej: Cadena de frío (2 °C a 8 °C). Referencia solicitud interna #342."
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <!-- traspaso interno: despacho a la red sin solicitud previa -->
                    <div id="div_motivo_traspaso" class="md:col-span-4 hidden">
                        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                            <label for="motivo_traspaso" class="block text-[12px] font-bold text-amber-900 mb-1">Motivo del traspaso interno <span class="text-rose-500">*</span></label>
                            <p class="text-[11.5px] text-amber-800 mb-2">Este despacho no viene de una solicitud. Indique el motivo. Si quien recibe no está presente, puede firmar después desde la bandeja de despachos y la guía quedará como «Pendiente de recepción».</p>
                            <input type="text" name="motivo_traspaso" id="motivo_traspaso" maxlength="500"
                                placeholder="Ej: Reposición urgente por quiebre de stock."
                                class="w-full bg-white border border-amber-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- seccion de tabla de productos y stock -->
            <div class="p-6 bg-slate-50/40 border-b border-slate-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <div>
                        <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide flex items-center gap-2">
                            2. Detalle de Extracción (Lotes en Stock)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200 lowercase">criterio fefo</span>
                        </h3>
                        <p class="text-[11.5px] text-slate-500 mt-0.5">Seleccione el producto o escanee el QR del lote para rebajar stock de bodega central.</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="openScanner()"
                            class="px-3.5 py-2 text-white rounded-xl text-[12px] font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer"
                            style="background-color: #0f968c !important; color: #ffffff !important;">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h2m4 0h2m4 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-4 0h-2m-4 0H5a2 2 0 01-2-2v-2m4-10h2v2H7V7zm8 0h2v2h-2V7zM7 15h2v2H7v-2zm8-2h2v4h-2v-4zm-4-2h2v2h-2v-2z">
                                </path>
                            </svg>
                            Escanear QR
                        </button>
                        <button type="button" onclick="addRow()"
                            class="px-3.5 py-2 bg-white text-slate-700 hover:text-teal-700 border border-slate-300 hover:border-teal-400 rounded-xl text-[12px] font-bold hover:bg-teal-50/40 transition-colors flex items-center gap-2 shadow-xs cursor-pointer">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Añadir Fila
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-xs bg-white">
                    <table class="w-full min-w-[780px] table-fixed text-left text-[13px] whitespace-nowrap" id="productsTable">
                        <thead
                            class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-extrabold text-[10.5px]">
                            <tr>
                                <th class="px-4 py-3.5 w-[36%]">Producto en Stock</th>
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
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                    <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">3. Firmas de Respaldo</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- firma despacha (bodega central) -->
                    <div class="flex flex-col items-center">
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma de quien despacha (Bodega/DAS)</p>
                        <input type="hidden" name="firma_despacha" id="firma_despacha_input">
                        <div
                            class="border-2 border-dashed border-slate-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_despacha" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>
                        <button type="button" onclick="signatureDespacha.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar
                            Firma de Despacho</button>
                    </div>

                    <!-- firma solicita/recibe (establecimiento) -->
                    <div class="flex flex-col items-center">
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma de quien solicita / recibe
                            (Establecimiento)</p>
                        <input type="hidden" name="firma_recibe" id="firma_recibe_input">
                        <div
                            class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_recibe" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>
                        <button type="button" onclick="signatureRecibe.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar
                            Firma de Recepción</button>
                        <p id="nota_firma_recibe_traspaso" class="hidden mt-1 text-[11px] text-amber-700 font-semibold text-center">Opcional en un traspaso interno: si no firma ahora, la guía queda «Pendiente de recepción».</p>
                    </div>
                </div>
            </div>

            <!-- barra inferior fija: resumen del despacho + acciones (siempre visible al bajar) -->
            <div class="das-summary mx-4 mb-4">
                <div class="das-summary-items" id="das_resumen_items" aria-live="polite"></div>
                <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="btn_limpiar_form"
                    class="das-btn das-btn-ghost">
                    Limpiar Formulario
                </button>
                <button type="submit" name="action" value="save_and_download"
                    class="das-btn das-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Guardar y Descargar PDF
                </button>
                <button type="submit" name="action" value="save_only"
                    class="das-btn das-btn-primary" title="Atajo: Ctrl + Enter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirmar Despacho y Registrarlo
                </button>
            </div>
            </div>
        </form>
    </div>

    <!-- resumen en vivo de la barra inferior + atajo Ctrl+Enter -->
    <script>
        (function () {
            const form = document.getElementById('despachoForm');
            const salida = document.getElementById('das_resumen_items');
            if (!form || !salida) return;
            const esc = (t) => String(t).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'})[c]);
            const visible = (el) => el && el.offsetParent !== null;
            function destino() {
                const interno = document.getElementById('establecimiento_interno');
                const externo = document.getElementById('establecimiento_externo');
                if (visible(interno) && interno.value) return interno.options[interno.selectedIndex]?.text || interno.value;
                if (visible(externo) && externo.value) return externo.value;
                return '';
            }
            function firmaLista(pad, inputId) {
                const input = document.getElementById(inputId);
                return !!((pad && !pad.isEmpty()) || (input && input.value));
            }
            function render() {
                const productos = [...form.querySelectorAll('select[name="productos[]"]')].filter(s => s.value).length;
                const unidades = [...form.querySelectorAll('input[name="cantidades[]"]')].reduce((a, i) => a + (parseInt(i.value) || 0), 0);
                const d = destino().trim();
                const fd = firmaLista(typeof signatureDespacha !== 'undefined' ? signatureDespacha : null, 'firma_despacha_input');
                const fr = firmaLista(typeof signatureRecibe !== 'undefined' ? signatureRecibe : null, 'firma_recibe_input');
                const recibeOpcional = typeof esModoTraspasoInterno === 'function' && esModoTraspasoInterno();
                salida.innerHTML =
                    `<span class="${d ? 'is-done' : 'is-pending'}">Destino: <b>${d ? esc(d) : 'sin elegir'}</b></span>`
                    + `<span>Productos: <b>${productos}</b></span>`
                    + `<span>Unidades: <b>${unidades.toLocaleString('es-CL')}</b></span>`
                    + `<span class="${fd ? 'is-done' : 'is-pending'}">Firma despacha: <b>${fd ? 'lista' : 'pendiente'}</b></span>`
                    + `<span class="${fr ? 'is-done' : (recibeOpcional ? '' : 'is-pending')}">Firma recibe: <b>${fr ? 'lista' : (recibeOpcional ? 'opcional' : 'pendiente')}</b></span>`;
            }
            ['input', 'change'].forEach(ev => form.addEventListener(ev, render));
            form.addEventListener('pointerup', () => setTimeout(render, 60));
            setInterval(render, 1500); // también refleja lo que se carga desde una solicitud o por FEFO
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    const btn = form.querySelector('button[type="submit"][value="save_only"]');
                    if (btn) { e.preventDefault(); btn.click(); }
                }
            });
            render();
        })();
    </script>

    <!-- plantilla oculta para generar nuevas filas de productos -->
    <template id="rowTemplate">
        <tr class="bg-white hover:bg-slate-50/80 transition-colors group">
            <td data-label="Producto" class="col-producto px-3.5 py-2.5 border-r border-slate-100 align-middle max-w-0">
                <select name="productos[]" required onchange="llenarDatosFila(this)"
                    class="w-full max-w-full truncate bg-white border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/10 rounded-lg px-2.5 py-2 text-[12.5px] font-bold text-slate-800 outline-none shadow-xs cursor-pointer transition-colors">
                    <option value="" disabled selected>Seleccione producto del inventario...</option>
                    @foreach ($inventarioDisponible as $inv)
                        @php
                            $nomInv = trim(preg_replace('/\s+/', ' ', $inv->producto ?? ''));
                            $loteInv = trim(preg_replace('/\s+/', ' ', $inv->lote ?? '')) ?: 'S/L';
                            $cantInv = (int) ($inv->cantidad ?? 0);
                            $progInv = trim(preg_replace('/\s+/', ' ', $inv->programa ?? ''));
                        @endphp
                        @if($nomInv !== '')
                            <option value="{{ $nomInv }}" title="{{ $nomInv }} - Lote: {{ $loteInv }} (Disp: {{ $cantInv }})" data-nombre-completo="{{ $nomInv }}" data-lote="{{ $loteInv }}" data-vence="{{ $inv->fecha_vencimiento ?? '' }}" data-stock="{{ $cantInv }}" data-programa="{{ $progInv }}">{{ \Illuminate\Support\Str::limit($nomInv, 62) }} - Lote: {{ $loteInv }} (Disp: {{ $cantInv }})</option>
                        @endif
                    @endforeach
                </select>
                <div class="producto-nombre-movil"></div>
            </td>
            <td data-label="Lote" class="col-lote px-3 py-2.5 border-r border-slate-100 align-middle">
                <input type="text" name="lotes[]" readonly placeholder="Auto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] font-mono font-bold text-slate-600 outline-none shadow-inner cursor-not-allowed text-center">
            </td>
            <td data-label="Vencimiento (FEFO)" class="col-vence px-3 py-2.5 border-r border-slate-100 align-middle relative">
                <input type="date" name="vencimientos[]" readonly
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] text-slate-600 outline-none shadow-inner cursor-not-allowed">
                <div class="fefo-container"></div>
            </td>
            <td data-label="Programa" class="col-programa px-3 py-2.5 border-r border-slate-100 align-middle max-w-0">
                <select name="programas[]" required
                    class="w-full max-w-full truncate bg-white border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/10 rounded-lg px-2.5 py-2 text-[12px] font-semibold text-slate-700 outline-none shadow-xs cursor-pointer transition-colors">
                    <option value="" selected>Seleccione programa...</option>
                    @foreach ($programasLista ?? [] as $idxProg => $progOpt)
                        @php $progLimpio = trim(preg_replace('/\s+/', ' ', $progOpt ?? '')); @endphp
                        @if($progLimpio !== '')
                            <option value="{{ $progLimpio }}">{{ $progLimpio }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td data-label="Cantidad" class="col-cantidad px-3 py-2.5 border-r border-slate-100 align-middle">
                <div class="space-y-1">
                    <input type="number" name="cantidades[]" placeholder="0" min="1" oninput="validarStock(this)"
                        required
                        class="w-full bg-white border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 rounded-lg px-2.5 py-2 text-[14px] font-black text-center text-slate-900 outline-none shadow-xs transition-colors">
                    <div
                        class="stock-container text-center text-[10px] font-bold text-teal-700 hidden leading-tight">
                        Disp: <span class="stock-max-badge font-mono font-black">0</span> uds.
                    </div>
                </div>
            </td>
            <td data-label="" class="col-quitar px-3 py-2.5 text-center align-middle">
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

    <!-- ventana modal para visualizar solicitudes pendientes -->
    <div id="SolicitudesModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-7xl max-h-[92vh] flex flex-col overflow-hidden my-auto">

            <!-- cabecera del modal -->
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Solicitudes de Establecimientos y CESFAM</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">
                        Gestión, asignación a trabajadores y despacho de solicitudes
                    </p>
                </div>

                <button type="button" onclick="closeSolicitudes()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- buscador y filtros avanzados de solicitudes -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Buscar solicitud
                        </label>
                        <input type="text" id="filtroSolicitudTexto" oninput="filtrarSolicitudesModal()"
                            placeholder="Establecimiento, N° solicitud, responsable o producto..."
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm">
                    </div>

                    <div class="min-w-0">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Tipo Origen
                        </label>
                        <select id="filtroSolicitudTipo" onchange="filtrarSolicitudesModal()"
                            class="w-full max-w-full truncate py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todos (CESFAM y Establec.)</option>
                            <option value="cesfam">Solo CESFAM</option>
                            <option value="establecimiento">Solo Establecimientos</option>
                        </select>
                    </div>

                    <div class="min-w-0">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Estado
                        </label>
                        <select id="filtroSolicitudEstado" onchange="filtrarSolicitudesModal()"
                            class="w-full max-w-full truncate py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="activas" selected>Activas (Por despachar)</option>
                            <option value="">Todas (Incluye Despachadas)</option>
                            <option value="pendiente">Pendiente (Sin asignar)</option>
                            <option value="asignada">Asignada</option>
                            <option value="parcial">Parcial</option>
                            <option value="despachado">Despachado</option>
                        </select>
                    </div>

                    <div class="min-w-0">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Asignación
                        </label>
                        <select id="filtroSolicitudAsignacion" onchange="filtrarSolicitudesModal()"
                            class="w-full max-w-full truncate py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todas</option>
                            <option value="mias">Asignadas a mí ({{ $totalMias }})</option>
                            <option value="sin_asignar">Sin asignar</option>
                            <option value="asignadas">Con trabajador asignado</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- listado de solicitudes -->
            <div class="overflow-x-auto max-h-[60vh] bg-white">
                <table class="w-full text-left text-[13px] whitespace-nowrap">
                    <thead
                        class="bg-white text-slate-500 uppercase text-[11px] font-extrabold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                        <tr>
                            <th class="px-4 py-4 w-12 text-center">Detalle</th>
                            <th class="px-4 py-4">Solicitud</th>
                            <th class="px-4 py-4">Establecimiento / Origen</th>
                            <th class="px-4 py-4">Fecha</th>
                            <th class="px-4 py-4 text-center">Productos</th>
                            <th class="px-4 py-4 text-center">Estado</th>
                            <th class="px-4 py-4">Asignar Trabajador (Despacho)</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100" id="tablaSolicitudesBody">

                        @forelse($solicitudes as $solicitud)

                            @php
                                $solicitudKey = 'solicitud-' . $solicitud->id;
                                $esCesfamSol = \App\Services\InventoryService::esCesfam($solicitud->establecimiento);
                                $esMia = (int) ($solicitud->assigned_user_id ?? 0) === (int) auth()->id();
                                $nombresProdSol = $solicitud->items->map(fn($i) => $i->product->nombre ?? '')->implode(' ');
                                $textoBusqueda = mb_strtolower(
                                    $solicitud->numero_solicitud . ' ' .
                                    $solicitud->establecimiento . ' ' .
                                    ($solicitud->responsable ?? '') . ' ' .
                                    ($solicitud->assignedUser->name ?? '') . ' ' .
                                    $nombresProdSol
                                );
                                $totalItemsSol = $solicitud->items->count();
                                $completadosItemsSol = $solicitud->items->filter(fn($i) => $i->cantidad_despachada >= $i->cantidad_solicitada)->count();
                                $tieneItemsControlados = $solicitud->items->contains(fn($i) => \App\Services\InventoryService::esControlado($i->product->nombre ?? ''));
                                $tieneItemsNormales = $solicitud->items->contains(fn($i) => !\App\Services\InventoryService::esControlado($i->product->nombre ?? ''));
                                $esMixta = $tieneItemsControlados && $tieneItemsNormales;
                            @endphp

                            <!-- solicitud -->
                            <tr class="fila-solicitud-modal bg-white hover:bg-slate-50 transition-colors cursor-pointer group {{ $esMia ? 'bg-teal-50/30' : '' }}"
                                data-key="{{ $solicitudKey }}"
                                data-search="{{ $textoBusqueda }}"
                                data-tipo="{{ $esCesfamSol ? 'cesfam' : 'establecimiento' }}"
                                data-estado="{{ mb_strtolower($solicitud->estado) }}"
                                data-assigned-id="{{ $solicitud->assigned_user_id ?? '' }}"
                                data-es-mia="{{ $esMia ? '1' : '0' }}"
                                onclick="toggleSolicitud('{{ $solicitudKey }}')">

                                <td class="px-4 py-4 text-center">
                                    <button type="button"
                                        class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200">
                                        <svg class="w-4 h-4 transform transition-transform duration-300"
                                            id="arrow-{{ $solicitudKey }}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7">
                                            </path>
                                        </svg>
                                    </button>
                                </td>

                                <td class="px-4 py-4 font-mono font-extrabold text-slate-800">
                                    {{ $solicitud->numero_solicitud }}
                                    @if ($esMixta)
                                        <span class="block mt-1">
                                            <span class="das-badge-mixta-amber inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9.5px] font-extrabold bg-gradient-to-r from-teal-50 to-amber-50 text-amber-800 border border-amber-200" title="Contiene productos normales y fármacos controlados">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Mixta
                                            </span>
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">{{ $solicitud->establecimiento }}</span>
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase {{ $esCesfamSol ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                            {{ $esCesfamSol ? 'CESFAM' : 'Establecimiento' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Solicita: {{ $solicitud->responsable }} {{ $solicitud->cargo ? '('.$solicitud->cargo.')' : '' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 font-medium text-slate-600">
                                    {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d-m-Y') }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <span class="font-extrabold text-slate-800">{{ $totalItemsSol }}</span>
                                    <span class="block text-[10px] text-slate-400 font-semibold">
                                        {{ $completadosItemsSol }}/{{ $totalItemsSol }} compl.
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @php
                                        $estadoClase = match($solicitud->estado) {
                                            'Pendiente' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'Asignada' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                            'Parcial' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'Despachado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span id="badge-estado-solicitud-{{ $solicitud->id }}"
                                        class="{{ $estadoClase }} border px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase inline-block">
                                        {{ $solicitud->estado }}
                                    </span>
                                </td>

                                <td class="px-4 py-4" onclick="event.stopPropagation()">
                                    @role('Administrador')
                                        <div class="flex items-center gap-2">
                                            <select
                                                onfocus="this.dataset.prevVal = this.value"
                                                onchange="asignarSolicitudUsuario({{ $solicitud->id }}, this.value, this)"
                                                class="bg-slate-50 hover:bg-white border border-slate-300 text-slate-800 text-[12px] font-semibold rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 cursor-pointer min-w-[185px] max-w-[230px] truncate">
                                                <option value="">— Sin asignar —</option>
                                                @foreach ($usuariosAsignables ?? [] as $usr)
                                                    @php $nomUsr = trim(($usr->name ?? '') . ' ' . ($usr->last_name ?? '')); @endphp
                                                    @if($nomUsr !== '')
                                                        <option value="{{ $usr->id }}" {{ (int) $solicitud->assigned_user_id === (int) $usr->id ? 'selected' : '' }}>{{ $nomUsr }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <span id="assign-spinner-{{ $solicitud->id }}" class="hidden text-[11px] font-bold text-teal-600">
                                                Guardando...
                                            </span>
                                        </div>
                                    @else
                                        @if ($esMia)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                                                Asignada a usted
                                            </span>
                                        @elseif ($solicitud->assignedUser)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $solicitud->assignedUser->name }} {{ $solicitud->assignedUser->last_name }}
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Sin asignar</span>
                                        @endif
                                    @endrole
                                </td>

                            </tr>


                            <!-- detalle de la solicitud -->
                            <tr id="{{ $solicitudKey }}" class="hidden bg-slate-100/50 shadow-inner">

                                <td colspan="7" class="p-0 border-b border-slate-300">

                                    <div class="py-4 pr-6 pl-14">

                                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                                            <!-- cabecera del detalle -->
                                            <div
                                                class="px-4 py-3 bg-slate-700 text-white flex flex-col sm:flex-row justify-between sm:items-center gap-2">

                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-teal-300">
                                                        Detalle de Solicitud {{ $solicitud->numero_solicitud }}
                                                    </span>

                                                    <p class="text-[12px] font-bold text-white mt-0.5">
                                                        {{ $solicitud->establecimiento }}
                                                        @if ($solicitud->observaciones)
                                                            <span class="font-normal text-slate-300"> · Obs: {{ $solicitud->observaciones }}</span>
                                                        @endif
                                                    </p>
                                                </div>

                                                @if ($solicitud->estado !== 'Despachado')
                                                    <button type="button" onclick="usarSolicitud({{ $solicitud->id }})"
                                                        class="px-3.5 py-1.5 bg-[#0f968c] hover:bg-teal-700 text-white rounded-lg text-[11.5px] font-bold transition-colors cursor-pointer shrink-0">
                                                        Usar Solicitud para Despacho
                                                    </button>
                                                @else
                                                    <span class="px-3 py-1 bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 rounded-lg text-[11px] font-bold">
                                                        Solicitud Despachada Completamente
                                                    </span>
                                                @endif

                                            </div>


                                            <!-- tabla de productos -->
                                            <table class="w-full text-left text-[12px] whitespace-nowrap">

                                                <thead
                                                    class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200 text-[10px]">

                                                    <tr>
                                                        <th class="px-4 py-3">Producto / Programa</th>
                                                        <th class="px-4 py-3 text-center">Consumo mensual anterior</th>
                                                        <th class="px-4 py-3 text-center">Stock Actual del establecimiento</th>
                                                        <th class="px-4 py-3 text-center">Solicitado</th>
                                                        <th class="px-4 py-3 text-center">Despachado / Pendiente</th>
                                                    </tr>

                                                </thead>


                                                <tbody class="divide-y divide-slate-100 text-slate-700">

                                                    @foreach ($solicitud->items as $item)
                                                        @php
                                                            $nombreProdItem = $item->product->nombre ?? ('Producto #' . $item->product_id);
                                                            $esItemControlado = in_array($nombreProdItem, $controlados ?? []);
                                                            $pendienteItem = max(0, (int) $item->cantidad_solicitada - (int) ($item->cantidad_despachada ?? 0));
                                                        @endphp
                                                        <tr class="hover:bg-slate-50">

                                                            <td class="px-4 py-3">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="font-bold text-slate-800">{{ $nombreProdItem }}</span>
                                                                    @if ($item->programa)
                                                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                                                            {{ $item->programa }}
                                                                        </span>
                                                                    @endif
                                                                    @if ($esItemControlado)
                                                                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[10px] font-extrabold border border-rose-200">
                                                                            Estupefaciente (Despacho Controlado)
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>

                                                            <td class="px-4 py-3 text-center font-bold text-slate-700">
                                                                {{ number_format((int) ($item->consumo_anterior ?? 0), 0, ',', '.') }} uds.
                                                            </td>

                                                            <td class="px-4 py-3 text-center font-extrabold text-blue-700">
                                                                {{ number_format((int) ($item->stock_actual ?? 0), 0, ',', '.') }} uds.
                                                            </td>

                                                            <td class="px-4 py-3 text-center font-extrabold text-teal-700">
                                                                {{ number_format((int) $item->cantidad_solicitada, 0, ',', '.') }} uds.
                                                            </td>

                                                            <td class="px-4 py-3 text-center">
                                                                @if ($pendienteItem === 0)
                                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-extrabold text-[11px] border border-emerald-200">
                                                                        {{ number_format((int) $item->cantidad_despachada, 0, ',', '.') }} desp. (Completo)
                                                                    </span>
                                                                @else
                                                                    <span class="font-bold text-slate-600">{{ number_format((int) $item->cantidad_despachada, 0, ',', '.') }} desp.</span>
                                                                    <span class="ml-1 px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-extrabold text-[11px] border border-amber-200">
                                                                        Faltan {{ number_format($pendienteItem, 0, ',', '.') }} uds.
                                                                    </span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-16 text-center">

                                    <div class="flex flex-col items-center justify-center text-slate-400">

                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">

                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2z">
                                                </path>

                                            </svg>

                                        </div>

                                        <p class="text-[15px] font-bold text-slate-600">
                                            No hay solicitudes pendientes
                                        </p>

                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">
                                            No existen solicitudes de establecimientos pendientes de despacho.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeSolicitudes()"
                    class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                    Cerrar Solicitudes
                </button>
            </div>

        </div>
    </div>

    <!-- ventana modal para el historial de despachos emitidos con buscador y multiples filtros -->
    <div id="BandejaModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden my-auto">

            <!-- cabecera de modal historico -->
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Bandeja Histórica de Despachos</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">Trazabilidad de guías
                        emitidas y stock rebajado</p>
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
                    <!-- filtro correlativo -->
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Correlativo</label>
                        <input type="text" id="filtroCorrelativo" list="sugerencias-correlativos"
                            oninput="filtrarBandeja()" placeholder="Ej: 123"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm">
                        <datalist id="sugerencias-correlativos">
                            @foreach ($dispatches as $despacho)
                                <option value="{{ $despacho->correlativo }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- filtro fecha -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fecha
                            Emisión</label>
                        <input type="date" id="filtroFecha" oninput="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                    </div>

                    <!-- filtro clasificacion -->
                    <div class="min-w-0">
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Clasificación</label>
                        <select id="filtroClasificacion" onchange="filtrarBandeja()"
                            class="w-full max-w-full truncate py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todas</option>
                            <option value="interno">Interno</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>

                    <!-- filtro destino -->
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Destino</label>
                        <input type="text" id="filtroDestino" list="sugerencias-destinos" oninput="filtrarBandeja()"
                            placeholder="Ej: CESFAM"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm">
                        <datalist id="sugerencias-destinos">
                            <option value="CESFAM LOS CERROS">
                            <option value="CESFAM PAULINA AVENDAÑO PEREDA">
                            <option value="CESFAM SAN VICENTE">
                            <option value="CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN">
                            <option value="CECOSF ESMERALDA">
                            <option value="CECOSF 8 DE MAYO">
                            <option value="CECOSF LIBERTAD-GAETE">
                            <option value="CECOSF LOS LOBOS-LA GLORIA">
                            <option value="CECOSF VILLA CENTINELA">
                            <option value="POSTA RURAL TUMBES">
                            <option value="SAR SAN VICENTE">
                            <option value="SAR LOS CERROS">
                            <option value="UAPO">
                            <option value="Dental - CESFAM SAN VICENTE">
                            <option value="Laboratorio - CESFAM SAN VICENTE">
                            <option value="MÓDULO DENTAL JUNAEB">
                            <option value="MÓVIL: LICEO POLIVALENTE Y LICEO COMERCIAL">
                            <option value="MÓVIL: LICEO INDUSTRIAL Y LICEO ASUNCIÓN">
                            <option value="MÓVIL: LICEO A-21">
                        </datalist>
                    </div>

                    <!-- filtro estado -->
                    <div class="min-w-0">
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estado</label>
                        <select id="filtroEstadoBandeja" onchange="filtrarBandeja()"
                            class="w-full max-w-full truncate py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
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
                            <!-- clases identificadoras para el filtrado -->
                            <tr class="bg-white hover:bg-slate-50 transition-colors cursor-pointer group fila-despacho"
                                data-key="{{ $dKey }}" onclick="toggleItems('{{ $dKey }}')">
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200"
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
                                    <!-- badge de clasificacion -->
                                    @if (strtolower($despacho->clasificacion) == 'interno')
                                        <span
                                            class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded text-[10px] uppercase">Interno</span>
                                        @if ($despacho->esTraspasoInterno())
                                            <span
                                                class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded text-[10px] uppercase">Traspaso</span>
                                        @endif
                                    @else
                                        <span
                                            class="bg-purple-50 text-purple-700 border border-purple-200 px-2 py-1 rounded text-[10px] uppercase">{{ $despacho->clasificacion ?? 'Externo' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $despacho->establecimiento_destino }}
                                    @if ($despacho->esTraspasoInterno())
                                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 whitespace-normal">Motivo: {{ $despacho->motivo_traspaso }}</p>
                                    @endif
                                    @if ($despacho->pendienteRecepcion())
                                        <span class="inline-block mt-1 bg-amber-100 text-amber-800 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase">Pendiente de recepción</span>
                                    @endif
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
                                    <a href="{{ route('dispatch.pdf', $despacho->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 rounded-lg text-[11px] font-bold transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        VER PDF
                                    </a>
                                    @if ($despacho->pendienteRecepcion())
                                        <button type="button"
                                            onclick="registrarRecepcion({{ $despacho->id }}, @js($despacho->correlativo))"
                                            class="mt-1.5 inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 hover:border-amber-600 rounded-lg text-[11px] font-bold transition-colors cursor-pointer">
                                            REGISTRAR RECEPCIÓN
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            <!-- detalle de articulos y firmas oculto -->
                            <tr id="items-{{ $dKey }}" class="hidden bg-slate-100/50 shadow-inner">
                                <td colspan="7" class="p-0 border-b border-slate-300">
                                    <div class="py-4 pr-6 pl-14">

                                        <!-- tabla de productos rebajados -->
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
                                                    Productos Rebajados en esta Guía
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
                                                    @if ($despacho->esTraspasoInterno() && $despacho->fecha_recepcion)
                                                        <p class="text-[10px] text-slate-400 mt-1">Recibido el {{ $despacho->fecha_recepcion->format('d-m-Y H:i') }}</p>
                                                    @endif
                                                @elseif ($despacho->pendienteRecepcion())
                                                    <p class="text-xs text-amber-700 font-semibold py-2">Pendiente de recepción</p>
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

    <!-- modal del escaner QR -->
    <div id="scannerModal" class="hidden fixed inset-0 z-[100] !m-0 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-2 sm:p-4 transition-all">
        <div class="flex min-h-full items-center justify-center">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
                <!-- cabecera del modal -->
                <div class="px-5 py-4 bg-slate-800 text-white flex justify-between items-center shrink-0 border-b border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-white tracking-wide leading-tight">
                                Escanear Etiqueta QR
                            </h2>
                            <p class="text-[11px] text-teal-300 font-medium">Validación automática de Stock</p>
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
                            <div class="absolute top-0 left-0 w-5 h-5 border-t-[3px] border-l-[3px] border-teal-400 rounded-tl-md"></div>
                            <div class="absolute top-0 right-0 w-5 h-5 border-t-[3px] border-r-[3px] border-teal-400 rounded-tr-md"></div>
                            <div class="absolute bottom-0 left-0 w-5 h-5 border-b-[3px] border-l-[3px] border-teal-400 rounded-bl-md"></div>
                            <div class="absolute bottom-0 right-0 w-5 h-5 border-b-[3px] border-r-[3px] border-teal-400 rounded-br-md"></div>

                            <div class="scan-laser-bar"></div>
                        </div>

                        <div id="scannerLoadingState" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-4 text-center z-10 transition-opacity">
                            <div class="w-8 h-8 border-3 border-teal-400 border-t-transparent rounded-full animate-spin mb-2.5"></div>
                            <p class="text-[13px] font-bold text-white">Iniciando sensor de cámara...</p>
                            <p class="text-[11px] text-slate-300 mt-0.5">Conceda permiso de acceso a la cámara en el navegador.</p>
                        </div>
                    </div>

                    <div class="w-full max-w-[300px] sm:max-w-[320px] flex items-center justify-between mt-3.5 px-0.5">
                        <div class="flex items-center gap-2 text-[12px] text-slate-600 font-medium">
                            <span class="inline-block w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                            <span id="scannerStatusTxt">Buscando código QR...</span>
                        </div>

                        <button type="button" id="btnSwitchCamera" onclick="switchScannerCamera()"
                            class="hidden px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-[11px] font-bold border border-slate-300 transition-colors items-center gap-1.5 cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Cambiar Cámara
                        </button>
                    </div>

                    <p class="text-[11.5px] text-slate-500 mt-3 text-center leading-relaxed max-w-sm">
                        Apunte la cámara a la etiqueta del producto. El sistema leerá el Lote, seleccionará el producto en la tabla y establecerá el tope máximo de extracción.
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

    <!-- librerias externas -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <!-- avisos con SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // instancias de firmas para que sean globales en la vista
        let signatureDespacha = null;
        let signatureRecibe = null;
        const solicitudes = @json($solicitudes);
        const listaControlados = @json($controlados ?? []);

        const esControladoJS = (nombre) => listaControlados.some(c => c.toLowerCase() === String(nombre ?? '').trim()
            .toLowerCase());

        // Normaliza nombres (sin mayúsculas, tildes ni símbolos) para comparar productos
        const normTextDespacho = s => (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, ' ').replace(/\s+/g, ' ').trim();

        // Unidades que faltan por despachar de la solicitud cargada, por producto (clave = nombre normalizado)
        let pendientesSolicitudActiva = {};

        function obtenerNombreCompletoOpcionDespacho(opt) {
            if (!opt) return '';
            return (opt.getAttribute('data-nombre-completo') || opt.value || '').trim();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // inicializar pads de firmas si existen
            const padDespachaElem = document.getElementById('pad_despacha');
            const padRecibeElem = document.getElementById('pad_recibe');
            const padOptions = {
                penColor: 'rgb(15, 23, 42)',
                backgroundColor: 'rgba(255, 255, 255, 1)'
            };

            if (padDespachaElem) signatureDespacha = new SignaturePad(padDespachaElem, padOptions);
            if (padRecibeElem) signatureRecibe = new SignaturePad(padRecibeElem, padOptions);

            // asignacion de firmas a los inputs ocultos antes del submit con confirmación SweetAlert2 y AJAX
            const form = document.getElementById('despachoForm');
            let despachoSubmitAction = 'save_only';

            document.querySelectorAll('#despachoForm button[type="submit"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    despachoSubmitAction = this.value || 'save_only';
                });
            });

            function triggerSilentDownload(pdfUrl) {
                if (!pdfUrl) return;
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = pdfUrl;
                document.body.appendChild(iframe);
                setTimeout(() => {
                    if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
                }, 60000);
            }

            function showDispatchSuccessModal(dispData) {
                const htmlDetail = `
                    <div class="das-swal-container text-left text-xs text-slate-700">
                        <div class="das-swal-summary p-3.5 bg-slate-50 border border-slate-200 rounded-xl mb-3.5 space-y-1.5">
                            <div class="das-swal-summary-header flex items-center justify-between pb-2 mb-2 border-b border-slate-200">
                                <span class="das-swal-summary-title font-bold text-slate-900 text-sm">Guía N° ${dispData.correlativo}</span>
                                <span class="das-swal-badge-ok px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">DESPACHO CONFORME</span>
                            </div>
                            <p class="das-swal-meta"><strong>Destino:</strong> ${dispData.destino}</p>
                            <p class="das-swal-meta"><strong>Clasificación:</strong> ${dispData.clasificacion}${dispData.es_traspaso ? ' (traspaso interno)' : ''}</p>
                            ${dispData.pendiente_recepcion ? '<p class="das-swal-meta font-semibold text-amber-700">Pendiente de recepción: quien recibe debe firmar desde la bandeja de despachos.</p>' : ''}
                            <p class="das-swal-meta"><strong>Fecha de Emisión:</strong> ${dispData.fecha}</p>
                            <p class="das-swal-meta"><strong>Total Unidades Despachadas:</strong> ${Number(dispData.total_unidades || 0).toLocaleString('es-CL')} uds. (${dispData.total_items} producto${dispData.total_items === 1 ? '' : 's'})</p>
                        </div>
                        <p class="das-swal-section-title font-bold text-slate-700 mb-1.5 text-[11px] uppercase tracking-wide">Detalle de Productos Despachados:</p>
                        <div class="das-swal-table-wrap max-h-56 overflow-y-auto border border-slate-200 rounded-lg shadow-inner">
                            <table class="das-swal-table w-full text-[11.5px] text-left">
                                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 border-b border-slate-200">
                                    <tr>
                                        <th class="p-2.5">Producto</th>
                                        <th class="p-2.5 text-center">Lote FEFO</th>
                                        <th class="p-2.5 text-center">Vencimiento</th>
                                        <th class="p-2.5 text-right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    ${(dispData.items || []).map(it => `
                                        <tr class="hover:bg-slate-50">
                                            <td class="p-2.5 font-medium text-slate-800">${it.producto}</td>
                                            <td class="p-2.5 text-center font-mono text-[11px] font-semibold text-slate-700">${it.lote}</td>
                                            <td class="p-2.5 text-center text-slate-600">${it.vencimiento}</td>
                                            <td class="p-2.5 text-right font-bold text-emerald-700 das-swal-qty">${Number(it.cantidad).toLocaleString('es-CL')} u.</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                if (dispData.auto_download && dispData.pdf_url) {
                    triggerSilentDownload(dispData.pdf_url);
                }

                if (dispData.tiene_controlados_pendientes && dispData.url_despacho_controlado) {
                    Swal.fire({
                        title: '¡Despacho Normal Emitido!',
                        html: `
                            <div class="das-swal-container text-left text-xs text-slate-700">
                                ${htmlDetail}
                                <div class="das-swal-mixed-banner mt-4 p-3.5 bg-gradient-to-r from-amber-50 to-rose-50 border border-amber-300 rounded-xl flex items-start gap-3 shadow-xs">
                                    <div class="das-swal-mixed-icon w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs font-extrabold text-sm">!</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="das-swal-mixed-title font-bold text-amber-950 text-[13px]">Solicitud Mixta: Fármacos Controlados Pendientes</p>
                                        <p class="das-swal-mixed-text text-slate-600 text-[11.5px] mt-1 leading-relaxed">
                                            Esta solicitud aún tiene <strong>${dispData.cant_controlados_pendientes} fármaco(s) controlado(s)</strong> pendiente(s) por despachar en el módulo de Despacho Controlado.
                                        </p>
                                        <p class="das-swal-mixed-cta text-amber-900 font-semibold text-[11.5px] mt-1.5">
                                            ¿Desea emitir el Despacho Controlado ahora mismo?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        showDenyButton: Boolean(dispData.pdf_url),
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg> Ir a Despacho Controlado',
                        denyButtonColor: '#2563eb',
                        denyButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Ver Guía PDF',
                        cancelButtonColor: '#475569',
                        cancelButtonText: 'Dejar para después',
                        allowOutsideClick: false,
                        width: '660px'
                    }).then((res) => {
                        if (res.isConfirmed) {
                            window.location.href = dispData.url_despacho_controlado;
                        } else if (res.isDenied && dispData.pdf_url) {
                            window.open(dispData.pdf_url, '_blank');
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
                    showCancelButton: Boolean(dispData.pdf_url),
                    cancelButtonColor: '#2563eb',
                    cancelButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Ver Guía PDF',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    width: '640px'
                }).then((res) => {
                    if (res.dismiss === Swal.DismissReason.cancel && dispData.pdf_url) {
                        window.open(dispData.pdf_url, '_blank');
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

                    // no despachar más de lo que falta en la solicitud vinculada (sumando todos los lotes)
                    const solicitudVinculadaId = document.getElementById('establishment_request_id')?.value;
                    if (solicitudVinculadaId && Object.keys(pendientesSolicitudActiva).length > 0) {
                        const sumaPorProducto = {};
                        document.querySelectorAll('#productsBody tr').forEach(tr => {
                            const sel = tr.querySelector('select[name="productos[]"]');
                            const opt = sel ? sel.options[sel.selectedIndex] : null;
                            if (!opt || !opt.value) return;
                            const clave = normTextDespacho(obtenerNombreCompletoOpcionDespacho(opt));
                            const cant = parseInt(tr.querySelector('input[name="cantidades[]"]')?.value) || 0;
                            sumaPorProducto[clave] = (sumaPorProducto[clave] || 0) + cant;
                        });

                        const excesos = Object.entries(sumaPorProducto)
                            .filter(([clave, total]) => pendientesSolicitudActiva[clave] && total > pendientesSolicitudActiva[clave].pendiente)
                            .map(([clave, total]) => `<li><strong>${pendientesSolicitudActiva[clave].nombre}</strong>: faltan ${pendientesSolicitudActiva[clave].pendiente}, vas a despachar ${total}</li>`);

                        if (excesos.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Excede lo solicitado',
                                html: `<div style="font-size:13px;text-align:left;">No puede despachar más de lo que falta en la solicitud. Ajuste las cantidades:<ul style="margin:8px 0 0 18px;list-style:disc;">${excesos.join('')}</ul></div>`,
                                confirmButtonColor: '#0f968c'
                            });
                            return;
                        }
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
                            : 'Establecimiento Interno';
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
                        title: '¿Confirmar Emisión de Despacho?',
                        html: `Se emitirá la guía de despacho para <strong>${destinatario}</strong> con <strong>${itemsCount}</strong> producto(s)${textoUnidades}.<br><span class="text-[12px] text-slate-500 mt-1 block">El stock de los lotes asignados se rebajará según orden FEFO.</span>${esModoTraspasoInterno() ? `<span class="text-[12px] text-amber-700 font-semibold mt-2 block">Traspaso interno sin solicitud${(!signatureRecibe || signatureRecibe.isEmpty()) ? ': quedará «Pendiente de recepción» hasta que firme quien recibe.' : '.'}</span>` : ''}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0f766e',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, emitir despacho',
                        cancelButtonText: 'Revisar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const faltaFirmaDespacha = !signatureDespacha || signatureDespacha.isEmpty();
                            const faltaFirmaRecibe = !esModoTraspasoInterno() && (!signatureRecibe || signatureRecibe.isEmpty()) && !document.getElementById('firma_recibe_input').value;
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
                            formData.set('action', despachoSubmitAction);

                            Swal.fire({
                                title: 'Procesando Despacho...',
                                html: '<span class="text-xs text-slate-500">Descontando existencias por FEFO y registrando trazabilidad...</span>',
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
                                    let errorMsg = data.message || 'Error al procesar el despacho.';
                                    if (data.errors) {
                                        errorMsg = Object.values(data.errors).flat().join('<br>');
                                    }
                                    throw new Error(errorMsg);
                                }
                                return data;
                            })
                            .then(data => {
                                if (data.success && data.detail) {
                                    showDispatchSuccessModal(data.detail);
                                } else {
                                    throw new Error(data.message || 'Error inesperado al emitir el despacho.');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'No se pudo emitir el despacho',
                                    html: `<div class="text-xs text-rose-700 bg-rose-50 p-3 rounded-lg border border-rose-200">${error.message}</div>`,
                                    confirmButtonColor: '#e11d48',
                                    confirmButtonText: 'Entendido'
                                });
                            });
                        }
                    });
                });
            }

            // logica de boton limpiar form general
            const btnLimpiar = document.getElementById('btn_limpiar_form');
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', function() {
                    if (signatureDespacha) signatureDespacha.clear();
                    if (signatureRecibe) signatureRecibe.clear();
                    // eliminar filas extra de productos
                    const items = document.querySelectorAll('#productsBody tr');
                    for (let i = 1; i < items.length; i++) {
                        items[i].remove();
                    }
                    // dejar que window.location.reload o el reset natural actuen
                    window.location.reload();
                });
            }
        });

        // funcion para alternar visualmente la logica de clasificacion interno/externo
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

            actualizarModoTraspaso();
        }

        // funcion para buscar la direccion del cesfam elegido y rellenar el input readonly
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

        // funciones para mostrar u ocultar la bandeja de historial
        function openBandeja() {
            document.getElementById('BandejaModal').classList.remove('hidden');
        }

        function closeBandeja() {
            document.getElementById('BandejaModal').classList.add('hidden');
        }

        // limpia todos los campos de busqueda del historial y refresca la tabla
        function limpiarFiltrosBandeja() {
            document.getElementById('filtroCorrelativo').value = '';
            document.getElementById('filtroFecha').value = '';
            document.getElementById('filtroClasificacion').value = '';
            document.getElementById('filtroDestino').value = '';
            document.getElementById('filtroEstadoBandeja').value = '';
            filtrarBandeja();
        }

        // buscador interno dinamico multi-criterio para la tabla de historial
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
                            iconContainer.classList.remove('bg-blue-600', 'text-white', 'shadow-md',
                                'border-blue-600');
                        }
                    }
                }
            });
        }

        // despliegue de detalles y productos de una guia de despacho en particular
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
                        iconContainer.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'border-blue-600');
                    }
                } else {
                    row.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'border-blue-600');
                    }
                }
            }
        }

        // manipulacion del dom para la tabla de productos y extraccion
        const tbody = document.getElementById('productsBody');

        // inicia la tabla con una fila vacia lista para usar
        document.addEventListener("DOMContentLoaded", () => {
            addRow();
        });

        // funcion de utilidad para clonar el html del template oculto
        function addNewRowFromTemplate() {
            const template = document.getElementById('rowTemplate');
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
            return tbody.lastElementChild;
        }

        // agrega una nueva fila e inyecta los datos en caso de ser escaneados por qr
        function addRow(data = null) {
            let tr = addNewRowFromTemplate();
            if (data) {
                let select = tr.querySelector('select[name="productos[]"]');
                let options = Array.from(select.options);
                let targetOption = options.find(opt => opt.value === data.producto && opt.getAttribute('data-lote') === data
                    .lote);

                if (targetOption) {
                    targetOption.selected = true;
                    select.selectedIndex = targetOption.index;
                    select.value = targetOption.value;
                    llenarDatosFila(select);
                }
            }
            return tr;
        }

        // llena los campos restringidos de acuerdo al producto seleccionado

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

            loteInput.value = option.getAttribute('data-lote') || '';
            venceInput.value = option.getAttribute('data-vence') || '';

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

            let stock = parseInt(option.getAttribute('data-stock')) || 0;
            cantInput.setAttribute('max', stock);
            stockBadge.textContent = stock;
            stockContainer.classList.remove('hidden');

            fefoContainer.innerHTML = '';
            fefoContainer.classList.remove('hidden');
            venceInput.classList.remove(
                'text-rose-700',
                'font-extrabold',
                'bg-rose-50'
            );

            if (venceInput.value) {
                const fechaVencimiento = new Date(venceInput.value + 'T00:00:00');
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);

                const limite = new Date(hoy);
                limite.setMonth(limite.getMonth() + 6);

                if (!isNaN(fechaVencimiento.getTime())) {
                    let texto = '';
                    let color = '';

                    if (fechaVencimiento < hoy) {
                        texto = 'VENCIDO';
                        color = 'bg-rose-700';
                    } else if (fechaVencimiento <= limite) {
                        texto = 'PRONTO A VENCER';
                        color = 'bg-rose-500';
                    }

                    if (texto) {
                        venceInput.classList.add(
                            'text-rose-700',
                            'font-extrabold',
                            'bg-rose-50'
                        );

                        fefoContainer.innerHTML = `
    <span class="absolute right-1 top-1/2 -translate-y-1/2
        text-[7px] ${color} text-white font-bold
        px-1 py-0.5 rounded whitespace-nowrap">
        ${texto}
    </span>
`;
                    }
                }
            }

            cantInput.focus();
        }


        // funcion para quitar una fila dinamica del formulario
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

        // validador visual para restringir ingresos superiores a la cantidad disponible
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
                    text: 'Para usar la cámara necesita acceder mediante localhost, 127.0.0.1 o HTTPS.',
                    confirmButtonColor: '#0f968c'
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
                            <p class="text-xs text-slate-400 mt-1">Verifique los permisos en su navegador o conecte una cámara.</p>
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

        // captura correcta de la informacion del qr e integracion con el select
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
                    confirmButtonColor: '#0f968c'
                });
                return;
            }

            let emptySelect = Array.from(tbody.querySelectorAll('select[name="productos[]"]')).find(sel => sel.value ===
                '');
            let tr = emptySelect ? emptySelect.closest('tr') : addRow();

            let select = tr.querySelector('select[name="productos[]"]');
            let options = Array.from(select.options).filter(opt => opt.value && !opt.disabled);

            let targetOption = null;

            // 1. Coincidencia exacta de Producto Y Lote
            if (prodTarget && loteTarget) {
                const targetProdNorm = norm(prodTarget);
                const targetLoteNorm = cleanLote(loteTarget);
                targetOption = options.find(opt => {
                    return norm(obtenerNombreCompletoOpcionDespacho(opt)) === targetProdNorm && cleanLote(opt.getAttribute('data-lote')) === targetLoteNorm;
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
                targetOption = options.find(opt => norm(obtenerNombreCompletoOpcionDespacho(opt)) === targetProdNorm);
            }

            // 4. Coincidencia parcial / difusa por Producto
            if (!targetOption && prodTarget) {
                const targetProdNorm = norm(prodTarget);
                targetOption = options.find(opt => {
                    const optNorm = norm(obtenerNombreCompletoOpcionDespacho(opt));
                    return optNorm && targetProdNorm && (optNorm.includes(targetProdNorm) || targetProdNorm.includes(optNorm));
                });
            }

            if (targetOption) {
                // Seleccionar índice exacto para no colisionar con productos de mismo nombre y diferente lote
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
                    title: `Producto cargado: ${obtenerNombreCompletoOpcionDespacho(targetOption)}`,
                    text: `Lote: ${loteEncontrado} · Stock disp: ${stockDisp} uds.`,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            } else {
                let nombreBuscado = prodTarget || loteTarget;
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin Stock Disponible',
                    html: `El producto o lote <strong>${nombreBuscado}</strong> no figura con stock disponible en la bodega central.<br><br><span class="text-xs text-slate-500">Verifique que el lote tenga stock en estado "Disponible" o revise el módulo de Cuarentena / Controlados.</span>`,
                    confirmButtonColor: '#0f968c'
                });
                if (!emptySelect && tbody.children.length > 1) tr.remove();
            }
        }

        // funciones para mostrar u ocultar las solicitudes
        function openSolicitudes(filtroAsignacion = null) {
            if (filtroAsignacion) {
                const selAsig = document.getElementById('filtroSolicitudAsignacion');
                if (selAsig) selAsig.value = filtroAsignacion;
            }
            filtrarSolicitudesModal();
            document.getElementById('SolicitudesModal').classList.remove('hidden');
        }

        function closeSolicitudes() {
            document.getElementById('SolicitudesModal').classList.add('hidden');
        }

        function filtrarSolicitudesModal() {
            const texto = (document.getElementById('filtroSolicitudTexto')?.value || '').trim().toLowerCase();
            const tipo = (document.getElementById('filtroSolicitudTipo')?.value || '').toLowerCase();
            const estado = (document.getElementById('filtroSolicitudEstado')?.value || '').toLowerCase();
            const asignacion = (document.getElementById('filtroSolicitudAsignacion')?.value || '').toLowerCase();

            document.querySelectorAll('.fila-solicitud-modal').forEach(fila => {
                const rowSearch = (fila.dataset.search || '').toLowerCase();
                const rowTipo = (fila.dataset.tipo || '').toLowerCase();
                const rowEstado = (fila.dataset.estado || '').toLowerCase();
                const rowAssignedId = fila.dataset.assignedId || '';
                const rowEsMia = fila.dataset.esMia === '1';
                const key = fila.dataset.key;
                const detailRow = key ? document.getElementById(key) : null;

                let matchTexto = !texto || rowSearch.includes(texto);
                let matchTipo = !tipo || rowTipo === tipo;
                let matchEstado = true;
                if (estado === 'activas') {
                    matchEstado = ['pendiente', 'asignada', 'parcial'].includes(rowEstado);
                } else if (estado) {
                    matchEstado = rowEstado === estado;
                }

                let matchAsignacion = true;
                if (asignacion === 'mias') {
                    matchAsignacion = rowEsMia;
                } else if (asignacion === 'sin_asignar') {
                    matchAsignacion = !rowAssignedId;
                } else if (asignacion === 'asignadas') {
                    matchAsignacion = !!rowAssignedId;
                }

                if (matchTexto && matchTipo && matchEstado && matchAsignacion) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                    if (detailRow) detailRow.classList.add('hidden');
                }
            });
        }

        async function asignarSolicitudUsuario(solicitudId, userId, selectElem) {
            const userName = selectElem && selectElem.selectedIndex >= 0 ? selectElem.options[selectElem.selectedIndex].text.trim() : '';
            const prevVal = selectElem ? (selectElem.dataset.prevVal ?? '') : '';

            const confirmTitle = userId ? '¿Asignar Solicitud?' : '¿Desasignar Solicitud?';
            const confirmHtml = userId
                ? `¿Desea asignar la atención de esta solicitud al funcionario <strong>${userName}</strong>?`
                : 'La solicitud volverá al estado <strong>Pendiente</strong> sin funcionario responsable asignado.';
            const confirmBtnText = userId ? 'Sí, asignar' : 'Sí, desasignar';

            const result = await Swal.fire({
                title: confirmTitle,
                html: confirmHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
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

            const spinner = document.getElementById('assign-spinner-' + solicitudId);
            if (spinner) spinner.classList.remove('hidden');
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

                // Actualizar estado visual del badge y atributos de la fila
                const badge = document.getElementById('badge-estado-solicitud-' + solicitudId);
                if (badge && data.estado) {
                    badge.textContent = data.estado;
                    badge.className = 'border px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase inline-block ';
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

                const fila = selectElem ? selectElem.closest('.fila-solicitud-modal') : null;
                if (fila) {
                    fila.dataset.estado = (data.estado || '').toLowerCase();
                    fila.dataset.assignedId = data.assigned_user_id ? String(data.assigned_user_id) : '';
                    fila.dataset.esMia = (parseInt(data.assigned_user_id || 0) === {{ (int) auth()->id() }}) ? '1' : '0';
                }

                const solObj = solicitudes.find(s => s.id === solicitudId);
                if (solObj) {
                    solObj.estado = data.estado;
                    solObj.assigned_user_id = data.assigned_user_id;
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
                    title: 'Error al asignar',
                    text: err.message || 'Ocurrió un error al asignar la solicitud.',
                    confirmButtonColor: '#0f968c'
                });
            } finally {
                if (spinner) spinner.classList.add('hidden');
                if (selectElem) selectElem.disabled = false;
            }
        }

        function desvincularSolicitudActiva() {
            const requestInput = document.getElementById('establishment_request_id');
            if (requestInput) requestInput.value = '';
            pendientesSolicitudActiva = {};
            const banner = document.getElementById('banner_solicitud_activa');
            if (banner) banner.classList.add('hidden');
            actualizarModoTraspaso();
        }

        // Traspaso interno = clasificación "Interno" sin solicitud vinculada
        function esModoTraspasoInterno() {
            const clasif = document.getElementById('clasificacion_select')?.value;
            const solicitudId = document.getElementById('establishment_request_id')?.value;
            return clasif === 'Interno' && !solicitudId;
        }

        function actualizarModoTraspaso() {
            const activo = esModoTraspasoInterno();
            const div = document.getElementById('div_motivo_traspaso');
            const inp = document.getElementById('motivo_traspaso');
            const nota = document.getElementById('nota_firma_recibe_traspaso');
            if (div) div.classList.toggle('hidden', !activo);
            if (nota) nota.classList.toggle('hidden', !activo);
            if (inp) {
                if (activo) {
                    inp.setAttribute('required', 'true');
                    inp.setAttribute('name', 'motivo_traspaso');
                } else {
                    inp.removeAttribute('required');
                    inp.removeAttribute('name');
                }
            }
        }

        // Firma posterior de quien recibe, para guías "Pendiente de recepción"
        function registrarRecepcion(dispatchId, correlativo) {
            let pad = null;
            Swal.fire({
                title: 'Registrar recepción',
                html: `<p class="text-[12.5px] text-slate-600 mb-3">Guía <strong>${correlativo}</strong>: firma de quien recibe el despacho.</p>
                       <div class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden mx-auto" style="max-width:400px;">
                           <canvas id="pad_recepcion_posterior" width="400" height="150" class="cursor-crosshair touch-none w-full"></canvas>
                       </div>
                       <button type="button" id="btn_limpiar_recepcion_posterior" class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500">Limpiar firma</button>`,
                showCancelButton: true,
                confirmButtonText: 'Guardar recepción',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0f766e',
                reverseButtons: true,
                didOpen: () => {
                    const canvas = document.getElementById('pad_recepcion_posterior');
                    pad = new SignaturePad(canvas, { penColor: 'rgb(15, 23, 42)', backgroundColor: 'rgba(255, 255, 255, 1)' });
                    document.getElementById('btn_limpiar_recepcion_posterior').addEventListener('click', () => pad.clear());
                },
                preConfirm: async () => {
                    if (!pad || pad.isEmpty()) {
                        Swal.showValidationMessage('Falta la firma de quien recibe.');
                        return false;
                    }
                    try {
                        const res = await fetch(`{{ url('dispatch') }}/${dispatchId}/recepcion`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ firma_recibe: pad.toDataURL('image/png') })
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'No se pudo registrar la recepción.');
                        }
                        return data;
                    } catch (err) {
                        Swal.showValidationMessage(err.message);
                        return false;
                    }
                }
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({ icon: 'success', title: 'Recepción registrada', text: result.value.message, confirmButtonColor: '#0f766e' })
                        .then(() => window.location.reload());
                }
            });
        }

        // despliegue de detalles de una solicitud
        function toggleSolicitud(key) {
            const row = document.getElementById(key);
            const arrow = document.getElementById('arrow-' + key);

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                arrow.classList.add('rotate-90');
            } else {
                row.classList.add('hidden');
                arrow.classList.remove('rotate-90');
            }
        }

        // funcion para usar una solicitud y rellenar el formulario de despacho
        function usarSolicitud(id) {

            // Buscar la solicitud por su ID en la variable global
            const solicitud = solicitudes.find(s => s.id === id);

            if (!solicitud) return;

            // marcar la solicitud de origen para que el servidor la actualice al despachar
            const requestInput = document.getElementById('establishment_request_id');
            if (requestInput) requestInput.value = solicitud.id;

            // Mostrar banner de solicitud activa
            const banner = document.getElementById('banner_solicitud_activa');
            const bannerTitulo = document.getElementById('banner_solicitud_titulo');
            const bannerSubtitulo = document.getElementById('banner_solicitud_subtitulo');
            if (banner && bannerTitulo && bannerSubtitulo) {
                bannerTitulo.textContent = `Atendiendo Solicitud ${solicitud.numero_solicitud} — ${solicitud.establecimiento}`;
                bannerSubtitulo.textContent = `Solicitante: ${solicitud.responsable || 'N/A'} · Estado actual: ${solicitud.estado} · Al confirmar el despacho se actualizará el estado automáticamente.`;
                banner.classList.remove('hidden');
            }

            // Helper de normalización para matching insensible a mayúsculas, tildes y símbolos
            const normText = s => (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, ' ').replace(/\s+/g, ' ').trim();

            // seleccionar clasificacion interna
            const clasificacion = document.getElementById('clasificacion_select');
            clasificacion.value = 'Interno';
            toggleClasificacion();

            // seleccionar establecimiento
            const selectEstablecimiento = document.getElementById('establecimiento_interno');
            const estSolicitudNorm = normText(solicitud.establecimiento);

            let opcionEstablecimiento = Array.from(selectEstablecimiento.options).find(option => {
                const optNorm = normText(option.value);
                return optNorm === estSolicitudNorm;
            });

            if (!opcionEstablecimiento) {
                opcionEstablecimiento = Array.from(selectEstablecimiento.options).find(option => {
                    const optNorm = normText(option.value);
                    return optNorm && estSolicitudNorm && (optNorm.includes(estSolicitudNorm) || estSolicitudNorm.includes(optNorm));
                });
            }

            if (opcionEstablecimiento) {
                selectEstablecimiento.value = opcionEstablecimiento.value;
                actualizarDireccionInterna();
            } else if (solicitud.establecimiento) {
                // Si es un destino externo que no figura en la lista interna
                const selectClasif = document.getElementById('clasificacion_select');
                if (selectClasif) {
                    selectClasif.value = 'Externo';
                    toggleClasificacion();
                    const inputExt = document.getElementById('establecimiento_externo');
                    if (inputExt) inputExt.value = solicitud.establecimiento;
                }
            }

            // rellenar observaciones con referencia clara
            const observaciones = document.querySelector('input[name="condiciones_transporte"]');
            if (observaciones) {
                const refTexto = `Ref: Solicitud ${solicitud.numero_solicitud}${solicitud.responsable ? ' (' + solicitud.responsable + ')' : ''}`;
                observaciones.value = solicitud.observaciones ? `${solicitud.observaciones} - ${refTexto}` : refTexto;
            }

            // eliminar las filas actuales de productos
            while (tbody.children.length > 0) {
                tbody.children[0].remove();
            }

            let productosFaltantes = [];
            let productosControlados = [];
            let productosParciales = [];
            pendientesSolicitudActiva = {};

            // agregar los productos pendientes de la solicitud (los estupefacientes van al módulo controlado)
            solicitud.items.forEach(item => {
                const nombreItem = item.product?.nombre ?? ('Producto #' + item.product_id);
                const pendiente = Math.max(0, (parseInt(item.cantidad_solicitada) || 0) - (parseInt(item.cantidad_despachada) || 0));

                if (pendiente <= 0) {
                    return;
                }

                if (esControladoJS(nombreItem)) {
                    productosControlados.push(nombreItem + ' (x' + pendiente + ')');
                    return;
                }

                const tr = addRow();
                const selectProducto = tr.querySelector('select[name="productos[]"]');
                const itemNorm = normText(nombreItem);
                const opcionesValidas = Array.from(selectProducto.options).filter(o => o.value && !o.disabled);

                // todos los lotes del producto (primero coincidencia exacta, si no la más parecida)
                let lotesProducto = opcionesValidas.filter(o =>
                    normText(obtenerNombreCompletoOpcionDespacho(o)) === itemNorm
                );
                if (lotesProducto.length === 0) {
                    const aprox = opcionesValidas.find(o => {
                        const optNorm = normText(obtenerNombreCompletoOpcionDespacho(o));
                        return optNorm && itemNorm && (optNorm.includes(itemNorm) || itemNorm.includes(optNorm));
                    });
                    if (aprox) {
                        const nombreAprox = obtenerNombreCompletoOpcionDespacho(aprox);
                        lotesProducto = opcionesValidas.filter(o => obtenerNombreCompletoOpcionDespacho(o) === nombreAprox);
                    }
                }

                // solo lotes con stock, ordenados FEFO (vence antes, sale primero)
                lotesProducto = lotesProducto
                    .filter(o => (parseInt(o.getAttribute('data-stock')) || 0) > 0)
                    .sort((a, b) => (a.getAttribute('data-vence') || '9999-12-31').localeCompare(b.getAttribute('data-vence') || '9999-12-31'));

                if (lotesProducto.length === 0) {
                    // si el producto no tiene stock disponible, se elimina esta fila y lo guardamos
                    tr.remove();
                    productosFaltantes.push(nombreItem);
                    return;
                }

                // recordar cuánto falta de este producto para no pasarse al confirmar
                pendientesSolicitudActiva[normTextDespacho(obtenerNombreCompletoOpcionDespacho(lotesProducto[0]))] = {
                    nombre: nombreItem,
                    pendiente: pendiente
                };

                // repartir lo pendiente entre los lotes, una fila por lote, hasta cubrirlo
                let restante = pendiente;
                lotesProducto.forEach((opt, idx) => {
                    if (restante <= 0) return;

                    const fila = idx === 0 ? tr : addRow();
                    const selFila = fila.querySelector('select[name="productos[]"]');
                    const optFila = Array.from(selFila.options).find(o =>
                        o.value === opt.value && o.getAttribute('data-lote') === opt.getAttribute('data-lote')
                    );
                    if (!optFila) return;

                    // selectedIndex (y no value) porque varios lotes comparten el mismo nombre
                    optFila.selected = true;
                    selFila.selectedIndex = optFila.index;

                    // Llena la fila con lotes y límites
                    llenarDatosFila(selFila);

                    // Seleccionar programa si viene en la solicitud
                    const selectPrograma = fila.querySelector('select[name="programas[]"]');
                    if (selectPrograma && item.programa) {
                        const progNorm = normText(item.programa);
                        const optProg = Array.from(selectPrograma.options).find(o =>
                            normText(o.value) === progNorm || normText(o.text) === progNorm
                        );
                        if (optProg) selectPrograma.value = optProg.value;
                    }

                    const stockLote = parseInt(opt.getAttribute('data-stock')) || 0;
                    const usar = Math.min(stockLote, restante);
                    const cantidad = fila.querySelector('input[name="cantidades[]"]');
                    if (cantidad) {
                        cantidad.value = usar;
                        validarStock(cantidad);
                    }
                    restante -= usar;
                });

                if (restante > 0) {
                    productosParciales.push(`${nombreItem}: se cargan ${pendiente - restante} de ${pendiente} pendientes`);
                }
            });

            // Si nos quedamos sin filas, dejar 1 fila lista para usar
            if (tbody.children.length === 0) {
                addRow();
            }

            // alerta para notificar productos sin stock o con stock insuficiente
            if (productosFaltantes.length > 0 || productosParciales.length > 0) {
                const escapar = t => String(t).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
                let htmlStock = '';
                if (productosFaltantes.length > 0) {
                    htmlStock += '<p style="margin-bottom:6px;">No se agregaron porque no hay stock Disponible en bodega:</p><ul style="text-align:left;margin:0 0 10px 18px;list-style:disc;">' +
                        productosFaltantes.map(p => `<li>${escapar(p)}</li>`).join('') + '</ul>';
                }
                if (productosParciales.length > 0) {
                    htmlStock += '<p style="margin-bottom:6px;">No alcanza el stock para todo lo pedido; se despacha lo disponible y la solicitud quedará <strong>Parcial</strong>:</p><ul style="text-align:left;margin:0 0 0 18px;list-style:disc;">' +
                        productosParciales.map(p => `<li>${escapar(p)}</li>`).join('') + '</ul>';
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta de Stock',
                    html: `<div style="font-size:13px;">${htmlStock}</div>`,
                    confirmButtonColor: '#0f968c'
                });
            }

            // alerta: los estupefacientes de esta solicitud van por Despacho Controlado
            if (productosControlados.length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Solicitud Mixta: Fármacos Controlados',
                    html: `
                        <div class="das-swal-container text-left text-xs">
                            <div class="das-swal-mixed-banner p-3.5 bg-gradient-to-r from-amber-50 to-rose-50 border border-amber-300 rounded-xl flex items-start gap-3">
                                <div class="das-swal-mixed-icon w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 font-extrabold text-sm">!</div>
                                <div class="flex-1 min-w-0">
                                    <p class="das-swal-mixed-title font-bold text-amber-950 text-[13px]">
                                        Solicitud ${solicitud.numero_solicitud ?? ''} contiene Estupefacientes
                                    </p>
                                    <p class="das-swal-mixed-text text-slate-600 text-[11.5px] mt-1 leading-relaxed">
                                        Se cargaron los medicamentos e insumos normales en este formulario. Los siguientes fármacos deben despacharse por el módulo <strong>Despacho Controlado</strong>:
                                    </p>
                                    <ul class="das-swal-pill-list mt-2.5 space-y-1.5">
                                        ${productosControlados.map(prod => `
                                            <li class="das-swal-pill-item flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[11.5px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                <span>${prod}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#0f968c',
                    confirmButtonText: 'Entendido, continuar despacho normal',
                    width: '540px'
                });
            }

            // precargar la firma del solicitante del establecimiento en el pad de recepcion
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

            // cerrar modal de solicitudes
            closeSolicitudes();

            // Notificación de que se completaron los datos de la solicitud
            if (typeof Swal !== 'undefined' && productosControlados.length === 0 && productosFaltantes.length === 0 && productosParciales.length === 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Solicitud ${solicitud.numero_solicitud} cargada`,
                    text: 'Se completaron el establecimiento, observaciones y productos solicitados.',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            filtrarSolicitudesModal();
            const params = new URLSearchParams(window.location.search);
            const solIdParam = parseInt(params.get('solicitud_id'));
            if (solIdParam) {
                setTimeout(() => usarSolicitud(solIdParam), 150);
            }
        });

        function onScanFailure(error) {}

        @if (session('dispatch_success_detail'))
            document.addEventListener('DOMContentLoaded', function() {
                const dispData = @json(session('dispatch_success_detail'));
                if (typeof showDispatchSuccessModal === 'function') {
                    showDispatchSuccessModal(dispData);
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
        .scan-laser-bar {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent, #2dd4bf, #5eead4, #2dd4bf, transparent);
            box-shadow: 0 0 12px 2px rgba(45, 212, 191, 0.9);
            animation: scanLaserAnim 2s ease-in-out infinite;
        }
        #reader-feed video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
    </style>
@endsection