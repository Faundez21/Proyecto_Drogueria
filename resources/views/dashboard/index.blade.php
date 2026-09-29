@extends('layouts.app')
@section('content')
    <div class="w-full px-4 sm:px-6 pt-0 pb-6 space-y-5">

        <!-- CABECERA Y FILTRO GLOBAL -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-[13px] font-medium text-slate-500 mt-0.5">Resumen operativo y estado general de la Droguería
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
                <!-- Filtro Global de Período -->
                <form method="GET" action="{{ route('dashboard.index') }}"
                    class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200 w-full sm:w-auto">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider pl-2">Mostrar:</span>
                    <select name="periodo" onchange="this.form.submit()"
                        class="bg-white border border-slate-300 text-slate-700 px-3 py-1.5 rounded-lg text-[12px] font-bold shadow-sm outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                        <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ $periodo == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                        <option value="anio" {{ $periodo == 'anio' ? 'selected' : '' }}>Este Año</option>
                    </select>
                </form>

                <div class="h-6 w-px bg-slate-200 hidden sm:block mx-1"></div>

                <button type="button" onclick="openDashboardScanner()"
                    class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-all shadow-sm cursor-pointer inline-flex">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                        </path>
                    </svg>
                    Escanear Lote QR
                </button>
            </div>
        </div>

        <!-- 1. GRID DE KPIs (MÉTRICAS CLAVES) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Valor Capital</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">${{ number_format($valorInventario, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-emerald-600 font-extrabold mt-1 tracking-wider uppercase">En Bodega Activa</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-teal-50 rounded-lg text-teal-600 border border-teal-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Unidades Físicas</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">{{ number_format($unidadesTotales, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">Stock Disponible</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    @php
                        $periodoTexto =
                            [
                                'hoy' => 'Hoy',
                                'semana' => 'Semana',
                                'mes' => 'Mes',
                                'anio' => 'Año',
                            ][$periodo] ?? 'Mes';
                    @endphp
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ingresos
                        ({{ $periodoTexto }})</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">{{ $recepcionesPeriodo }}</h3>
                <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">
                    {{ number_format($totalRecepciones, 0, ',', '.') }} Históricas</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 border border-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Despachos
                        ({{ $periodoTexto }})</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">{{ $despachosPeriodo }}</h3>
                <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">
                    {{ number_format($totalDespachos, 0, ',', '.') }} Históricos</p>
            </div>

            <div
                class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-l-[3px] border-l-rose-500">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-rose-50 rounded-lg text-rose-600 border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Próx. a Vencer</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">{{ $proximosVencer }}</h3>
                <p class="text-[9px] text-rose-500 font-extrabold mt-1 tracking-wider uppercase">Próximos 30 Días</p>
            </div>

            <div
                class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between border-l-[3px] border-l-amber-500">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600 border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">En Cuarentena</p>
                </div>
                <h3 class="text-xl font-black text-slate-800">{{ $cantidadCuarentena }}</h3>
                <p class="text-[9px] text-amber-600 font-extrabold mt-1 tracking-wider uppercase">Bloqueados temporal</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-50 rounded-lg text-teal-600 border border-teal-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Consumo promedio
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">

                </div>

                <div>
                    <h3 class="text-xl font-black text-slate-800">
                        125
                    </h3>

                    <p class="text-[9px] text-slate-400 font-extrabold mt-1 tracking-wider uppercase">
                        Unidades promedio mensual
                    </p>
                </div>

            </div>

        </div>

        <!-- 2. SECCIÓN PRINCIPAL: Trazabilidad y QR -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Flujo Reciente de Movimientos -->
            <div
                class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col min-h-[380px]">
                <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
                    <h2
                        class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Flujo Reciente de Movimientos
                    </h2>
                    <a href="{{ route('traceability.index') ?? '#' }}"
                        class="text-[12px] font-bold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1 rounded-md">Ver
                        Kardex completo &rarr;</a>
                </div>

                <div class="space-y-0 flex-1 overflow-y-auto pr-2 pl-2">
                    <?php if(count($recentMovements) > 0): ?>
                    <?php foreach($recentMovements as $mov): ?>
                    <?php
                    $isRec = $mov->tipo === 'Recepción';
                    $dotColor = $isRec ? 'bg-emerald-500 ring-emerald-50' : 'bg-blue-500 ring-blue-50';
                    $badgeColor = $isRec ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200';
                    ?>
                    <div class="flex gap-4 relative">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 <?php echo $dotColor; ?> rounded-full mt-2 ring-4"></div>
                            <div class="w-0.5 h-full bg-slate-100 my-1"></div>
                        </div>
                        <div class="pb-6 w-full">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-widest border <?php echo $badgeColor; ?>">
                                        {{ $mov->tipo }}
                                    </span>
                                    <p class="text-[13px] font-bold text-slate-800">{{ $mov->entidad }}</p>
                                </div>
                                <span
                                    class="text-[10.5px] font-bold text-slate-400 font-mono">{{ \Carbon\Carbon::parse($mov->fecha)->diffForHumans() }}</span>
                            </div>
                            <p class="text-[12.5px] font-medium text-slate-600 mt-1">{{ $mov->producto }}</p>
                            <div class="flex items-center gap-3 mt-1.5">
                                <p
                                    class="text-[11px] text-slate-500 font-mono border border-slate-200 bg-slate-50 inline-block px-2 py-0.5 rounded shadow-sm">
                                    Lote: {{ $mov->lote }}</p>
                                <p class="text-[11px] font-bold <?php echo $isRec ? 'text-emerald-600' : 'text-blue-600'; ?>">
                                    Cant: <?php echo $isRec ? '+' : '-'; ?>{{ number_format($mov->cantidad, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-full text-slate-400">
                        <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-[13px] font-medium">No hay movimientos registrados.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <!-- Accesos Rápidos Compactos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-4">Módulos
                        Principales</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('reception.index') ?? '#' }}"
                            class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                            <div
                                class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Recepción</span>
                        </a>
                        <a href="{{ route('dispatch.index') ?? '#' }}" class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg></div>
                            <span class="text-[12px] font-bold text-slate-700">Despacho</span>
                        </a>
                        <a href="{{ route('traceability.index') ?? '#' }}"
                            class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-teal-50 hover:border-teal-200 transition-colors group">
                            <div
                                class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Trazabilidad</span>
                        </a>
                        <a href="{{ route('inventario.index') ?? '#' }}"
                            class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition-colors group">
                            <div
                                class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-[12px] font-bold text-slate-700">Inventario</span>
                        </a>
                    </div>
                </div>

                <!-- Panel Sistema QR (Dinámico) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                        <h2
                            class="text-[13px] font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                            Último Lote Ingresado
                        </h2>
                    </div>

                    <?php if($latestInventory): ?>
                    <div class="flex flex-col items-center flex-1 justify-center">
                        <!-- Marco del QR -->
                        <div class="w-36 h-36 bg-slate-50 p-3 rounded-2xl mb-5 shadow-sm border border-slate-200 relative group"
                            id="qr-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=PROD:{{ urlencode($latestInventory->producto) }}|LOTE:{{ urlencode($latestInventory->lote) }}|STOCK:{{ $latestInventory->cantidad }}"
                                alt="QR Code"
                                class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                        </div>

                        <!-- Datos del Lote -->
                        <div class="text-center w-full bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h3 class="font-black text-[14px] text-slate-800 leading-tight mb-2 truncate px-2"
                                title="{{ $latestInventory->producto }}">{{ $latestInventory->producto }}</h3>
                            <div class="flex justify-center items-center gap-2 mb-3">
                                <span
                                    class="text-[11px] font-mono font-bold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200 shadow-sm">Lote:
                                    {{ $latestInventory->lote ?? 'S/L' }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-left bg-white p-3 rounded-lg border border-slate-100">
                                <div>
                                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Stock
                                        Restante</p>
                                    <p class="text-[14px] font-black text-teal-600">{{ $latestInventory->cantidad }} <span
                                            class="text-[10px] font-bold text-slate-500">Uds</span></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Ubicación
                                    </p>
                                    <p class="text-[12px] font-bold text-slate-700">
                                        P{{ $latestInventory->pasillo ?? '0' }}-E{{ $latestInventory->estante ?? '0' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button onclick="printQR()"
                            class="w-full mt-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            Imprimir Etiqueta
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="h-full flex flex-col items-center justify-center text-slate-400">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-200 mb-3 shadow-sm">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-[13px] font-bold text-slate-600">Bodega sin stock</p>
                        <p class="text-[11.5px] text-slate-500 mt-1 text-center px-4">Realiza una recepción para generar
                            automáticamente el código QR.</p>
                    </div>
                    <?php endif; ?>
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
                <a href="#"
                    class="text-[12px] font-bold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1 rounded-md">Gestionar
                    cuarentenas</a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-[12px] text-slate-600">
                    <thead
                        class="text-[10px] font-extrabold uppercase text-slate-400 border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Lote</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Días en Espera</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(count($lotesCuarentenaDb) > 0): ?>
                        <?php foreach($lotesCuarentenaDb as$cuarentena): ?>
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-slate-800">{{ $cuarentena->producto }}</td>
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-500">{{ $cuarentena->lote }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span
                                    class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-extrabold uppercase rounded-md border border-amber-200">En
                                    Revisión</span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-black text-slate-700">
                                <?php
                                $dias = (int) floor(
                                    \Carbon\Carbon::parse($cuarentena->created_at)
                                        ->startOfDay()
                                        ->diffInDays(now()->startOfDay()),
                                );
                                ?>
                                {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400 font-medium">
                                <svg class="w-10 h-10 mx-auto text-emerald-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-[14px] font-bold text-slate-600 mb-1">Excelente trabajo</p>
                                No hay lotes retenidos en cuarentena actualmente.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE ESCÁNER QR DE DIRECCIONAMIENTO    -->
    <div id="scannerModalDashboard"
        class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4 transition-all">
        <div
            class="bg-white rounded-3xl shadow-2xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-800 to-slate-900 flex justify-between items-center">
                <div>
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Escanear Lote Clínico</h2>
                    <p class="text-[11px] text-teal-300 mt-0.5 uppercase tracking-wider font-semibold">Búsqueda rápida en
                        Kardex</p>
                </div>
                <button type="button" onclick="closeDashboardScanner()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/10 p-2 rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6 bg-slate-50 flex flex-col items-center">
                <div
                    class="bg-white border-[3px] border-slate-200 border-dashed rounded-2xl p-2 w-full max-w-sm flex flex-col items-center relative min-h-[250px]">
                    <div id="reader-dashboard"
                        class="w-full rounded-xl overflow-hidden shadow-inner bg-black flex items-center justify-center text-white text-xs">
                        Iniciando cámara...</div>
                </div>
                <p class="text-[12px] font-medium text-slate-500 mt-5 text-center leading-relaxed max-w-sm">
                    Apunta la cámara a la etiqueta QR del producto. El sistema te redirigirá automáticamente a su Ficha de
                    Trazabilidad.
                </p>
            </div>

            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeDashboardScanner()"
                    class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">Cancelar
                    Escaneo</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT DEL ESCÁNER QR PARA EL DASHBOARD -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let dashboardScanner = null;

        function openDashboardScanner() {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                alert(
                    "⚠️ ATENCIÓN: Por seguridad, los navegadores bloquean la cámara si no usas una conexión HTTPS o localhost."
                );
            }

            document.getElementById('scannerModalDashboard').classList.remove('hidden');

            dashboardScanner = new Html5QrcodeScanner(
                "reader-dashboard", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
                },
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
                let [key, val] = chunk.split(':');
                if (key && val) parsedData[key.trim()] = val.trim();
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
                            <h2>${"{{ $latestInventory->producto ?? '' }}"}</h2>
                            <p>Lote: ${"{{ $latestInventory->lote ?? '' }}"}</p>
                        </div>
                        <script>
                            window.onload = function() { window.print(); window.close(); }
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
@endsection
