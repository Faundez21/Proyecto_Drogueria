@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Visualización y exportación de datos consolidados.')

@section('actions')
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        <a href=""
            class="flex-1 sm:flex-none px-5 py-2.5 bg-[#0d9488] hover:bg-[#0f766e] text-white rounded-2xl font-bold text-[13px] shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer border border-transparent">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <span>Exportar a Excel</span>
        </a>

        <a href=""
            class="flex-1 sm:flex-none px-5 py-2.5 bg-rose-600 text-white rounded-2xl text-[12px] font-bold hover:bg-rose-700 text-white rounded-2xl font-bold text-[13px] shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer border border-transparent">
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                </path>
            </svg>
            <span>Exportar a PDF</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-6">

        <!-- Migas de pan -->
        <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
            <span class="text-slate-400 font-semibold">04</span>
            <span>Control</span>
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-semibold text-slate-800">Reportes</span>
        </div>

        <!-- Contenedor Principal Tabla Inventario -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

            <!-- Filtros Avanzados -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col gap-4">
                <!-- Se envía al action actual del controlador de Reportes -->
                <form id="filtersForm" method="GET" action="{{ Route::has('reportes.index') ? route('reportes.index') : (Route::has('reportes') ? route('reportes') : url()->current()) }}">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-teal-100 shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Reporte Consolidado de Stock</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Filtre los resultados antes de realizar las descargas</p>
                            </div>
                        </div>

                        <button type="button" onclick="toggleFiltersPanel()"
                            class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:text-teal-700 hover:bg-slate-50 rounded-xl text-[13px] font-bold shadow-sm flex items-center gap-2 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            <span>Filtros Avanzados</span>
                        </button>
                    </div>

                    @php
                        $hasActiveFilters = request()->filled('search') || request()->filled('categoria') || request()->filled('pasillo') || request()->filled('estante') || request()->filled('per_page') || request()->filled('order_by');

                        $invRawList = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];

                        $invNonMermaList = collect($invRawList)->reject(function($item) {
                            $obj = is_object($item) ? $item : (object)$item;
                            return str_contains(strtolower($obj->estado ?? ''), 'merma');
                        });

                        $pasillosUnicos = $invNonMermaList->map(function($item) {
                            $obj = is_object($item) ? $item : (object)$item;
                            return $obj->pasillo_nombre ?? (is_object($obj->aisle ?? null) ? $obj->aisle->nombre ?? ($obj->aisle->name ?? null) : $obj->pasillo ?? null);
                        })->filter()->unique()->sort();

                        $estantesUnicos = $invNonMermaList->map(function($item) {
                            $obj = is_object($item) ? $item : (object)$item;
                            return $obj->estanteria_nombre ?? (is_object($obj->shelf ?? null) ? $obj->shelf->nombre ?? ($obj->shelf->name ?? null) : $obj->estante ?? ($obj->estanteria ?? null));
                        })->filter()->unique()->sort();
                    @endphp

                    <div id="filtersPanel" class="{{ $hasActiveFilters ? '' : 'hidden' }} space-y-4 pt-4 mt-4 border-t border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <!-- BUSCAR -->
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Buscar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        placeholder="Producto, categoría o lote..."
                                        class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-700 shadow-sm placeholder:text-slate-400">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Categoría</label>
                                <select name="categoria"
                                    class="block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-sm cursor-pointer">
                                    <option value="">Todas</option>
                                    <option value="Fármacos" {{ request('categoria') == 'Fármacos' ? 'selected' : '' }}>Fármacos</option>
                                    <option value="Insumos Clínicos" {{ request('categoria') == 'Insumos Clínicos' ? 'selected' : '' }}>Insumos Clínicos</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Pasillo</label>
                                <select name="pasillo" id="pasilloSelect" onchange="filterTableByLocation()"
                                    class="block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-sm cursor-pointer">
                                    <option value="">Todos los Pasillos</option>
                                    @foreach($pasillosUnicos as $pas)
                                        <option value="{{ $pas }}" {{ request('pasillo') == $pas ? 'selected' : '' }}>{{ $pas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Estante</label>
                                <select name="estante" id="estanteSelect" onchange="filterTableByLocation()"
                                    class="block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-sm cursor-pointer">
                                    <option value="">Todos los Estantes</option>
                                    @foreach($estantesUnicos as $est)
                                        <option value="{{ $est }}" {{ request('estante') == $est ? 'selected' : '' }}>{{ $est }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Ordenar por</label>
                                <select name="order_by"
                                    class="block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-sm cursor-pointer">
                                    <option value="recent" {{ request('order_by', 'recent') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                                    <option value="nombre_asc" {{ request('order_by') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                                    <option value="stock_desc" {{ request('order_by') == 'stock_desc' ? 'selected' : '' }}>Mayor Stock</option>
                                    <option value="stock_asc" {{ request('order_by') == 'stock_asc' ? 'selected' : '' }}>Menor Stock</option>
                                    <option value="vencimiento" {{ request('order_by') == 'vencimiento' ? 'selected' : '' }}>Próximos a Vencer</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end items-center gap-3 pt-3 border-t border-slate-200/80">
                            <a href="{{ Route::has('reportes.index') ? route('reportes.index') : (Route::has('reportes') ? route('reportes') : url()->current()) }}"
                                class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl text-[12.5px] font-bold transition-all shadow-sm">
                                Limpiar Filtros
                            </a>

                        </div>
                    </div>
                </form>
            </div>

            <div id="activeFilterAlert" class="hidden px-5 py-3 bg-teal-50 border-b border-teal-200 flex justify-between items-center text-xs text-teal-800 font-bold">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Filtro activo: <span id="filteredProductName" class="underline"></span>
                </span>
                <button type="button" onclick="clearClientFilter()" class="px-3 py-1 bg-white border border-teal-300 rounded-lg text-teal-700 hover:bg-teal-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                    Limpiar Filtros
                </button>
            </div>

            <div class="overflow-x-auto w-full">
                @php
                    $invRaw = isset($inventories)
                        ? (method_exists($inventories, 'items')
                            ? $inventories->items()
                            : $inventories)
                        : [];
                    $invCollection = collect($invRaw);

                    $mainInventories = $invCollection->reject(function ($item) {
                        $itemObj = is_object($item) ? $item : (object) $item;
                        return str_contains(strtolower($itemObj->estado ?? ''), 'merma');
                    });

                    $groupedInventories = $mainInventories->groupBy(function ($item) {
                        $prod = is_object($item) ? $item->product ?? ($item->producto ?? null) : null;
                        if (is_object($prod) && isset($prod->nombre)) {
                            return $prod->nombre;
                        }
                        if (is_string($prod)) {
                            return $prod;
                        }
                        if (is_object($item) && isset($item->producto)) {
                            return is_object($item->producto) ? ($item->producto->nombre ?? 'Producto General') : $item->producto;
                        }
                        return 'Producto General';
                    });
                @endphp

                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-extrabold">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-14 text-center">Detalle</th>
                            <th scope="col" class="px-6 py-4">Producto Principal</th>
                            <th scope="col" class="px-6 py-4">Categoría</th>
                            <th scope="col" class="px-6 py-4 text-center">Lotes Activos</th>
                            <th scope="col" class="px-6 py-4 text-center">Stock Físico Acumulado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100" id="mainTableBody">
                        @if ($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-[15px] font-bold text-slate-600">Sin datos registrados para reportes</p>
                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">No existen registros que coincidan con la búsqueda requerida.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($groupedInventories as $nombreProducto => $lotes)
                                @php
                                    $stockTotalProducto = $lotes->sum(function ($i) {
                                        return is_object($i) ? $i->stock_total ?? ($i->cantidad ?? 0) : 0;
                                    });
                                    $groupKey = 'grp_' . md5($nombreProducto);
                                    $primerLote = $lotes->first();
                                    $categoriaLote = is_object($primerLote)
                                        ? $primerLote->categoria ?? 'General'
                                        : 'General';
                                @endphp

                                <tr id="product-row-{{ $groupKey }}"
                                    data-product-name="{{ strtolower(trim($nombreProducto)) }}"
                                    class="product-master-row bg-white hover:bg-slate-50/80 transition-colors cursor-pointer group"
                                    onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-teal-500 transition-colors"></div>
                                        <button type="button"
                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-teal-200"
                                            id="icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300"
                                                id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
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
                                        @if ($stockTotalProducto > 10)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                            </span>
                                        @elseif($stockTotalProducto > 0)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ number_format($stockTotalProducto, 0, ',', '.') }} Unid. (Bajo)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                0 Unidades (Agotado)
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr id="lotes-{{ $groupKey }}" class="lotes-subrow hidden bg-slate-100/50 shadow-inner">
                                    <td colspan="5" class="p-0 border-b border-slate-300">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                        Desglose Físico de Lotes
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                                                            <tr>
                                                                <th class="px-4 py-3">Lote / Vencimiento</th>
                                                                <th class="px-4 py-3">Ubicación (Pasillo/Estante)</th>
                                                                <th class="px-4 py-3 text-center">Estado Sanitario</th>
                                                                <th class="px-4 py-3 text-center">Stock de Lote</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                                            @foreach ($lotes as $loteObj)
                                                                @php
                                                                    $loteData = is_object($loteObj) ? $loteObj : (object) $loteObj;
                                                                    $badgeVencimientoClass = 'text-slate-500';
                                                                    $fechaVencRaw = $loteData->fecha_vencimiento ?? null;
                                                                    $textoVencimiento = 'N/A';

                                                                    if (!empty($fechaVencRaw)) {
                                                                        try {
                                                                            $fVenc = \Carbon\Carbon::parse($fechaVencRaw);
                                                                            $textoVencimiento = $fVenc->format('d/m/Y');
                                                                            $now = \Carbon\Carbon::now();

                                                                            if ($fVenc->isPast()) {
                                                                                $badgeVencimientoClass = 'text-rose-600 font-extrabold';
                                                                            } else {
                                                                                $diasRestantes = $now->diffInDays($fVenc, false);
                                                                                if ($diasRestantes > 90) {
                                                                                    $badgeVencimientoClass = 'text-emerald-600 font-bold';
                                                                                } elseif ($diasRestantes >= 0) {
                                                                                    $badgeVencimientoClass = 'text-amber-600 font-bold';
                                                                                } else {
                                                                                    $badgeVencimientoClass = 'text-rose-600 font-extrabold';
                                                                                }
                                                                            }
                                                                        } catch (\Exception $e) {
                                                                            $textoVencimiento = $fechaVencRaw;
                                                                        }
                                                                    }

                                                                    $pasilloVal = $loteData->pasillo_nombre ?? (is_object($loteData->aisle ?? null) ? $loteData->aisle->nombre ?? ($loteData->aisle->name ?? null) : $loteData->pasillo ?? 'P.A');
                                                                    $estanteVal = $loteData->estanteria_nombre ?? (is_object($loteData->shelf ?? null) ? $loteData->shelf->nombre ?? ($loteData->shelf->name ?? null) : $loteData->estante ?? ($loteData->estanteria ?? 'P.A'));
                                                                    $stockLote = $loteData->stock_total ?? ($loteData->cantidad ?? 0);
                                                                    $isCuarentena = isset($loteData->cuarentena) ? (bool) $loteData->cuarentena : strtolower($loteData->estado ?? '') === 'cuarentena';
                                                                    $loteCodigo = $loteData->lote ?? 'S/L';
                                                                @endphp
                                                                <tr class="lote-item-row hover:bg-slate-50 transition-colors"
                                                                    data-lote-code="{{ strtolower(trim($loteCodigo)) }}"
                                                                    data-pasillo="{{ strtolower(trim($pasilloVal)) }}"
                                                                    data-estante="{{ strtolower(trim($estanteVal)) }}">
                                                                    <td class="px-4 py-3">
                                                                        <span class="font-mono font-extrabold text-slate-800 text-[13px]">{{ $loteCodigo }}</span><br>
                                                                        <span class="text-[10px] uppercase font-semibold {{ $badgeVencimientoClass }}">Vence: {{ $textoVencimiento }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex items-center gap-1.5 text-[11px]">
                                                                            <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">P:</span> <b class="text-slate-800">{{ $pasilloVal }}</b></span>
                                                                            <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        @if ($isCuarentena)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                                                                CUARENTENA
                                                                            </span>
                                                                        @elseif($stockLote <= 0)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                                                                AGOTADO
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                                                DISPONIBLE
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[14px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
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

            <!-- Pie de la Tabla con enlaces a la paginación -->
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                @if (isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>

    </div>

    <script>
        function normalizeText(text) {
            return (text || '')
                .toString()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .trim()
                .toLowerCase();
        }

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
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                            iconContainer.classList.add('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
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
            loteRows.forEach(loteRow => {
                loteRow.classList.remove('hidden', 'bg-teal-50/80');
            });

            const pasilloSel = document.getElementById('pasilloSelect');
            const estanteSel = document.getElementById('estanteSelect');
            if (pasilloSel) pasilloSel.value = '';
            if (estanteSel) estanteSel.value = '';

            const alertBanner = document.getElementById('activeFilterAlert');
            if (alertBanner) alertBanner.classList.add('hidden');

            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
        }

        function toggleFiltersPanel() {
            const panel = document.getElementById('filtersPanel');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        }

        function toggleLotes(key) {
            const row = document.getElementById('lotes-' + key);
            const arrow = document.getElementById('arrow-' + key);
            const iconContainer = document.getElementById('icon-' + key);

            if (row) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    if (arrow) arrow.classList.add('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                        iconContainer.classList.add('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
                    }
                } else {
                    row.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400', 'border-slate-200');
                    }
                }
            }
        }
    </script>
@endsection
