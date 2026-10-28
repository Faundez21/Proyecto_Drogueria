@extends('layouts.app')

@section('title', 'Inventario de Droguería')
@section('subtitle', 'Gestión centralizada de productos, lotes y ubicaciones físicas.')

@section('actions')
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        <button type="button" onclick="openMerma()"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-[13px] font-semibold hover:bg-rose-100 transition-all duration-200 shadow-sm hover:shadow cursor-pointer">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                </path>
            </svg>
            Ver Mermas
        </button>
        @role('Administrador')
            <button type="button" onclick="openHistory()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-[13px] font-semibold hover:bg-slate-50 hover:text-teal-700 transition-all duration-200 shadow-sm hover:shadow cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ver Historial
            </button>
        @endrole
        <button type="button" onclick="openImport()"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-all duration-200 text-[13px] font-bold shadow-sm hover:shadow cursor-pointer">
            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Carga Masiva
        </button>
        <button type="button" onclick="openDeposit()"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all duration-200 text-[13px] font-bold shadow-sm hover:shadow cursor-pointer">
            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Ingreso Manual
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">02</span>
            <span>Almacenamiento</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-medium text-slate-800">Inventario</span>
        </div>

        <!-- Contenedor Principal Tabla Inventario -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- Cabecera de la tabla y botón de filtros -->
            <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center">
                <h3 class="text-[15px] font-bold text-slate-800">Directorio de Productos</h3>
                <button type="button" id="toggle-filters-btn"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtros Avanzados
                </button>
            </div>

            @php
                $hasActiveFilters = request()->filled('search') || request()->filled('categoria') || request()->filled('pasillo') || request()->filled('estante') || request()->filled('order_by');

                $invRawList = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];

                $invNonMermaList = collect($invRawList)->reject(function ($item) {
                    $obj = is_object($item) ? $item : (object) $item;
                    return str_contains(strtolower($obj->estado ?? ''), 'merma');
                });

                $pasillosUnicos = collect($pasillosLista ?? [])->filter()->unique()->sort();
                $estantesUnicos = collect($estantesLista ?? [])->filter()->unique()->sort();
            @endphp

            <!-- Formulario de Filtros -->
            <form method="GET" action="{{ Route::has('inventory.index') ? route('inventory.index') : '#' }}"
                id="filters-panel" class="hidden bg-slate-50/50 border-b border-slate-200 p-5 shadow-inner">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

                    <!-- Búsqueda con Dropdown -->
                    <div class="xl:col-span-2 relative" id="searchContainer">
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Buscar</label>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Producto, categoría o lote..." autocomplete="off" onfocus="showSearchDropdown()"
                            oninput="filterSearchDropdown()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">

                        <div id="searchDropdown"
                            class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            @if (isset($productosLista) && count($productosLista) > 0)
                                @foreach ($productosLista as $prodNombre)
                                    <div class="search-item px-3.5 py-2.5 text-[12.5px] font-medium text-slate-700 hover:bg-teal-50 hover:text-teal-900 cursor-pointer transition-colors flex items-center justify-between"
                                        onclick="selectSearchProduct('{{ $prodNombre }}')">
                                        <span>{{ $prodNombre }}</span>
                                        <svg class="w-3.5 h-3.5 text-slate-300 hover:text-teal-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
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
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría</label>
                        <select name="categoria" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todas</option>
                            <option value="Fármacos" {{ request('categoria') == 'Fármacos' ? 'selected' : '' }}>Fármacos</option>
                            <option value="Insumos Clínicos" {{ request('categoria') == 'Insumos Clínicos' ? 'selected' : '' }}>Insumos Clínicos</option>
                            @if(auth()->user()->hasRole('Administrador') || auth()->user()->can('ver.despacho_controlado'))
                                <option value="Fármacos Controlados" {{ request('categoria') == 'Fármacos Controlados' ? 'selected' : '' }}>
                                    Fármacos Controlados
                                </option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Pasillo</label>
                        <select name="pasillo" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-[13px] bg-white">
                            <option value="">Todos</option>
                            @foreach ($pasillosUnicos as $pasillo)
                                <option value="{{ $pasillo }}" {{ request('pasillo') == $pasillo ? 'selected' : '' }}>{{ $pasillo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Estante</label>
                        <select name="estante" id="estanteSelect" onchange="filterTableByLocation()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach ($estantesUnicos as $est)
                                <option value="{{ $est }}" {{ request('estante') == $est ? 'selected' : '' }}>{{ $est }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Programa</label>
                        <select name="programa" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach ($programasLista ?? [] as $prog)
                                <option value="{{ $prog }}" {{ request('programa') == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ordenar por</label>
                        <select name="order_by" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="recent" {{ request('order_by', 'recent') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                            <option value="nombre_asc" {{ request('order_by') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                            <option value="stock_desc" {{ request('order_by') == 'stock_desc' ? 'selected' : '' }}>Mayor Stock</option>
                            <option value="stock_asc" {{ request('order_by') == 'stock_asc' ? 'selected' : '' }}>Menor Stock</option>
                            <option value="vencimiento" {{ request('order_by') == 'vencimiento' ? 'selected' : '' }}>Próximos a Vencer</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                    <a href="{{ Route::has('inventory.index') ? route('inventory.index') : '#' }}"
                        class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                        Limpiar Filtros
                    </a>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all">
                        Aplicar Filtros
                    </button>
                </div>
            </form>

            <!-- Alerta Verde de Filtros Activos (Blade + JS) -->
            @if (request()->filled('pasillo') || request()->filled('estante') || request()->filled('categoria') || request()->filled('programa') || request()->filled('search'))
                <div id="activeFilterAlert" class="px-5 py-3 bg-teal-50 border-b border-teal-200 flex justify-between items-center text-xs text-teal-800 font-bold">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filtro activo: <span id="filteredProductName" class="underline">
                            {{ implode(' | ', array_filter([
                                request('search') ? 'Búsqueda: ' . request('search') : null,
                                request('categoria') ? 'Cat: ' . request('categoria') : null,
                                request('pasillo') ? 'Pasillo: ' . request('pasillo') : null,
                                request('estante') ? 'Estante: ' . request('estante') : null,
                                request('programa') ? 'Programa: ' . request('programa') : null,
                            ])) }}
                        </span>
                    </span>
                    <a href="{{ Route::has('inventory.index') ? route('inventory.index') : '#' }}" class="px-3 py-1 bg-white border border-teal-300 rounded-lg text-teal-700 hover:bg-teal-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                        Limpiar Filtros
                    </a>
                </div>
            @else
                <div id="activeFilterAlert" class="hidden px-5 py-3 bg-teal-50 border-b border-teal-200 flex justify-between items-center text-xs text-teal-800 font-bold">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filtro activo: <span id="filteredProductName" class="underline"></span>
                    </span>
                    <button type="button" onclick="clearClientFilter()" class="px-3 py-1 bg-white border border-teal-300 rounded-lg text-teal-700 hover:bg-teal-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                        Limpiar Filtros
                    </button>
                </div>
            @endif

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

                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="text-[12px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                            <th scope="col" class="px-6 py-4 w-14 text-center whitespace-nowrap">Detalle</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Producto Principal</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Categoría</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Programa</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Lotes Activos</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Stock</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Valor Total</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">QR</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-[13px]" id="mainTableBody">
                        @if ($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-[14px] font-medium text-slate-600">Bodega sin productos activos</p>
                                    <p class="text-[13px] text-slate-400 mt-1">No existen medicamentos o insumos disponibles en el directorio actual.</p>
                                </td>
                            </tr>
                        @else
                            @foreach ($groupedInventories as $nombreProducto => $lotes)
                                @php
                                    $stockTotalProducto = $lotes->sum(function ($i) { return is_object($i) ? $i->stock_total ?? ($i->cantidad ?? 0) : 0; });
                                    $valorTotalProducto = $lotes->sum(function ($i) {
                                        $obj = is_object($i) ? $i : (object) $i;
                                        return ((float) ($obj->cantidad ?? 0)) * ((float) ($obj->valor_neto_unitario ?? 0));
                                    });
                                    $groupKey = 'grp_' . md5($nombreProducto);
                                    $primerLote = $lotes->first();
                                    $categoriaLote = is_object($primerLote) ? $primerLote->categoria ?? 'General' : 'General';

                                    $loteMasProximo = $lotes->filter(function ($l) {
                                            $fecha = is_object($l) ? $l->fecha_vencimiento ?? null : null;
                                            return !empty($fecha);
                                        })->sortBy(function ($l) {
                                            return \Carbon\Carbon::parse(is_object($l) ? $l->fecha_vencimiento : $l['fecha_vencimiento']);
                                        })->first() ?? $primerLote;

                                    $proxLoteData = is_object($loteMasProximo) ? $loteMasProximo : (object) $loteMasProximo;
                                    $proxLoteId = data_get($proxLoteData, 'id', 0);
                                    $proxLoteCodigo = data_get($proxLoteData, 'lote', 'S/L');
                                    $proxLoteStock = data_get($proxLoteData, 'stock_total') ?? data_get($proxLoteData, 'cantidad', 0);

                                    $proxLoteVencRaw = data_get($proxLoteData, 'fecha_vencimiento');
                                    $proxLoteVencText = 'N/A';
                                    if (!empty($proxLoteVencRaw)) {
                                        try {
                                            $proxLoteVencText = \Carbon\Carbon::parse($proxLoteVencRaw)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $proxLoteVencText = $proxLoteVencRaw;
                                        }
                                    }
                                @endphp

                                <tr id="product-row-{{ $groupKey }}" data-product-name="{{ strtolower(trim($nombreProducto)) }}" class="product-master-row hover:bg-slate-50 transition-colors group cursor-pointer" onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-teal-500 transition-colors"></div>
                                        <button type="button" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-teal-200" id="icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300" id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800">{{ $nombreProducto }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-white text-slate-600 border border-slate-200 shadow-sm whitespace-nowrap">{{ $categoriaLote }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $programaProducto = data_get($primerLote, 'programa') ?? (is_object($primerLote) ? ($primerLote->programa ?? '') : ''); @endphp
                                        @if ($programaProducto)
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm whitespace-nowrap">{{ $programaProducto }}</span>
                                        @else
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-medium bg-slate-50 text-slate-400 border border-slate-100 shadow-sm whitespace-nowrap">Sin programa</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $lotes->count() }}</span>
                                        <span class="text-slate-400 text-[11.5px] font-semibold uppercase ml-1">Lote(s)</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($stockTotalProducto > 10)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                            </span>
                                        @elseif($stockTotalProducto > 0)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ number_format($stockTotalProducto, 0, ',', '.') }} Unid. (Bajo)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                0 Unidades (Agotado)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[12.5px] font-bold bg-slate-50 text-slate-800 border border-slate-200 shadow-sm whitespace-nowrap font-mono-das">
                                            $ {{ number_format($valorTotalProducto, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                        <button type="button" onclick="openQRModal('{{ e($proxLoteId) }}', '{{ e($nombreProducto) }}', '{{ e($proxLoteCodigo) }}', '{{ e($proxLoteVencText) }}', '{{ $proxLoteStock }}', 'Droguería DAS')" class="p-2 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer inline-flex items-center gap-1.5 text-xs font-bold" title="Generar Etiqueta QR del lote más próximo a vencer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr id="lotes-{{ $groupKey }}" class="lotes-subrow hidden bg-slate-50/50 shadow-inner">
                                    <td colspan="8" class="p-0 border-b border-slate-200">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                        Desglose Físico de Lotes
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left whitespace-nowrap">
                                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                                                            <tr>
                                                                <th class="px-4 py-3">Lote / Vencimiento</th>
                                                                <th class="px-4 py-3">Ubicación (Pasillo/Estante)</th>
                                                                <th class="px-4 py-3 text-center">Estado Sanitario</th>
                                                                <th class="px-4 py-3 text-center">Stock de Lote</th>
                                                                <th class="px-4 py-3 text-center">Valor Unitario</th>
                                                                <th class="px-4 py-3 text-center">Valor Total Lote</th>
                                                                <th class="px-4 py-3 text-center w-12"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700 text-[12px]">
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

                                                                    $pasilloVal = data_get($loteData, 'pasillo_nombre') ?? (data_get($loteData, 'aisle.nombre') ?? (data_get($loteData, 'pasillo') ?? 'P.A'));
                                                                    $estanteVal = data_get($loteData, 'estanteria_nombre') ?? (data_get($loteData, 'shelf.nombre') ?? (data_get($loteData, 'estante') ?? 'P.A'));
                                                                    $stockLote = data_get($loteData, 'stock_total') ?? (data_get($loteData, 'cantidad') ?? 0);
                                                                    $isCuarentena = isset($loteData->cuarentena) ? (bool) $loteData->cuarentena : strtolower($loteData->estado ?? '') === 'cuarentena';
                                                                    $loteId = data_get($loteData, 'id') ?? 0;
                                                                    $loteCodigo = data_get($loteData, 'lote') ?? 'S/L';
                                                                    $estadoActualStr = $isCuarentena ? 'Cuarentena' : $loteData->estado ?? 'Disponible';
                                                                    $loteMotivo = data_get($loteData, 'motivo') ?? (data_get($loteData, 'observacion') ?? '');
                                                                    $valorUnitarioLote = (float) (data_get($loteData, 'valor_neto_unitario') ?? 0);
                                                                    $valorTotalLote = ((float) $stockLote) * $valorUnitarioLote;
                                                                    $loteCategoria = data_get($loteData, 'categoria') ?? 'General';
                                                                    $lotePrograma = data_get($loteData, 'programa') ?? '';
                                                                @endphp
                                                                <tr class="lote-item-row hover:bg-slate-50 transition-colors" data-lote-code="{{ strtolower(trim($loteCodigo)) }}" data-pasillo="{{ strtolower(trim($pasilloVal)) }}" data-estante="{{ strtolower(trim($estanteVal)) }}">
                                                                    <td class="px-4 py-3">
                                                                        <span class="font-mono font-bold text-slate-800">{{ $loteCodigo }}</span><br>
                                                                        <span class="text-[10px] uppercase font-semibold {{ $badgeVencimientoClass }}">Vence: {{ $textoVencimiento }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                                                            <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">P:</span> <b class="text-slate-800">{{ $pasilloVal }}</b></span>
                                                                            @can('ver.distribucion')
                                                                                @if (!empty($estanteVal) && $estanteVal !== 'P.A')
                                                                                    <a href="{{ route('distribution.index', ['estante' => $estanteVal]) }}" title="Ver estante {{ $estanteVal }} en el Mapa 3D" class="inline-flex items-center gap-1 bg-white hover:bg-teal-50 text-slate-600 hover:text-teal-700 px-2 py-1 rounded shadow-sm border border-slate-200 hover:border-teal-300 transition-colors">
                                                                                        <span class="text-slate-400">E:</span> <b class="text-slate-800 group-hover:text-teal-700">{{ $estanteVal }}</b>
                                                                                        <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                                @endif
                                                                            @else
                                                                                <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                            @endcan
                                                                            @if ($nivelVal = data_get($loteData, 'nivel'))
                                                                                <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">N:</span> <b class="text-slate-800">{{ $nivelVal }}</b></span>
                                                                            @endif
                                                                            @if ($posicionVal = data_get($loteData, 'posicion'))
                                                                                <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">Pos:</span> <b class="text-slate-800">{{ $posicionVal }}</b></span>
                                                                            @endif
                                                                            @if ($progVal = data_get($loteData, 'programa'))
                                                                                <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded shadow-sm border border-indigo-100 font-semibold">{{ $progVal }}</span>
                                                                            @endif
                                                                            @if (data_get($loteData, 'solicitud_pedido_mensual'))
                                                                                <span class="bg-teal-50 text-teal-700 px-2 py-1 rounded shadow-sm border border-teal-100 font-semibold">Pedido Mensual</span>
                                                                            @endif
                                                                            @if (data_get($loteData, 'solicitud_stock_critico'))
                                                                                <span class="bg-rose-50 text-rose-700 px-2 py-1 rounded shadow-sm border border-rose-100 font-semibold">Stock Crítico</span>
                                                                            @endif
                                                                            @if (data_get($loteData, 'adelanto_de_pedido'))
                                                                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded shadow-sm border border-blue-100 font-semibold">Adelanto</span>
                                                                            @endif
                                                                            @if (data_get($loteData, 'traspaso'))
                                                                                <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded shadow-sm border border-purple-100 font-semibold">Traspaso</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        @if ($isCuarentena)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                        @elseif($stockLote <= 0)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">AGOTADO</span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">DISPONIBLE</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[13px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="text-[12px] font-semibold text-slate-600 font-mono-das">$ {{ number_format($valorUnitarioLote, 2, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[12px] font-bold bg-teal-50 text-teal-800 border border-teal-200 font-mono-das">$ {{ number_format($valorTotalLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <div class="inline-flex items-center justify-center gap-1.5">
                                                                            <button type="button" onclick="openQRModal('{{ e($loteId) }}', '{{ e($nombreProducto) }}', '{{ e($loteCodigo) }}', '{{ e($textoVencimiento) }}', '{{ $stockLote }}', '{{ e(data_get($loteData, 'razon_social') ?? 'Droguería DAS') }}')" class="p-1.5 text-slate-600 bg-slate-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-slate-200 hover:border-teal-600 shadow-sm cursor-pointer" title="Generar Etiqueta QR de este lote">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                                            </button>
                                                                            <button type="button" onclick="openEditLote('{{ e($loteId) }}', '{{ e($loteCodigo) }}', '{{ e($nombreProducto) }}', '{{ e($estadoActualStr) }}', '{{ e($loteMotivo) }}', '{{ e($loteCategoria) }}', '{{ e($lotePrograma) }}', '{{ $valorUnitarioLote }}', '{{ $stockLote }}')" class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Cambiar Estado">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                            </button>
                                                                        </div>
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
            @if (isset($inventories) && method_exists($inventories, 'hasPages') && $inventories->hasPages())
                <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white rounded-b-2xl">
                    <div class="text-[13px] text-slate-500 text-center sm:text-left font-medium">
                        Mostrando del <span class="font-bold text-slate-800">{{ $inventories->firstItem() ?? 0 }}</span>
                        al <span class="font-bold text-slate-800">{{ $inventories->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $inventories->total() }}</span> registros
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if ($inventories->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Anterior</span>
                        @else
                            <a href="{{ $inventories->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Anterior</a>
                        @endif

                        @php
                            $start = max($inventories->currentPage() - 2, 1);
                            $end = min($inventories->currentPage() + 2, $inventories->lastPage());
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $inventories->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">1</a>
                            @if ($start > 2)
                                <span class="px-1 text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $inventories->currentPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-bold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">{{ $i }}</span>
                            @else
                                <a href="{{ $inventories->url($i) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $i }}</a>
                            @endif
                        @endfor

                        @if ($end < $inventories->lastPage())
                            @if ($end < $inventories->lastPage() - 1)
                                <span class="px-1 text-slate-400">...</span>
                            @endif
                            <a href="{{ $inventories->url($inventories->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $inventories->lastPage() }}</a>
                        @endif

                        @if ($inventories->hasMorePages())
                            <a href="{{ $inventories->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Siguiente</a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Siguiente</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal: Historial de Movimientos -->
        <div id="HistoryModal"
            class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-7xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Historial General de Inventario</h2>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Registro de auditoría de recepciones y movimientos (Últimos 150).</p>
                    </div>
                    <button type="button" onclick="closeHistory()"
                        class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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
                            @forelse($historial ?? [] as $item)
                                @php
                                    $itemObj = is_object($item) ? $item : (object) $item;
                                    $prodNombre = data_get($itemObj, 'product.nombre') ?? (data_get($itemObj, 'producto') ?? 'Producto Desconocido');
                                    $cant = data_get($itemObj, 'stock_total') ?? (data_get($itemObj, 'cantidad') ?? 0);
                                    $loteCodigo = data_get($itemObj, 'lote') ?? (data_get($itemObj, 'loteRef.lote') ?? 'S/L');

                                    // FECHA Y HORA (Usamos created_at para el ingreso de inventario)
                                    $rawDate = data_get($itemObj, 'created_at') ?? data_get($itemObj, 'updated_at');
                                    if ($rawDate) {
                                        try {
                                            $fechaFormateada = \Carbon\Carbon::parse($rawDate)->format('d/m/Y H:i');
                                        } catch (\Exception $e) {
                                            $fechaFormateada = 'Fecha Inválida';
                                        }
                                    } else {
                                        $fechaFormateada = 'Sin Registro';
                                    }

                                    // USUARIO RESPONSABLE
                                    $uName = data_get($itemObj, 'user.name') ?? (data_get($itemObj, 'usuario.name') ?? data_get($itemObj, 'causer.name'));
                                    $uLast = data_get($itemObj, 'user.last_name') ?? (data_get($itemObj, 'usuario.last_name') ?? data_get($itemObj, 'causer.last_name'));

                                    if (!empty($uName)) {
                                        $usuarioResp = trim($uName . ' ' . $uLast);
                                    } else {
                                        $strUser = data_get($itemObj, 'usuario_nombre') ?? (data_get($itemObj, 'responsable') ?? data_get($itemObj, 'usuario'));
                                        $usuarioResp = is_string($strUser) && !empty(trim($strUser)) ? $strUser : 'No registrado';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-100/70 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $fechaFormateada }}</td>
                                    <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $itemObj->tipo_documento ?? 'Doc' }} #{{ $itemObj->numero_documento ?? '000' }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $itemObj->razon_social ?? 'Interno/Proveedor' }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ $prodNombre }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono font-extrabold text-[12px] border border-slate-300">{{ $loteCodigo }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-medium text-slate-700">{{ $usuarioResp }}</td>
                                    <td class="px-6 py-3.5 text-center font-extrabold text-emerald-600">+{{ $cant }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <button type="button" onclick="filterTableByLote('{{ e($prodNombre) }}', '{{ e($loteCodigo) }}')" class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer inline-flex items-center justify-center" title="Ver este lote específico">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
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

        <!-- Pantalla de Carga Global para Procesamiento de Archivos Pesados -->
        <div id="importLoadingOverlay" class="hidden fixed inset-0 z-[10050] bg-slate-900/85 backdrop-blur-md flex flex-col items-center justify-center p-6 transition-all">
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 p-8 max-w-md w-full text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-slate-100 overflow-hidden">
                    <div id="importProgressBarTop" class="h-full bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-600 transition-all duration-300" style="width: 15%"></div>
                </div>

                <div class="relative w-20 h-20 mx-auto mb-5 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-teal-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-teal-600 border-t-transparent animate-spin"></div>
                    <svg class="w-9 h-9 text-teal-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-slate-800 tracking-tight mb-1">Procesando Carga Masiva</h3>
                <p id="loadingStepText" class="text-[13px] font-semibold text-teal-700 mb-4">Analizando estructura y cabeceras del archivo...</p>

                <div id="loadingFileBadge" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-[12px] font-bold text-slate-700 mb-5 max-w-full truncate">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span id="loadingFileName" class="truncate">planilla_inventario.xlsx</span>
                    <span id="loadingFileSize" class="text-slate-400 font-mono text-[11px] shrink-0"></span>
                </div>

                <div class="w-full bg-slate-100 rounded-full h-3 mb-2 overflow-hidden border border-slate-200/80">
                    <div id="importProgressBar" class="bg-gradient-to-r from-teal-600 to-emerald-500 h-full rounded-full transition-all duration-500" style="width: 12%"></div>
                </div>
                <div class="flex justify-between items-center text-[11px] font-bold text-slate-400 mb-5">
                    <span id="loadingStageLabel">Validando registros...</span>
                    <span id="loadingPercentLabel" class="text-teal-700 font-mono">12%</span>
                </div>

                <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-3 text-left flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                        <strong>Por favor no cierre ni recargue esta ventana.</strong> Archivos pesados pueden tardar algunos segundos mientras se verifican duplicados y trazabilidad.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal: Importar Carga Masiva -->
        <div id="importModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 flex flex-col w-full max-w-lg overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Importar Carga Masiva</h2>
                            <p class="text-[11px] text-slate-300 font-medium">Validación inteligente de formato, lotes y duplicados</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeImport()"
                        class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 bg-slate-50 space-y-4">
                    @if(session('open_modal') === 'import' && session('error'))
                        <div class="bg-rose-50 border-l-4 border-rose-600 border border-rose-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="flex-1">
                                    <h4 class="text-[13px] font-extrabold text-rose-900">{{ session('import_error_title', 'Error al Procesar el Archivo') }}</h4>
                                    <p class="text-[12px] text-rose-700 mt-1 leading-relaxed font-medium">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alerta dinámica JS (Formato inválido o Archivo Duplicado detectado en cliente) -->
                    <div id="clientImportAlert" class="hidden rounded-xl p-4 border shadow-sm">
                        <div class="flex items-start gap-3">
                            <svg id="clientImportAlertIcon" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                            <div class="flex-1">
                                <h4 id="clientImportAlertTitle" class="text-[13px] font-extrabold"></h4>
                                <p id="clientImportAlertMsg" class="text-[12px] mt-1 leading-relaxed font-medium"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between gap-4 shadow-sm">
                        <div class="text-left">
                            <span class="text-[12px] font-extrabold text-slate-800 block">1. Plantilla Oficial de Inventario</span>
                            <span class="text-[11px] text-slate-500">Incluye las columnas exactas requeridas por bodega.</span>
                        </div>
                        <a href="{{ Route::has('inventory.template') ? route('inventory.template') : '#' }}"
                            class="shrink-0 inline-flex items-center gap-2 bg-teal-50 border border-teal-200 text-teal-700 px-3.5 py-2 rounded-lg hover:bg-teal-600 hover:text-white transition-all font-bold text-[12px] shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Descargar Plantilla
                        </a>
                    </div>

                    <form id="importForm" action="{{ Route::has('inventory.import') ? route('inventory.import') : '#' }}" method="POST" enctype="multipart/form-data" onsubmit="return handleImportSubmit(event)" class="bg-white border-2 border-dashed border-slate-300 hover:border-teal-500 transition-all rounded-xl p-6 flex flex-col items-center gap-3 text-center shadow-sm"
                        ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleFileDrop(event)">
                        @csrf
                        <div id="dropzoneIconWrap" class="w-14 h-14 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 mb-1 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>

                        <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">

                        <label for="excel" class="w-full bg-slate-50 border border-slate-300 text-slate-700 hover:bg-teal-50 hover:border-teal-300 hover:text-teal-700 px-4 py-2.5 rounded-xl cursor-pointer inline-flex items-center justify-center gap-2 text-[13px] font-bold transition-all shadow-sm">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <span id="fileNameLabel">Seleccionar o arrastrar archivo aquí</span>
                        </label>

                        <!-- Tarjeta de vista previa del archivo seleccionado -->
                        <div id="selectedFileCard" class="hidden w-full bg-teal-50/70 border border-teal-200 rounded-xl p-3 flex items-center justify-between gap-3 text-left">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span id="selectedFileExtBadge" class="px-2 py-1 rounded-md bg-teal-600 text-white font-mono text-[10px] font-extrabold uppercase shrink-0">XLSX</span>
                                <div class="min-w-0">
                                    <p id="selectedFileNameText" class="text-[12px] font-bold text-slate-800 truncate"></p>
                                    <p id="selectedFileSizeText" class="text-[11px] text-slate-500 font-mono"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelectedImportFile()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" title="Quitar archivo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Opción de forzar reimportación cuando se detecta hash duplicado -->
                        <div id="forceReimportContainer" class="hidden w-full bg-amber-50 border border-amber-200 rounded-lg p-2.5 text-left">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="force_reimport" id="forceReimportCheck" value="1" class="mt-0.5 rounded border-amber-400 text-teal-600 focus:ring-teal-500">
                                <span class="text-[11px] text-amber-900 font-semibold leading-snug">
                                    Confirmo que deseo volver a procesar este archivo aunque haya sido subido previamente.
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 font-medium">
                            <span>Formatos permitidos: <strong class="text-slate-600">.xlsx, .xls, .csv</strong></span>
                            <span>·</span>
                            <span>Máx. <strong class="text-slate-600">20 MB</strong></span>
                        </div>

                        <button type="submit" id="importSubmitBtn" class="w-full px-4 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm mt-2 cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Validar y Procesar Carga Masiva</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Ingreso Manual -->
        <div id="DepositModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Ingreso Manual Directo de Producto</h2>
                            <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Registro individual con trazabilidad inmediata en bodega.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeposit()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto bg-slate-50/50">
                    @if(isset($errors) && $errors->any() && session('open_modal') !== 'import')
                        <div class="mb-6 bg-rose-50 border-l-4 border-rose-600 border border-rose-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div>
                                    <h4 class="text-[13px] font-extrabold text-rose-900">No se pudo completar el ingreso manual:</h4>
                                    <ul class="mt-1 list-disc list-inside text-[12px] text-rose-700 font-medium space-y-0.5">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <datalist id="catalogoProductosManual">
                        @foreach($productosLista ?? [] as $prodItem)
                            <option value="{{ $prodItem }}"></option>
                        @endforeach
                    </datalist>

                    <datalist id="catalogoProveedoresRutManual">
                        @foreach($proveedoresActivos ?? [] as $provItem)
                            <option value="{{ $provItem->rut }}">{{ $provItem->name }}</option>
                        @endforeach
                    </datalist>

                    <datalist id="catalogoProveedoresNombreManual">
                        @foreach($proveedoresActivos ?? [] as $provItem)
                            <option value="{{ $provItem->name }}">{{ $provItem->rut }}</option>
                        @endforeach
                    </datalist>

                    <form action="{{ Route::has('inventory.store') ? route('inventory.store') : '#' }}" method="POST" id="manualDepositForm" novalidate onsubmit="return validateIngresoDirecto(event)">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="sm:col-span-2">
                                <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-1">
                                    <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[11px] font-bold flex items-center justify-center">1</span>
                                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider">Datos del Documento y Proveedor</h4>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">N° Correlativo Interno</label>
                                <input type="number" min="1" step="1" name="correlativo_interno" value="{{ old('correlativo_interno') }}" placeholder="Autogenerado si se deja vacío" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-mono" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Fecha Recepción <span class="text-rose-500">*</span></label>
                                <input type="date" name="fecha_recepcion" required value="{{ old('fecha_recepcion', date('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Tipo Documento <span class="text-rose-500">*</span></label>
                                <select name="tipo_documento" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="" disabled {{ old('tipo_documento') ? '' : 'selected' }}>Seleccione...</option>
                                    <option value="Factura Electrónica" {{ old('tipo_documento') == 'Factura Electrónica' ? 'selected' : '' }}>Factura Electrónica</option>
                                    <option value="Factura" {{ old('tipo_documento') == 'Factura' ? 'selected' : '' }}>Factura</option>
                                    <option value="Guía de Despacho" {{ old('tipo_documento') == 'Guía de Despacho' ? 'selected' : '' }}>Guía de Despacho</option>
                                    <option value="Orden de Compra" {{ old('tipo_documento') == 'Orden de Compra' ? 'selected' : '' }}>Orden de Compra</option>
                                    <option value="Acta de Recepción" {{ old('tipo_documento') == 'Acta de Recepción' ? 'selected' : '' }}>Acta de Recepción / Traspaso</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">N° Documento <span class="text-rose-500">*</span></label>
                                <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" placeholder="Ej. 104590" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-mono" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">RUT Proveedor</label>
                                <input type="text" id="manualRutProveedor" name="rut_proveedor" list="catalogoProveedoresRutManual" oninput="syncManualProvider('rut', this.value)" value="{{ old('rut_proveedor') }}" placeholder="Ej. 61.608.700-K" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-mono" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Razón Social Proveedor / Origen</label>
                                <input type="text" id="manualRazonSocial" name="razon_social" list="catalogoProveedoresNombreManual" oninput="syncManualProvider('name', this.value)" value="{{ old('razon_social') }}" placeholder="Ej. CENABAST / Laboratorio Chile" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>

                            <div class="sm:col-span-2 mt-3">
                                <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-1">
                                    <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[11px] font-bold flex items-center justify-center">2</span>
                                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider">Detalle Técnico del Producto y Ubicación</h4>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Producto / Principio Activo <span class="text-rose-500">*</span></label>
                                <input type="text" name="producto" list="catalogoProductosManual" value="{{ old('producto') }}" placeholder="Escriba o seleccione del catálogo (Ej. PARACETAMOL 500MG COMPRIMIDOS)" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-bold text-slate-800" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Categoría <span class="text-rose-500">*</span></label>
                                <select name="categoria" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="" disabled {{ old('categoria') ? '' : 'selected' }}>Seleccionar...</option>
                                    <option value="Medicamentos" {{ old('categoria') == 'Medicamentos' ? 'selected' : '' }}>Medicamentos</option>
                                    <option value="Fármacos" {{ old('categoria') == 'Fármacos' ? 'selected' : '' }}>Fármacos</option>
                                    <option value="Insumos" {{ old('categoria') == 'Insumos' ? 'selected' : '' }}>Insumos</option>
                                    <option value="Insumos Clínicos" {{ old('categoria') == 'Insumos Clínicos' ? 'selected' : '' }}>Insumos Clínicos</option>
                                    <option value="Equipamiento" {{ old('categoria') == 'Equipamiento' ? 'selected' : '' }}>Equipamiento</option>
                                    @role('Administrador')
                                        <option value="Fármacos Controlados" {{ old('categoria') == 'Fármacos Controlados' ? 'selected' : '' }}>Fármacos Controlados (Estupefacientes/Psicotrópicos)</option>
                                    @endrole
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Estado de Ingreso <span class="text-rose-500">*</span></label>
                                <select name="estado" id="manualEstadoSelect" onchange="toggleManualEstadoHelp(this.value)" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-semibold text-slate-800">
                                    <option value="Disponible" {{ old('estado', 'Disponible') == 'Disponible' ? 'selected' : '' }}>Disponible (Visible en Inventario Principal)</option>
                                    <option value="Cuarentena" {{ old('estado') == 'Cuarentena' ? 'selected' : '' }}>En Cuarentena (Enviar a Revisión Técnica)</option>
                                </select>
                                <p id="manualEstadoHelpText" class="hidden text-[11px] text-amber-700 font-semibold mt-1">
                                    Nota: Los lotes en Cuarentena se gestionan en la vista "Cuarentena" hasta su liberación.
                                </p>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Pasillo Ubicación</label>
                                <select name="pasillo" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="">Seleccione Pasillo...</option>
                                    @foreach ($pasillosLista ?? [] as $pasillo)
                                        <option value="{{ $pasillo }}" {{ old('pasillo') == $pasillo ? 'selected' : '' }}>{{ $pasillo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Estante Ubicación</label>
                                <select name="estante" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="">Seleccione Estante...</option>
                                    @foreach ($estantesLista ?? [] as $estante)
                                        <option value="{{ $estante }}" {{ old('estante') == $estante ? 'selected' : '' }}>{{ $estante }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Nivel</label>
                                <select name="nivel" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="">Seleccione Nivel...</option>
                                    @foreach ($nivelesLista ?? [] as $nivel)
                                        <option value="{{ $nivel }}" {{ old('nivel') == $nivel ? 'selected' : '' }}>{{ $nivel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Posición</label>
                                <select name="posicion" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                                    <option value="">Seleccione Posición...</option>
                                    @foreach ($posicionesLista ?? [] as $posicion)
                                        <option value="{{ $posicion }}" {{ old('posicion') == $posicion ? 'selected' : '' }}>{{ $posicion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Programa Ministerial</label>
                                <input type="text" name="programa" list="programasManualList" value="{{ old('programa') }}" placeholder="Ej. FOFAR / Morbilidad General" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                                <datalist id="programasManualList">
                                    <option value="Morbilidad General"></option>
                                    <option value="Fondo de Farmacia (FOFAR)"></option>
                                    <option value="Cardiovascular"></option>
                                    <option value="IRA/ERA"></option>
                                    <option value="Salud Mental"></option>
                                    @foreach($programasLista ?? [] as $progOpt)
                                        <option value="{{ $progOpt }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Lote / Serie</label>
                                <input type="text" name="lote" value="{{ old('lote') }}" placeholder="Ej. L-4893 (o S/L)" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-mono font-bold" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Cantidad a Ingresar <span class="text-rose-500">*</span></label>
                                <input type="number" name="cantidad" value="{{ old('cantidad') }}" placeholder="Ej. 500" min="1" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-bold text-slate-800" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Valor Neto Unitario ($ CLP)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[13px] font-bold text-slate-400">$</span>
                                    <input type="number" name="valor_neto_unitario" value="{{ old('valor_neto_unitario') ? (int) old('valor_neto_unitario') : '' }}" placeholder="0" min="0" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm font-bold text-slate-800 font-mono-das" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeDeposit()" class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cancelar</button>
                    <button type="submit" id="manualDepositSubmitBtn" form="manualDepositForm" class="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors shadow-sm text-[13px] font-bold cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span id="manualDepositSubmitText">Confirmar Ingreso</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Etiqueta QR -->
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
                <div class="no-print-qr mt-6 flex justify-center gap-4 w-full">
                    <button type="button" onclick="closeQRModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md cursor-pointer">Cerrar Vista</button>
                    <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Etiqueta
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Lote -->
        <div id="editLoteModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[10000] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-md flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Actualizar Estado de Lote</h2>
                    <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-white" onsubmit="return confirmEditLoteInventario(event)">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Modificando Lote de:</span>
                            <p id="modal_producto_nombre" class="text-[14px] font-extrabold text-slate-800 w-full break-words leading-tight"></p>
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm">
                                <p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p>
                            </div>
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
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Stock</span>
                                <p id="modal_cantidad" class="text-[13px] font-bold text-slate-800">0</p>
                            </div>
                            <div class="bg-teal-50 border border-teal-200 rounded-xl p-2.5 text-center shadow-sm">
                                <span class="text-[10px] font-bold text-teal-500 uppercase block">Valor Total</span>
                                <p id="modal_valor_total" class="text-[13px] font-extrabold text-teal-800 font-mono-das">$ 0</p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Seleccione Nuevo Estado Físico *</label>
                            <select id="modal_estado" name="estado" onchange="toggleMotivoField(this.value)" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Disponible">🟢 Disponible (Liberado para entrega)</option>
                                <option value="Cuarentena">🟠 En Cuarentena (Bloqueo preventivo)</option>
                                <option value="Merma">🔴 En Merma (Dañado / Vencido)</option>
                            </select>
                        </div>

                        <div id="motivoContainer" class="hidden">
                            <label for="modal_motivo" class="block text-[13px] font-bold text-slate-700 mb-1.5">Motivo <span class="text-rose-500">*</span></label>
                            <textarea id="modal_motivo" name="motivo" rows="3" placeholder="Indique la razón de la merma o cuarentena..." class="w-full rounded-xl border border-slate-300 bg-white p-3 text-[12px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all text-slate-700 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Ver Mermas -->
        <div id="mermaModal" class="hidden fixed -inset-x-[100vw] -inset-y-[100vh] z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-6xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-rose-800 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-700 border border-rose-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Registro de Productos Mermados</h2>
                            <p class="text-[12px] text-rose-200 mt-0.5 font-medium">Listado histórico de medicamentos e insumos dados de baja.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeMerma()" class="text-rose-200 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- NUEVO: FILTROS AVANZADOS DE MERMAS -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap gap-4 items-end z-20 shadow-inner">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Buscar Texto</label>
                        <input type="text" id="mermaSearchInput" onkeyup="filterMermas()" placeholder="Producto, lote o motivo..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Desde</label>
                        <input type="date" id="mermaDateFrom" onchange="filterMermas()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hasta</label>
                        <input type="date" id="mermaDateTo" onchange="filterMermas()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-sm">
                    </div>
                    <div class="w-full sm:w-[200px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Responsable</label>
                        <select id="mermaUserFilter" onchange="filterMermas()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-sm">
                            <option value="">Todos los usuarios</option>
                            @php
                                $usuariosMerma = collect($mermas ?? [])->map(function($m) {
                                    $mObj = is_object($m) ? $m : (object) $m;
                                    $uName = data_get($mObj, 'user.name') ?? data_get($mObj, 'usuario_nombre');
                                    $uLast = data_get($mObj, 'user.last_name') ?? '';
                                    return !empty($uName) ? trim($uName . ' ' . $uLast) : 'No registrado';
                                })->unique()->filter()->values();
                            @endphp
                            @foreach($usuariosMerma as $um)
                                <option value="{{ $um }}">{{ $um }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="button" onclick="clearMermaFilters()" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors text-[13px] font-bold shadow-sm">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[50vh] bg-white">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-6 py-4">Fecha Mov.</th>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Lote</th>
                                <th class="px-6 py-4">Responsable</th>
                                <th class="px-6 py-4">Motivo</th>
                                <th class="px-6 py-4 text-center">Cant.</th>
                                <th class="px-6 py-4 text-center">Valor Total</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="mermasTableBody">
                            @forelse($mermas ?? [] as $mItem)
                                @php
                                    $mObj = is_object($mItem) ? $mItem : (object) $mItem;
                                    $rawDateMerma = data_get($mObj, 'updated_at') ?? data_get($mObj, 'created_at');

                                    // Para filtros en JS
                                    $fechaISO = '';
                                    if ($rawDateMerma) {
                                        try {
                                            $cDate = \Carbon\Carbon::parse($rawDateMerma);
                                            $fechaMerma = $cDate->format('d/m/Y H:i');
                                            $fechaISO = $cDate->format('Y-m-d');
                                        } catch (\Exception $e) {
                                            $fechaMerma = 'Fecha Inválida';
                                        }
                                    } else {
                                        $fechaMerma = 'Sin Registro';
                                    }

                                    $mNombre = data_get($mObj, 'product.nombre') ?? (data_get($mObj, 'producto') ?? 'Desconocido');
                                    $mStock = data_get($mObj, 'stock_total') ?? (data_get($mObj, 'cantidad') ?? 0);
                                    $mLoteCodigo = data_get($mObj, 'lote') ?? 'S/L';
                                    $mLoteId = data_get($mObj, 'id') ?? 0;

                                    $uName = data_get($mObj, 'user.name') ?? data_get($mObj, 'usuario_nombre');
                                    $uLast = data_get($mObj, 'user.last_name') ?? '';
                                    $usuarioMerma = !empty($uName) ? trim($uName . ' ' . $uLast) : 'No registrado';

                                    $mMotivo = data_get($mObj, 'motivo') ?? 'Sin motivo especificado';

                                    $mValorUnitario = (float) (data_get($mObj, 'valor_neto_unitario') ?? 0);
                                    $mValorTotal = ((float) $mStock) * $mValorUnitario;
                                @endphp
                                <tr class="merma-row hover:bg-rose-50/40 transition-colors" data-valor="{{ $mValorTotal }}" data-date="{{ $fechaISO }}" data-user="{{ $usuarioMerma }}">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $fechaMerma }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800 searchable">{{ $mNombre }}</td>
                                    <td class="px-6 py-3.5 text-center searchable">
                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono font-extrabold text-[12px] border border-slate-300">{{ $mLoteCodigo }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700 font-semibold searchable">{{ $usuarioMerma }}</td>
                                    <td class="px-6 py-3.5 italic text-rose-600 max-w-[150px] truncate searchable" title="{{ $mMotivo }}">{{ $mMotivo }}</td>
                                    <td class="px-6 py-3.5 text-center font-extrabold text-rose-600">{{ number_format($mStock, 0, ',', '.') }} u.</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[12px] font-bold bg-rose-100 text-rose-800 border border-rose-200 font-mono-das">
                                            - $ {{ number_format($mValorTotal, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <button type="button"
                                            onclick="openEditLote('{{ e($mLoteId) }}', '{{ e($mLoteCodigo) }}', '{{ e($mNombre) }}', 'Merma', '{{ e($mMotivo != 'Sin motivo especificado' ? $mMotivo : '') }}', '{{ e($mObj->categoria ?? 'General') }}', '{{ e($mObj->programa ?? '') }}', '{{ $mValorUnitario }}', '{{ $mStock }}')"
                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer inline-flex items-center justify-center" title="Editar Estado">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyMermasRow">
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>No hay productos marcados en estado de merma actualmente.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="filteredEmptyRow" class="hidden">
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-medium">
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
                    <div class="text-[13px] font-extrabold text-slate-500 uppercase tracking-wider flex items-center gap-3">
                        <span>Mostrando <span id="mermasCount" class="text-rose-600 font-black">0</span> res.</span>
                        <div class="h-4 w-px bg-slate-300"></div>
                        <span>Valor Total:
                            <span id="mermaTotalDisplay" class="inline-flex items-center px-3 py-1.5 rounded-lg text-[13px] font-extrabold bg-rose-600 text-white font-mono-das ml-2 shadow-sm">
                                $ 0
                            </span>
                        </span>
                    </div>
                    <button type="button" onclick="closeMerma()" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                        Cerrar Ventana
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
            const panel = document.getElementById('filters-panel');
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

        function openEditLote(id, lote, producto, estado, motivo = '', categoria = '', programa = '', valorUnitario = 0, cantidad = 0) {
            const form = document.getElementById('editLoteForm');
            if (form) form.action = "/inventory/" + id;

            document.getElementById('modal_producto_nombre').textContent = producto;
            document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;

            const fmt = (n) => '$ ' + Number(n || 0).toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            const valorU = parseFloat(valorUnitario) || 0;
            const cant = parseInt(cantidad) || 0;

            document.getElementById('modal_valor_unitario').textContent = fmt(valorU);
            document.getElementById('modal_cantidad').textContent = cant.toLocaleString('es-CL') + ' u';
            document.getElementById('modal_valor_total').textContent = fmt(valorU * cant);

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

            let estadoSelect = document.getElementById('modal_estado');
            let estadoFormat = (estado || '').toLowerCase();
            let selectedVal = 'Disponible';

            if (estadoFormat.includes('cuarentena')) selectedVal = 'Cuarentena';
            else if (estadoFormat.includes('merma')) selectedVal = 'Merma';

            estadoSelect.value = selectedVal;

            let motivoInput = document.getElementById('modal_motivo');
            if (motivoInput) motivoInput.value = motivo;

            toggleMotivoField(selectedVal);

            // Si el modal de merma está abierto, lo ocultamos para que no colisionen
            closeMerma();
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

            const qrImg = document.getElementById('qrcode');
            qrImg.src = "{{ url('qr') }}/" + id + "/image";
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
        }

        function closeHistory() {
            document.getElementById('HistoryModal').classList.add('hidden');
        }

        function openImport() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImport() {
            document.getElementById('importModal').classList.add('hidden');
        }

        const manualProvidersCatalog = @json($proveedoresActivos ?? []);

        function syncManualProvider(by, val) {
            const clean = (val || '').trim().toLowerCase();
            if (!clean) return;
            const rutInput = document.getElementById('manualRutProveedor');
            const nameInput = document.getElementById('manualRazonSocial');
            if (by === 'rut') {
                const found = manualProvidersCatalog.find(p => (p.rut || '').trim().toLowerCase() === clean);
                if (found && nameInput) nameInput.value = found.name;
            } else if (by === 'name') {
                const found = manualProvidersCatalog.find(p => (p.name || '').trim().toLowerCase() === clean);
                if (found && rutInput) rutInput.value = found.rut;
            }
        }

        function openDeposit() {
            document.getElementById('DepositModal').classList.remove('hidden');
        }

        function closeDeposit() {
            document.getElementById('DepositModal').classList.add('hidden');
        }

        // ====== LÓGICA FILTROS AVANZADOS MERMA ======
        function openMerma() {
            document.getElementById('mermaModal').classList.remove('hidden');
            filterMermas();
        }

        function closeMerma() {
            document.getElementById('mermaModal').classList.add('hidden');
        }

        function filterMermas() {
            const search = normalizeText(document.getElementById('mermaSearchInput')?.value || '');
            const dateFrom = document.getElementById('mermaDateFrom')?.value || '';
            const dateTo = document.getElementById('mermaDateTo')?.value || '';
            const userFilter = document.getElementById('mermaUserFilter')?.value || '';

            const rows = document.querySelectorAll('.merma-row');
            const filteredEmptyRow = document.getElementById('filteredEmptyRow');
            const mainEmptyRow = document.getElementById('emptyMermasRow');

            let visibleCount = 0;
            let totalValue = 0;

            rows.forEach(row => {
                // Validación Búsqueda
                const searchables = row.querySelectorAll('.searchable');
                let rowText = '';
                searchables.forEach(td => rowText += normalizeText(td.textContent) + ' ');
                const matchSearch = rowText.includes(search);

                // Validación Usuario
                const rowUser = row.getAttribute('data-user') || '';
                const matchUser = userFilter === '' || rowUser === userFilter;

                // Validación Fechas
                const rowDateStr = row.getAttribute('data-date'); // YYYY-MM-DD
                let matchDate = true;
                if (rowDateStr) {
                    if (dateFrom && rowDateStr < dateFrom) matchDate = false;
                    if (dateTo && rowDateStr > dateTo) matchDate = false;
                } else if (dateFrom || dateTo) {
                    matchDate = false; // Si no tiene fecha y hay filtro de fecha
                }

                // Aplicar Filtro Final
                if (matchSearch && matchUser && matchDate) {
                    row.style.display = '';
                    visibleCount++;
                    totalValue += parseFloat(row.getAttribute('data-valor')) || 0;
                } else {
                    row.style.display = 'none';
                }
            });

            // Manejo de UI de filas vacías
            if (rows.length > 0 && filteredEmptyRow) {
                filteredEmptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            // Actualizar contadores
            document.getElementById('mermasCount').textContent = visibleCount;
            document.getElementById('mermaTotalDisplay').textContent = '$ ' + totalValue.toLocaleString('es-CL', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function clearMermaFilters() {
            if(document.getElementById('mermaSearchInput')) document.getElementById('mermaSearchInput').value = '';
            if(document.getElementById('mermaDateFrom')) document.getElementById('mermaDateFrom').value = '';
            if(document.getElementById('mermaDateTo')) document.getElementById('mermaDateTo').value = '';
            if(document.getElementById('mermaUserFilter')) document.getElementById('mermaUserFilter').value = '';
            filterMermas();
        }
        // ============================================

        function showSearchDropdown() {
            const dropdown = document.getElementById('searchDropdown');
            if (dropdown) dropdown.classList.remove('hidden');
        }

        function selectSearchProduct(productName) {
            const input = document.getElementById('searchInput');
            if (input) input.value = productName;

            const dropdown = document.getElementById('searchDropdown');
            if (dropdown) dropdown.classList.add('hidden');

            const form = document.getElementById('filters-panel');
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

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 KB';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showClientImportAlert(type, title, message) {
            const box = document.getElementById('clientImportAlert');
            const titleEl = document.getElementById('clientImportAlertTitle');
            const msgEl = document.getElementById('clientImportAlertMsg');
            const iconEl = document.getElementById('clientImportAlertIcon');
            if (!box) return;

            box.classList.remove('hidden', 'bg-rose-50', 'border-rose-200', 'bg-amber-50', 'border-amber-200');
            titleEl.className = 'text-[13px] font-extrabold';
            msgEl.className = 'text-[12px] mt-1 leading-relaxed font-medium';

            if (type === 'error') {
                box.classList.add('bg-rose-50', 'border-rose-200', 'border-l-4', 'border-l-rose-600');
                titleEl.classList.add('text-rose-900');
                msgEl.classList.add('text-rose-700');
                iconEl.setAttribute('class', 'w-5 h-5 text-rose-600 shrink-0 mt-0.5');
                iconEl.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            } else {
                box.classList.add('bg-amber-50', 'border-amber-200', 'border-l-4', 'border-l-amber-500');
                titleEl.classList.add('text-amber-900');
                msgEl.classList.add('text-amber-800');
                iconEl.setAttribute('class', 'w-5 h-5 text-amber-600 shrink-0 mt-0.5');
                iconEl.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
            }

            titleEl.textContent = title;
            msgEl.textContent = message;
        }

        function hideClientImportAlert() {
            const box = document.getElementById('clientImportAlert');
            if (box) box.classList.add('hidden');
        }

        function clearSelectedImportFile() {
            const input = document.getElementById('excel');
            const label = document.getElementById('fileNameLabel');
            const card = document.getElementById('selectedFileCard');
            const forceWrap = document.getElementById('forceReimportContainer');
            const forceCheck = document.getElementById('forceReimportCheck');
            if (input) input.value = '';
            if (label) {
                label.textContent = 'Seleccionar o arrastrar archivo aquí';
                label.classList.remove('text-teal-700');
            }
            if (card) card.classList.add('hidden');
            if (forceWrap) forceWrap.classList.add('hidden');
            if (forceCheck) forceCheck.checked = false;
            hideClientImportAlert();
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            const form = document.getElementById('importForm');
            if (form) form.classList.add('border-teal-500', 'bg-teal-50/30');
        }

        function handleDragLeave(event) {
            event.preventDefault();
            event.stopPropagation();
            const form = document.getElementById('importForm');
            if (form) form.classList.remove('border-teal-500', 'bg-teal-50/30');
        }

        function handleFileDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            const form = document.getElementById('importForm');
            if (form) form.classList.remove('border-teal-500', 'bg-teal-50/30');

            const input = document.getElementById('excel');
            if (event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length > 0 && input) {
                input.files = event.dataTransfer.files;
                updateFileName(input);
            }
        }

        function updateFileName(input) {
            const label = document.getElementById('fileNameLabel');
            const card = document.getElementById('selectedFileCard');
            const extBadge = document.getElementById('selectedFileExtBadge');
            const nameText = document.getElementById('selectedFileNameText');
            const sizeText = document.getElementById('selectedFileSizeText');
            const forceWrap = document.getElementById('forceReimportContainer');

            hideClientImportAlert();
            if (forceWrap) forceWrap.classList.add('hidden');

            if (!input.files || input.files.length === 0) {
                clearSelectedImportFile();
                return;
            }

            const file = input.files[0];
            const parts = file.name.split('.');
            const ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
            const allowed = ['xlsx', 'xls', 'csv'];

            // 1. Validar formato del archivo inmediatamente
            if (!allowed.includes(ext)) {
                input.value = '';
                if (card) card.classList.add('hidden');
                if (label) label.textContent = 'Seleccionar o arrastrar archivo aquí';

                const extDisplay = ext ? ('.' + ext) : 'desconocido';
                const msg = `El archivo «${file.name}» posee un formato no permitido (${extDisplay}). Por favor seleccione un archivo Excel (.xlsx, .xls) o CSV (.csv) con el formato de la Plantilla Oficial.`;
                showClientImportAlert('error', 'Formato de Archivo No Compatible', msg);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato No Compatible',
                        text: msg,
                        confirmButtonColor: '#0d9488'
                    });
                }
                return;
            }

            // 2. Validar tamaño máximo (20 MB)
            const maxBytes = 20 * 1024 * 1024;
            if (file.size > maxBytes) {
                input.value = '';
                if (card) card.classList.add('hidden');
                const msg = `El archivo «${file.name}» (${formatBytes(file.size)}) supera el límite máximo permitido de 20 MB.`;
                showClientImportAlert('error', 'Archivo Demasiado Pesado', msg);
                return;
            }

            // 3. Mostrar tarjeta del archivo seleccionado
            if (label) {
                label.textContent = 'Cambiar archivo seleccionado';
                label.classList.add('text-teal-700');
            }
            if (card) {
                card.classList.remove('hidden');
                if (extBadge) extBadge.textContent = ext.toUpperCase();
                if (nameText) nameText.textContent = file.name;
                if (sizeText) sizeText.textContent = formatBytes(file.size);
            }

            // 4. Detectar si el usuario ya subió o seleccionó este mismo archivo recientemente (huella local)
            try {
                const fileFingerprint = `${file.name}|${file.size}|${file.lastModified}`;
                const historyRaw = localStorage.getItem('das_imported_files_history') || '{}';
                const history = JSON.parse(historyRaw);

                if (history[fileFingerprint]) {
                    showClientImportAlert(
                        'warning',
                        'Advertencia: Archivo Ya Utilizado Recientemente',
                        `El archivo «${file.name}» coincide exactamente con uno subido el ${history[fileFingerprint]}. Si vuelve a importarlo sin cambios, el sistema bloqueará los lotes duplicados.`
                    );
                    if (forceWrap) forceWrap.classList.remove('hidden');
                }
            } catch (e) {}
        }

        function handleImportSubmit(event) {
            const input = document.getElementById('excel');
            if (!input || !input.files || input.files.length === 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Archivo Requerido',
                    text: 'Debes seleccionar un archivo Excel (.xlsx, .xls) o CSV (.csv) antes de presionar Validar y Procesar.',
                    confirmButtonColor: '#0d9488'
                });
                return false;
            }

            const file = input.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Formato Inválido',
                    text: 'Solo se admiten archivos .xlsx, .xls o .csv.',
                    confirmButtonColor: '#0d9488'
                });
                return false;
            }

            event.preventDefault();

            Swal.fire({
                title: '¿Iniciar Carga Masiva?',
                html: `Se procesará el archivo <strong>${file.name}</strong> (${formatBytes(file.size)}).<br><span class="text-[12px] text-slate-500 mt-1 block">Esta operación actualizará las existencias en bodega y generará registros de trazabilidad.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, procesar archivo',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Registrar huella local del archivo enviado
                    try {
                        const fileFingerprint = `${file.name}|${file.size}|${file.lastModified}`;
                        const historyRaw = localStorage.getItem('das_imported_files_history') || '{}';
                        const history = JSON.parse(historyRaw);
                        const nowStr = new Date().toLocaleDateString('es-CL') + ' ' + new Date().toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
                        history[fileFingerprint] = nowStr;
                        localStorage.setItem('das_imported_files_history', JSON.stringify(history));
                    } catch (e) {}

                    // Activar Pantalla de Carga para archivos pesados
                    const overlay = document.getElementById('importLoadingOverlay');
                    const fileNameEl = document.getElementById('loadingFileName');
                    const fileSizeEl = document.getElementById('loadingFileSize');
                    const stepTextEl = document.getElementById('loadingStepText');
                    const stageLabelEl = document.getElementById('loadingStageLabel');
                    const percentLabelEl = document.getElementById('loadingPercentLabel');
                    const barEl = document.getElementById('importProgressBar');
                    const barTopEl = document.getElementById('importProgressBarTop');
                    const submitBtn = document.getElementById('importSubmitBtn');

                    if (fileNameEl) fileNameEl.textContent = file.name;
                    if (fileSizeEl) fileSizeEl.textContent = '(' + formatBytes(file.size) + ')';
                    if (overlay) overlay.classList.remove('hidden');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    }

                    const steps = [
                        { pct: 25, text: 'Verificando estructura y columnas de la Plantilla Oficial...', stage: 'Validando encabezados...' },
                        { pct: 55, text: 'Comprobando lotes y detectando registros duplicados...', stage: 'Analizando integridad...' },
                        { pct: 82, text: 'Registrando existencias y movimientos de trazabilidad en bodega...', stage: 'Guardando en base de datos...' },
                        { pct: 95, text: 'Finalizando transacción y actualizando catálogo...', stage: 'Completando operación...' }
                    ];

                    let currentStep = 0;
                    const interval = setInterval(() => {
                        if (currentStep >= steps.length) {
                            clearInterval(interval);
                            return;
                        }
                        const st = steps[currentStep];
                        if (barEl) barEl.style.width = st.pct + '%';
                        if (barTopEl) barTopEl.style.width = st.pct + '%';
                        if (percentLabelEl) percentLabelEl.textContent = st.pct + '%';
                        if (stepTextEl) stepTextEl.textContent = st.text;
                        if (stageLabelEl) stageLabelEl.textContent = st.stage;
                        currentStep++;
                    }, 650);

                    document.getElementById('importForm').submit();
                }
            });

            return false;
        }

        function toggleManualEstadoHelp(val) {
            const help = document.getElementById('manualEstadoHelpText');
            if (!help) return;
            if (val === 'Cuarentena') {
                help.classList.remove('hidden');
            } else {
                help.classList.add('hidden');
            }
        }

        function validateIngresoDirecto(event) {
            const form = document.getElementById('manualDepositForm');
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.reportValidity();
                    firstInvalid.focus();
                }
                return false;
            }

            event.preventDefault();

            const prodName = document.getElementById('manualProductSearchInput')?.value || 'Producto';
            const lote = document.getElementById('manualLoteInput')?.value || '';
            const cantidad = document.getElementById('manualCantidadInput')?.value || '0';
            const estado = document.getElementById('manualEstadoSelect')?.value || 'Disponible';
            const isCuar = estado === 'Cuarentena';
            const htmlMessage = isCuar
                ? `Se registrará el producto <strong>${prodName}</strong> (Lote: <strong>${lote}</strong>) con <strong>${cantidad}</strong> unidades.<br><span class="inline-block mt-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded">Atención: El lote ingresará inmovilizado preventivamente en Cuarentena para revisión técnica.</span>`
                : `Se registrará el producto <strong>${prodName}</strong> (Lote: <strong>${lote}</strong>) con <strong>${cantidad}</strong> unidades en estado <strong>${estado}</strong>.`;

            Swal.fire({
                title: isCuar ? '¿Ingresar Lote a Cuarentena?' : '¿Confirmar Ingreso Manual?',
                html: htmlMessage,
                icon: isCuar ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isCuar ? '#d97706' : '#0d9488',
                cancelButtonColor: '#64748b',
                confirmButtonText: isCuar ? 'Sí, ingresar a cuarentena' : 'Sí, registrar ingreso',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('manualDepositSubmitBtn');
                    const txt = document.getElementById('manualDepositSubmitText');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-70', 'cursor-wait');
                    }
                    if (txt) {
                        txt.textContent = 'Guardando Ingreso...';
                    }
                    form.submit();
                }
            });

            return false;
        }

        function confirmEditLoteInventario(event) {
            event.preventDefault();
            const form = document.getElementById('editLoteForm');
            const estado = document.getElementById('modal_estado')?.value;
            const motivo = (document.getElementById('modal_motivo')?.value || '').trim();
            const prod = document.getElementById('modal_producto_nombre')?.textContent || 'Producto';
            const lote = document.getElementById('modal_lote_nombre')?.textContent || '';

            if ((estado === 'Cuarentena' || estado === 'Merma') && !motivo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Debes indicar el motivo para cambiar el estado a ' + estado + '.',
                    confirmButtonColor: '#0d9488'
                });
                document.getElementById('modal_motivo')?.focus();
                return false;
            }

            if (estado === 'Cuarentena') {
                Swal.fire({
                    title: '¿Enviar lote a Cuarentena?',
                    html: `El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) quedará bloqueado preventivamente y pasará a control técnico en Cuarentena.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, enviar a cuarentena',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            } else if (estado === 'Merma') {
                Swal.fire({
                    title: '¿Declarar Merma de Lote?',
                    html: `El lote <strong>${lote}</strong> de <strong>${prod}</strong> será dado de baja definitiva como Merma y retirado del stock disponible.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, declarar merma',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            } else {
                Swal.fire({
                    title: '¿Cambiar estado a Disponible?',
                    html: `El lote <strong>${lote}</strong> de <strong>${prod}</strong> quedará liberado para entrega y despacho.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, guardar cambio',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            }

            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selEstado = document.getElementById('manualEstadoSelect');
            if (selEstado) {
                toggleManualEstadoHelp(selEstado.value);
            }

            @if (session('open_modal') === 'import')
                openImport();
            @elseif (session('open_modal') === 'deposit' || (isset($errors) && $errors->any()))
                openDeposit();
            @endif
        });
    </script>
@endsection
