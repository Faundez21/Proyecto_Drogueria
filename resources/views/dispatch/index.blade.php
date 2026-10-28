@extends('layouts.app')
@section('title', 'Despacho')
@section('subtitle', 'Directorio y gestion de guias de despacho emitidas.')

@section('actions')
    <button type="button" onclick="openBandeja()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-[#0f968c] hover:bg-teal-700 shadow-sm transition-all duration-200">
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
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">03</span>
        <span>Salida</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Despacho</span>
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

    <!-- Solicitudes asignadas al usuario logueado -->
    @if (isset($misSolicitudesAsignadas) && count($misSolicitudesAsignadas) > 0)
        <div class="mb-6 bg-gradient-to-r from-teal-50 via-white to-indigo-50 border border-teal-200 rounded-2xl p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[13.5px] font-extrabold text-slate-800">
                            Tienes {{ count($misSolicitudesAsignadas) }} solicitud(es) asignada(s) para despachar
                        </h3>
                        <p class="text-[11.5px] text-slate-500">
                            Haz clic en "Cargar en Formulario" para preparar el despacho automáticamente o revisa el detalle en "Ver Solicitudes".
                        </p>
                    </div>
                </div>
                <button type="button" onclick="openSolicitudes('mias')"
                    class="px-3.5 py-2 rounded-xl text-[12px] font-bold bg-white hover:bg-teal-50 text-teal-700 border border-teal-200 shadow-sm transition-colors cursor-pointer shrink-0">
                    Ver mis asignadas
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @foreach ($misSolicitudesAsignadas as $miSol)
                    @php
                        $esCesfamMiSol = \App\Services\InventoryService::esCesfam($miSol->establecimiento);
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-between gap-3 shadow-xs">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-mono font-extrabold text-[12px] text-slate-800">{{ $miSol->numero_solicitud }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $esCesfamMiSol ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                    {{ $esCesfamMiSol ? 'CESFAM' : 'Establecimiento' }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-indigo-100 text-indigo-700">
                                    {{ $miSol->estado }}
                                </span>
                            </div>
                            <p class="text-[12.5px] font-bold text-slate-800 truncate mt-0.5" title="{{ $miSol->establecimiento }}">
                                {{ $miSol->establecimiento }}
                            </p>
                            <p class="text-[11px] text-slate-500">
                                {{ $miSol->items->count() }} producto(s) · {{ \Carbon\Carbon::parse($miSol->fecha_solicitud)->format('d/m/Y') }}
                            </p>
                        </div>
                        <button type="button" onclick="usarSolicitud({{ $miSol->id }})"
                            class="px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition-colors cursor-pointer shrink-0">
                            Cargar
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $totalActivas }}</h3>
            </div>
            <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Historico Total</p>
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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

        <!-- cabecera de la tarjeta del formulario -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex items-center gap-4">
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
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-[#0f968c] hover:bg-teal-700 shadow-sm transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2zM13 3v4h4"></path>
                </svg>
                Ver Solicitudes
                @if ($totalActivas > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-white/20 text-white">
                        {{ $totalActivas }}
                    </span>
                @endif
                @if ($totalMias > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ $totalMias }} asignada(s) a ti
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

            <!-- seccion de datos del remitente y correlativo -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-100">
                <div>
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Datos del Remitente</h4>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl shadow-sm">
                        <p class="text-[14px] font-extrabold text-slate-800">I. Municipalidad de Talcahuano</p>
                        <p class="text-[12px] font-bold text-teal-700">Drogueria Comunal Central</p>
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
                        class="w-full md:w-48 bg-slate-100 border border-slate-300 text-slate-800 text-lg font-mono font-extrabold rounded-xl text-center py-2 focus:outline-none cursor-default shadow-inner">
                </div>
            </div>

            <!-- seccion de destino y logistica con clasificacion -->
            <div class="p-6 bg-white border-b border-slate-100">
                <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide mb-4">1. Tipo, Destino y
                    Logistica</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                    <!-- clasificacion interno/externo -->
                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Clasificacion del Despacho <span
                                class="text-rose-500">*</span></label>
                        <select name="clasificacion" id="clasificacion_select" onchange="toggleClasificacion()" required
                            class="w-full bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione la clasificacion...</option>
                            <option value="Interno">Interno (Nivel Comunal / CESFAM)</option>
                            <option value="Externo">Externo (Traspasos, Prestamos, Donaciones, etc.)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Salida <span
                                class="text-rose-500">*</span></label>
                        <input type="date" name="fecha_despacho" value="{{ date('Y-m-d') }}" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Liberacion <span
                                class="text-rose-500">*</span></label>
                        <select name="estado_liberacion" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="Liberada">Liberada / Aprobada</option>
                            <option value="Pendiente">Pendiente de V°B°</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino interno -->
                    <div id="div_interno" class="md:col-span-2 hidden">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Establecimiento Destino (Interno)
                            <span class="text-rose-500">*</span></label>
                        <select id="establecimiento_interno" onchange="actualizarDireccionInterna()"
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione el Establecimiento...</option>
                            <option value="CESFAM LOS CERROS"
                                data-direccion="Calle El Galgo, esquina Calle Coliumo, Villa Badaran, Talcahuano">CESFAM
                                LOS CERROS</option>
                            <option value="CECOSF CENTINELA"
                                data-direccion="Calle Punta Peñablanca 1301, poblacion Centinela, Talcahuano">CECOSF
                                CENTINELA</option>
                            <option value="CECOSF LOS LOBOS - LA GLORIA"
                                data-direccion="Calle Los Lobos 1960, poblacion Los Lobos, Talcahuano">CECOSF LOS LOBOS -
                                LA GLORIA</option>
                            <option value="POSTA TUMBE" data-direccion="Maryland s/n, Caleta Tumbes, Talcahuano">POSTA
                                TUMBE</option>
                            <option value="CESFAM PAULINA AVENDAÑO"
                                data-direccion="Calle Carlos Dittborn 4100, Villa San Martin, Talcahuano">CESFAM PAULINA
                                AVENDAÑO</option>
                            <option value="CESFAM SAN VICENTE" data-direccion="Calle Brasil 360, San Vicente, Talcahuano">
                                CESFAM SAN VICENTE</option>
                            <option value="CECOSF LIBERTAD GAETE"
                                data-direccion="Calle Isla San Ambrosio 509, poblacion Libertad, Talcahuano">CECOSF
                                LIBERTAD GAETE</option>
                            <option value="CECOSF 8 DE MAYO"
                                data-direccion="Calle Aysen 7370, poblacion Diego Portales, Talcahuano">CECOSF 8 DE MAYO
                            </option>
                            <option value="CECOSF ESMERALDA"
                                data-direccion="Calle San Miguel 546, Poblacion Patricio Lynch, Talcahuano">CECOSF
                                ESMERALDA</option>
                            <option value="CESFAM LEOCAN PORTUS"
                                data-direccion="Calle Manuel Barros Borgoño 2645, Las Salinas, Talcahuano">CESFAM LEOCAN
                                PORTUS</option>
                            <option value="SAR SAN VICENTE" data-direccion="Juan de la Cruz Tapia s/n, Talcahuano">SAR SAN
                                VICENTE</option>
                            <option value="SAR LOS CERROS" data-direccion="Calle Coliumo S/N Nueva los Lobos, Talcahuano">
                                SAR LOS CERROS</option>
                            <option value="UAPO" data-direccion="Calle Blanco Encalada 1142, Talcahuano">UAPO</option>
                            <option value="Dental - CESFAM SAN VICENTE" data-direccion="Brasil 360, Talcahuano">Dental -
                                CESFAM SAN VICENTE</option>
                            <option value="Laboratorio - CESFAM SAN VICENTE" data-direccion="Brasil 360, Talcahuano">
                                Laboratorio - CESFAM SAN VICENTE</option>
                        </select>
                    </div>

                    <!-- div dinamico para destino externo -->
                    <div id="div_externo" class="md:col-span-2 hidden">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Tipo y Destino Externo <span
                                class="text-rose-500">*</span></label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="tipo_externo"
                                class="w-full sm:w-2/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                                <option value="" disabled selected>Tipo...</option>
                                <option value="Traspaso">Traspaso</option>
                                <option value="Prestamo">Prestamo</option>
                                <option value="Donacion">Donacion</option>
                                <option value="Devolucion">Devolucion a Proveedor</option>
                                <option value="Merma">Merma / Destruccion</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <input type="text" id="establecimiento_externo"
                                placeholder="Nombre institucion/destino..."
                                class="w-full sm:w-3/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Direccion Destino</label>
                        <input type="text" name="direccion_destino" id="direccion_destino"
                            placeholder="Esperando clasificacion..." readonly
                            class="w-full bg-slate-50 border border-slate-300 text-slate-500 font-medium rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-inner transition-all">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Condiciones Transporte y
                            Observaciones</label>
                        <input type="text" name="condiciones_transporte"
                            placeholder="Ej: Cadena de Frio (2° a 8°C). Referencia solicitud interna #342."
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>
                </div>
            </div>

            <!-- seccion de tabla de productos y stock -->
            <div class="p-6 bg-slate-50/30 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide">2. Detalle de Extraccion
                        (Lotes en Stock)</h3>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openScanner()"
                            class="px-4 py-2 bg-[#0f968c] text-white rounded-lg text-[12px] font-bold hover:bg-teal-700 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h2m4 0h2m4 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-4 0h-2m-4 0H5a2 2 0 01-2-2v-2m4-10h2v2H7V7zm8 0h2v2h-2V7zM7 15h2v2H7v-2zm8-2h2v4h-2v-4zm-4-2h2v2h-2v-2z">
                                </path>
                            </svg>
                            Escanear Etiqueta QR
                        </button>
                        <button type="button" onclick="addRow()"
                            class="px-4 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg text-[12px] font-bold hover:bg-blue-50 transition-colors flex items-center gap-2 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Añadir Fila Manual
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm bg-white">
                    <table class="w-full text-left text-[13px] whitespace-nowrap" id="productsTable">
                        <thead
                            class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[10px]">
                            <tr>
                                <th class="px-4 py-3.5 w-[35%]">Seleccionar Producto en Stock</th>
                                <th class="px-4 py-3.5 w-[15%]">Lote Extraido</th>
                                <th class="px-4 py-3.5 w-[15%]">Vencimiento (FEFO)</th>
                                <th class="px-4 py-3.5 w-[15%]">Programa</th>
                                <th class="px-4 py-3.5 w-[15%] text-center">Cantidad Rebajar</th>
                                <th class="px-4 py-3.5 w-[5%] text-center"></th>
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
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Despacha (Bodega/DAS)</p>
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
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Solicita / Recibe
                            (Establecimiento)</p>
                        <input type="hidden" name="firma_recibe" id="firma_recibe_input">
                        <div
                            class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_recibe" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>
                        <button type="button" onclick="signatureRecibe.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar
                            Firma de Recepcion</button>
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
                <button type="submit"
                    class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 text-white rounded-xl text-[13px] font-bold hover:bg-slate-900 flex items-center justify-center gap-2 transition-all shadow-md hover:-translate-y-0.5 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirmar Despacho y Registrarlo
                </button>
            </div>
        </form>
    </div>

    <!-- plantilla oculta para generar nuevas filas de productos -->
    <template id="rowTemplate">
        <tr class="bg-white hover:bg-slate-50/80 transition-colors group">
            <td class="px-3 py-2 border-r border-slate-100">
                <select name="productos[]" required onchange="llenarDatosFila(this)"
                    class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[12px] font-bold text-slate-800 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="" disabled selected>Seleccione producto del inventario...</option>
                    @foreach ($inventarioDisponible as $inv)
                        <option value="{{ $inv->producto }}" data-lote="{{ $inv->lote ?? 'S/L' }}"
                            data-vence="{{ $inv->fecha_vencimiento ?? '' }}" data-stock="{{ $inv->cantidad ?? 0 }}">
                            {{ $inv->producto }} - Lote: {{ $inv->lote ?? 'S/L' }} (Disp: {{ $inv->cantidad ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-3 py-2 border-r border-slate-100">
                <input type="text" name="lotes[]" readonly placeholder="Auto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] font-mono font-bold text-slate-500 outline-none shadow-inner cursor-not-allowed">
            </td>
            <td class="px-3 py-2 border-r border-slate-100 relative">
                <input type="date" name="vencimientos[]" readonly
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2 text-[12px] text-slate-500 outline-none shadow-inner cursor-not-allowed">
                <div class="fefo-container"></div>
            </td>
            <td class="px-3 py-2 border-r border-slate-100">
                <select name="programas[]" required
                    class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[12px] font-medium text-slate-700 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="Morbilidad General" selected>Morbilidad General</option>
                    <option value="Cardiovascular">Cardiovascular</option>
                    <option value="IRA/ERA">Respiratorio (IRA/ERA)</option>
                </select>
            </td>
            <td class="px-3 py-2 border-r border-slate-100 relative pb-6">
                <input type="number" name="cantidades[]" placeholder="0" min="1" oninput="validarStock(this)"
                    required
                    class="w-full bg-white border border-slate-300 focus:border-teal-500 rounded-lg px-2.5 py-2 text-[14px] font-black text-center text-slate-800 outline-none shadow-sm transition-colors">
                <div
                    class="stock-container absolute bottom-1 left-0 w-full text-center text-[10px] font-extrabold text-blue-600 hidden">
                    Max: <span class="stock-max-badge">0</span></div>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeRow(this)"
                    class="p-2 bg-white text-slate-400 hover:text-white hover:bg-rose-600 rounded-lg transition-colors cursor-pointer shadow-sm border border-slate-200"
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
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-7xl flex flex-col overflow-hidden">

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

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Tipo Origen
                        </label>
                        <select id="filtroSolicitudTipo" onchange="filtrarSolicitudesModal()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="">Todos (CESFAM y Establec.)</option>
                            <option value="cesfam">Solo CESFAM</option>
                            <option value="establecimiento">Solo Establecimientos</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Estado
                        </label>
                        <select id="filtroSolicitudEstado" onchange="filtrarSolicitudesModal()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                            <option value="activas" selected>Activas (Por despachar)</option>
                            <option value="">Todas (Incluye Despachadas)</option>
                            <option value="pendiente">Pendiente (Sin asignar)</option>
                            <option value="asignada">Asignada</option>
                            <option value="parcial">Parcial</option>
                            <option value="despachado">Despachado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Asignación
                        </label>
                        <select id="filtroSolicitudAsignacion" onchange="filtrarSolicitudesModal()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
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
                                                class="bg-slate-50 hover:bg-white border border-slate-300 text-slate-800 text-[12px] font-semibold rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 cursor-pointer min-w-[185px]">
                                                <option value="">— Sin asignar —</option>
                                                @foreach ($usuariosAsignables ?? [] as $usr)
                                                    <option value="{{ $usr->id }}" {{ (int) $solicitud->assigned_user_id === (int) $usr->id ? 'selected' : '' }}>
                                                        {{ $usr->name }} {{ $usr->last_name }}
                                                    </option>
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
                                                Asignada a ti
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
                                                            $esItemControlado = in_array($nombreProdItem, $controlados);
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
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl flex flex-col overflow-hidden">

            <!-- cabecera de modal historico -->
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Bandeja Historica de Despachos</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">Trazabilidad de guias
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
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Filtros de Busqueda</span>
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
                            Emision</label>
                        <input type="date" id="filtroFecha" oninput="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                    </div>

                    <!-- filtro clasificacion -->
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Clasificacion</label>
                        <select id="filtroClasificacion" onchange="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
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
                            <option value="CECOSF CENTINELA">
                            <option value="CECOSF LOS LOBOS - LA GLORIA">
                            <option value="POSTA TUMBE">
                            <option value="CESFAM PAULINA AVENDAÑO">
                            <option value="CESFAM SAN VICENTE">
                            <option value="CECOSF LIBERTAD GAETE">
                            <option value="CECOSF 8 DE MAYO">
                            <option value="CECOSF ESMERALDA">
                            <option value="CESFAM LEOCAN PORTUS">
                            <option value="SAR SAN VICENTE">
                            <option value="SAR LOS CERROS">
                            <option value="UAPO">
                            <option value="Dental - CESFAM SAN VICENTE">
                            <option value="Laboratorio - CESFAM SAN VICENTE">
                        </datalist>
                    </div>

                    <!-- filtro estado -->
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estado</label>
                        <select id="filtroEstadoBandeja" onchange="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
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
                            <th class="px-6 py-4">Fecha Emision</th>
                            <th class="px-6 py-4">Clasificacion</th>
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
                                                    Productos Rebajados en esta Guia
                                                </span>
                                            </div>
                                            <table class="w-full text-left text-[12px] whitespace-nowrap">
                                                <thead
                                                    class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200 text-[10px]">
                                                    <tr>
                                                        <th class="px-4 py-2.5">Producto</th>
                                                        <th class="px-4 py-2.5">Lote</th>
                                                        <th class="px-4 py-2.5">Vencimiento</th>
                                                        <th class="px-4 py-2.5 text-center">Cant. Extraida</th>
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
                                                    Firma Quien Despacha</p>
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
                                                    Firma Quien Solicita/Recibe</p>
                                                @if (isset($despacho->firma_recibe) && $despacho->firma_recibe)
                                                    <img src="{{ $despacho->firma_recibe }}" class="mx-auto max-h-16"
                                                        alt="Firma Recepcion">
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
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">No hay guias
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

    <!-- ventana modal para el escaner de codigos qr -->
    <div id="scannerModal"
        class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Escanear Etiqueta QR</h2>
                    <p class="text-[11px] text-slate-300 mt-0.5 uppercase tracking-wider font-semibold">Validacion
                        automatica de Stock</p>
                </div>
                <button type="button" onclick="closeScanner()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50 flex flex-col items-center">
                <div
                    class="bg-white border-[3px] border-slate-200 border-dashed rounded-2xl p-2 w-full max-w-sm flex flex-col items-center">
                    <div id="reader" class="w-full rounded-xl overflow-hidden shadow-inner bg-black"></div>
                </div>
                <p class="text-[12px] font-medium text-slate-500 mt-5 text-center leading-relaxed max-w-sm">
                    Apunte la camara a la etiqueta del producto. El sistema leera el Lote, seleccionara el producto en la
                    tabla y establecera el tope maximo de extraccion.
                </p>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeScanner()"
                    class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">Cancelar
                    Escaneo</button>
            </div>
        </div>
    </div>

    <!-- librerias externas -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <!-- IMPLEMENTACION SWEETALERT 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // instancias de firmas para que sean globales en la vista
        let signatureDespacha = null;
        let signatureRecibe = null;
        const solicitudes = @json($solicitudes);
        const listaControlados = @json($controlados);

        const esControladoJS = (nombre) => listaControlados.some(c => c.toLowerCase() === String(nombre ?? '').trim()
            .toLowerCase());

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

            // asignacion de firmas a los inputs ocultos antes del submit con confirmación SweetAlert2
            const form = document.getElementById('despachoForm');
            let despachoSubmitConfirmed = false;
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (despachoSubmitConfirmed) return;

                    if (!form.checkValidity()) {
                        return;
                    }

                    e.preventDefault();

                    const destSelect = document.getElementById('destinatario_select');
                    const destinatario = destSelect && destSelect.selectedIndex >= 0 ? destSelect.options[destSelect.selectedIndex].text.trim() : 'Destinatario';
                    const itemsCount = document.querySelectorAll('.producto-item').length;

                    Swal.fire({
                        title: '¿Confirmar Emisión de Despacho?',
                        html: `Se emitirá la guía de despacho para <strong>${destinatario}</strong> con <strong>${itemsCount}</strong> producto(s).<br><span class="text-[12px] text-slate-500 mt-1 block">El stock de los lotes asignados se rebajará según orden FEFO.</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0f766e',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, emitir despacho',
                        cancelButtonText: 'Revisar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (signatureDespacha && !signatureDespacha.isEmpty()) {
                                document.getElementById('firma_despacha_input').value = signatureDespacha.toDataURL('image/png');
                            }
                            if (signatureRecibe && !signatureRecibe.isEmpty()) {
                                document.getElementById('firma_recibe_input').value = signatureRecibe.toDataURL('image/png');
                            }
                            despachoSubmitConfirmed = true;
                            form.submit();
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
                    const items = document.querySelectorAll('.producto-item');
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
                inpDir.placeholder = "Se autocompletara al seleccionar destino...";

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
                inpDir.placeholder = 'Ingrese la direccion manualmente...';
            }
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
            let loteInput = tr.querySelector('input[name="lotes[]"]');
            let venceInput = tr.querySelector('input[name="vencimientos[]"]');
            let cantInput = tr.querySelector('input[name="cantidades[]"]');

            let stockBadge = tr.querySelector('.stock-max-badge');
            let stockContainer = tr.querySelector('.stock-container');
            let fefoContainer = tr.querySelector('.fefo-container');

            loteInput.value = option.getAttribute('data-lote') || '';
            venceInput.value = option.getAttribute('data-vence') || '';

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

        // inicializacion e interaccion con la libreria de la camara qr
        let html5QrcodeScanner = null;

        function openScanner() {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aviso de Seguridad',
                    text: 'Para usar la cámara necesitas acceder mediante localhost, 127.0.0.1 o HTTPS.',
                    confirmButtonColor: '#0f968c'
                });
            }

            document.getElementById('scannerModal').classList.remove('hidden');
            html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0
            }, false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function closeScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(e => console.error(e));
                html5QrcodeScanner = null;
            }
            document.getElementById('scannerModal').classList.add('hidden');
        }

        // captura correcta de la informacion del qr e integracion con el select
        function onScanSuccess(decodedText) {
            closeScanner();

            let parsedData = {};
            let chunks = decodedText.split('|');
            chunks.forEach(chunk => {
                let [key, val] = chunk.split(':');
                if (key && val) parsedData[key.trim()] = val.trim();
            });

            if (!parsedData.PROD || !parsedData.STOCK) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato Inválido',
                    text: 'QR no compatible con el sistema.',
                    confirmButtonColor: '#0f968c'
                });
                return;
            }

            let rowData = {
                producto: parsedData.PROD.trim(),
                lote: parsedData.LOTE ? parsedData.LOTE.trim() : 'S/L',
                vencimiento: parsedData.VENCE ? parsedData.VENCE.trim() : 'N/A',
                stock: parseInt(parsedData.STOCK)
            };

            let emptySelect = Array.from(tbody.querySelectorAll('select[name="productos[]"]')).find(sel => sel.value ===
                '');
            let tr;

            if (emptySelect) {
                tr = emptySelect.closest('tr');
            } else {
                tr = addRow();
            }

            let select = tr.querySelector('select[name="productos[]"]');
            let options = Array.from(select.options);
            let targetOption = options.find(opt => opt.value === rowData.producto && opt.getAttribute('data-lote') ===
                rowData.lote);

            if (targetOption) {
                select.value = targetOption.value;
                llenarDatosFila(select);
                tr.querySelector('input[name="cantidades[]"]').focus();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin Stock',
                    text: 'El producto o lote escaneado ya no tiene stock disponible en la bodega.',
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
                ? `¿Deseas asignar la atención de esta solicitud al funcionario <strong>${userName}</strong>?`
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
            const banner = document.getElementById('banner_solicitud_activa');
            if (banner) banner.classList.add('hidden');
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

            // seleccionar clasificacion interna
            const clasificacion = document.getElementById('clasificacion_select');
            clasificacion.value = 'Interno';
            toggleClasificacion();

            // seleccionar establecimiento
            const selectEstablecimiento = document.getElementById('establecimiento_interno');

            const establecimientoSolicitud = solicitud.establecimiento
                .trim()
                .toLowerCase();

            const opcionEstablecimiento = Array.from(selectEstablecimiento.options).find(option =>
                option.value.trim().toLowerCase() === establecimientoSolicitud
            );

            if (opcionEstablecimiento) {
                selectEstablecimiento.value = opcionEstablecimiento.value;
                actualizarDireccionInterna();
            }

            // rellenar observaciones
            const observaciones = document.querySelector(
                'input[name="condiciones_transporte"]'
            );

            if (observaciones) {
                observaciones.value = solicitud.observaciones ?? '';
            }

            // eliminar las filas actuales de productos
            while (tbody.children.length > 0) {
                tbody.children[0].remove();
            }

            let productosFaltantes = [];
            let productosControlados = [];

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
                const selectProducto = tr.querySelector(
                    'select[name="productos[]"]'
                );

                const nombreProducto = nombreItem
                    .trim()
                    .toLowerCase();

                const opcionProducto = Array.from(selectProducto.options).find(option =>
                    option.value.trim().toLowerCase() === nombreProducto
                );

                if (opcionProducto) {
                    selectProducto.value = opcionProducto.value;

                    // Llena la fila con lotes y límites
                    llenarDatosFila(selectProducto);

                    // Seleccionar programa si viene en la solicitud
                    const selectPrograma = tr.querySelector('select[name="programas[]"]');
                    if (selectPrograma && item.programa) {
                        const optProg = Array.from(selectPrograma.options).find(o => o.value.trim().toLowerCase() === String(item.programa).trim().toLowerCase());
                        if (optProg) selectPrograma.value = optProg.value;
                    }

                    const cantidad = tr.querySelector(
                        'input[name="cantidades[]"]'
                    );
                    cantidad.value = pendiente;

                    // Forzar que la cantidad no exceda el stock real que tienes actualmente
                    validarStock(cantidad);
                } else {
                    // si el producto no tiene stock disponible, se elimina esta fila y lo guardamos
                    tr.remove();
                    productosFaltantes.push(nombreItem);
                }
            });

            // Si nos quedamos sin filas (porque no había stock de nada), dejar 1 fila lista para usar
            if (tbody.children.length === 0) {
                addRow();
            }

            // alerta para notificar productos sin stock
            if (productosFaltantes.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta de Stock',
                    text: 'Los siguientes productos no se agregaron porque no hay stock Disponible en bodega: ' +
                        productosFaltantes.join(', '),
                    confirmButtonColor: '#0f968c'
                });
            }

            // alerta: los estupefacientes de esta solicitud van por Despacho Controlado
            if (productosControlados.length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Estupefacientes -> Despacho Controlado',
                    html: 'Los siguientes estupefacientes de la solicitud <b>' + (solicitud.numero_solicitud ??
                            '') +
                        '</b> deben despacharse por el módulo <b>Despacho Controlado</b>:<br><br><b>' +
                        productosControlados.join('<br>') + '</b>',
                    confirmButtonColor: '#0f968c'
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
    </script>
@endsection
