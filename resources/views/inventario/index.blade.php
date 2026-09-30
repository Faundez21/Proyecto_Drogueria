@extends('layouts.app')

@section('title', 'Inventario de Droguería')
@section('subtitle', 'Gestión centralizada de productos, lotes y ubicaciones físicas.')

@section('actions')
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        <button type="button" onclick="openMerma()"
            class="flex-1 sm:flex-none px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-[13px] font-bold hover:bg-rose-100 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                </path>
            </svg>
            Ver Mermas
        </button>
        <button type="button" onclick="openHistory()"
            class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 hover:text-teal-700 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Ver Historial
        </button>
        <button type="button" onclick="openImport()"
            class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Carga Masiva
        </button>
        <button type="button" onclick="openDeposit()"
            class="flex-1 sm:flex-none px-5 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4 text-teal-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Ingreso Manual
        </button>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-6">

        <!-- Migas de pan -->
        <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-2 px-1 pt-2">
            <span class="text-slate-400 font-semibold">02</span>
            <span>Almacenamiento</span>
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-semibold text-slate-800">Inventario</span>
        </div>

        <!-- Contenedor Principal Tabla Inventario -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

            <!-- Filtros Avanzados -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col gap-4">
                <form id="filtersForm" method="GET" action="{{ Route::has('inventario.index') ? route('inventario.index') : '#' }}">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-teal-100 shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Directorio de Productos</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Agrupados por categoría, pasillo/estante y stock total</p>
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
                            <!-- BUSCAR CON DESPLEGABLE PERSONALIZADO -->
                            <div class="lg:col-span-2 relative" id="searchContainer">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-600 mb-1.5 tracking-wider">Buscar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>

                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        placeholder="Producto, categoría o lote..." autocomplete="off"
                                        onfocus="showSearchDropdown()" oninput="filterSearchDropdown()"
                                        class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-[12.5px] font-medium focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-700 shadow-sm placeholder:text-slate-400">
                                </div>

                                <div id="searchDropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100 sidebar-scroll">
                                    @if(isset($productosLista) && count($productosLista) > 0)
                                        @foreach($productosLista as $prodNombre)
                                            <div class="search-item px-3.5 py-2.5 text-[12.5px] font-medium text-slate-700 hover:bg-teal-50 hover:text-teal-900 cursor-pointer transition-colors flex items-center justify-between"
                                                 onclick="selectSearchProduct('{{ $prodNombre }}')">
                                                <span>{{ $prodNombre }}</span>
                                                <svg class="w-3.5 h-3.5 text-slate-300 hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="px-3.5 py-2.5 text-[12px] text-slate-400 text-center">No hay productos registrados</div>
                                    @endif
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
                            <a href="{{ Route::has('inventario.index') ? route('inventario.index') : '#' }}"
                                class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl text-[12.5px] font-bold transition-all shadow-sm">
                                Limpiar Filtros
                            </a>
                            <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl text-[12.5px] font-bold shadow-sm hover:bg-teal-700 transition-all cursor-pointer">
                                Aplicar Filtros
                            </button>
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
                    $invRaw = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];
                    $invCollection = collect($invRaw);

                    $mainInventories = $invCollection->reject(function ($item) {
                        $itemObj = is_object($item) ? $item : (object) $item;
                        return str_contains(strtolower($itemObj->estado ?? ''), 'merma');
                    });

                    $groupedInventories = $mainInventories->groupBy(function ($item) {
                        $prod = is_object($item) ? $item->product ?? null : null;
                        if ($prod && isset($prod->nombre)) return $prod->nombre;
                        if (is_object($item) && isset($item->producto)) return $item->producto;
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
                                        <p class="text-[15px] font-bold text-slate-600">Bodega sin productos activos</p>
                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">No existen medicamentos o insumos disponibles en el directorio actual.</p>
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
                                    $categoriaLote = is_object($primerLote) ? $primerLote->categoria ?? 'General' : 'General';
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

                                                                    $pasilloVal = data_get($loteData, 'pasillo_nombre') ?? data_get($loteData, 'aisle.nombre') ?? data_get($loteData, 'pasillo') ?? 'P.A';
                                                                    $estanteVal = data_get($loteData, 'estanteria_nombre') ?? data_get($loteData, 'shelf.nombre') ?? data_get($loteData, 'estante') ?? 'P.A';
                                                                    $stockLote = data_get($loteData, 'stock_total') ?? data_get($loteData, 'cantidad') ?? 0;
                                                                    $isCuarentena = isset($loteData->cuarentena) ? (bool) $loteData->cuarentena : strtolower($loteData->estado ?? '') === 'cuarentena';
                                                                    $loteId = data_get($loteData, 'id') ?? 0;
                                                                    $loteCodigo = data_get($loteData, 'lote') ?? 'S/L';
                                                                    $estadoActualStr = $isCuarentena ? 'Cuarentena' : ($loteData->estado ?? 'Disponible');
                                                                    $loteMotivo = data_get($loteData, 'motivo') ?? data_get($loteData, 'observacion') ?? '';
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
                                                                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                                        <button type="button"
                                                                            onclick="openQRModal('{{ e($loteId) }}', '{{ e($nombreProducto) }}', '{{ e($loteCodigo) }}', '{{ e($textoVencimiento) }}', '{{ $stockLote }}', 'Droguería DAS')"
                                                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Generar Etiqueta QR">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                                        </button>

                                                                        <button type="button"
                                                                            onclick="openEditLote('{{ e($loteId) }}', '{{ e($loteCodigo) }}', '{{ e($nombreProducto) }}', '{{ e($estadoActualStr) }}', '{{ e($loteMotivo) }}')"
                                                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Cambiar Estado">
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

            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                @if (isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>

        <!-- Modal: Historial de Movimientos -->
        <div id="HistoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-7xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Historial General de Inventario</h2>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Registro de auditoría de recepciones, ingresos manuales y movimientos.</p>
                    </div>
                    <button type="button" onclick="closeHistory()"
                        class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-[60vh] bg-slate-50">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm">
                            <tr>
                                <th class="px-6 py-4">Fecha y Hora</th>
                                <th class="px-6 py-4">Doc / Correlativo</th>
                                <th class="px-6 py-4">Proveedor</th>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Lote</th>
                                <th class="px-6 py-4 text-center">Responsable</th>
                                <th class="px-6 py-4 text-center">Cantidad</th>
                                <th class="px-6 py-4 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventories ?? [] as $item)
                                @php
                                    $itemObj = is_object($item) ? $item : (object) $item;
                                    $prodNombre = data_get($itemObj, 'product.nombre') ?? data_get($itemObj, 'producto') ?? 'Producto Desconocido';
                                    $cant = data_get($itemObj, 'stock_total') ?? data_get($itemObj, 'cantidad') ?? 0;
                                    $loteCodigo = data_get($itemObj, 'lote') ?? data_get($itemObj, 'loteRef.lote') ?? 'S/L';
                                    
                                    // FECHA Y HORA (Usamos created_at para el ingreso de inventario)
                                    $rawDate = data_get($itemObj, 'created_at') ?? data_get($itemObj, 'updated_at');
                                    if ($rawDate) {
                                        try {
                                            $fechaFormateada = \Carbon\Carbon::parse($rawDate)->format('d/m/Y H:i');
                                        } catch(\Exception $e) {
                                            $fechaFormateada = 'Fecha Inválida';
                                        }
                                    } else {
                                        $fechaFormateada = 'Sin Registro';
                                    }

                                    // USUARIO RESPONSABLE
                                    $uName = data_get($itemObj, 'user.name') ?? data_get($itemObj, 'usuario.name') ?? data_get($itemObj, 'causer.name');
                                    $uLast = data_get($itemObj, 'user.last_name') ?? data_get($itemObj, 'usuario.last_name') ?? data_get($itemObj, 'causer.last_name');
                                    
                                    if (!empty($uName)) {
                                        $usuarioResp = trim($uName . ' ' . $uLast);
                                    } else {
                                        $strUser = data_get($itemObj, 'usuario_nombre') ?? data_get($itemObj, 'responsable') ?? data_get($itemObj, 'usuario');
                                        $usuarioResp = (is_string($strUser) && !empty(trim($strUser))) ? $strUser : 'No registrado';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-100/70 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">
                                        {{ $fechaFormateada }}
                                    </td>
                                    <td class="px-6 py-3.5 font-semibold text-slate-800">
                                        {{ $itemObj->tipo_documento ?? 'Doc' }} #{{ $itemObj->numero_documento ?? '000' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $itemObj->razon_social ?? 'Interno/Proveedor' }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ $prodNombre }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono font-extrabold text-[12px] border border-slate-300">
                                            {{ $loteCodigo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-medium text-slate-700">
                                        {{ $usuarioResp }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-extrabold text-emerald-600">+{{ $cant }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <button type="button"
                                            onclick="filterTableByLote('{{ e($prodNombre) }}', '{{ e($loteCodigo) }}')"
                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer inline-flex items-center justify-center"
                                            title="Ver este lote específico">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-medium">No hay movimientos registrados recientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                    <button type="button" onclick="closeHistory()"
                        class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                        Cerrar Historial
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Importar Carga Masiva -->
        <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 flex flex-col w-full max-w-md overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white tracking-wide">Importar Carga Masiva</h2>
                    <button type="button" onclick="closeImport()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 bg-slate-50">
                    <p class="text-[13px] text-slate-600 mb-5 text-center leading-relaxed">Descargue la plantilla oficial, complete los datos de los productos y suba el archivo Excel para registrar el inventario masivamente.</p>

                    <a href="{{ Route::has('inventario.template') ? route('inventario.template') : '#' }}" class="w-full mb-6 flex justify-center items-center gap-2 bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-100 hover:text-teal-700 transition-all font-bold text-[13px] shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Descargar Plantilla Oficial
                    </a>

                    <form action="{{ Route::has('inventario.import') ? route('inventario.import') : '#' }}" method="POST" enctype="multipart/form-data" class="bg-white border border-dashed border-slate-300 hover:border-teal-400 transition-colors rounded-xl p-6 flex flex-col items-center gap-4 text-center shadow-sm">
                        @csrf
                        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-teal-600 mb-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx, .xls, .csv" onchange="updateFileName(this)">
                        <label for="excel" class="w-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-teal-700 px-4 py-2 rounded-lg cursor-pointer inline-flex items-center justify-center gap-2 text-[13px] font-bold transition-all shadow-sm">
                            <span id="fileNameLabel">Explorar Archivos</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-medium">Formatos soportados: .xlsx, .csv</span>

                        <button type="submit" class="w-full px-4 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm mt-3 cursor-pointer">
                            Procesar e Importar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Ingreso Manual -->
        <div id="DepositModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Ingreso Directo de Producto</h2>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Registro individual directo a bodega.</p>
                    </div>
                    <button type="button" onclick="closeDeposit()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto bg-slate-50/50">
                    <form action="{{ Route::has('inventario.store') ? route('inventario.store') : '#' }}" method="POST" id="manualDepositForm">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            <div class="sm:col-span-2">
                                <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-2">
                                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider">1. Datos del Documento</h4>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">N° Correlativo Interno</label>
                                <input type="number" name="correlativo_interno" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Fecha Recepción <span class="text-rose-500">*</span></label>
                                <input type="date" name="fecha_recepcion" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Tipo Documento <span class="text-rose-500">*</span></label>
                                <select name="tipo_documento" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Factura">Factura</option>
                                    <option value="Guía de Despacho">Guía de Despacho</option>
                                    <option value="Orden de Compra">Orden de Compra</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">N° Documento <span class="text-rose-500">*</span></label>
                                <input type="text" name="numero_documento" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>

                            <div class="sm:col-span-2 mt-4">
                                <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-2">
                                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider">2. Detalle Físico e Ubicación</h4>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Producto <span class="text-rose-500">*</span></label>
                                <input type="text" name="producto" placeholder="Ej. PARACETAMOL 500MG COMPRIMIDOS" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-bold text-slate-800" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Categoría <span class="text-rose-500">*</span></label>
                                <select name="categoria" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="Fármacos">Fármacos</option>
                                    <option value="Insumos Clínicos">Insumos Clínicos</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Estado de Ingreso <span class="text-rose-500">*</span></label>
                                <select name="estado" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-medium">
                                    <option value="Disponible">Disponible Directo</option>
                                    <option value="Cuarentena" selected>A Cuarentena (Sugerido)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Pasillo Ubicación</label>
                                <input type="text" name="pasillo" placeholder="Ej. Pasillo A1" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Estante Ubicación</label>
                                <input type="text" name="estante" placeholder="Ej. Estante 03" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Lote / Serie</label>
                                <input type="text" name="lote" placeholder="Ej. L-4893" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-mono font-bold" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">F. Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Cantidad a Ingresar <span class="text-rose-500">*</span></label>
                                <input type="number" name="cantidad" placeholder="0" min="1" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-bold text-slate-800" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeDeposit()" class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cancelar</button>
                    <button type="button" onclick="document.getElementById('manualDepositForm').submit()" class="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors shadow-sm text-[13px] font-bold cursor-pointer">Confirmar Ingreso</button>
                </div>
            </div>
        </div>

        <!-- Modal: Etiqueta QR -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="relative w-full max-w-sm flex flex-col items-center">
                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full flex flex-col items-center relative overflow-hidden ring-1 ring-slate-900/5">
                    <div class="absolute inset-2 border-2 border-dashed border-slate-300 rounded-xl pointer-events-none"></div>
                    <div class="w-full flex flex-col items-center mb-4 z-10 border-b-2 border-black pb-3">
                        <h2 class="text-[17px] text-black font-black tracking-widest uppercase">DROGUERÍA DAS</h2>
                        <span class="text-[11px] text-black font-bold font-mono tracking-widest uppercase mt-0.5">Control de Trazabilidad</span>
                    </div>
                    <div class="bg-white p-2 mb-4 z-10 flex justify-center w-full">
                        <div id="qrcode" class="w-[140px] h-[140px]"></div>
                    </div>
                    <div class="w-full z-10 space-y-2.5 px-2">
                        <div class="text-center mb-4">
                            <span id="qrProducto" class="text-[14px] font-black text-black block leading-tight uppercase">PRODUCTO</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[12px] font-mono border-t border-b border-black py-2.5">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Lote Identificador</span>
                                <span id="qrLote" class="font-bold text-black text-[13px]">L-000</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Fecha Vencimiento</span>
                                <span id="qrVencimiento" class="font-bold text-black text-[13px]">00/00/00</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-end pt-1.5">
                            <div class="flex flex-col max-w-[60%]">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Origen / Bodega</span>
                                <span id="qrProveedor" class="font-bold text-black text-[12px] truncate uppercase">INTERNO</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Stock Físico</span>
                                <span id="qrStock" class="font-black text-black text-[17px]">0 UNID.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-center gap-4 w-full">
                    <button type="button" onclick="closeQRModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md cursor-pointer">Cerrar Vista</button>
                    <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Etiqueta
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Lote -->
        <div id="editLoteModal" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Actualizar Estado de Lote</h2>
                    <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-white">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Modificando Lote de:</span>
                            <p id="modal_producto_nombre" class="text-[14px] font-extrabold text-slate-800 w-full break-words leading-tight"></p>
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm">
                                <p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Seleccione Nuevo Estado Físico *</label>
                            <select id="modal_estado" name="estado" onchange="toggleMotivoField(this.value)" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Disponible">🟢 Disponible (Liberado para entrega)</option>
                                <option value="Cuarentena">🟠 En Cuarentena (Bloqueo preventivo)</option>
                                <option value="Merma">🔴 En Merma (Dañado / Vencido)</option>
                            </select>
                        </div>

                        <div id="motivoContainer" class="hidden">
                            <label for="modal_motivo" class="block text-[13px] font-bold text-slate-700 mb-1.5">
                                Motivo <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="modal_motivo" name="motivo" rows="3"
                                placeholder="Indique la razón de la merma o cuarentena..."
                                class="w-full rounded-xl border border-slate-300 bg-white p-3 text-[12px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all text-slate-700 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Ver Mermas (CON FECHA Y USUARIO) -->
        <div id="mermaModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-6xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-rose-800 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-700 border border-rose-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Registro de Productos Mermados</h2>
                            <p class="text-[12px] text-rose-200 mt-0.5 font-medium">Listado histórico de medicamentos e insumos dados de baja, con fecha y responsable.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeMerma()" class="text-rose-200 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-[60vh] bg-slate-50">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-6 py-4">Fecha Movimiento</th>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Lote / Serie</th>
                                <th class="px-6 py-4">Responsable</th>
                                <th class="px-6 py-4">Motivo Documentado</th>
                                <th class="px-6 py-4 text-center">Cant. Mermada</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $mermas = collect($invRaw)->filter(function ($item) {
                                    $itemObj = is_object($item) ? $item : (object) $item;
                                    $estado = strtolower($itemObj->estado ?? '');
                                    return str_contains($estado, 'merma');
                                });
                            @endphp

                            @forelse($mermas as $mItem)
                                @php
                                    $mObj = is_object($mItem) ? $mItem : (object) $mItem;
                                    
                                    // 1. FECHA (Lectura Segura y Formateada)
                                    $rawDateMerma = data_get($mObj, 'updated_at') ?? data_get($mObj, 'created_at');
                                    if ($rawDateMerma) {
                                        try {
                                            $fechaMerma = \Carbon\Carbon::parse($rawDateMerma)->format('d/m/Y H:i');
                                        } catch(\Exception $e) {
                                            $fechaMerma = 'Fecha Inválida';
                                        }
                                    } else {
                                        $fechaMerma = 'Sin Registro';
                                    }

                                    // 2. PRODUCTO Y LOTE
                                    $mNombre = data_get($mObj, 'product.nombre') ?? data_get($mObj, 'producto') ?? 'Producto Desconocido';
                                    $mStock = data_get($mObj, 'stock_total') ?? data_get($mObj, 'cantidad') ?? 0;
                                    $mLoteCodigo = data_get($mObj, 'lote') ?? 'S/L';
                                    $mLoteId = data_get($mObj, 'id') ?? 0;

                                    // 3. RESPONSABLE (Lectura Segura)
                                    $uName = data_get($mObj, 'user.name') ?? data_get($mObj, 'usuario.name') ?? data_get($mObj, 'causer.name');
                                    $uLast = data_get($mObj, 'user.last_name') ?? data_get($mObj, 'usuario.last_name') ?? data_get($mObj, 'causer.last_name');
                                    
                                    if (!empty($uName)) {
                                        $usuarioMerma = trim($uName . ' ' . $uLast);
                                    } else {
                                        $strUser = data_get($mObj, 'usuario_nombre') ?? data_get($mObj, 'responsable') ?? data_get($mObj, 'usuario');
                                        $usuarioMerma = (is_string($strUser) && !empty(trim($strUser))) ? $strUser : 'No registrado';
                                    }

                                    // 4. MOTIVO
                                    $mMotivo = data_get($mObj, 'motivo') ?? data_get($mObj, 'observacion') ?? data_get($mObj, 'descripcion') ?? 'Sin motivo especificado';
                                @endphp
                                <tr class="hover:bg-rose-50/40 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $fechaMerma }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ $mNombre }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono font-extrabold text-[12px] border border-slate-300">
                                            {{ $mLoteCodigo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700 font-semibold">{{ $usuarioMerma }}</td>
                                    <td class="px-6 py-3.5 italic text-rose-600 max-w-xs truncate" title="{{ $mMotivo }}">
                                        {{ $mMotivo }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-extrabold text-rose-600">
                                        {{ number_format($mStock, 0, ',', '.') }} Unid.
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <button type="button"
                                            onclick="openEditLote('{{ e($mLoteId) }}', '{{ e($mLoteCodigo) }}', '{{ e($mNombre) }}', 'Merma', '{{ e($mMotivo != 'Sin motivo especificado' ? $mMotivo : '') }}')"
                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer inline-flex items-center justify-center"
                                            title="Cambiar Estado">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>No hay productos marcados en estado de merma actualmente.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-between items-center">
                    <span class="text-xs text-slate-500 font-medium">Total de lotes mermados: <b>{{ $mermas->count() }}</b></span>
                    <button type="button" onclick="closeMerma()" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        function normalizeText(text) {
            return (text || '').toString().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase();
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

        function filterTableByLote(nombreProducto, codigoLote) {
            closeHistory();

            const nombreNorm = normalizeText(nombreProducto);
            const loteNorm = normalizeText(codigoLote);
            const rows = document.querySelectorAll('.product-master-row');

            rows.forEach(row => {
                const rowProdName = normalizeText(row.getAttribute('data-product-name'));
                const groupKey = row.id.replace('product-row-', '');
                const lotesSubrow = document.getElementById('lotes-' + groupKey);
                const esMismoProducto = rowProdName.includes(nombreNorm) || nombreNorm.includes(rowProdName);

                if (esMismoProducto) {
                    row.classList.remove('hidden');

                    if (lotesSubrow) {
                        lotesSubrow.classList.remove('hidden');
                        const arrow = document.getElementById('arrow-' + groupKey);
                        const iconContainer = document.getElementById('icon-' + groupKey);
                        if (arrow) arrow.classList.add('rotate-90');
                        if (iconContainer) {
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                            iconContainer.classList.add('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
                        }

                        const loteRows = lotesSubrow.querySelectorAll('.lote-item-row');
                        loteRows.forEach(loteRow => {
                            const rowLoteCode = normalizeText(loteRow.getAttribute('data-lote-code'));

                            if (loteNorm !== 's/l' && (rowLoteCode.includes(loteNorm) || loteNorm.includes(rowLoteCode))) {
                                loteRow.classList.remove('hidden');
                                loteRow.classList.add('bg-teal-50/80');
                            } else if (loteNorm === 's/l') {
                                loteRow.classList.remove('hidden');
                            } else {
                                loteRow.classList.add('hidden');
                            }
                        });
                    }
                } else {
                    row.classList.add('hidden');
                    if (lotesSubrow) {
                        lotesSubrow.classList.add('hidden');
                    }
                }
            });

            const alertBanner = document.getElementById('activeFilterAlert');
            const filteredText = document.getElementById('filteredProductName');
            const searchInput = document.getElementById('searchInput');

            if (searchInput) searchInput.value = codigoLote !== 'S/L' ? codigoLote : nombreProducto;

            if (alertBanner && filteredText) {
                filteredText.textContent = `${nombreProducto} (Lote: ${codigoLote})`;
                alertBanner.classList.remove('hidden');
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
            if (panel) panel.classList.toggle('hidden');
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

        function openEditLote(id, lote, producto, estado, motivo = '') {
            const form = document.getElementById('editLoteForm');
            if (form) form.action = "/inventario/" + id;

            document.getElementById('modal_producto_nombre').textContent = producto;
            document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;

            let estadoSelect = document.getElementById('modal_estado');
            let estadoFormat = (estado || '').toLowerCase();
            let selectedVal = 'Disponible';

            if (estadoFormat.includes('cuarentena')) selectedVal = 'Cuarentena';
            else if (estadoFormat.includes('merma')) selectedVal = 'Merma';

            estadoSelect.value = selectedVal;

            let motivoInput = document.getElementById('modal_motivo');
            if (motivoInput) motivoInput.value = motivo;

            toggleMotivoField(selectedVal);
            document.getElementById('editLoteModal').classList.remove('hidden');
        }

        function closeEditLote() {
            document.getElementById('editLoteModal').classList.add('hidden');
        }

        function toggleMotivoField(estado) {
            const motivoContainer = document.getElementById('motivoContainer');
            const motivoInput = document.getElementById('modal_motivo');

            if (estado === 'Cuarentena' || estado === 'Merma') {
                motivoContainer.classList.remove('hidden');
                motivoInput.setAttribute('required', 'required');
            } else {
                motivoContainer.classList.add('hidden');
                motivoInput.removeAttribute('required');
            }
        }

        function openQRModal(id, producto, lote, vencimiento, stock, proveedor) {
            const modal = document.getElementById('qrModal');
            modal.classList.remove('hidden');

            document.getElementById('qrProducto').textContent = producto;
            document.getElementById('qrLote').textContent = lote;
            document.getElementById('qrVencimiento').textContent = vencimiento;
            document.getElementById('qrStock').textContent = stock + " UNID.";
            document.getElementById('qrProveedor').textContent = proveedor;

            const qrContainer = document.getElementById('qrcode');
            qrContainer.innerHTML = '';

            setTimeout(function() {
                try {
                    new QRCode(qrContainer, {
                        text: 'LOTE:' + lote + '|PROD:' + producto + '|VENCE:' + vencimiento + '|STOCK:' + stock,
                        width: 140,
                        height: 140,
                        colorDark: "#0f172a",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } catch (error) {
                    console.error("Error al generar código QR:", error);
                }
            }, 150);
        }

        function closeQRModal() { document.getElementById('qrModal').classList.add('hidden'); }
        function openHistory() { document.getElementById('HistoryModal').classList.remove('hidden'); }
        function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }
        function openImport() { document.getElementById('importModal').classList.remove('hidden'); }
        function closeImport() { document.getElementById('importModal').classList.add('hidden'); }
        function openDeposit() { document.getElementById('DepositModal').classList.remove('hidden'); }
        function closeDeposit() { document.getElementById('DepositModal').classList.add('hidden'); }
        function openMerma() { document.getElementById('mermaModal').classList.remove('hidden'); }
        function closeMerma() { document.getElementById('mermaModal').classList.add('hidden'); }

        function showSearchDropdown() {
            const dropdown = document.getElementById('searchDropdown');
            if (dropdown) dropdown.classList.remove('hidden');
        }

        function selectSearchProduct(productName) {
            const input = document.getElementById('searchInput');
            if (input) input.value = productName;

            const dropdown = document.getElementById('searchDropdown');
            if (dropdown) dropdown.classList.add('hidden');

            const form = document.getElementById('filtersForm');
            if (form) form.submit();
        }

        function filterSearchDropdown() {
            const input = document.getElementById('searchInput');
            const filter = (input ? input.value : '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const items = document.querySelectorAll('#searchDropdown .search-item');

            let visibleCount = 0;
            items.forEach(item => {
                const text = item.textContent.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                if (text.includes(filter)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const dropdown = document.getElementById('searchDropdown');
            if (dropdown) dropdown.classList.remove('hidden');
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('searchContainer');
            const dropdown = document.getElementById('searchDropdown');
            if (container && dropdown && !container.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
@endsection