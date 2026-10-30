@extends('layouts.app')
@section('content')
    @php
        $periodoTexto = [
            'hoy' => 'Hoy',
            'semana' => 'Esta Semana',
            'mes' => 'Este Mes',
            'anio' => 'Este Año',
            'historico' => 'Histórico',
            'custom' => 'Rango Fechas',
        ][$periodo] ?? 'Este Mes';

        $hayFiltrosActivos = !empty($establecimiento) || !empty($tipo_establecimiento) || !empty($programa) || !empty($categoria) || !empty($estado_solicitud) || ($periodo ?? 'mes') !== 'mes';
    @endphp

    <div class="w-full px-4 sm:px-6 pt-0 pb-6 space-y-5">

        <!-- CABECERA COMPACTA -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Panel Principal</h1>
                <p class="text-[13px] font-medium text-slate-500 mt-0.5">Estado operativo de Droguería Central, Establecimientos y CESFAM</p>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                @can('ver.solicitudes_cesfam')
                    <a href="{{ route('requests.cesfam') }}" class="px-3.5 py-2 rounded-xl text-[12px] font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-colors">
                        + Solicitud CESFAM
                    </a>
                @endcan
                @can('ver.solicitudes')
                    <a href="{{ route('requests.index') }}" class="px-3.5 py-2 rounded-xl text-[12px] font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition-colors">
                        + Solicitud Establecimiento
                    </a>
                @endcan
                <button type="button" onclick="openDashboardScanner()"
                    class="items-center justify-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-xl text-[12.5px] font-bold hover:bg-slate-900 transition-all shadow-sm cursor-pointer inline-flex">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Escanear QR
                </button>
            </div>
        </div>

        <!-- BANNER: SOLICITUDES ASIGNADAS AL USUARIO LOGUEADO -->
        @if (isset($misSolicitudesAsignadas) && count($misSolicitudesAsignadas) > 0)
            <div class="bg-gradient-to-r from-teal-50 via-white to-indigo-50 border border-teal-200 rounded-2xl p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[13.5px] font-extrabold text-slate-800">
                                Tienes {{ count($misSolicitudesAsignadas) }} solicitud(es) asignada(s) para despachar
                            </h2>
                            <p class="text-[11.5px] text-slate-500">
                                Haz clic en "Despachar" para cargar automáticamente los productos solicitados.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    @foreach ($misSolicitudesAsignadas as $miSol)
                        @php
                            $esCesfamMiSol = in_array($miSol->establecimiento, $nombresCesfam ?? []);
                        @endphp
                        <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-between gap-3 shadow-xs">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono font-extrabold text-[11.5px] text-slate-900">{{ $miSol->numero_solicitud }}</span>
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
                            </div>
                            <a href="{{ route('dispatch.index', ['solicitud_id' => $miSol->id]) }}"
                                class="px-3 py-1.5 rounded-lg text-[11px] font-bold bg-teal-600 hover:bg-teal-700 text-white transition-colors shrink-0">
                                Despachar &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- BARRA DE FILTROS SIMPLIFICADA Y AUTOMÁTICA -->
        <form method="GET" action="{{ route('dashboard.index') }}" id="dashboardFilterForm"
            class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <!-- 1. Período -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Período</label>
                    <select name="periodo" id="dash_periodo" onchange="onDashboardPeriodoChange(this.value)"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-xl text-[12px] font-bold outline-none focus:border-teal-500 cursor-pointer">
                        <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ $periodo == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                        <option value="anio" {{ $periodo == 'anio' ? 'selected' : '' }}>Este Año</option>
                        <option value="historico" {{ $periodo == 'historico' ? 'selected' : '' }}>Histórico Total</option>
                        <option value="custom" {{ $periodo == 'custom' ? 'selected' : '' }}>Personalizar Fechas...</option>
                    </select>
                </div>

                <!-- 2. Tipo de Red -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Tipo de Red</label>
                    <select name="tipo_establecimiento" id="dash_tipo_est" onchange="filtrarOpcionesEstablecimientoDash(true)"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-xl text-[12px] font-bold outline-none focus:border-teal-500 cursor-pointer">
                        <option value="" {{ empty($tipo_establecimiento) ? 'selected' : '' }}>Toda la Red Comunal</option>
                        <option value="cesfam" {{ ($tipo_establecimiento ?? '') === 'cesfam' ? 'selected' : '' }}>Solo CESFAM</option>
                        <option value="establecimiento" {{ ($tipo_establecimiento ?? '') === 'establecimiento' ? 'selected' : '' }}>Solo Establecimientos</option>
                    </select>
                </div>

                <!-- 3. Establecimiento / CESFAM -->
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Centro de Salud (Establecimiento / CESFAM)</label>
                    <select name="establecimiento" id="dash_establecimiento" onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-xl text-[12px] font-bold outline-none focus:border-teal-500 cursor-pointer">
                        <option value="">Todos los Centros de Salud</option>
                        <optgroup label="CESFAM" id="optgroup_dash_cesfam">
                            @foreach ($nombresCesfam ?? [] as $nomCesfam)
                                <option value="{{ $nomCesfam }}" data-tipo-centro="cesfam" {{ ($establecimiento ?? '') === $nomCesfam ? 'selected' : '' }}>
                                    {{ $nomCesfam }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Establecimientos (CECOSF / SAR / Postas)" id="optgroup_dash_estab">
                            @foreach ($nombresNoCesfam ?? [] as $nomEst)
                                <option value="{{ $nomEst }}" data-tipo-centro="establecimiento" {{ ($establecimiento ?? '') === $nomEst ? 'selected' : '' }}>
                                    {{ $nomEst }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- 4. Programa y Botón Limpiar -->
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Programa</label>
                        <select name="programa" onchange="this.form.submit()"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-xl text-[12px] font-bold outline-none focus:border-teal-500 cursor-pointer">
                            <option value="">Todos</option>
                            @foreach ($programasLista as $prog)
                                <option value="{{ $prog }}" {{ $programa == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($hayFiltrosActivos)
                        <a href="{{ route('dashboard.index') }}"
                            title="Limpiar todos los filtros"
                            class="px-3 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 text-[12px] font-bold transition-colors shrink-0">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>

            <!-- Rango de fechas personalizadas (solo se muestra cuando Período == 'custom') -->
            <div id="dashFechasPersonalizadas" class="{{ $periodo === 'custom' ? 'flex' : 'hidden' }} flex-wrap items-end gap-3 mt-3 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ $fecha_desde }}"
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-[12px] font-bold outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}"
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-[12px] font-bold outline-none">
                </div>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[12px] font-bold shadow-xs cursor-pointer">
                    Filtrar Fechas
                </button>
            </div>
        </form>

        <!-- FILA 1: 4 KPIs PRINCIPALES DE BODEGA CENTRAL -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Valor y Stock Bodega -->
            <a href="{{ route('inventory.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Inventario en Bodega</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">${{ number_format($valorInventario, 0, ',', '.') }}</h3>
                    <p class="text-[11px] font-bold text-teal-600 mt-0.5">{{ number_format($unidadesTotales, 0, ',', '.') }} unidades disponibles</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </a>

            <!-- 2. Movimientos del Período (Recepciones y Despachos) -->
            <a href="{{ route('traceability.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Actividad ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($despachosPeriodo, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">despachos</span></h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">{{ number_format($recepcionesPeriodo, 0, ',', '.') }} recepciones registradas</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
            </a>

            <!-- 3. Alertas de Vencimiento FEFO -->
            <a href="{{ route('inventory.index', ['order_by' => 'vencimiento']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-rose-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Alerta FEFO (&le; 30 Días)</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($proximosVencer, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">lotes</span></h3>
                    <p class="text-[11px] font-bold text-rose-600 mt-0.5">Próximos a vencer o vencidos</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- 4. Lotes en Cuarentena -->
            <a href="{{ route('quarantine.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-amber-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">En Cuarentena</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($cantidadCuarentena, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">lotes</span></h3>
                    <p class="text-[11px] font-bold text-amber-600 mt-0.5">Retenidos para revisión técnica</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- FILA 2: 4 KPIs DE ESTABLECIMIENTOS Y CESFAM -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Solicitudes y Despachos CESFAM -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-purple-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Red CESFAM ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesCesfamCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">solicitudes</span></h3>
                    <p class="text-[11px] font-bold text-purple-600 mt-0.5">
                        {{ $cesfamActivasCount ?? 0 }} activas · {{ number_format($unidadesDespachadasCesfam ?? 0, 0, ',', '.') }} uds. desp.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>

            <!-- 2. Solicitudes y Despachos Establecimientos -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-teal-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Establecimientos ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesEstablecimientosCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">solicitudes</span></h3>
                    <p class="text-[11px] font-bold text-teal-600 mt-0.5">
                        {{ $estabActivasCount ?? 0 }} activas · {{ number_format($unidadesDespachadasEstablecimientos ?? 0, 0, ',', '.') }} uds. desp.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <!-- 3. Cola de Despacho (Asignadas vs Sin Asignar) -->
            <a href="{{ route('dispatch.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-indigo-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Pedidos por Despachar</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesPorDespacharCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">en cola</span></h3>
                    <p class="text-[11px] font-bold text-indigo-600 mt-0.5">
                        {{ $solicitudesAsignadasCount ?? 0 }} asignadas · {{ $solicitudesSinAsignarCount ?? 0 }} sin asignar
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
            </a>

            <!-- 4. Cumplimiento y Stock Declarado -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-emerald-500 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Cumplimiento de Red</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ $porcentajeCumplimientoRed ?? 0 }}% <span class="text-xs font-bold text-slate-500">entregado</span></h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">
                        Stock red: {{ number_format($stockDeclaradoRed ?? 0, 0, ',', '.') }} uds.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- SECCIÓN PRINCIPAL EN 2 COLUMNAS: PANEL CON PESTAÑAS (IZQUIERDA) + QR Y ACCESOS (DERECHA) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- COLUMNA IZQUIERDA (2/3): PESTAÑAS LIMPIAS (CENTROS / MOVIMIENTOS / CUARENTENA) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
                <!-- Barra de Pestañas -->
                <div class="px-5 pt-4 pb-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60">
                    <div class="inline-flex bg-slate-200/70 p-1 rounded-xl gap-1">
                        <button type="button" id="tabBtn_centros" onclick="switchDashTab('centros')"
                            class="px-3.5 py-1.5 rounded-lg text-[12px] font-extrabold bg-white text-slate-800 shadow-xs transition-all cursor-pointer">
                            Establecimientos y CESFAM ({{ count($resumenEstablecimientos ?? []) }})
                        </button>
                        <button type="button" id="tabBtn_movimientos" onclick="switchDashTab('movimientos')"
                            class="px-3.5 py-1.5 rounded-lg text-[12px] font-bold text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                            Últimos Movimientos ({{ count($recentMovements ?? []) }})
                        </button>
                        <button type="button" id="tabBtn_cuarentena" onclick="switchDashTab('cuarentena')"
                            class="px-3.5 py-1.5 rounded-lg text-[12px] font-bold text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                            Cuarentena ({{ count($lotesCuarentenaDb ?? []) }})
                        </button>
                    </div>

                    <!-- Buscador rápido para la pestaña de Centros -->
                    <div id="dashSearchCentrosWrap" class="flex items-center gap-2">
                        <input type="text" id="buscadorTablaCentros" oninput="filtrarTablaCentrosDash()"
                            placeholder="Buscar CESFAM o centro..."
                            class="bg-white border border-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-[12px] font-medium outline-none focus:border-teal-500 w-full sm:w-48">
                    </div>
                </div>

                <!-- PESTAÑA 1: TABLA DE ESTABLECIMIENTOS Y CESFAM -->
                <div id="tabPanel_centros" class="overflow-x-auto max-h-[420px]">
                    <table class="w-full text-left text-[12px] text-slate-600 whitespace-nowrap">
                        <thead class="text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200 bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3">Centro de Salud</th>
                                <th class="px-3 py-3 text-center">Tipo</th>
                                <th class="px-3 py-3 text-center">Solicitudes</th>
                                <th class="px-3 py-3 text-center">Stock Declarado</th>
                                <th class="px-3 py-3 text-center">Solic. / Desp.</th>
                                <th class="px-4 py-3 text-center">Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="tbodyResumenCentros">
                            @forelse ($resumenEstablecimientos ?? [] as $rowCentro)
                                <tr class="fila-centro-dash hover:bg-slate-50/80 transition-colors"
                                    data-nombre="{{ mb_strtolower($rowCentro->nombre . ' ' . $rowCentro->tipo) }}">
                                    <td class="px-4 py-3 font-bold text-slate-800">
                                        <a href="{{ route('dashboard.index', ['establecimiento' => $rowCentro->nombre, 'periodo' => $periodo]) }}"
                                            class="hover:text-teal-600 transition-colors">
                                            {{ $rowCentro->nombre }}
                                        </a>
                                        @if ($rowCentro->ultima_solicitud)
                                            <span class="block text-[10px] font-normal text-slate-400">
                                                Últ. pedido: {{ \Carbon\Carbon::parse($rowCentro->ultima_solicitud)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase {{ $rowCentro->es_cesfam ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                            {{ $rowCentro->es_cesfam ? 'CESFAM' : $rowCentro->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-extrabold text-slate-800">{{ $rowCentro->total_solicitudes }}</span>
                                        @if ($rowCentro->activas > 0)
                                            <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9.5px] font-extrabold">
                                                {{ $rowCentro->activas }} act.
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-blue-700">
                                        {{ number_format($rowCentro->stock_declarado, 0, ',', '.') }} uds.
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-bold text-teal-700">{{ number_format($rowCentro->unidades_solicitadas, 0, ',', '.') }}</span>
                                        <span class="text-slate-300 mx-0.5">/</span>
                                        <span class="font-bold text-indigo-700">{{ number_format($rowCentro->unidades_despachadas, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-14 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $rowCentro->cumplimiento >= 80 ? 'bg-emerald-500' : ($rowCentro->cumplimiento >= 40 ? 'bg-amber-500' : 'bg-slate-300') }}"
                                                    style="width: {{ $rowCentro->cumplimiento }}%"></div>
                                            </div>
                                            <span class="text-[11px] font-extrabold text-slate-700">{{ $rowCentro->cumplimiento }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-slate-400 font-medium">
                                        No hay centros de salud que coincidan con el filtro seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PESTAÑA 2: ÚLTIMOS MOVIMIENTOS DE BODEGA -->
                <div id="tabPanel_movimientos" class="hidden p-5 max-h-[420px] overflow-y-auto">
                    @if (count($recentMovements) > 0)
                        <div class="divide-y divide-slate-100">
                            @foreach ($recentMovements as $mov)
                                @php
                                    $isRec = $mov->tipo_movimiento === 'Recepción';
                                    $badgeColor = $isRec ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200';
                                @endphp
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase border {{ $badgeColor }}">
                                                {{ $mov->tipo_movimiento }}
                                            </span>
                                            <span class="text-[12.5px] font-bold text-slate-800 truncate">{{ $mov->producto }}</span>
                                        </div>
                                        <p class="text-[11.5px] text-slate-500 mt-0.5">
                                            {{ $mov->origen_destino }} · <span class="font-mono">Lote: {{ $mov->lote }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[13px] font-black {{ $isRec ? 'text-emerald-600' : 'text-blue-600' }}">
                                            {{ $isRec ? '+' : '-' }}{{ number_format($mov->cantidad, 0, ',', '.') }} uds.
                                        </span>
                                        <span class="block text-[10px] text-slate-400 font-mono">
                                            {{ \Carbon\Carbon::parse($mov->fecha_hora)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                            <a href="{{ route('traceability.index') }}" class="text-[12px] font-bold text-teal-600 hover:text-teal-800">
                                Ver historial completo en Trazabilidad &rarr;
                            </a>
                        </div>
                    @else
                        <div class="py-12 text-center text-slate-400">
                            <p class="text-[13px] font-medium">No hay movimientos registrados en este período.</p>
                        </div>
                    @endif
                </div>

                <!-- PESTAÑA 3: LOTES EN CUARENTENA -->
                <div id="tabPanel_cuarentena" class="hidden overflow-x-auto max-h-[420px]">
                    <table class="w-full text-left text-[12px] text-slate-600">
                        <thead class="text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3">Lote</th>
                                <th class="px-4 py-3 text-center">Tiempo Retenido</th>
                                <th class="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($lotesCuarentenaDb as $cuarentena)
                                @php
                                    $dias = (int) floor(\Carbon\Carbon::parse($cuarentena->created_at)->startOfDay()->diffInDays(now()->startOfDay()));
                                @endphp
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $cuarentena->producto }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-slate-600">{{ $cuarentena->lote }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-amber-700">{{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('quarantine.index') }}" class="text-[11.5px] font-bold text-amber-600 hover:text-amber-800 underline">
                                            Revisar &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-400 font-medium">
                                        No hay lotes retenidos en cuarentena actualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- COLUMNA DERECHA (1/3): ÚLTIMO LOTE QR Y ACCESOS RÁPIDOS -->
            <div class="flex flex-col gap-5">
                <!-- Panel QR del Último Lote -->
                <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 flex flex-col">
                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
                        <h2 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            Último Lote Ingresado
                        </h2>
                        <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">Rotulación QR</span>
                    </div>

                    @if ($latestInventory)
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 bg-slate-50 p-2 rounded-xl border border-slate-200 shrink-0 flex items-center justify-center shadow-2xs" id="qr-container">
                                <img src="{{ route('qr.image', $latestInventory->id) }}"
                                    alt="Código QR Lote" class="w-full h-full object-contain">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-[13px] text-slate-800 truncate" title="{{ $latestInventory->producto }}">
                                    {{ $latestInventory->producto }}
                                </h3>
                                <p class="text-[11px] font-mono font-bold text-slate-500 mt-0.5">
                                    Lote: <span class="text-slate-700">{{ $latestInventory->lote ?? 'S/L' }}</span>
                                </p>
                                <div class="flex items-center gap-2 mt-1.5 text-[11px]">
                                    <span class="font-extrabold text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded">{{ number_format($latestInventory->cantidad, 0, ',', '.') }} u.</span>
                                    <span class="text-slate-400 font-medium">Ubicación: <strong>{{ $latestInventory->pasillo ?? 'S/P' }}-{{ $latestInventory->estante ?? 'S/E' }}</strong></span>
                                </div>

                                <div class="grid grid-cols-2 gap-1.5 mt-2.5">
                                    <button type="button" onclick="printQR()"
                                        class="py-1.5 px-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[11px] font-bold transition-all shadow-2xs cursor-pointer inline-flex items-center justify-center gap-1"
                                        title="Imprimir etiqueta adhesiva para rotular la caja o pallet">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Imprimir Rótulo
                                    </button>
                                    <a href="{{ route('traceability.index', ['search' => $latestInventory->lote]) }}"
                                        class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all cursor-pointer inline-flex items-center justify-center gap-1 text-center"
                                        title="Ver trazabilidad completa de este lote en Kardex">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Ver Kardex
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2.5 text-center">Rótulo para identificación física de cajas y pallets recién ingresados a bodega.</p>
                    @else
                        <div class="py-6 text-center text-slate-400">
                            <p class="text-[12px] font-medium">Sin lotes registrados aún.</p>
                        </div>
                    @endif
                </div>

                <!-- Accesos Directos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-3.5">
                        <h2 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Accesos Directos</h2>
                        <span class="text-[10.5px] font-semibold text-slate-400">Módulos</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <!-- 1. Recepción -->
                        <a href="{{ route('reception.index') }}" class="group p-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl transition-colors flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block leading-tight">Recepción</span>
                                <span class="text-[10px] text-slate-500 truncate block">Ingreso de stock</span>
                            </div>
                        </a>

                        <!-- 2. Despacho -->
                        <a href="{{ route('dispatch.index') }}" class="group p-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl transition-colors flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block leading-tight">Despacho</span>
                                <span class="text-[10px] text-slate-500 truncate block">Salida de pedidos</span>
                            </div>
                        </a>

                        <!-- 3. Inventario -->
                        <a href="{{ route('inventory.index') }}" class="group p-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl transition-colors flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block leading-tight">Inventario</span>
                                <span class="text-[10px] text-slate-500 truncate block">Kardex y stock</span>
                            </div>
                        </a>

                        <!-- 4. Reportes -->
                        <a href="{{ route('reports.index') }}" class="group p-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl transition-colors flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block leading-tight">Reportes</span>
                                <span class="text-[10px] text-slate-500 truncate block">Actas y balances</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ESCÁNER QR DE DIRECCIONAMIENTO CLÍNICO (REDISEÑADO) -->
    <div id="scannerModalDashboard" class="hidden fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 transition-all animate-fade-in">
        <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 p-0 w-full max-w-md max-h-[94vh] flex flex-col overflow-hidden my-auto text-white">
            <!-- Header futurista -->
            <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border-b border-slate-700/80 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping absolute"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 relative"></span>
                    </div>
                    <div>
                        <h2 class="text-[15px] font-extrabold text-white tracking-wide flex items-center gap-2">
                            Escáner Óptico de Lotes
                        </h2>
                        <p class="text-[11px] text-teal-300 font-medium">Lectura QR y Trazabilidad Inmediata</p>
                    </div>
                </div>
                <button type="button" onclick="closeDashboardScanner()" class="text-slate-400 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Visor HUD de Cámara -->
            <div class="p-5 flex flex-col items-center bg-slate-950/60">
                <div class="relative w-full max-w-[320px] aspect-square rounded-2xl overflow-hidden bg-black border-2 border-slate-700/80 shadow-2xl flex items-center justify-center" id="scannerVisualContainer">
                    <!-- Elemento nativo de feed de cámara -->
                    <div id="reader-dashboard-feed" class="w-full h-full object-cover"></div>

                    <!-- HUD Overlay: 4 esquinas neón + láser animado -->
                    <div class="pointer-events-none absolute inset-6 flex items-center justify-center" id="scannerHudOverlay">
                        <!-- 4 Esquinas -->
                        <div class="absolute top-0 left-0 w-6 h-6 border-t-[3.5px] border-l-[3.5px] border-teal-400 rounded-tl-lg shadow-[0_0_8px_rgba(45,212,191,0.8)]"></div>
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-[3.5px] border-r-[3.5px] border-teal-400 rounded-tr-lg shadow-[0_0_8px_rgba(45,212,191,0.8)]"></div>
                        <div class="absolute bottom-0 left-0 w-6 h-6 border-b-[3.5px] border-l-[3.5px] border-teal-400 rounded-bl-lg shadow-[0_0_8px_rgba(45,212,191,0.8)]"></div>
                        <div class="absolute bottom-0 right-0 w-6 h-6 border-b-[3.5px] border-r-[3.5px] border-teal-400 rounded-br-lg shadow-[0_0_8px_rgba(45,212,191,0.8)]"></div>

                        <!-- Línea láser animada -->
                        <div class="scan-laser-bar"></div>
                    </div>

                    <!-- Mensaje de estado mientras conecta la cámara -->
                    <div id="scannerLoadingState" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-4 text-center z-10 transition-opacity">
                        <div class="w-10 h-10 border-3 border-teal-400 border-t-transparent rounded-full animate-spin mb-3"></div>
                        <p class="text-sm font-bold text-white">Iniciando sensor de cámara...</p>
                        <p class="text-xs text-slate-400 mt-1">Conceda permiso de acceso a la cámara si el navegador lo solicita.</p>
                    </div>
                </div>

                <!-- Barra de control y estado -->
                <div class="w-full max-w-[320px] flex items-center justify-between mt-4 px-1">
                    <div class="flex items-center gap-2 text-[12px] text-slate-300 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span id="scannerStatusTxt">Buscando código QR...</span>
                    </div>

                    <!-- Botón para alternar cámaras si hay más de una -->
                    <button type="button" id="btnSwitchCamera" onclick="switchScannerCamera()"
                        class="hidden px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-teal-300 rounded-lg text-[11px] font-bold border border-slate-700 transition-colors items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Cambiar Cámara
                    </button>
                </div>

                <p class="text-[12px] text-slate-400 mt-3 text-center leading-relaxed max-w-sm">
                    Apunta la cámara al código QR de la caja o medicamento. Al detectarlo, el sistema abrirá automáticamente su Ficha de Trazabilidad y Kardex.
                </p>
            </div>

            <!-- Footer modal -->
            <div class="px-5 py-3.5 bg-slate-900 border-t border-slate-800 flex justify-end">
                <button type="button" onclick="closeDashboardScanner()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-[12.5px] font-bold transition-colors cursor-pointer">
                    Cerrar Escáner
                </button>
            </div>
        </div>
    </div>

    <style>
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
        #reader-dashboard-feed video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1rem;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let dashboardScanner = null;

        function switchDashTab(tab) {
            ['centros', 'movimientos', 'cuarentena'].forEach(t => {
                const panel = document.getElementById('tabPanel_' + t);
                const btn = document.getElementById('tabBtn_' + t);
                if (panel) panel.classList.toggle('hidden', t !== tab);
                if (btn) {
                    btn.className = (t === tab)
                        ? 'px-3.5 py-1.5 rounded-lg text-[12px] font-extrabold bg-white text-slate-800 shadow-xs transition-all cursor-pointer'
                        : 'px-3.5 py-1.5 rounded-lg text-[12px] font-bold text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
                }
            });

            const searchWrap = document.getElementById('dashSearchCentrosWrap');
            if (searchWrap) {
                searchWrap.classList.toggle('hidden', tab !== 'centros');
            }
        }

        function onDashboardPeriodoChange(val) {
            const box = document.getElementById('dashFechasPersonalizadas');
            if (val === 'custom') {
                if (box) {
                    box.classList.remove('hidden');
                    box.classList.add('flex');
                }
            } else {
                if (box) {
                    box.classList.add('hidden');
                    box.classList.remove('flex');
                }
                document.getElementById('dashboardFilterForm')?.submit();
            }
        }

        function filtrarOpcionesEstablecimientoDash(autoSubmit = false) {
            const tipoSel = document.getElementById('dash_tipo_est')?.value || '';
            const estSelect = document.getElementById('dash_establecimiento');
            const optCesfam = document.getElementById('optgroup_dash_cesfam');
            const optEstab = document.getElementById('optgroup_dash_estab');

            if (optCesfam) optCesfam.hidden = (tipoSel === 'establecimiento');
            if (optEstab) optEstab.hidden = (tipoSel === 'cesfam');

            if (estSelect && estSelect.value) {
                const selectedOpt = estSelect.selectedOptions[0];
                const optTipo = selectedOpt?.getAttribute('data-tipo-centro');
                if (tipoSel && optTipo && tipoSel !== optTipo) {
                    estSelect.value = '';
                }
            }

            if (autoSubmit) {
                document.getElementById('dashboardFilterForm')?.submit();
            }
        }

        function filtrarTablaCentrosDash() {
            const q = (document.getElementById('buscadorTablaCentros')?.value || '').toLowerCase().trim();
            document.querySelectorAll('.fila-centro-dash').forEach(row => {
                const nombre = row.getAttribute('data-nombre') || '';
                row.classList.toggle('hidden', q !== '' && !nombre.includes(q));
            });
        }

        let html5QrScanner = null;
        let availableCameras = [];
        let currentCameraIndex = 0;

        function playScannerBeep() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = "sine";
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            } catch(e) {}
        }

        async function openDashboardScanner() {
            const modal = document.getElementById('scannerModalDashboard');
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
                    html5QrScanner = new Html5Qrcode("reader-dashboard-feed");
                }

                // Detectar cámaras disponibles
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

            if (html5QrScanner.isScanning) {
                await html5QrScanner.stop();
            }

            html5QrScanner.start(
                cameraConfig,
                scanConfig,
                onScanDashboardSuccess,
                function() {}
            ).then(() => {
                if (loadingState) {
                    loadingState.style.opacity = '0';
                    setTimeout(() => { loadingState.style.display = 'none'; }, 200);
                }
                if (statusTxt) statusTxt.textContent = 'Enfoca el código QR en el visor';
            }).catch(e => {
                console.error("Fallo al iniciar cámara específica:", e);
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

        async function closeDashboardScanner() {
            if (html5QrScanner && html5QrScanner.isScanning) {
                try {
                    await html5QrScanner.stop();
                } catch(e) {}
            }
            document.getElementById('scannerModalDashboard').classList.add('hidden');
        }

        function onScanDashboardSuccess(decodedText) {
            playScannerBeep();

            let parsedData = {};
            decodedText.split('|').forEach(chunk => {
                let idx = chunk.indexOf(':');
                if (idx === -1) return;
                let key = chunk.substring(0, idx).trim();
                let val = chunk.substring(idx + 1).trim();
                if (key && val) parsedData[key] = val;
            });

            closeDashboardScanner();

            const loteKey = parsedData.LOTE || decodedText.trim();
            if (!loteKey) {
                alert("El código QR no contiene información válida de Lote.");
                return;
            }
            window.location.href = `/traceability?search=${encodeURIComponent(loteKey)}`;
        }

        function printQR() {
            const qrImage = document.querySelector('#qr-container img');
            if (!qrImage) return;

            @php
                $loteProdTxt = $latestInventory?->producto ?? '';
                $loteNumTxt = $latestInventory?->lote ?? '';
                $loteUbicTxt = ($latestInventory?->pasillo ?? 'S/P') . ' - ' . ($latestInventory?->estante ?? 'S/E');
                $loteCantTxt = number_format($latestInventory?->cantidad ?? 0, 0, ',', '.');
                $loteVencTxt = !empty($latestInventory?->fecha_vencimiento) ? date('d/m/Y', strtotime($latestInventory->fecha_vencimiento)) : 'N/A';
            @endphp

            const productoTxt = @json($loteProdTxt);
            const loteTxt = @json($loteNumTxt);
            const ubicacionTxt = @json($loteUbicTxt);
            const cantidadTxt = @json($loteCantTxt);
            const vencimientoTxt = @json($loteVencTxt);

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                    <head>
                        <title>Rótulo Adhesivo QR - Lote ${loteTxt}</title>
                        <style>
                            @page { size: auto; margin: 6mm; }
                            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; margin: 0; padding: 12px; background: #fff; color: #0f172a; }
                            .label-container {
                                width: 280px;
                                border: 2px solid #0f172a;
                                border-radius: 12px;
                                padding: 12px;
                                margin: 0 auto;
                                text-align: center;
                                box-sizing: border-box;
                            }
                            .header { font-size: 10px; font-weight: 800; letter-spacing: 1px; color: #0d9488; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 6px; margin-bottom: 8px; }
                            .qr-wrap { display: flex; justify-content: center; margin: 6px 0; }
                            .qr-wrap img { width: 130px; height: 130px; }
                            .prod-title { font-size: 13px; font-weight: 800; margin: 6px 0 3px 0; line-height: 1.2; word-break: break-word; }
                            .lote-badge { font-family: monospace; font-size: 14px; font-weight: 900; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; display: inline-block; margin: 4px 0; }
                            .info-grid { font-size: 10.5px; margin-top: 6px; border-top: 1px dashed #cbd5e1; padding-top: 6px; display: flex; justify-content: space-between; text-align: left; }
                            .info-grid div span { color: #64748b; font-size: 9px; display: block; text-transform: uppercase; font-weight: 700; }
                        </style>
                    </head>
                    <body>
                        <div class="label-container">
                            <div class="header">Droguería Municipal · Control de Lote</div>
                            <div class="qr-wrap"><img src="${qrImage.src}" /></div>
                            <div class="prod-title">${productoTxt}</div>
                            <div class="lote-badge">LOTE: ${loteTxt}</div>
                            <div class="info-grid">
                                <div><span>Vencimiento</span><strong>${vencimientoTxt}</strong></div>
                                <div><span>Ubicación</span><strong>${ubicacionTxt}</strong></div>
                                <div><span>Stock Inicial</span><strong>${cantidadTxt} u.</strong></div>
                            </div>
                        </div>
                        <script>
                            window.onload = function() { window.print(); setTimeout(() => window.close(), 500); }
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }

        document.addEventListener('DOMContentLoaded', () => {
            filtrarOpcionesEstablecimientoDash(false);
        });
    </script>
@endsection
