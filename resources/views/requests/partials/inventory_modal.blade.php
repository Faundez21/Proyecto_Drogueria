{{-- Modal: Inventario de Solicitudes (Establecimientos o CESFAM), independiente del Inventario de Droguería --}}
@role('Administrador')
    <div id="RequestInventoryModal"
        class="hidden fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all"
        onclick="if(event.target === this) closeRequestInventoryModal()">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-7xl max-h-[92vh] flex flex-col overflow-hidden my-auto">

            <!-- Cabecera Oscura del Modal -->
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center shrink-0">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-wide">
                        {{ $modalTitle ?? 'Inventario de Solicitudes' }}
                    </h2>
                    <p class="text-[12px] text-slate-300 mt-0.5 font-medium">
                        {{ $modalSubtitle ?? 'Directorio independiente de stock y productos registrados en las solicitudes.' }}
                    </p>
                </div>
                <button type="button" onclick="closeRequestInventoryModal()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Barra de Directorio y Botón de Filtros Avanzados (igual a vista Inventario) -->
            <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center shrink-0">
                <h3 class="text-[15px] font-bold text-slate-800">
                    Directorio de Productos ({{ $esVistaCesfam ? 'CESFAM' : 'Establecimientos' }})
                </h3>
                <button type="button" id="toggle-req-filters-btn" onclick="toggleReqFiltersPanel()"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtros Avanzados
                </button>
            </div>

            <!-- Panel de Filtros Avanzados -->
            <div id="req-filters-panel" class="hidden bg-slate-50/50 border-b border-slate-200 p-5 shadow-inner shrink-0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

                    <!-- Búsqueda con Dropdown -->
                    <div class="xl:col-span-2 relative" id="reqSearchContainer">
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Buscar</label>
                        <input type="text" id="reqFilterSearch"
                            placeholder="Producto, establecimiento o responsable..." autocomplete="off"
                            onfocus="showReqSearchDropdown()" oninput="filterReqSearchDropdown(); applyReqInventoryFilters()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">

                        <div id="reqSearchDropdown"
                            class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-56 overflow-y-auto divide-y divide-slate-100">
                            @if (isset($productosLista) && count($productosLista) > 0)
                                @foreach ($productosLista as $prodNombre)
                                    <div class="req-search-item px-3.5 py-2.5 text-[12.5px] font-medium text-slate-700 hover:bg-teal-50 hover:text-teal-900 cursor-pointer transition-colors flex items-center justify-between"
                                        onclick="selectReqSearchProduct('{{ addslashes($prodNombre) }}')">
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

                    <!-- Establecimiento / CESFAM -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            {{ $esVistaCesfam ? 'CESFAM' : 'Establecimiento' }}
                        </label>
                        <select id="reqFilterEstablecimiento" onchange="applyReqInventoryFilters()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach ($establecimientos as $est)
                                <option value="{{ strtolower(trim($est['nombre'])) }}">{{ $est['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría</label>
                        <select id="reqFilterCategoria" onchange="applyReqInventoryFilters()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todas</option>
                            <option value="medicamentos e insumos">Medicamentos e Insumos</option>
                            @if ($esVistaCesfam)
                                <option value="estupefacientes">Estupefacientes</option>
                            @endif
                        </select>
                    </div>

                    <!-- Programa -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Programa</label>
                        <select id="reqFilterPrograma" onchange="applyReqInventoryFilters()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            @foreach ($programasLista ?? [] as $prog)
                                <option value="{{ mb_strtolower(trim($prog)) }}">{{ $prog }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ordenar por -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ordenar por</label>
                        <select id="reqFilterOrder" onchange="applyReqInventoryFilters()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="recent">Más recientes</option>
                            <option value="nombre_asc">Nombre (A-Z)</option>
                            <option value="stock_desc">Mayor Stock</option>
                            <option value="stock_asc">Menor Stock</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                    <button type="button" onclick="clearReqInventoryFilters()"
                        class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors cursor-pointer">
                        Limpiar Filtros
                    </button>
                    <button type="button" onclick="applyReqInventoryFilters()"
                        class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all cursor-pointer">
                        Aplicar Filtros
                    </button>
                </div>
            </div>

            <!-- Alerta Verde de Filtros Activos -->
            <div id="reqActiveFilterAlert"
                class="hidden px-5 py-3 bg-teal-50 border-b border-teal-200 flex justify-between items-center text-xs text-teal-800 font-bold shrink-0">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Filtro activo: <span id="reqFilteredProductName" class="underline"></span>
                </span>
                <button type="button" onclick="clearReqInventoryFilters()"
                    class="px-3 py-1 bg-white border border-teal-300 rounded-lg text-teal-700 hover:bg-teal-100 transition-colors shadow-sm cursor-pointer text-[11px]">
                    Limpiar Filtros
                </button>
            </div>

            <!-- Cuerpo de la Tabla -->
            <div class="overflow-y-auto overflow-x-auto flex-1 bg-white">
                @php
                    $groupedReqInventories = collect($inventarioSolicitudes ?? [])->groupBy(function ($item) {
                        return $item->product->nombre ?? 'Producto General';
                    });
                @endphp

                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead class="sticky top-0 z-10">
                        <tr class="text-[12px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                            <th scope="col" class="px-6 py-4 w-14 text-center whitespace-nowrap">Detalle</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Producto Principal</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Categoría</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Programa</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Registros</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Stock en {{ $esVistaCesfam ? 'CESFAM' : 'Establecimientos' }}</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Total Solicitado</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Total Despachado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-[13px]" id="reqMainTableBody">
                        @if ($groupedReqInventories->isEmpty())
                            <tr id="reqEmptyRow">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-[14px] font-medium text-slate-600">Sin registros de inventario en solicitudes</p>
                                    <p class="text-[13px] text-slate-400 mt-1">
                                        Aún no se han registrado solicitudes para {{ $esVistaCesfam ? 'CESFAM' : 'establecimientos' }}.
                                    </p>
                                </td>
                            </tr>
                        @else
                            @foreach ($groupedReqInventories as $nombreProducto => $itemsGrupo)
                                @php
                                    $groupKey = 'req_grp_' . md5($nombreProducto);
                                    $esControlado = in_array($nombreProducto, $controlados ?? []);
                                    $categoriaProducto = $esControlado ? 'Estupefacientes' : 'Medicamentos e Insumos';
                                    $primerItem = $itemsGrupo->first();
                                    $programaPrincipal = $itemsGrupo->pluck('programa')->filter()->first() ?? '';

                                    // Para el stock actual de cada establecimiento tomamos el último reportado por establecimiento
                                    $stockActualTotal = $itemsGrupo
                                        ->groupBy(fn ($it) => $it->request->establecimiento ?? 'General')
                                        ->sum(fn ($porEst) => (int) ($porEst->first()->stock_actual ?? 0));

                                    $totalSolicitado = (int) $itemsGrupo->sum('cantidad_solicitada');
                                    $totalDespachado = (int) $itemsGrupo->sum('cantidad_despachada');
                                    $maxIdGrupo = (int) $itemsGrupo->max('id');
                                @endphp

                                <tr id="req-product-row-{{ $groupKey }}"
                                    data-group-key="{{ $groupKey }}"
                                    data-product-name="{{ strtolower(trim($nombreProducto)) }}"
                                    data-categoria="{{ strtolower($categoriaProducto) }}"
                                    data-programa="{{ strtolower(trim($programaPrincipal)) }}"
                                    data-stock="{{ $stockActualTotal }}"
                                    data-recent="{{ $maxIdGrupo }}"
                                    class="req-product-master-row hover:bg-slate-50 transition-colors group cursor-pointer"
                                    onclick="toggleReqLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-teal-500 transition-colors"></div>
                                        <button type="button"
                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-teal-200"
                                            id="req-icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300"
                                                id="req-arrow-{{ $groupKey }}" fill="none" stroke="currentColor"
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
                                        @if ($esControlado)
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 shadow-sm whitespace-nowrap">
                                                Estupefacientes
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-white text-slate-600 border border-slate-200 shadow-sm whitespace-nowrap">
                                                Medicamentos e Insumos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($programaPrincipal)
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm whitespace-nowrap">
                                                {{ $programaPrincipal }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-medium bg-slate-50 text-slate-400 border border-slate-100 shadow-sm whitespace-nowrap">
                                                Sin programa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded req-count-badge">{{ $itemsGrupo->count() }}</span>
                                        <span class="text-slate-400 text-[11.5px] font-semibold uppercase ml-1">Solicitud(es)</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($stockActualTotal > 10)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="req-stock-text">{{ number_format($stockActualTotal, 0, ',', '.') }}</span> Unidades
                                            </span>
                                        @elseif ($stockActualTotal > 0)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span class="req-stock-text">{{ number_format($stockActualTotal, 0, ',', '.') }}</span> Unid. (Bajo)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                <span class="req-stock-text">0</span> Unidades
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[12.5px] font-bold bg-slate-50 text-slate-800 border border-slate-200 shadow-sm whitespace-nowrap font-mono-das req-solicitado-text">
                                            {{ number_format($totalSolicitado, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[12.5px] font-bold bg-teal-50 text-teal-800 border border-teal-200 shadow-sm whitespace-nowrap font-mono-das req-despachado-text">
                                            {{ number_format($totalDespachado, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>

                                <tr id="req-lotes-{{ $groupKey }}" class="req-lotes-subrow hidden bg-slate-50/50 shadow-inner">
                                    <td colspan="8" class="p-0 border-b border-slate-200">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                            </path>
                                                        </svg>
                                                        Desglose de Inventario por {{ $esVistaCesfam ? 'CESFAM' : 'Establecimiento' }}
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left whitespace-nowrap">
                                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                                                            <tr>
                                                                <th class="px-4 py-3">{{ $esVistaCesfam ? 'CESFAM' : 'Establecimiento' }}</th>
                                                                <th class="px-4 py-3">Fecha Solicitud</th>
                                                                <th class="px-4 py-3">Responsable</th>
                                                                <th class="px-4 py-3">Programa</th>
                                                                <th class="px-4 py-3 text-center">Stock Actual</th>
                                                                <th class="px-4 py-3 text-center">Cant. Solicitada</th>
                                                                <th class="px-4 py-3 text-center">Cant. Despachada</th>
                                                                <th class="px-4 py-3 text-center">Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-700 text-[12px]">
                                                            @foreach ($itemsGrupo as $reqItem)
                                                                @php
                                                                    $estNombre = $reqItem->request->establecimiento ?? 'N/A';
                                                                    $fechaSol = $reqItem->request->fecha_solicitud
                                                                        ? \Carbon\Carbon::parse($reqItem->request->fecha_solicitud)->format('d/m/Y')
                                                                        : 'N/A';
                                                                    $responsableSol = $reqItem->request->responsable ?? 'N/A';
                                                                    $cargoSol = $reqItem->request->cargo ?? '';
                                                                    $progItem = $reqItem->programa ?? 'Sin programa';
                                                                    $estadoSol = $reqItem->request->estado ?? 'Pendiente';
                                                                @endphp
                                                                <tr class="req-item-row hover:bg-slate-50 transition-colors"
                                                                    data-establecimiento="{{ strtolower(trim($estNombre)) }}"
                                                                    data-responsable="{{ strtolower(trim($responsableSol)) }}"
                                                                    data-programa="{{ strtolower(trim($reqItem->programa ?? '')) }}"
                                                                    data-stock="{{ (int) ($reqItem->stock_actual ?? 0) }}"
                                                                    data-solicitado="{{ (int) ($reqItem->cantidad_solicitada ?? 0) }}"
                                                                    data-despachado="{{ (int) ($reqItem->cantidad_despachada ?? 0) }}">
                                                                    <td class="px-4 py-3 font-bold text-slate-800">
                                                                        {{ $estNombre }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-slate-600 font-medium">
                                                                        {{ $fechaSol }}
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <span class="font-semibold text-slate-700">{{ $responsableSol }}</span>
                                                                        @if ($cargoSol)
                                                                            <span class="block text-[10px] text-slate-400">{{ $cargoSol }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        @if ($reqItem->programa)
                                                                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded shadow-sm border border-indigo-100 font-semibold text-[11px]">
                                                                                {{ $reqItem->programa }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-slate-400 text-[11px]">Sin programa</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[13px]">
                                                                            {{ number_format((int) ($reqItem->stock_actual ?? 0), 0, ',', '.') }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-bold text-slate-700 font-mono-das">
                                                                            {{ number_format((int) ($reqItem->cantidad_solicitada ?? 0), 0, ',', '.') }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-bold text-teal-700 font-mono-das">
                                                                            {{ number_format((int) ($reqItem->cantidad_despachada ?? 0), 0, ',', '.') }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        @if ($estadoSol === 'Despachado')
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">DESPACHADO</span>
                                                                        @elseif ($estadoSol === 'Parcial')
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">PARCIAL</span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">PENDIENTE</span>
                                                                        @endif
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

            <!-- Pie del Modal -->
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end shrink-0">
                <button type="button" onclick="closeRequestInventoryModal()"
                    class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                    Cerrar Inventario
                </button>
            </div>
        </div>
    </div>

    <script>
        function openRequestInventoryModal() {
            document.getElementById('RequestInventoryModal').classList.remove('hidden');
        }

        function closeRequestInventoryModal() {
            document.getElementById('RequestInventoryModal').classList.add('hidden');
        }

        function toggleReqFiltersPanel() {
            const panel = document.getElementById('req-filters-panel');
            if (panel) panel.classList.toggle('hidden');
        }

        function toggleReqLotes(groupKey) {
            const subrow = document.getElementById('req-lotes-' + groupKey);
            const arrow = document.getElementById('req-arrow-' + groupKey);
            if (!subrow) return;

            subrow.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        }

        function showReqSearchDropdown() {
            const dd = document.getElementById('reqSearchDropdown');
            if (dd) dd.classList.remove('hidden');
        }

        function filterReqSearchDropdown() {
            const val = (document.getElementById('reqFilterSearch')?.value || '').toLowerCase().trim();
            document.querySelectorAll('.req-search-item').forEach(item => {
                const txt = item.textContent.toLowerCase();
                item.style.display = txt.includes(val) ? '' : 'none';
            });
        }

        function selectReqSearchProduct(prodName) {
            const input = document.getElementById('reqFilterSearch');
            if (input) input.value = prodName;
            const dd = document.getElementById('reqSearchDropdown');
            if (dd) dd.classList.add('hidden');
            applyReqInventoryFilters();
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('reqSearchContainer');
            const dd = document.getElementById('reqSearchDropdown');
            if (container && dd && !container.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        function applyReqInventoryFilters() {
            const search = (document.getElementById('reqFilterSearch')?.value || '').toLowerCase().trim();
            const est = (document.getElementById('reqFilterEstablecimiento')?.value || '').toLowerCase().trim();
            const cat = (document.getElementById('reqFilterCategoria')?.value || '').toLowerCase().trim();
            const prog = (document.getElementById('reqFilterPrograma')?.value || '').toLowerCase().trim();
            const order = document.getElementById('reqFilterOrder')?.value || 'recent';

            const tbody = document.getElementById('reqMainTableBody');
            if (!tbody) return;

            const masterRows = Array.from(tbody.querySelectorAll('.req-product-master-row'));

            masterRows.forEach(row => {
                const groupKey = row.dataset.groupKey;
                const prodName = row.dataset.productName || '';
                const prodCat = row.dataset.categoria || '';
                const subrow = document.getElementById('req-lotes-' + groupKey);
                const itemRows = subrow ? Array.from(subrow.querySelectorAll('.req-item-row')) : [];

                let matchingItems = 0;

                itemRows.forEach(itemRow => {
                    const itemEst = itemRow.dataset.establecimiento || '';
                    const itemResp = itemRow.dataset.responsable || '';
                    const itemProg = itemRow.dataset.programa || '';

                    const matchSearch = !search || prodName.includes(search) || itemEst.includes(search) || itemResp.includes(search);
                    const matchEst = !est || itemEst === est;
                    const matchCat = !cat || prodCat === cat;
                    const matchProg = !prog || itemProg === prog;

                    if (matchSearch && matchEst && matchCat && matchProg) {
                        itemRow.style.display = '';
                        matchingItems++;
                    } else {
                        itemRow.style.display = 'none';
                    }
                });

                if (matchingItems > 0) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                    if (subrow) subrow.classList.add('hidden');
                }
            });

            // Ordenamiento
            masterRows.sort((a, b) => {
                if (order === 'nombre_asc') {
                    return (a.dataset.productName || '').localeCompare(b.dataset.productName || '');
                }
                if (order === 'stock_desc') {
                    return (parseInt(b.dataset.stock) || 0) - (parseInt(a.dataset.stock) || 0);
                }
                if (order === 'stock_asc') {
                    return (parseInt(a.dataset.stock) || 0) - (parseInt(b.dataset.stock) || 0);
                }
                return (parseInt(b.dataset.recent) || 0) - (parseInt(a.dataset.recent) || 0);
            });

            masterRows.forEach(row => {
                const groupKey = row.dataset.groupKey;
                const subrow = document.getElementById('req-lotes-' + groupKey);
                tbody.appendChild(row);
                if (subrow) tbody.appendChild(subrow);
            });

            // Actualizar alerta de filtro activo
            const activeLabels = [];
            if (search) activeLabels.push('Búsqueda: ' + document.getElementById('reqFilterSearch').value);
            if (est) {
                const sel = document.getElementById('reqFilterEstablecimiento');
                activeLabels.push('Establecimiento: ' + sel.options[sel.selectedIndex].text);
            }
            if (cat) {
                const sel = document.getElementById('reqFilterCategoria');
                activeLabels.push('Categoría: ' + sel.options[sel.selectedIndex].text);
            }
            if (prog) {
                const sel = document.getElementById('reqFilterPrograma');
                activeLabels.push('Programa: ' + sel.options[sel.selectedIndex].text);
            }

            const alertBox = document.getElementById('reqActiveFilterAlert');
            const alertText = document.getElementById('reqFilteredProductName');
            if (alertBox && alertText) {
                if (activeLabels.length > 0) {
                    alertText.textContent = activeLabels.join(' | ');
                    alertBox.classList.remove('hidden');
                } else {
                    alertBox.classList.add('hidden');
                }
            }
        }

        function clearReqInventoryFilters() {
            if (document.getElementById('reqFilterSearch')) document.getElementById('reqFilterSearch').value = '';
            if (document.getElementById('reqFilterEstablecimiento')) document.getElementById('reqFilterEstablecimiento').value = '';
            if (document.getElementById('reqFilterCategoria')) document.getElementById('reqFilterCategoria').value = '';
            if (document.getElementById('reqFilterPrograma')) document.getElementById('reqFilterPrograma').value = '';
            if (document.getElementById('reqFilterOrder')) document.getElementById('reqFilterOrder').value = 'recent';
            applyReqInventoryFilters();
        }
    </script>
@endrole
