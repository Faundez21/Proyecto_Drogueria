@extends('layouts.app')
@section('content')
    <div class="w-full px-4 sm:px-6 pt-0 pb-6 space-y-5">

        <!-- CABECERA -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-2">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-[13px] font-medium text-slate-500 mt-0.5">Resumen operativo y estado general de la Droguería</p>
            </div>

            <button type="button" onclick="openDashboardScanner()"
                class="flex-1 sm:flex-none items-center justify-center gap-2 bg-slate-800 text-white px-5 py-2.5 rounded-xl text-[13px] font-bold hover:bg-slate-900 transition-all shadow-sm cursor-pointer inline-flex">
                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                Escanear Lote QR
            </button>
        </div>

        <!-- SOLICITUDES ASIGNADAS AL TRABAJADOR LOGUEADO -->
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
                            <h2 class="text-[14px] font-extrabold text-slate-800">
                                Tienes {{ count($misSolicitudesAsignadas) }} solicitud(es) asignada(s) para despachar
                            </h2>
                            <p class="text-[12px] text-slate-500">
                                Fueron asignadas a tu usuario por Administración. Haz clic en "Despachar Ahora" para cargarlas automáticamente.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('dispatch.index') }}"
                        class="px-4 py-2 rounded-xl text-[12px] font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition-colors text-center shrink-0">
                        Ir al Módulo de Despacho &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($misSolicitudesAsignadas as $miSol)
                        @php
                            $esCesfamMiSol = in_array($miSol->establecimiento, $nombresCesfam ?? []);
                        @endphp
                        <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex items-center justify-between gap-3 shadow-xs">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono font-extrabold text-[12px] text-slate-900">{{ $miSol->numero_solicitud }}</span>
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase {{ $esCesfamMiSol ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ $esCesfamMiSol ? 'CESFAM' : 'Establecimiento' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase bg-indigo-100 text-indigo-700">
                                        {{ $miSol->estado }}
                                    </span>
                                </div>
                                <p class="text-[13px] font-bold text-slate-800 truncate mt-1" title="{{ $miSol->establecimiento }}">
                                    {{ $miSol->establecimiento }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    {{ $miSol->items->count() }} producto(s) · {{ \Carbon\Carbon::parse($miSol->fecha_solicitud)->format('d/m/Y') }}
                                </p>
                            </div>
                            <a href="{{ route('dispatch.index', ['solicitud_id' => $miSol->id]) }}"
                                class="px-3 py-2 rounded-lg text-[11.5px] font-bold bg-slate-800 hover:bg-teal-600 text-white transition-colors shrink-0">
                                Despachar Ahora
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FILTROS GLOBALES AVANZADOS (BODEGA + ESTABLECIMIENTOS Y CESFAM) -->
        <form method="GET" action="{{ route('dashboard.index') }}" id="dashboardFilterForm"
            class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col gap-3 w-full">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </div>
                    <span class="text-[12px] font-extrabold text-slate-700 uppercase tracking-wide">Filtros Operativos Globales (Droguería, Establecimientos y CESFAM)</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard.index') }}" class="px-3.5 py-1.5 rounded-lg text-[11.5px] font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">Limpiar Filtros</a>
                    <button type="submit" class="px-4 py-1.5 rounded-lg text-[11.5px] font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-colors cursor-pointer">Aplicar Filtros</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3 w-full">
                <!-- Periodo Rápido -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Período</label>
                    <select name="periodo" id="dash_periodo" onchange="onDashboardPeriodoChange(this.value)"
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ $periodo == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                        <option value="anio" {{ $periodo == 'anio' ? 'selected' : '' }}>Este Año</option>
                        <option value="historico" {{ $periodo == 'historico' ? 'selected' : '' }}>Histórico Total</option>
                        <option value="custom" {{ $periodo == 'custom' ? 'selected' : '' }}>Rango Personalizado</option>
                    </select>
                </div>

                <!-- Tipo de Establecimiento -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipo Centro</label>
                    <select name="tipo_establecimiento" id="dash_tipo_est" onchange="filtrarOpcionesEstablecimientoDash()"
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="" {{ empty($tipo_establecimiento) ? 'selected' : '' }}>Todos (Red Comunal)</option>
                        <option value="cesfam" {{ ($tipo_establecimiento ?? '') === 'cesfam' ? 'selected' : '' }}>Solo CESFAM</option>
                        <option value="establecimiento" {{ ($tipo_establecimiento ?? '') === 'establecimiento' ? 'selected' : '' }}>Solo Establecimientos</option>
                    </select>
                </div>

                <!-- Establecimiento / CESFAM Específico -->
                <div class="flex flex-col xl:col-span-2">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Establecimiento / CESFAM</label>
                    <select name="establecimiento" id="dash_establecimiento"
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="">Todos los Centros</option>
                        <optgroup label="CESFAM" id="optgroup_dash_cesfam">
                            @foreach ($nombresCesfam ?? [] as $nomCesfam)
                                <option value="{{ $nomCesfam }}" data-tipo-centro="cesfam" {{ ($establecimiento ?? '') === $nomCesfam ? 'selected' : '' }}>
                                    {{ $nomCesfam }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Establecimientos (CECOSF / SAR / Otros)" id="optgroup_dash_estab">
                            @foreach ($nombresNoCesfam ?? [] as $nomEst)
                                <option value="{{ $nomEst }}" data-tipo-centro="establecimiento" {{ ($establecimiento ?? '') === $nomEst ? 'selected' : '' }}>
                                    {{ $nomEst }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Estado Solicitud -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estado Solicitud</label>
                    <select name="estado_solicitud" class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="">Todos</option>
                        <option value="Pendiente" {{ ($estado_solicitud ?? '') === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Asignada" {{ ($estado_solicitud ?? '') === 'Asignada' ? 'selected' : '' }}>Asignada</option>
                        <option value="Parcial" {{ ($estado_solicitud ?? '') === 'Parcial' ? 'selected' : '' }}>Parcial</option>
                        <option value="Despachado" {{ ($estado_solicitud ?? '') === 'Despachado' ? 'selected' : '' }}>Despachado</option>
                    </select>
                </div>

                <!-- Programa -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Programa</label>
                    <select name="programa" class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="">Todos</option>
                        @foreach ($programasLista as $prog)
                            <option value="{{ $prog }}" {{ $programa == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Categoría Bodega -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Categoría</label>
                    <select name="categoria" class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-2 rounded-lg text-[12px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer w-full">
                        <option value="">Todas</option>
                        @foreach ($categoriasLista as $cat)
                            <option value="{{ $cat }}" {{ $categoria == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fechas Personalizadas -->
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Desde / Hasta</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="date" name="fecha_desde" id="dash_fecha_desde" value="{{ $fecha_desde }}"
                            onchange="document.getElementById('dash_periodo').value='custom'"
                            title="Fecha Desde"
                            class="bg-slate-50 border border-slate-200 text-slate-700 px-2 py-1.5 rounded-lg text-[11px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 w-full">
                        <input type="date" name="fecha_hasta" id="dash_fecha_hasta" value="{{ $fecha_hasta }}"
                            onchange="document.getElementById('dash_periodo').value='custom'"
                            title="Fecha Hasta"
                            class="bg-slate-50 border border-slate-200 text-slate-700 px-2 py-1.5 rounded-lg text-[11px] font-bold outline-none focus:ring-2 focus:ring-teal-500/20 w-full">
                    </div>
                </div>
            </div>
        </form>

        @php
            $periodoTexto = ['hoy' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'anio' => 'Año', 'historico' => 'Histórico', 'custom' => 'Fechas'][$periodo] ?? 'Mes';
        @endphp

        <!-- 1. GRID DE KPIs BODEGA CENTRAL (DROGUERÍA) -->
        <div>
            <div class="flex items-center justify-between mb-2.5 px-1">
                <h2 class="text-[11.5px] font-extrabold text-slate-500 uppercase tracking-widest">1. Indicadores de Bodega Central (Droguería)</h2>
                @if ($establecimiento || $tipo_establecimiento || $programa || $categoria)
                    <span class="text-[11px] font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">
                        Vista filtrada activa
                    </span>
                @endif
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Valor Capital</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">${{ number_format($valorInventario, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-emerald-600 font-extrabold mt-1 tracking-wider uppercase">En Bodega Activa</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-teal-50 rounded-lg text-teal-600 border border-teal-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Unidades Físicas</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($unidadesTotales, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">Stock Disponible</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 border border-emerald-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ingresos ({{ $periodoTexto }})</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($recepcionesPeriodo, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">{{ number_format($totalRecepciones, 0, ',', '.') }} Históricas</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 border border-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Despachos ({{ $periodoTexto }})</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($despachosPeriodo, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">{{ number_format($totalDespachos, 0, ',', '.') }} Históricos</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-l-[3px] border-l-rose-500">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-rose-50 rounded-lg text-rose-600 border border-rose-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Próx. a Vencer</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($proximosVencer, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-rose-500 font-extrabold mt-1 tracking-wider uppercase">Próximos 30 Días</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-l-[3px] border-l-amber-500">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-amber-50 rounded-lg text-amber-600 border border-amber-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">En Cuarentena</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($cantidadCuarentena, 0, ',', '.') }}</h3>
                    <p class="text-[9px] text-amber-600 font-extrabold mt-1 tracking-wider uppercase">Lotes Bloqueados</p>
                </div>

            </div>
        </div>

        <!-- 2. NUEVO GRID DE KPIs DE ESTABLECIMIENTOS Y CESFAM -->
        <div>
            <div class="flex items-center justify-between mb-2.5 px-1">
                <h2 class="text-[11.5px] font-extrabold text-slate-500 uppercase tracking-widest">2. Indicadores de Solicitudes, Stock y Despachos por Establecimientos y CESFAM ({{ $periodoTexto }})</h2>
                <div class="flex items-center gap-2">
                    @can('ver.solicitudes_cesfam')
                        <a href="{{ route('requests.cesfam') }}" class="text-[11px] font-bold text-purple-700 hover:text-purple-900 bg-purple-50 px-2.5 py-1 rounded-md border border-purple-200 transition-colors">
                            + Solicitud CESFAM
                        </a>
                    @endcan
                    @can('ver.solicitudes')
                        <a href="{{ route('requests.index') }}" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-200 transition-colors">
                            + Solicitud Establecimiento
                        </a>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- KPI 1: Solicitudes CESFAM -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-purple-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-purple-50 rounded-lg text-purple-600 border border-purple-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Solicitudes CESFAM</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($solicitudesCesfamCount ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[9.5px] font-extrabold mt-1 tracking-wide uppercase text-purple-600">
                        {{ $cesfamActivasCount ?? 0 }} activas · {{ $cesfamDespachadasCount ?? 0 }} desp.
                    </p>
                </div>

                <!-- KPI 2: Solicitudes Establecimientos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-teal-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-teal-50 rounded-lg text-teal-600 border border-teal-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Solic. Establecim.</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($solicitudesEstablecimientosCount ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[9.5px] font-extrabold mt-1 tracking-wide uppercase text-teal-600">
                        {{ $estabActivasCount ?? 0 }} activas · {{ $estabDespachadasCount ?? 0 }} desp.
                    </p>
                </div>

                <!-- KPI 3: Por Despachar / Asignadas -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-indigo-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 border border-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Por Despachar</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($solicitudesPorDespacharCount ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[9.5px] font-extrabold mt-1 tracking-wide uppercase text-indigo-600">
                        {{ $solicitudesAsignadasCount ?? 0 }} asignadas · {{ $solicitudesSinAsignarCount ?? 0 }} sin asignar
                    </p>
                </div>

                <!-- KPI 4: Unidades Despachadas a CESFAM -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-blue-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Uds. a CESFAM</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($unidadesDespachadasCesfam ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[9.5px] text-blue-600 font-extrabold mt-1 tracking-wide uppercase">Despachadas ({{ $periodoTexto }})</p>
                </div>

                <!-- KPI 5: Unidades Despachadas a Establecimientos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-cyan-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-cyan-50 rounded-lg text-cyan-600 border border-cyan-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Uds. a Establecim.</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($unidadesDespachadasEstablecimientos ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[9.5px] text-cyan-600 font-extrabold mt-1 tracking-wide uppercase">Despachadas ({{ $periodoTexto }})</p>
                </div>

                <!-- KPI 6: Stock Declarado y Cumplimiento -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-t-[3px] border-t-emerald-500">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 border border-emerald-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Stock Red / Cumpl.</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">{{ number_format($stockDeclaradoRed ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">uds</span></h3>
                    <p class="text-[9.5px] text-emerald-600 font-extrabold mt-1 tracking-wide uppercase">
                        {{ $porcentajeCumplimientoRed ?? 0 }}% Entregado vs Solicitado
                    </p>
                </div>
            </div>
        </div>

        <!-- TABLA COMPARATIVA: ESTADO OPERATIVO POR ESTABLECIMIENTO Y CESFAM -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Resumen Operativo por Establecimiento y CESFAM
                    </h2>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">
                        Monitoreo de solicitudes, consumo mensual anterior, stock actual declarado y unidades despachadas por centro.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="buscadorTablaCentros" oninput="filtrarTablaCentrosDash()"
                        placeholder="Filtrar centro en tabla..."
                        class="bg-slate-50 border border-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-[12px] font-medium outline-none focus:ring-2 focus:ring-teal-500/20">
                    <a href="{{ route('dispatch.index') }}" class="text-[12px] font-bold text-teal-700 hover:text-teal-900 bg-teal-50 px-3 py-1.5 rounded-lg border border-teal-200 transition-colors whitespace-nowrap">
                        Gestionar Despachos &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[340px]">
                <table class="w-full text-left text-[12px] text-slate-600 whitespace-nowrap">
                    <thead class="text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200 bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3">Establecimiento / CESFAM</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-center">Solicitudes</th>
                            <th class="px-4 py-3 text-center">Consumo Mes Ant.</th>
                            <th class="px-4 py-3 text-center">Stock Actual Declarado</th>
                            <th class="px-4 py-3 text-center">Solicitado / Despachado</th>
                            <th class="px-4 py-3 text-center">Cumplimiento</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="tbodyResumenCentros">
                        @forelse ($resumenEstablecimientos ?? [] as $rowCentro)
                            <tr class="fila-centro-dash hover:bg-slate-50/80 transition-colors"
                                data-nombre="{{ mb_strtolower($rowCentro->nombre . ' ' . $rowCentro->tipo) }}">
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $rowCentro->nombre }}
                                    @if ($rowCentro->ultima_solicitud)
                                        <span class="block text-[10px] font-normal text-slate-400">
                                            Últ. solicitud: {{ \Carbon\Carbon::parse($rowCentro->ultima_solicitud)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $rowCentro->es_cesfam ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                        {{ $rowCentro->es_cesfam ? 'CESFAM' : $rowCentro->tipo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-extrabold text-slate-800">{{ $rowCentro->total_solicitudes }}</span>
                                    @if ($rowCentro->activas > 0)
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9.5px] font-extrabold">
                                            {{ $rowCentro->activas }} act.
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-700">
                                    {{ number_format($rowCentro->consumo_anterior, 0, ',', '.') }} uds.
                                </td>
                                <td class="px-4 py-3 text-center font-extrabold text-blue-700">
                                    {{ number_format($rowCentro->stock_declarado, 0, ',', '.') }} uds.
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-extrabold text-teal-700">{{ number_format($rowCentro->unidades_solicitadas, 0, ',', '.') }}</span>
                                    <span class="text-slate-400 mx-0.5">/</span>
                                    <span class="font-extrabold text-indigo-700">{{ number_format($rowCentro->unidades_despachadas, 0, ',', '.') }} uds.</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $rowCentro->cumplimiento >= 80 ? 'bg-emerald-500' : ($rowCentro->cumplimiento >= 40 ? 'bg-amber-500' : 'bg-slate-300') }}"
                                                style="width: {{ $rowCentro->cumplimiento }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-extrabold text-slate-700">{{ $rowCentro->cumplimiento }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('dashboard.index', ['establecimiento' => $rowCentro->nombre, 'periodo' => $periodo]) }}"
                                        class="text-[11px] font-bold text-teal-600 hover:text-teal-800 underline">
                                        Filtrar KPI
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400 font-medium">
                                    No hay establecimientos que coincidan con el filtro seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. SECCIÓN PRINCIPAL: Trazabilidad y QR -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Flujo Reciente de Movimientos -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col min-h-[380px]">
                <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Flujo Reciente de Movimientos
                    </h2>
                    <a href="{{ route('traceability.index') }}" class="text-[12px] font-bold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1 rounded-md">Ver Kardex completo &rarr;</a>
                </div>

                <div class="space-y-0 flex-1 overflow-y-auto pr-2 pl-2">
                    @if (count($recentMovements) > 0)
                        @foreach ($recentMovements as $mov)
                            @php
                                $isRec = $mov->tipo_movimiento === 'Recepción';
                                $dotColor = $isRec ? 'bg-emerald-500 ring-emerald-50' : 'bg-blue-500 ring-blue-50';
                                $badgeColor = $isRec ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200';
                            @endphp
                            <div class="flex gap-4 relative">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 {{ $dotColor }} rounded-full mt-2 ring-4"></div>
                                    <div class="w-0.5 h-full bg-slate-100 my-1"></div>
                                </div>
                                <div class="pb-6 w-full">
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-widest border {{ $badgeColor }}">
                                                {{ $mov->tipo_movimiento }}
                                            </span>
                                            <p class="text-[13px] font-bold text-slate-800">{{ $mov->origen_destino }}</p>
                                        </div>
                                        <span class="text-[10.5px] font-bold text-slate-400 font-mono">{{ \Carbon\Carbon::parse($mov->fecha_hora)->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[12.5px] font-medium text-slate-600 mt-1">{{ $mov->producto }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <p class="text-[11px] text-slate-500 font-mono border border-slate-200 bg-slate-50 inline-block px-2 py-0.5 rounded shadow-sm">Lote: {{ $mov->lote }}</p>
                                        <p class="text-[11px] font-bold {{ $isRec ? 'text-emerald-600' : 'text-blue-600' }}">
                                            Cant: {{ $isRec ? '+' : '-' }}{{ number_format($mov->cantidad, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-slate-400 py-10">
                            <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-[13px] font-medium">No hay movimientos con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <!-- Accesos Rápidos Compactos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-4">Módulos Principales</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('reception.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Recepción</span>
                        </a>
                        <a href="{{ route('dispatch.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Despacho</span>
                        </a>
                        <a href="{{ route('traceability.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-teal-50 hover:border-teal-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Trazabilidad</span>
                        </a>
                        <a href="{{ route('inventario.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Inventario</span>
                        </a>
                        <a href="{{ route('requests.index') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 leading-tight">Solicitudes Estab.</span>
                        </a>
                        <a href="{{ route('requests.cesfam') }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-purple-50 hover:border-purple-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 leading-tight">Solicitudes Cesfam</span>
                        </a>
                    </div>
                </div>

                <!-- Panel Sistema QR (Dinámico) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                        <h2 class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Último Lote Ingresado
                        </h2>
                    </div>

                    @if ($latestInventory)
                        <div class="flex flex-col items-center flex-1 justify-center">
                            <!-- Marco del QR -->
                            <div class="w-36 h-36 bg-slate-50 p-3 rounded-2xl mb-5 shadow-sm border border-slate-200 relative group" id="qr-container">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=PROD:{{ urlencode($latestInventory->producto) }}|LOTE:{{ urlencode($latestInventory->lote) }}|STOCK:{{ $latestInventory->cantidad }}"
                                    alt="QR Code" class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                            </div>

                            <!-- Datos del Lote -->
                            <div class="text-center w-full bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <h3 class="font-black text-[14px] text-slate-800 leading-tight mb-2 truncate px-2" title="{{ $latestInventory->producto }}">{{ $latestInventory->producto }}</h3>
                                <div class="flex justify-center items-center gap-2 mb-3">
                                    <span class="text-[11px] font-mono font-bold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200 shadow-sm">Lote: {{ $latestInventory->lote ?? 'S/L' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-left bg-white p-3 rounded-lg border border-slate-100">
                                    <div>
                                        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Stock</p>
                                        <p class="text-[14px] font-black text-teal-600">{{ $latestInventory->cantidad }} <span class="text-[10px] font-bold text-slate-500">Uds</span></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Ubicación</p>
                                        <p class="text-[12px] font-bold text-slate-700">P{{ $latestInventory->pasillo ?? '0' }}-E{{ $latestInventory->estante ?? '0' }}</p>
                                    </div>
                                </div>
                            </div>

                            <button onclick="printQR()" class="w-full mt-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Imprimir Etiqueta
                            </button>
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-slate-400">
                            <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-200 mb-3 shadow-sm">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <p class="text-[13px] font-bold text-slate-600">Bodega sin stock</p>
                            <p class="text-[11.5px] text-slate-500 mt-1 text-center px-4">Realiza una recepción para generar automáticamente el código QR.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. PANEL INFERIOR: Lotes en Cuarentena -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse ring-4 ring-amber-50"></span>
                    Lotes Retenidos / En Cuarentena
                </h2>
                <a href="{{ route('quarantine.index') }}" class="text-[12px] font-bold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1 rounded-md">Gestionar cuarentenas</a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-[12px] text-slate-600">
                    <thead class="text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Lote</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Días en Espera</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if (count($lotesCuarentenaDb) > 0)
                            @foreach ($lotesCuarentenaDb as $cuarentena)
                                @php
                                    $dias = (int) floor(\Carbon\Carbon::parse($cuarentena->created_at)->startOfDay()->diffInDays(now()->startOfDay()));
                                @endphp
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-slate-800">{{ $cuarentena->producto }}</td>
                                    <td class="px-4 py-3.5 font-mono font-bold text-slate-500">{{ $cuarentena->lote }}</td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-extrabold uppercase rounded-md border border-amber-200">En Revisión</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-black text-slate-700">
                                        {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-400 font-medium">
                                    <svg class="w-10 h-10 mx-auto text-emerald-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-[14px] font-bold text-slate-600 mb-1">Excelente trabajo</p>
                                    No hay lotes retenidos en cuarentena con los filtros actuales.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE ESCÁNER QR DE DIRECCIONAMIENTO -->
    <div id="scannerModalDashboard" class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
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

    <!-- SCRIPT DEL ESCÁNER QR PARA EL DASHBOARD -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let dashboardScanner = null;

        function openDashboardScanner() {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                alert("⚠️ ATENCIÓN: Por seguridad, los navegadores bloquean la cámara si no usas una conexión HTTPS o localhost.");
            }

            document.getElementById('scannerModalDashboard').classList.remove('hidden');

            dashboardScanner = new Html5QrcodeScanner(
                "reader-dashboard",
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0, supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] },
                false
            );
            dashboardScanner.render(onScanDashboardSuccess, onScanDashboardFailure);
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
            let chunks = decodedText.split('|');
            chunks.forEach(chunk => {
                let idx = chunk.indexOf(':');
                if (idx === -1) return;
                let key = chunk.substring(0, idx).trim();
                let val = chunk.substring(idx + 1).trim();
                if (key && val) parsedData[key] = val;
            });

            if (!parsedData.LOTE) {
                alert("El código QR no es válido o no contiene información de Lote.");
                return;
            }

            window.location.href = `/traceability?search=${encodeURIComponent(parsedData.LOTE)}`;
        }

        function onScanDashboardFailure(error) {
            // Ignorado intencionalmente
        }

        function printQR() {
            const qrImage = document.querySelector('#qr-container img');
            if (!qrImage) {
                alert('No hay código QR para imprimir.');
                return;
            }

            const productoTxt = @json($latestInventory->producto ?? '');
            const loteTxt = @json($latestInventory->lote ?? '');

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Imprimir Etiqueta QR</title>
                        <style>
                            body { font-family: monospace; text-align: center; padding: 20px; }
                            .qr-container { display: inline-block; padding: 10px; border: 1px solid #ccc; border-radius: 8px; }
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

        function onDashboardPeriodoChange(selectEl) {
            const box = document.getElementById('dashFechasPersonalizadas');
            if (!box) return;
            if (selectEl.value === 'personalizado') {
                box.classList.remove('hidden');
                box.classList.add('grid');
            } else {
                box.classList.add('hidden');
                box.classList.remove('grid');
                document.getElementById('dashboardFilterForm')?.submit();
            }
        }

        function filtrarOpcionesEstablecimientoDash() {
            const tipoSel = document.getElementById('dashTipoEstablecimiento')?.value || 'todos';
            const estSelect = document.getElementById('dashEstablecimientoSelect');
            if (!estSelect) return;

            Array.from(estSelect.options).forEach(opt => {
                if (!opt.value) return;
                const tipoOpt = opt.getAttribute('data-tipo');
                const visible = (tipoSel === 'todos') || (tipoSel === tipoOpt);
                opt.hidden = !visible;
            });

            if (estSelect.selectedOptions[0] && estSelect.selectedOptions[0].hidden) {
                estSelect.value = '';
            }
        }

        function filtrarTablaCentrosDash(tipo) {
            document.querySelectorAll('.btn-tab-centro').forEach(btn => {
                const active = btn.getAttribute('data-tab') === tipo;
                btn.className = active
                    ? 'btn-tab-centro px-3 py-1 rounded-md text-[11px] font-extrabold bg-white text-slate-800 shadow-sm cursor-pointer'
                    : 'btn-tab-centro px-3 py-1 rounded-md text-[11px] font-bold text-slate-500 hover:text-slate-700 cursor-pointer';
            });

            document.querySelectorAll('.fila-resumen-centro').forEach(row => {
                const rowTipo = row.getAttribute('data-tipo-centro');
                const visible = (tipo === 'todos') || (rowTipo === tipo);
                row.classList.toggle('hidden', !visible);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            filtrarOpcionesEstablecimientoDash();
        });
    </script>
@endsection