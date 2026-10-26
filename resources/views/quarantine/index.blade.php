@extends('layouts.app')

@section('title', 'Productos en Cuarentena')
@section('subtitle', 'Lista de medicamentos e insumos bloqueados o retenidos preventivamente.')

@section('actions')
@role('Administrador')
    <button type="button" onclick="openHistory()"
        class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 hover:text-amber-600 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Ver Historial Avanzado
    </button>
@endrole
@endsection

@section('content')
    <div class="w-full space-y-6">

        <!-- Migas de pan -->
        <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-2 px-1 pt-2">
            <span class="text-slate-400 font-semibold">02</span>
            <span>Almacenamiento</span>
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-semibold text-slate-800">Cuarentena</span>
        </div>

        @php
            $totalLotes = $totalLotesGlobal ?? $inventories->total();
            $totalUnidades = $totalUnidadesGlobal ?? collect(isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [])->sum('cantidad');
            $valorRetenido = $valorTotalRetenido ?? 0;
        @endphp

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Lotes Retenidos</p>
                        <h4 class="text-3xl font-extrabold text-slate-800 font-mono-das">
                            {{ number_format($totalLotes, 0, ',', '.') }}
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Lotes activos en cuarentena (según filtros)</p>
                    </div>
                    <div class="p-3 bg-amber-500 text-white rounded-xl shadow-md shadow-amber-200 shrink-0">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-slate-100 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider mb-1">Unidades Inmovilizadas</p>
                        <h4 class="text-3xl font-extrabold text-slate-800 font-mono-das">
                            {{ number_format($totalUnidades, 0, ',', '.') }}
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Total acumulado de unidades retenidas</p>
                    </div>
                    <div class="p-3 bg-slate-700 text-white rounded-xl shadow-md shadow-slate-200 shrink-0">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-rose-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider mb-1">Valor Total Retenido</p>
                        <h4 class="text-3xl font-extrabold text-slate-800 font-mono-das">
                            $ {{ number_format($valorRetenido, 0, ',', '.') }}
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Valorización neta de stock en revisión</p>
                    </div>
                    <div class="p-3 bg-rose-600 text-white rounded-xl shadow-md shadow-rose-200 shrink-0">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directorio Físico -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <!-- Cabecera y Botón de Filtros -->
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-[15px] font-bold text-slate-800">Directorio Físico</h3>
                </div>
                <button type="button" id="toggle-filters-btn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-amber-700 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filtros Avanzados
                </button>
            </div>

            <!-- Panel de Filtros -->
            <form method="GET" action="{{ route('quarantine.index') }}" id="filters-panel" class="hidden bg-slate-50/50 border-b border-slate-200 p-5 shadow-inner">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <!-- Buscador -->
                    <div class="xl:col-span-2 relative" id="searchContainer">
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Buscar Producto / Lote</label>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Producto, categoría o lote..." autocomplete="off" onfocus="showSearchDropdown()" oninput="filterSearchDropdown()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                        
                        <div id="searchDropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            @if (isset($productosLista) && count($productosLista) > 0)
                                @foreach ($productosLista as $prodNombre)
                                    <div class="search-item px-3.5 py-2.5 text-[12.5px] font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-900 cursor-pointer transition-colors flex items-center justify-between" onclick="selectSearchProduct('{{ addslashes($prodNombre) }}')">
                                        <span>{{ $prodNombre }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Filtros Select -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría</label>
                        <select name="categoria" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Todas</option>
                            <option value="Fármacos" {{ request('categoria') == 'Fármacos' ? 'selected' : '' }}>Fármacos</option>
                            <option value="Insumos Clínicos" {{ request('categoria') == 'Insumos Clínicos' ? 'selected' : '' }}>Insumos Clínicos</option>
                            @if(auth()->user()->hasRole('Administrador') || auth()->user()->can('ver.despacho_controlado'))
                                <option value="Fármacos Controlados" {{ request('categoria') == 'Fármacos Controlados' ? 'selected' : '' }}>Fármacos Controlados</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Pasillo</label>
                        <select name="pasillo" id="pasilloSelect" onchange="filterTableByLocation()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px]">
                            <option value="">Todos</option>
                            @foreach ($pasillosLista ?? [] as $pasillo)
                                <option value="{{ $pasillo }}" {{ request('pasillo') == $pasillo ? 'selected' : '' }}>{{ $pasillo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Estante</label>
                        <select name="estante" id="estanteSelect" onchange="filterTableByLocation()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px]">
                            <option value="">Todos</option>
                            @foreach ($estantesLista ?? [] as $est)
                                <option value="{{ $est }}" {{ request('estante') == $est ? 'selected' : '' }}>{{ $est }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ordenar por</label>
                        <select name="order_by" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px]">
                            <option value="recent" {{ request('order_by', 'recent') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                            <option value="nombre_asc" {{ request('order_by') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                            <option value="stock_desc" {{ request('order_by') == 'stock_desc' ? 'selected' : '' }}>Mayor Stock</option>
                            <option value="vencimiento" {{ request('order_by') == 'vencimiento' ? 'selected' : '' }}>Próximos a Vencer</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                    <a href="{{ route('quarantine.index') }}" class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                        Limpiar Filtros
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all cursor-pointer">
                        Aplicar Filtros
                    </button>
                </div>
            </form>

            <!-- Alerta Filtros Activos -->
            @if (request()->filled('pasillo') || request()->filled('estante') || request()->filled('categoria') || request()->filled('programa') || request()->filled('search'))
                <div id="activeFilterAlert" class="px-5 py-3 bg-amber-50 border-b border-amber-200 flex justify-between items-center text-xs text-amber-800 font-bold">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filtro activo: <span id="filteredProductName" class="underline">
                            {{ implode(' | ', array_filter([
                                request('search') ? 'Búsqueda: ' . request('search') : null,
                                request('categoria') ? 'Cat: ' . request('categoria') : null,
                                request('pasillo') ? 'Pasillo: ' . request('pasillo') : null,
                                request('estante') ? 'Estante: ' . request('estante') : null,
                            ])) }}
                        </span>
                    </span>
                    <a href="{{ route('quarantine.index') }}" class="px-3 py-1 bg-white border border-amber-300 rounded-lg text-amber-700 hover:bg-amber-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                        Limpiar Filtros
                    </a>
                </div>
            @else
                <div id="activeFilterAlert" class="hidden px-5 py-3 bg-amber-50 border-b border-amber-200 flex justify-between items-center text-xs text-amber-800 font-bold">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filtro activo: <span id="filteredProductName" class="underline"></span>
                    </span>
                    <button type="button" onclick="clearClientFilter()" class="px-3 py-1 bg-white border border-amber-300 rounded-lg text-amber-700 hover:bg-amber-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                        Limpiar Filtros
                    </button>
                </div>
            @endif

            <div class="overflow-x-auto w-full">
                @php
                    $invRaw = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];
                    $groupedInventories = collect($invRaw)->groupBy(function ($item) {
                        $prod = is_object($item) ? $item->product ?? null : null;
                        if ($prod && isset($prod->nombre)) return $prod->nombre;
                        if (is_object($item) && isset($item->producto)) return $item->producto;
                        return 'Producto General';
                    });
                @endphp

                <table class="w-full text-left text-sm whitespace-nowrap border-collapse min-w-[800px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-extrabold">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-14 text-center">Desglose</th>
                            <th scope="col" class="px-6 py-4">Producto Principal</th>
                            <th scope="col" class="px-6 py-4">Categoría</th>
                            <th scope="col" class="px-6 py-4 text-center">Lotes Retenidos</th>
                            <th scope="col" class="px-6 py-4 text-center">Stock Físico Inmovilizado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-[13px]">
                        @if ($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <p class="text-[15px] font-bold text-slate-600">No hay productos en cuarentena</p>
                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">Bodega limpia de retenciones con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($groupedInventories as $nombreProducto => $lotes)
                                @php
                                    $stockTotalProducto = $lotes->sum(function ($i) { return is_object($i) ? $i->stock_total ?? ($i->cantidad ?? 0) : 0; });
                                    $groupKey = 'grp_' . md5($nombreProducto);
                                    $primerLote = $lotes->first();
                                    $categoriaLote = is_object($primerLote) ? $primerLote->categoria ?? 'General' : 'General';
                                @endphp

                                <tr id="product-row-{{ $groupKey }}" data-product-name="{{ strtolower(trim($nombreProducto)) }}" class="product-master-row bg-white hover:bg-slate-50 transition-colors cursor-pointer group" onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-amber-500 transition-colors"></div>
                                        <button type="button" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-amber-100 group-hover:text-amber-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-amber-200" id="icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300" id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-extrabold text-slate-800 text-[14px]">{{ $nombreProducto }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">{{ $categoriaLote }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $lotes->count() }}</span>
                                        <span class="text-slate-400 text-[11px] font-semibold uppercase">Lote(s)</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                        </span>
                                    </td>
                                </tr>

                                <tr id="lotes-{{ $groupKey }}" class="lotes-subrow hidden bg-slate-50/80 shadow-inner">
                                    <td colspan="5" class="p-0 border-b border-slate-300">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                        Detalle de Lotes Retenidos
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                                                            <tr>
                                                                <th class="px-4 py-3">Lote / Vencimiento</th>
                                                                <th class="px-4 py-3">Ubicación Física</th>
                                                                <th class="px-4 py-3">Categoría</th>
                                                                <th class="px-4 py-3 text-center">Estado Seguridad</th>
                                                                <th class="px-4 py-3 text-center">Stock</th>
                                                                <th class="px-4 py-3 text-center">Valor Total</th>
                                                                <th class="px-4 py-3 text-center">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                                            @foreach ($lotes as $loteObj)
                                                                @php
                                                                    $loteData = is_object($loteObj) ? $loteObj : (object) $loteObj;
                                                                    $badgeVencimientoClass = 'text-slate-500';
                                                                    $fechaVencRaw = data_get($loteData, 'fecha_vencimiento');
                                                                    $textoVencimiento = 'N/A';

                                                                    if (!empty($fechaVencRaw)) {
                                                                        try {
                                                                            $fVenc = \Carbon\Carbon::parse($fechaVencRaw);
                                                                            $textoVencimiento = $fVenc->format('d/m/Y');
                                                                            $mesesRestantes = \Carbon\Carbon::now()->diffInMonths($fVenc, false);
                                                                            if ($mesesRestantes > 6) { $badgeVencimientoClass = 'text-emerald-600 font-bold'; } 
                                                                            elseif ($mesesRestantes >= 3) { $badgeVencimientoClass = 'text-amber-600 font-bold'; } 
                                                                            else { $badgeVencimientoClass = 'text-rose-600 font-extrabold'; }
                                                                        } catch (\Exception $e) { $textoVencimiento = $fechaVencRaw; }
                                                                    }

                                                                    $pasilloVal = data_get($loteData, 'pasillo_nombre') ?? data_get($loteData, 'aisle.nombre') ?? data_get($loteData, 'pasillo') ?? 'P.A';
                                                                    $estanteVal = data_get($loteData, 'estanteria_nombre') ?? data_get($loteData, 'shelf.nombre') ?? data_get($loteData, 'estante') ?? 'P.A';
                                                                    $stockLote = data_get($loteData, 'stock_total') ?? data_get($loteData, 'cantidad') ?? 0;
                                                                    $loteId = data_get($loteData, 'id') ?? 0;
                                                                    $loteCodigo = data_get($loteData, 'lote') ?? 'S/L';
                                                                    $loteMotivo = data_get($loteData, 'motivo') ?? data_get($loteData, 'observacion') ?? 'Bloqueo preventivo';
                                                                    $valorUnitarioLote = (float) (data_get($loteData, 'valor_neto_unitario') ?? 0);
                                                                    $valorTotalLote = ((float) $stockLote) * $valorUnitarioLote;
                                                                    $loteCategoria = data_get($loteData, 'categoria') ?? 'General';
                                                                    $lotePrograma = data_get($loteData, 'programa') ?? '';
                                                                @endphp
                                                                <tr class="lote-item-row hover:bg-slate-50 transition-colors" data-lote-code="{{ strtolower(trim($loteCodigo)) }}" data-pasillo="{{ strtolower(trim($pasilloVal)) }}" data-estante="{{ strtolower(trim($estanteVal)) }}">
                                                                    <td class="px-4 py-3">
                                                                        <span class="font-mono font-extrabold text-slate-800 text-[13px]">{{ $loteCodigo }}</span><br>
                                                                        <span class="text-[10px] uppercase font-semibold {{ $badgeVencimientoClass }}">Vence: {{ $textoVencimiento }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex items-center gap-1.5 text-[11px]">
                                                                            <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">P:</span> <b class="text-slate-800">{{ $pasilloVal }}</b></span>
                                                                            @can('ver.distribucion')
                                                                                @if (!empty($estanteVal) && $estanteVal !== 'P.A')
                                                                                    <a href="{{ route('distribution.index', ['estante' => $estanteVal]) }}" title="Ver estante {{ $estanteVal }} en el Mapa 3D" class="inline-flex items-center gap-1 bg-white hover:bg-amber-50 text-slate-600 hover:text-amber-700 px-2 py-1 rounded shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                                                                                        <span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b>
                                                                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                                @endif
                                                                            @else
                                                                                <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                            @endcan
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-white text-slate-600 border border-slate-200 shadow-sm whitespace-nowrap">{{ $loteCategoria }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[14px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[12px] font-bold bg-teal-50 text-teal-800 border border-teal-200 font-mono-das">$ {{ number_format($valorTotalLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                                        <button type="button" onclick="openQRModal('{{ addslashes($loteId) }}', '{{ addslashes($nombreProducto) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($textoVencimiento) }}', '{{ $stockLote }}', 'Droguería DAS')" class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Generar Etiqueta QR">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                                        </button>
                                                                        <button type="button" onclick="openEditLote('{{ addslashes($loteId) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($nombreProducto) }}', 'Cuarentena', '{{ addslashes($loteMotivo) }}', '{{ addslashes($loteCategoria) }}', '{{ addslashes($lotePrograma) }}', '{{ $valorUnitarioLote }}', '{{ $stockLote }}')" class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white rounded-lg transition-all border border-amber-200 shadow-sm cursor-pointer" title="Cambiar Estado (Liberar / Merma)">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                @if (isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>

        <!-- MODAL: Etiqueta QR -->
        <style>
            @media print {
                body * {
                    visibility: hidden !important;
                }
                #qrModal,
                #qrPrintableCard,
                #qrPrintableCard * {
                    visibility: visible !important;
                }
                #qrModal {
                    position: fixed !important;
                    inset: 0 !important;
                    background: #ffffff !important;
                    backdrop-filter: none !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0 !important;
                }
                #qrPrintableCard {
                    box-shadow: none !important;
                    max-width: 340px !important;
                    width: 100% !important;
                    margin: 0 auto !important;
                }
                .no-print-qr {
                    display: none !important;
                }
            }
        </style>
        <div id="qrModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="relative w-full max-w-sm flex flex-col items-center">
                <div id="qrPrintableCard" class="bg-white rounded-2xl shadow-2xl p-6 w-full flex flex-col items-center relative overflow-hidden ring-1 ring-slate-900/5">
                    <div class="absolute inset-2 border-2 border-dashed border-slate-300 rounded-xl pointer-events-none"></div>
                    <div class="w-full flex flex-col items-center mb-4 z-10 border-b-2 border-black pb-3">
                        <h2 class="text-[17px] text-black font-black tracking-widest uppercase">DROGUERÍA DAS</h2>
                        <span class="text-[11px] text-black font-bold font-mono tracking-widest uppercase mt-0.5">Control de Trazabilidad</span>
                    </div>
                    <div class="bg-white p-2 mb-4 z-10 flex justify-center w-full">
                        <img id="qrcode" alt="Código QR del lote" class="w-[140px] h-[140px]">
                    </div>
                    <div class="w-full z-10 space-y-2.5 px-2">
                        <div class="text-center mb-4"><span id="qrProducto" class="text-[14px] font-black text-black block leading-tight uppercase">PRODUCTO</span></div>
                        <div class="grid grid-cols-2 gap-2 text-[12px] font-mono border-t border-b border-black py-2.5">
                            <div class="flex flex-col"><span class="text-[10px] font-bold text-slate-500 uppercase">Lote Identificador</span><span id="qrLote" class="font-bold text-black text-[13px]">L-000</span></div>
                            <div class="flex flex-col text-right"><span class="text-[10px] font-bold text-slate-500 uppercase">Fecha Vencimiento</span><span id="qrVencimiento" class="font-bold text-black text-[13px]">00/00/00</span></div>
                        </div>
                        <div class="flex justify-between items-end pt-1.5">
                            <div class="flex flex-col max-w-[60%]"><span class="text-[10px] font-bold text-slate-500 uppercase">Origen / Bodega</span><span id="qrProveedor" class="font-bold text-black text-[12px] truncate uppercase">INTERNO</span></div>
                            <div class="flex flex-col text-right"><span class="text-[10px] font-bold text-slate-500 uppercase">Stock Físico</span><span id="qrStock" class="font-black text-black text-[17px]">0 UNID.</span></div>
                        </div>
                    </div>
                </div>
                <div class="no-print-qr mt-6 flex justify-center gap-4 w-full">
                    <button type="button" onclick="closeQRModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md cursor-pointer">Cerrar Vista</button>
                    <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>Imprimir Etiqueta</button>
                </div>
            </div>
        </div>

        <!-- MODAL: Editar Estado de Lote -->
        <div id="editLoteModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[10000] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Gestionar Cuarentena</h2>
                    <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-white">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Producto Seleccionado:</span>
                            <p id="modal_producto_nombre" class="text-[14px] font-extrabold text-slate-800 w-full break-words leading-tight"></p>
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm"><p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p></div>
                            <div class="flex justify-center gap-1.5 mt-2 flex-wrap">
                                <span id="modal_categoria_chip" class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-white text-slate-600 border border-slate-200"></span>
                                <span id="modal_programa_chip" class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-white border border-slate-200 rounded-xl p-2.5 text-center shadow-sm">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Valor Unitario</span>
                                <p id="modal_valor_unitario" class="text-[13px] font-bold text-slate-800 font-mono-das">$ 0</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-xl p-2.5 text-center shadow-sm">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Stock Retenido</span>
                                <p id="modal_cantidad" class="text-[13px] font-bold text-slate-800">0</p>
                            </div>
                            <div class="bg-teal-50 border border-teal-200 rounded-xl p-2.5 text-center shadow-sm">
                                <span class="text-[10px] font-bold text-teal-500 uppercase block">Valor Total</span>
                                <p id="modal_valor_total" class="text-[13px] font-extrabold text-teal-800 font-mono-das">$ 0</p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Decisión Técnica *</label>
                            <select id="modal_estado" name="estado" onchange="toggleMotivoField(this.value)" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Cuarentena" selected>🟠 Mantener en Cuarentena</option>
                                <option value="Disponible">🟢 Liberar (Aprobar y regresar a Disponible)</option>
                                <option value="Merma">🔴 Declarar Merma (Desecho / Rechazo)</option>
                            </select>
                        </div>

                        <div id="motivoContainer">
                            <label for="modal_motivo" class="block text-[13px] font-bold text-slate-700 mb-1.5">Motivo / Observación <span class="text-rose-500">*</span></label>
                            <textarea id="modal_motivo" name="motivo" rows="3" required placeholder="Escriba la justificación..." class="w-full rounded-xl border border-slate-300 bg-white p-3 text-[12px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-slate-700 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar Decisión</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Historial Avanzado -->
        <div id="HistoryModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-6xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Registro de Control de Cuarentena</h2>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Historial detallado de ingresos, retenciones y liberaciones.</p>
                    </div>
                    <button type="button" onclick="closeHistory()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- FILTROS AVANZADOS DEL HISTORIAL -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap gap-4 items-end z-20 shadow-inner">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Buscar Texto</label>
                        <input type="text" id="historialSearchInput" onkeyup="filterHistorial()" placeholder="Producto, lote o motivo..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Desde</label>
                        <input type="date" id="historialDateFrom" onchange="filterHistorial()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hasta</label>
                        <input type="date" id="historialDateTo" onchange="filterHistorial()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-[200px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Responsable</label>
                        <select id="historialUserFilter" onchange="filterHistorial()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-sm">
                            <option value="">Todos los usuarios</option>
                            @php
                                $usuariosHistorial = collect($historial ?? [])->map(function($m) {
                                    $mObj = is_object($m) ? $m : (object) $m;
                                    $uName = data_get($mObj, 'user.name') ?? data_get($mObj, 'causer.name') ?? data_get($mObj, 'usuario_nombre');
                                    $uLast = data_get($mObj, 'user.last_name') ?? data_get($mObj, 'causer.last_name') ?? '';
                                    return !empty($uName) ? trim($uName . ' ' . $uLast) : 'No registrado';
                                })->unique()->filter()->values();
                            @endphp
                            @foreach($usuariosHistorial as $uh)
                                <option value="{{ $uh }}">{{ $uh }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="button" onclick="clearHistorialFilters()" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors text-[13px] font-bold shadow-sm">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[50vh] bg-white">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-6 py-4">Fecha Movimiento</th>
                                <th class="px-6 py-4">Producto Afectado</th>
                                <th class="px-6 py-4 text-center">Lote Físico</th>
                                <th class="px-6 py-4 text-center">Acción / Estado</th>
                                <th class="px-6 py-4">Usuario Responsable</th>
                                <th class="px-6 py-4">Motivo Documentado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="historialTableBody">
                            @forelse($historial ?? [] as $movimiento)
                                @php
                                    $mObj = is_object($movimiento) ? $movimiento : (object) $movimiento;
                                    $isMovement = isset($mObj->tipo_movimiento);
                                    
                                    if ($isMovement) {
                                        $fechaMovRaw = $mObj->fecha_hora ?? $mObj->created_at;
                                        $motivoMov = $mObj->documento_respaldo ?? 'Cambio de estado (Historial)';
                                        $accionEstado = strtolower($mObj->estado_lote ?? 'cuarentena');
                                    } else {
                                        $fechaMovRaw = $mObj->updated_at ?? $mObj->created_at;
                                        $motivoMov = $mObj->motivo ?? 'Ingreso a retención preventiva';
                                        $accionEstado = strtolower($mObj->estado ?? 'cuarentena');
                                    }

                                    $fechaISO = '';
                                    if ($fechaMovRaw) {
                                        try {
                                            $cDate = \Carbon\Carbon::parse($fechaMovRaw);
                                            $fechaMovFormateada = $cDate->format('d/m/Y H:i');
                                            $fechaISO = $cDate->format('Y-m-d');
                                        } catch (\Exception $e) { $fechaMovFormateada = 'Fecha Inválida'; }
                                    } else { $fechaMovFormateada = 'Sin Registro'; }

                                    $prodNom = $mObj->producto ?? (data_get($mObj, 'product.nombre') ?? 'Desconocido');
                                    $loteCod = $mObj->lote ?? 'S/L';

                                    $uName = data_get($mObj, 'user.name') ?? data_get($mObj, 'causer.name') ?? data_get($mObj, 'usuario_nombre');
                                    $uLast = data_get($mObj, 'user.last_name') ?? data_get($mObj, 'causer.last_name') ?? '';
                                    $usuarioResp = !empty($uName) ? trim($uName . ' ' . $uLast) : 'No registrado';

                                    if (str_contains($accionEstado, 'disponible')) {
                                        $badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                        $badgeText  = 'LIBERADO';
                                    } elseif (str_contains($accionEstado, 'merma')) {
                                        $badgeColor = 'bg-rose-100 text-rose-800 border-rose-200';
                                        $badgeText  = 'ENVIADO A MERMA';
                                    } else {
                                        $badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
                                        $badgeText  = 'A CUARENTENA';
                                    }
                                @endphp
                                <tr class="historial-row hover:bg-slate-100/70 transition-colors" data-date="{{ $fechaISO }}" data-user="{{ $usuarioResp }}">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $fechaMovFormateada }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800 h-searchable">{{ $prodNom }}</td>
                                    <td class="px-6 py-3.5 text-center h-searchable">
                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono font-extrabold text-[12px] border border-slate-300">{{ $loteCod }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded text-[10px] font-extrabold border {{ $badgeColor }} uppercase tracking-wider">{{ $badgeText }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700 font-semibold flex items-center gap-2 h-searchable">
                                        <div class="w-6 h-6 rounded-md bg-slate-200 text-slate-600 flex items-center justify-center text-[9px] font-bold">{{ strtoupper(substr($usuarioResp, 0, 1)) }}</div>
                                        {{ $usuarioResp }}
                                    </td>
                                    <td class="px-6 py-3.5 italic text-slate-500 max-w-xs truncate h-searchable" title="{{ $motivoMov }}">{{ $motivoMov }}</td>
                                </tr>
                            @empty
                                <tr id="emptyHistorialRowMain">
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No hay registros de movimientos en cuarentena recientes.</td>
                                </tr>
                            @endforelse
                                <tr id="filteredEmptyRow" class="hidden">
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            <span>No se encontraron resultados con los filtros aplicados.</span>
                                        </div>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                    <span class="text-[13px] font-extrabold text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="historialCount" class="text-amber-600 font-black">{{ count($historial ?? []) }}</span> movimientos
                    </span>
                    <button type="button" onclick="closeHistory()" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                        Cerrar Historial
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts de interactividad -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // Inicializar Filtros Avanzados
        document.addEventListener('DOMContentLoaded', () => {
            const btnToggleFilters = document.getElementById('toggle-filters-btn');
            const filtersPanel = document.getElementById('filters-panel');

            if (window.location.search.includes('categoria') || window.location.search.includes('pasillo') || window.location.search.includes('estante') || window.location.search.includes('order_by') || window.location.search.includes('search')) {
                if (filtersPanel) filtersPanel.classList.remove('hidden');
            }

            if (btnToggleFilters && filtersPanel) {
                btnToggleFilters.addEventListener('click', () => {
                    filtersPanel.classList.toggle('hidden');
                });
            }
        });

        function normalizeText(text) {
            return (text || '').toString().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase();
        }

        // Helper seguro: evita errores si un elemento no existe
        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        }

        // Lógica de Filtros Frontend en Tabla de Directorio
        function filterTableByLocation() {
            const pasilloVal = normalizeText(document.getElementById('pasilloSelect').value);
            const estanteVal = normalizeText(document.getElementById('estanteSelect').value);
            const masterRows = document.querySelectorAll('.product-master-row');

            masterRows.forEach(row => {
                const groupKey = row.id.replace('product-row-', '');
                const lotesSubrow = document.getElementById('lotes-' + groupKey);
                if (!lotesSubrow) return;

                const loteRows = lotesSubrow.querySelectorAll('.lote-item-row');
                let visiblesCount = 0;

                loteRows.forEach(loteRow => {
                    const lotePasillo = normalizeText(loteRow.getAttribute('data-pasillo'));
                    const loteEstante = normalizeText(loteRow.getAttribute('data-estante'));

                    const matchPasillo = !pasilloVal || lotePasillo === pasilloVal || lotePasillo.includes(pasilloVal);
                    const matchEstante = !estanteVal || loteEstante === estanteVal || loteEstante.includes(estanteVal);

                    if (matchPasillo && matchEstante) {
                        loteRow.classList.remove('hidden');
                        visiblesCount++;
                    } else {
                        loteRow.classList.add('hidden');
                    }
                });

                if (visiblesCount > 0) {
                    row.classList.remove('hidden');
                    if (pasilloVal || estanteVal) {
                        lotesSubrow.classList.remove('hidden');
                        const arrow = document.getElementById('arrow-' + groupKey);
                        const iconContainer = document.getElementById('icon-' + groupKey);
                        if (arrow) arrow.classList.add('rotate-90');
                        if (iconContainer) {
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-400');
                            iconContainer.classList.add('bg-amber-500', 'text-white');
                        }
                    }
                } else {
                    row.classList.add('hidden');
                    lotesSubrow.classList.add('hidden');
                }
            });

            const alertBanner = document.getElementById('activeFilterAlert');
            const filteredText = document.getElementById('filteredProductName');

            if (pasilloVal || estanteVal) {
                let textoAlert = [];
                if (pasilloVal) textoAlert.push(`Pasillo: ${document.getElementById('pasilloSelect').value}`);
                if (estanteVal) textoAlert.push(`Estante: ${document.getElementById('estanteSelect').value}`);

                if (filteredText) filteredText.textContent = textoAlert.join(' | ');
                if (alertBanner) alertBanner.classList.remove('hidden');
            } else {
                if (alertBanner) alertBanner.classList.add('hidden');
            }
        }

        function clearClientFilter() {
            const masterRows = document.querySelectorAll('.product-master-row');
            masterRows.forEach(row => row.classList.remove('hidden'));

            const loteRows = document.querySelectorAll('.lote-item-row');
            loteRows.forEach(loteRow => loteRow.classList.remove('hidden'));

            const pasilloSel = document.getElementById('pasilloSelect');
            const estanteSel = document.getElementById('estanteSelect');
            if (pasilloSel) pasilloSel.value = '';
            if (estanteSel) estanteSel.value = '';

            const alertBanner = document.getElementById('activeFilterAlert');
            if (alertBanner) alertBanner.classList.add('hidden');

            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
        }

        // Buscador Dropdown
        function showSearchDropdown() { document.getElementById('searchDropdown')?.classList.remove('hidden'); }
        function selectSearchProduct(productName) {
            document.getElementById('searchInput').value = productName;
            document.getElementById('searchDropdown').classList.add('hidden');
            document.getElementById('filters-panel').submit();
        }
        function filterSearchDropdown() {
            const filter = document.getElementById('searchInput').value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            document.querySelectorAll('#searchDropdown .search-item').forEach(item => {
                const text = item.textContent.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                text.includes(filter) ? item.classList.remove('hidden') : item.classList.add('hidden');
            });
            document.getElementById('searchDropdown')?.classList.remove('hidden');
        }
        document.addEventListener('click', function(event) {
            const container = document.getElementById('searchContainer');
            if (container && !container.contains(event.target)) {
                document.getElementById('searchDropdown')?.classList.add('hidden');
            }
        });

        // Acordeón de tabla maestra
        function toggleLotes(key) {
            const row = document.getElementById('lotes-' + key);
            const arrow = document.getElementById('arrow-' + key);
            const iconContainer = document.getElementById('icon-' + key);

            if (row) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    if (arrow) arrow.classList.add('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-slate-100', 'text-slate-400');
                        iconContainer.classList.add('bg-amber-500', 'text-white');
                    }
                } else {
                    row.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-amber-500', 'text-white');
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                    }
                }
            }
        }

        // Modales (Edición Lote)
        function openEditLote(id, lote, producto, estado, motivo = '', categoria = '', programa = '', valorUnitario = 0, cantidad = 0) {
            const form = document.getElementById('editLoteForm');
            if (form) form.action = "/inventory/" + id;

            setText('modal_producto_nombre', producto);
            setText('modal_lote_nombre', "LOTE: " + lote);

            const fmt = (n) => '$ ' + Number(n || 0).toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            const valorU = parseFloat(valorUnitario) || 0;
            const cant = parseInt(cantidad) || 0;

            setText('modal_valor_unitario', fmt(valorU));
            setText('modal_cantidad', cant.toLocaleString('es-CL') + ' u');
            setText('modal_valor_total', fmt(valorU * cant));

            const chipCat = document.getElementById('modal_categoria_chip');
            if (chipCat) {
                chipCat.textContent = categoria || 'Sin categoría';
                chipCat.style.display = '';
            }
            const chipProg = document.getElementById('modal_programa_chip');
            if (chipProg) {
                if (programa) {
                    chipProg.textContent = programa;
                    chipProg.style.display = '';
                } else {
                    chipProg.style.display = 'none';
                }
            }

            const estadoSelect = document.getElementById('modal_estado');
            const estadoFormat = (estado || '').toLowerCase();
            let selectedVal = 'Cuarentena';
            if (estadoFormat.includes('disponible')) selectedVal = 'Disponible';
            else if (estadoFormat.includes('merma')) selectedVal = 'Merma';
            if (estadoSelect) estadoSelect.value = selectedVal;

            const motivoInput = document.getElementById('modal_motivo');
            if (motivoInput) motivoInput.value = motivo;

            toggleMotivoField(selectedVal);
            document.getElementById('editLoteModal').classList.remove('hidden');
        }

        function closeEditLote() { document.getElementById('editLoteModal').classList.add('hidden'); }

        function toggleMotivoField(estado) {
            const motivoInput = document.getElementById('modal_motivo');
            if (!motivoInput) return;
            motivoInput.setAttribute('required', 'required');
            if (estado === 'Disponible') motivoInput.placeholder = "Indique motivo de la liberación...";
            else if (estado === 'Merma') motivoInput.placeholder = "Indique motivo del desecho...";
            else motivoInput.placeholder = "Justificación para mantener inmovilizado...";
        }

        // Modal QR
        function openQRModal(id, producto, lote, vencimiento, stock, proveedor) {
            document.getElementById('qrModal').classList.remove('hidden');
            setText('qrProducto', producto);
            setText('qrLote', lote);
            setText('qrVencimiento', vencimiento);
            setText('qrStock', stock + " UNID.");
            setText('qrProveedor', proveedor);

            const qrImg = document.getElementById('qrcode');
            if (qrImg) {
                qrImg.src = "{{ url('qr') }}/" + id + "/image";
            }
        }
        function closeQRModal() { document.getElementById('qrModal').classList.add('hidden'); }

        // Modal Historial y Filtros JS
        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
            filterHistorial(); 
        }
        function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }

        function filterHistorial() {
            const search = normalizeText(document.getElementById('historialSearchInput')?.value || '');
            const dateFrom = document.getElementById('historialDateFrom')?.value || '';
            const dateTo = document.getElementById('historialDateTo')?.value || '';
            const userFilter = document.getElementById('historialUserFilter')?.value || '';
            
            const rows = document.querySelectorAll('.historial-row');
            const filteredEmptyRow = document.getElementById('filteredEmptyRow');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchables = row.querySelectorAll('.h-searchable');
                let rowText = '';
                searchables.forEach(td => rowText += normalizeText(td.textContent) + ' ');
                
                const matchSearch = rowText.includes(search);
                const matchUser = userFilter === '' || row.getAttribute('data-user') === userFilter;
                
                let matchDate = true;
                const rowDate = row.getAttribute('data-date');
                if (rowDate) {
                    if (dateFrom && rowDate < dateFrom) matchDate = false;
                    if (dateTo && rowDate > dateTo) matchDate = false;
                } else if (dateFrom || dateTo) {
                    matchDate = false;
                }

                if (matchSearch && matchUser && matchDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (rows.length > 0 && filteredEmptyRow) {
                filteredEmptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
            setText('historialCount', visibleCount);
        }

        function clearHistorialFilters() {
            if(document.getElementById('historialSearchInput')) document.getElementById('historialSearchInput').value = '';
            if(document.getElementById('historialDateFrom')) document.getElementById('historialDateFrom').value = '';
            if(document.getElementById('historialDateTo')) document.getElementById('historialDateTo').value = '';
            if(document.getElementById('historialUserFilter')) document.getElementById('historialUserFilter').value = '';
            filterHistorial();
        }
    </script>
@endsection