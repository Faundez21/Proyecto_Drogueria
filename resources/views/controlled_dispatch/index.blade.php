@extends('layouts.app')
@section('title', 'Controlados')
@section('subtitle', 'Directorio y gestion de guias de despacho de farmacos controlados.')

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

    <!-- indicadores de rendimiento -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                <h2 class="text-[16px] font-bold text-slate-800 tracking-tight">Despacho de Farmacos Controlados</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Acceso restringido unicamente a estupefacientes y talonarios de
                    recetas cheque. Solo solicitudes de CESFAM.</p>
            </div>
            <button type="button" onclick="openSolicitudesControlado()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all duration-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2zM13 3v4h4"></path>
                </svg>
                Ver Solicitudes (Estupefacientes)
            </button>
        </div>

        <form action="{{ route('controlled_dispatch.store') }}" method="POST" id="despachoControladoForm">
            @csrf
            <input type="hidden" name="establishment_request_id" id="establishment_request_id" value="">

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
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Clasificacion del Despacho <span
                                class="text-rose-500">*</span></label>
                        <select name="clasificacion" id="clasificacion_select" onchange="toggleClasificacion()" required
                            class="w-full bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all cursor-pointer outline-none">
                            <option value="" disabled selected>Seleccione la clasificacion...</option>
                            <option value="Interno">Interno (Nivel Comunal / CESFAM)</option>
                            <option value="Externo">Externo (Traspasos, Prestamos, Donaciones, etc.)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Salida <span
                                class="text-rose-500">*</span></label>
                        <input type="date" name="fecha_despacho" value="{{ date('Y-m-d') }}" required
                            class="w-full bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Liberacion <span
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
                            <option value="CESFAM PAULINA AVENDAÑO"
                                data-direccion="Calle Carlos Dittborn 4100, Villa San Martin, Talcahuano">CESFAM PAULINA
                                AVENDAÑO</option>
                            <option value="CESFAM SAN VICENTE" data-direccion="Calle Brasil 360, San Vicente, Talcahuano">
                                CESFAM SAN VICENTE</option>
                            <option value="CESFAM LEOCAN PORTUS"
                                data-direccion="Calle Manuel Barros Borgoño 2645, Las Salinas, Talcahuano">CESFAM LEOCAN
                                PORTUS</option>
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
                                <option value="Prestamo">Prestamo</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <input type="text" id="establecimiento_externo"
                                placeholder="Nombre institucion/destino..."
                                class="w-full sm:w-3/5 bg-white border border-slate-300 text-slate-900 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block py-2 px-3 text-[13px] shadow-sm transition-all outline-none">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Direccion Destino</label>
                        <input type="text" name="direccion_destino" id="direccion_destino"
                            placeholder="Esperando clasificacion..." readonly
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
            <div class="p-6 bg-slate-50/30 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h3 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wide">2. Detalle de Extraccion
                        Controlada</h3>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openScanner()"
                            class="px-4 py-2 bg-rose-600 text-white rounded-lg text-[12px] font-bold hover:bg-rose-700 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h2m4 0h2m4 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-4 0h-2m-4 0H5a2 2 0 01-2-2v-2m4-10h2v2H7V7zm8 0h2v2h-2V7zM7 15h2v2H7v-2zm8-2h2v4h-2v-4zm-4-2h2v2h-2v-2z">
                                </path>
                            </svg>
                            Escanear Etiqueta QR
                        </button>
                        <button type="button" onclick="addRow()"
                            class="px-4 py-2 bg-white text-rose-600 border border-rose-200 rounded-lg text-[12px] font-bold hover:bg-rose-50 transition-colors flex items-center gap-2 shadow-sm cursor-pointer">
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
                                <th class="px-4 py-3.5 w-[35%]">Seleccionar Producto Controlado</th>
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
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Despacha (Q.F / Encargado)</p>
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
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Recibe (Establecimiento)</p>
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
                <button type="submit"
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
            <td class="px-3 py-2 border-r border-slate-100">
                <select name="productos[]" required onchange="llenarDatosFila(this)"
                    class="w-full bg-white border border-slate-300 focus:border-rose-500 rounded-lg px-2.5 py-2 text-[12px] font-bold text-slate-800 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="" disabled selected>Seleccione estupefaciente...</option>
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
                    class="w-full bg-white border border-slate-300 focus:border-rose-500 rounded-lg px-2.5 py-2 text-[12px] font-medium text-slate-700 outline-none shadow-sm cursor-pointer transition-colors">
                    <option value="Morbilidad General" selected>Morbilidad General</option>
                    <option value="Cardiovascular">Cardiovascular</option>
                    <option value="Salud Mental">Salud Mental</option>
                    <option value="Paliativos">Cuidados Paliativos</option>
                </select>
            </td>
            <td class="px-3 py-2 border-r border-slate-100 relative pb-6">
                <input type="number" name="cantidades[]" placeholder="0" min="1" oninput="validarStock(this)"
                    required
                    class="w-full bg-white border border-slate-300 focus:border-rose-500 rounded-lg px-2.5 py-2 text-[14px] font-black text-center text-slate-800 outline-none shadow-sm transition-colors">
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

    <!-- ventana modal para el historial de despachos controlados -->
    <div id="BandejaModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl flex flex-col overflow-hidden">

            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Bandeja Historica de Controlados</h2>
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
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Filtros de Busqueda</span>
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
                            Emision</label>
                        <input type="date" id="filtroFecha" oninput="filtrarBandeja()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors shadow-sm cursor-pointer">
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Clasificacion</label>
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

    <!-- AQUÍ ESTÁ LA SOLUCIÓN: VENTANA MODAL PARA EL ESCÁNER QR -->
    <div id="scannerModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg flex flex-col overflow-hidden">
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Escanear Etiqueta QR</h2>
                    <p class="text-[12px] text-slate-300 mt-1 uppercase tracking-wider font-semibold">Acerque el código a
                        la cámara</p>
                </div>
                <button type="button" onclick="closeScanner()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6 bg-slate-50 flex items-center justify-center">
                <!-- CONTENEDOR 'reader' DONDE HTML5-QRCODE INYECTA LA CÁMARA -->
                <div id="reader"
                    class="w-full rounded-lg overflow-hidden border-2 border-dashed border-rose-300 bg-white"></div>
            </div>
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeScanner()"
                    class="px-6 py-2.5 bg-rose-600 text-white hover:bg-rose-700 rounded-xl transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cancelar
                    Escaneo</button>
            </div>
        </div>
    </div>

    <!-- modal de solicitudes con estupefacientes -->
    <div id="SolicitudesControladoModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl flex flex-col max-h-[90vh] overflow-hidden">
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

    <!-- Scripts -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <!-- IMPLEMENTACION SWEETALERT 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let signatureDespacha = null;
        let signatureRecibe = null;
        const solicitudesControlado = @json($solicitudes);

        const esControladoJS = (nombre) => @json($controlados).some(c => c.toLowerCase() === String(nombre ?? '')
            .trim().toLowerCase());

        // ===== SOLICITUDES (ESTUPEFACIENTES) =====
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

        function usarSolicitudControlado(id) {
            const solicitud = solicitudesControlado.find(s => s.id === id);
            if (!solicitud) return;

            // marcar la solicitud de origen
            const requestInput = document.getElementById('establishment_request_id');
            if (requestInput) requestInput.value = solicitud.id;

            // clasificacion interna + establecimiento (matching insensible a mayusculas)
            const clasificacion = document.getElementById('clasificacion_select');
            clasificacion.value = 'Interno';
            toggleClasificacion();

            const selectEst = document.getElementById('establecimiento_interno');
            const objetivo = (solicitud.establecimiento ?? '').trim().toLowerCase();
            const opcionEst = Array.from(selectEst.options).find(op => op.value.trim().toLowerCase() === objetivo);
            if (opcionEst) {
                selectEst.value = opcionEst.value;
                actualizarDireccionInterna();
            }

            // condiciones de transporte
            const condiciones = document.querySelector('input[name="condiciones_transporte"]');
            if (condiciones) condiciones.value = solicitud.observaciones ?? '';

            // limpiar filas actuales
            while (tbody.children.length > 0) tbody.children[0].remove();

            let normales = [];
            let sinStock = [];

            solicitud.items.forEach(item => {
                const nombreItem = item.product.nombre ?? ('Producto #' + item.product_id);
                const pendiente = Math.max(0, item.cantidad_solicitada - (item.cantidad_despachada ?? 0));

                if (pendiente <= 0) return;

                if (!esControladoJS(nombreItem)) {
                    normales.push(nombreItem + ' (x' + pendiente + ')');
                    return;
                }

                const tr = addRow();
                const selectProducto = tr.querySelector('select[name="productos[]"]');
                const opcion = Array.from(selectProducto.options).find(op => op.value.trim().toLowerCase() ===
                    nombreItem.trim().toLowerCase());

                if (opcion) {
                    selectProducto.value = opcion.value;
                    llenarDatosFila(selectProducto);
                    const selectPrograma = tr.querySelector('select[name="programas[]"]');
                    if (selectPrograma && item.programa) {
                        const optProg = Array.from(selectPrograma.options).find(o => o.value.trim().toLowerCase() === String(item.programa).trim().toLowerCase());
                        if (optProg) selectPrograma.value = optProg.value;
                    }
                    const cantInput = tr.querySelector('input[name="cantidades[]"]');
                    cantInput.value = pendiente;
                    validarStock(cantInput);
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

            if (normales.length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Medicamentos -> Despacho Normal',
                    html: 'Los siguientes productos de la solicitud <b>' + (solicitud.numero_solicitud ?? '') +
                        '</b> NO son estupefacientes y deben despacharse por el módulo <b>Despacho Normal</b>:<br><br><b>' +
                        normales.join('<br>') + '</b>',
                    confirmButtonColor: '#be123c'
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
            if (form) {
                form.addEventListener('submit', function() {
                    if (signatureDespacha && !signatureDespacha.isEmpty()) {
                        document.getElementById('firma_despacha_input').value = signatureDespacha.toDataURL(
                            'image/png');
                    }
                    if (signatureRecibe && !signatureRecibe.isEmpty()) {
                        document.getElementById('firma_recibe_input').value = signatureRecibe.toDataURL(
                            'image/png');
                    }
                });
            }

            const btnLimpiar = document.getElementById('btn_limpiar_form');
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', function() {
                    if (signatureDespacha) signatureDespacha.clear();
                    if (signatureRecibe) signatureRecibe.clear();
                    const items = document.querySelectorAll('.producto-item');
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
                inpDir.placeholder = 'Ingrese la direccion manually...';
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

        let html5QrcodeScanner = null;

        function openScanner() {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aviso de Seguridad',
                    text: 'Para usar la cámara necesitas acceder mediante localhost, 127.0.0.1 o HTTPS.',
                    confirmButtonColor: '#e11d48'
                });
            }

            // Aquí se muestra el modal que ahora sí existe en el HTML
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
                    confirmButtonColor: '#e11d48'
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
                    confirmButtonColor: '#e11d48'
                });
                if (!emptySelect && tbody.children.length > 1) tr.remove();
            }
        }

        function onScanFailure(error) {
            // Se ignora silenciosamente para no saturar la consola en cada frame fallido
        }
    </script>
@endsection
