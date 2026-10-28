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
            <a href="{{ route('inventory.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-teal-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Inventario en Bodega</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">${{ number_format($valorInventario, 0, ',', '.') }}</h3>
                    <p class="text-[11px] font-bold text-teal-600 mt-0.5">{{ number_format($unidadesTotales, 0, ',', '.') }} unidades disponibles</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </a>

            <!-- 2. Movimientos del Período (Recepciones y Despachos) -->
            <a href="{{ route('traceability.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-blue-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Actividad ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($despachosPeriodo, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">despachos</span></h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">{{ number_format($recepcionesPeriodo, 0, ',', '.') }} recepciones registradas</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
            </a>

            <!-- 3. Alertas de Vencimiento FEFO -->
            <a href="{{ route('inventory.index', ['order_by' => 'vencimiento']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-rose-500 shadow-sm hover:border-rose-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Alerta FEFO (&le; 30 Días)</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($proximosVencer, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">lotes</span></h3>
                    <p class="text-[11px] font-bold text-rose-600 mt-0.5">Próximos a vencer o vencidos</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </a>

            <!-- 4. Lotes en Cuarentena -->
            <a href="{{ route('quarantine.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-amber-500 shadow-sm hover:border-amber-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">En Cuarentena</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($cantidadCuarentena, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">lotes</span></h3>
                    <p class="text-[11px] font-bold text-amber-600 mt-0.5">Retenidos para revisión técnica</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </a>
        </div>

        <!-- FILA 2: 4 KPIs DE ESTABLECIMIENTOS Y CESFAM -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Solicitudes y Despachos CESFAM -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-purple-500 shadow-sm flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Red CESFAM ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesCesfamCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">solicitudes</span></h3>
                    <p class="text-[11px] font-bold text-purple-600 mt-0.5">
                        {{ $cesfamActivasCount ?? 0 }} activas · {{ number_format($unidadesDespachadasCesfam ?? 0, 0, ',', '.') }} uds. desp.
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                </div>
            </div>

            <!-- 2. Solicitudes y Despachos Establecimientos -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-teal-500 shadow-sm flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Establecimientos ({{ $periodoTexto }})</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesEstablecimientosCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">solicitudes</span></h3>
                    <p class="text-[11px] font-bold text-teal-600 mt-0.5">
                        {{ $estabActivasCount ?? 0 }} activas · {{ number_format($unidadesDespachadasEstablecimientos ?? 0, 0, ',', '.') }} uds. desp.
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <!-- 3. Cola de Despacho (Asignadas vs Sin Asignar) -->
            <a href="{{ route('dispatch.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-indigo-500 shadow-sm hover:border-indigo-300 transition-all flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Pedidos por Despachar</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ number_format($solicitudesPorDespacharCount ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">en cola</span></h3>
                    <p class="text-[11px] font-bold text-indigo-600 mt-0.5">
                        {{ $solicitudesAsignadasCount ?? 0 }} asignadas · {{ $solicitudesSinAsignarCount ?? 0 }} sin asignar
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                </div>
            </a>

            <!-- 4. Cumplimiento y Stock Declarado -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 border-t-[3px] border-t-emerald-500 shadow-sm flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Cumplimiento de Red</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1">{{ $porcentajeCumplimientoRed ?? 0 }}% <span class="text-xs font-bold text-slate-500">entregado</span></h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">
                        Stock red: {{ number_format($stockDeclaradoRed ?? 0, 0, ',', '.') }} uds.
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col">
                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
                        <h2 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Último Lote Ingresado
                        </h2>
                        <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">QR Activo</span>
                    </div>

                    @if ($latestInventory)
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 bg-slate-50 p-2 rounded-xl border border-slate-200 shrink-0 flex items-center justify-center" id="qr-container">
                                <img src="{{ route('qr.image', $latestInventory->id) }}"
                                    alt="Código QR" class="w-full h-full object-contain">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-[13px] text-slate-800 truncate" title="{{ $latestInventory->producto }}">
                                    {{ $latestInventory->producto }}
                                </h3>
                                <p class="text-[11px] font-mono font-bold text-slate-500 mt-0.5">
                                    Lote: {{ $latestInventory->lote ?? 'S/L' }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-[11.5px]">
                                    <span class="font-black text-teal-600">{{ number_format($latestInventory->cantidad, 0, ',', '.') }} uds.</span>
                                    <span class="text-slate-300">|</span>
                                    <span class="font-bold text-slate-600">{{ $latestInventory->pasillo ?? 'S/P' }} - {{ $latestInventory->estante ?? 'S/E' }}</span>
                                </div>
                                <button type="button" onclick="printQR()"
                                    class="mt-2.5 w-full py-1.5 px-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[11.5px] font-bold transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Imprimir Etiqueta
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="py-6 text-center text-slate-400">
                            <p class="text-[12px] font-medium">Sin lotes registrados aún.</p>
                        </div>
                    @endif
                </div>

                <!-- Accesos Directos Compactos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">Accesos Directos</h2>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('reception.index') }}" class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl hover:bg-emerald-50 hover:border-emerald-200 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Recepción</span>
                        </a>
                        <a href="{{ route('dispatch.index') }}" class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Despacho</span>
                        </a>
                        <a href="{{ route('inventory.index') }}" class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl hover:bg-teal-50 hover:border-teal-200 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Inventario</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Reportes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ESCÁNER QR DE DIRECCIONAMIENTO -->
    <div id="scannerModalDashboard" class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 p-0 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-900 flex justify-between items-center">
                <div>
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Escanear Lote Clínico</h2>
                    <p class="text-[11px] text-teal-300 mt-0.5 uppercase tracking-wider font-semibold">Búsqueda rápida en Kardex</p>
                </div>
                <button type="button" onclick="closeDashboardScanner()" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50 flex flex-col items-center">
                <div class="bg-white border-[3px] border-slate-200 border-dashed rounded-2xl p-2 w-full max-w-sm flex flex-col items-center relative min-h-[250px]">
                    <div id="reader-dashboard" class="w-full rounded-xl overflow-hidden shadow-inner bg-black flex items-center justify-center text-white text-xs">Iniciando cámara...</div>
                </div>
                <p class="text-[12px] font-medium text-slate-500 mt-5 text-center leading-relaxed max-w-sm">
                    Apunta la cámara a la etiqueta QR del producto. El sistema te redirigirá automáticamente a su Ficha de Trazabilidad.
                </p>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeDashboardScanner()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">Cancelar Escaneo</button>
            </div>
        </div>
    </div>

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

        function openDashboardScanner() {
            document.getElementById('scannerModalDashboard').classList.remove('hidden');
            dashboardScanner = new Html5QrcodeScanner(
                "reader-dashboard",
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0, supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] },
                false
            );
            dashboardScanner.render(onScanDashboardSuccess, function() {});
        }

        function closeDashboardScanner() {
            if (dashboardScanner) {
                dashboardScanner.clear().catch(e => console.error(e));
                dashboardScanner = null;
            }
            document.getElementById('scannerModalDashboard').classList.add('hidden');
        }

        function onScanDashboardSuccess(decodedText) {
            closeDashboardScanner();
            let parsedData = {};
            decodedText.split('|').forEach(chunk => {
                let idx = chunk.indexOf(':');
                if (idx === -1) return;
                let key = chunk.substring(0, idx).trim();
                let val = chunk.substring(idx + 1).trim();
                if (key && val) parsedData[key] = val;
            });

            if (!parsedData.LOTE) {
                alert("El código QR no contiene información válida de Lote.");
                return;
            }
            window.location.href = `/traceability?search=${encodeURIComponent(parsedData.LOTE)}`;
        }

        function printQR() {
            const qrImage = document.querySelector('#qr-container img');
            if (!qrImage) return;

            const productoTxt = @json($latestInventory->producto ?? '');
            const loteTxt = @json($latestInventory->lote ?? '');

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Imprimir Etiqueta QR</title>
                        <style>
                            body { font-family: monospace; text-align: center; padding: 20px; }
                            .qr-container { display: inline-block; padding: 14px; border: 1px dashed #333; border-radius: 8px; }
                            img { width: 150px; height: 150px; }
                            h2 { margin: 10px 0 5px 0; font-size: 14px; }
                            p { margin: 0; font-size: 12px; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class="qr-container">
                            <img src="${qrImage.src}" />
                            <h2>${productoTxt}</h2>
                            <p>Lote: ${loteTxt}</p>
                        </div>
                        <script>
                            window.onload = function() { window.print(); window.close(); }
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
