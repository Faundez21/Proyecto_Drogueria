@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Visualización y exportación de datos consolidados.')

@section('actions')
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        <!-- Botón Exportar a Excel -->
        <button type="button" onclick="exportarReporte('excel')"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm hover:shadow transition-all duration-200 w-full sm:w-auto">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exportar a Excel
        </button>

        <!-- Botón Exportar a PDF -->
        <button type="button" onclick="exportarReporte('pdf')"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm hover:shadow transition-all duration-200 w-full sm:w-auto">
            <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Exportar a PDF
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">04</span>
            <span>Control</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-800">Reportes</span>
        </div>

        <!-- KPI de Valorización -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-teal-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Valor Total del Stock (según filtros)</p>
                    <p class="text-2xl font-extrabold text-slate-800 font-mono-das">$ {{ number_format($valorTotal, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">Valor neto: cantidad en stock × valor unitario de cada lote Disponible</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-center">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nota de valorización</p>
                <p class="text-[12px] text-slate-600 mt-1 leading-relaxed">Los lotes en <b class="text-amber-600">Cuarentena</b> o <b class="text-rose-600">Merma</b> no se incluyen en este reporte porque no constituyen stock disponible.</p>
            </div>
        </div>

        <!-- Contenedor principal tabla Inventario -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- Cabecera de la tabla y botón de filtros -->
            <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center">
                <h3 class="text-[15px] font-bold text-slate-800">Reporte Consolidado de Stock</h3>
                <button type="button" id="toggle-filters-btn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filtros Avanzados
                </button>
            </div>

            <!-- Formulario de Filtros -->
            <form method="GET" action="{{ route('reports.index') }}" id="filters-panel" class="hidden bg-slate-50/50 border-b border-slate-200 p-5 shadow-inner">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div class="xl:col-span-2">
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Producto, categoría o lote..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría</label>
                        <select name="categoria" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todas</option>
                            @foreach($categoriasUnicas ?? [] as $cat)
                                <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Pasillo</label>
                        <select name="pasillo" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach($pasillosUnicos as $pas)
                                <option value="{{ $pas }}" {{ request('pasillo') == $pas ? 'selected' : '' }}>{{ $pas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Estante</label>
                        <select name="estante" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach($estantesUnicos as $est)
                                <option value="{{ $est }}" {{ request('estante') == $est ? 'selected' : '' }}>{{ $est }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Programa</label>
                        <select name="programa" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach($programasUnicos as $prog)
                                <option value="{{ $prog }}" {{ request('programa') == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ordenar por</label>
                        <select name="order_by" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="recent" {{ request('order_by', 'recent') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                            <option value="nombre_asc" {{ request('order_by') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                            <option value="stock_desc" {{ request('order_by') == 'stock_desc' ? 'selected' : '' }}>Mayor Stock</option>
                            <option value="stock_asc" {{ request('order_by') == 'stock_asc' ? 'selected' : '' }}>Menor Stock</option>
                            <option value="vencimiento" {{ request('order_by') == 'vencimiento' ? 'selected' : '' }}>Próximos a Vencer</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                        Limpiar Filtros
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all">
                        Aplicar Filtros
                    </button>
                </div>
            </form>

            @if (request()->filled('pasillo') || request()->filled('estante') || request()->filled('categoria') || request()->filled('programa') || request()->filled('search'))
                <div class="px-5 py-3 bg-teal-50 border-b border-teal-200 flex justify-between items-center text-xs text-teal-800 font-bold">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filtro activo: <span class="underline">
                            {{ implode(' | ', array_filter([
                                request('search') ? 'Búsqueda: ' . request('search') : null,
                                request('categoria') ? 'Categoría: ' . request('categoria') : null,
                                request('pasillo') ? 'Pasillo: ' . request('pasillo') : null,
                                request('estante') ? 'Estante: ' . request('estante') : null,
                                request('programa') ? 'Programa: ' . request('programa') : null,
                            ])) }}
                        </span>
                    </span>
                    <a href="{{ route('reports.index') }}" class="px-3 py-1 bg-white border border-teal-300 rounded-lg text-teal-700 hover:bg-teal-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                        Limpiar Filtros
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto w-full">
                @php
                    $invRaw = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];
                    $invCollection = collect($invRaw);

                    $groupedInventories = $invCollection->groupBy(function ($item) {
                        $prod = is_object($item) ? $item->product ?? ($item->producto ?? null) : null;
                        if (is_object($prod) && isset($prod->nombre)) return $prod->nombre;
                        if (is_string($prod)) return $prod;
                        if (is_object($item) && isset($item->producto)) return is_object($item->producto) ? ($item->producto->nombre ?? 'Producto General') : $item->producto;
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
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Stock Físico Acumulado</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Valor Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-[13px]" id="mainTableBody">
                        @if ($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-[14px] font-medium text-slate-600">No se encontraron registros para reportes.</p>
                                    <p class="text-[13px] text-slate-400 mt-1">Intenta ajustando los filtros de búsqueda.</p>
                                </td>
                            </tr>
                        @else
                            @foreach ($groupedInventories as $nombreProducto => $lotes)
                                @php
                                    $stockTotalProducto = $lotes->sum(function ($i) {
                                        return is_object($i) ? $i->stock_total ?? ($i->cantidad ?? 0) : 0;
                                    });
                                    $valorTotalProducto = $lotes->sum(function ($i) {
                                        $obj = is_object($i) ? $i : (object) $i;
                                        return ((float) ($obj->cantidad ?? 0)) * ((float) ($obj->valor_neto_unitario ?? 0));
                                    });
                                    $groupKey = 'grp_' . md5($nombreProducto);
                                    $primerLote = $lotes->first();
                                    $categoriaLote = is_object($primerLote) ? $primerLote->categoria ?? 'General' : 'General';
                                @endphp

                                <tr id="product-row-{{ $groupKey }}"
                                    class="product-master-row hover:bg-slate-50 transition-colors group cursor-pointer"
                                    onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div
                                            class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-teal-500 transition-colors">
                                        </div>
                                        <button type="button"
                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-teal-200"
                                            id="icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300"
                                                id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800">{{ $nombreProducto }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-white text-slate-600 border border-slate-200 shadow-sm whitespace-nowrap">{{ $categoriaLote }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $programaProducto = is_object($primerLote) ? ($primerLote->programa ?? '') : ''; @endphp
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
                                </tr>

                                <tr id="lotes-{{ $groupKey }}" class="lotes-subrow hidden bg-slate-50/50 shadow-inner">
                                    <td colspan="7" class="p-0 border-b border-slate-200">
                                        <div class="py-4 pr-6 pl-14">
                                            <div
                                                class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
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
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700 text-[12px]">
                                                            @foreach ($lotes as $loteObj)
                                                                @php
                                                                    $loteData = is_object($loteObj)
                                                                        ? $loteObj
                                                                        : (object) $loteObj;
                                                                    $badgeVencimientoClass = 'text-slate-500';
                                                                    $textoVencimiento = 'N/A';

                                                                    if (!empty($loteData->fecha_vencimiento)) {
                                                                        try {
                                                                            $fVenc = \Carbon\Carbon::parse($loteData->fecha_vencimiento);
                                                                            $textoVencimiento = $fVenc->format('d/m/Y');
                                                                            if ($fVenc->isPast()) {
                                                                                $badgeVencimientoClass =
                                                                                    'text-rose-600 font-extrabold';
                                                                            } else {
                                                                                $diasRestantes = now()->diffInDays($fVenc, false);
                                                                                if ($diasRestantes > 90) $badgeVencimientoClass = 'text-emerald-600 font-bold';
                                                                                elseif ($diasRestantes >= 0) $badgeVencimientoClass = 'text-amber-600 font-bold';
                                                                                else $badgeVencimientoClass = 'text-rose-600 font-extrabold';
                                                                            }
                                                                        } catch (\Exception $e) {
                                                                            $textoVencimiento = $loteData->fecha_vencimiento;
                                                                        }
                                                                    }

                                                                    $pasilloVal =
                                                                        $loteData->pasillo_nombre ??
                                                                        (is_object($loteData->aisle ?? null)
                                                                            ? $loteData->aisle->nombre ??
                                                                                ($loteData->aisle->name ?? null)
                                                                            : $loteData->pasillo ?? 'P.A');
                                                                    $estanteVal =
                                                                        $loteData->estanteria_nombre ??
                                                                        (is_object($loteData->shelf ?? null)
                                                                            ? $loteData->shelf->nombre ??
                                                                                ($loteData->shelf->name ?? null)
                                                                            : $loteData->estante ??
                                                                                ($loteData->estanteria ?? 'P.A'));
                                                                    $stockLote =
                                                                        $loteData->stock_total ??
                                                                        ($loteData->cantidad ?? 0);
                                                                    $isCuarentena = isset($loteData->cuarentena)
                                                                        ? (bool) $loteData->cuarentena
                                                                        : strtolower($loteData->estado ?? '') ===
                                                                            'cuarentena';
                                                                    $loteCodigo = $loteData->lote ?? 'S/L';
                                                                @endphp
                                                                <tr class="lote-item-row hover:bg-slate-50 transition-colors">
                                                                    <td class="px-4 py-3">
                                                                        <span class="font-mono font-bold text-slate-800">{{ $loteCodigo }}</span><br>
                                                                        <span class="text-[10px] uppercase font-semibold {{ $badgeVencimientoClass }}">Vence: {{ $textoVencimiento }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex items-center gap-1.5 text-[11px]">
                                                                            <span
                                                                                class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span
                                                                                    class="text-slate-400">P:</span> <b
                                                                                    class="text-slate-800">{{ $pasilloVal }}</b></span>
                                                                            @can('ver.distribucion')
                                                                                @if (!empty($estanteVal) && $estanteVal !== 'P.A')
                                                                                    <a href="{{ route('distribution.index', ['estante' => $estanteVal]) }}" title="Ver estante {{ $estanteVal }} en el Mapa 3D" class="inline-flex items-center gap-1 bg-white hover:bg-teal-50 text-slate-600 hover:text-teal-700 px-2 py-1 rounded shadow-sm border border-slate-200 hover:border-teal-300 transition-colors">
                                                                                        <span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b>
                                                                                        <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                                @endif
                                                                            @else
                                                                                <span class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span class="text-slate-400">E:</span> <b class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                            @endcan
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
                                                                        <span class="text-[12px] font-semibold text-slate-600 font-mono-das">$ {{ number_format((float) ($loteData->valor_neto_unitario ?? 0), 2, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[12px] font-bold bg-teal-50 text-teal-800 border border-teal-200 font-mono-das">$ {{ number_format(((float) $stockLote) * ((float) ($loteData->valor_neto_unitario ?? 0)), 0, ',', '.') }}</span>
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
            @if(isset($inventories) && method_exists($inventories, 'hasPages') && $inventories->hasPages())
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white rounded-b-2xl">
                <div class="text-[13px] text-slate-500 text-center sm:text-left font-medium">
                    Mostrando del <span class="font-bold text-slate-800">{{ $inventories->firstItem() ?? 0 }}</span> al <span class="font-bold text-slate-800">{{ $inventories->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $inventories->total() }}</span> registros
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

                    @if($start > 1)
                        <a href="{{ $inventories->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">1</a>
                        @if($start > 2)
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

                    @if($end < $inventories->lastPage())
                        @if($end < $inventories->lastPage() - 1)
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Lógica visual para mostrar filtros si hay búsquedas activas
            const btnToggleFilters = document.getElementById('toggle-filters-btn');
            const filtersPanel = document.getElementById('filters-panel');

            if(window.location.search.includes('categoria') || window.location.search.includes('pasillo') || window.location.search.includes('estante') || window.location.search.includes('programa') || window.location.search.includes('order_by') || window.location.search.includes('search')) {
                if(filtersPanel) filtersPanel.classList.remove('hidden');
            }

            if(btnToggleFilters && filtersPanel) {
                btnToggleFilters.addEventListener('click', () => {
                    filtersPanel.classList.toggle('hidden');
                });
            }
        });

        // Lógica visual para el acordeón de Lotes
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

        // Función que atrapa los filtros actuales para enviarlos a Excel/PDF
        function exportarReporte(tipo) {
            const urlExcel = "{{ route('reports.excel') }}";
            const urlPdf = "{{ route('reports.pdf') }}";

            let targetUrl = new URL(tipo === 'excel' ? urlExcel : urlPdf, window.location.origin);
            const form = document.getElementById('filters-panel');

            if (form) {
                const formData = new FormData(form);
                for (const [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        targetUrl.searchParams.append(key, value);
                    }
                }
            }

            window.location.href = targetUrl.toString();
        }
    </script>
@endsection
