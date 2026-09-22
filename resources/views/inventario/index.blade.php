@extends('layouts.app')

@section('content')
    <div class="w-full p-4 sm:p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
                <p class="text-gray-500 mt-1">
                    Control de productos y detalle de lotes
                </p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="button" onclick="openHistory()"
                    class="w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 flex items-center justify-center gap-2 transition-colors shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Ver Historial
                </button>
            </div>
        </div>

        <!-- Modal de Historial -->
        <div id="HistoryModal"
            class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 w-full max-w-4xl overflow-hidden">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Historial de Movimientos de Inventario</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Registro de auditoría de recepciones e importaciones.</p>
                    </div>
                    <button type="button" onclick="closeHistory()"
                        class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-96 border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Fecha Recepción</th>
                                <th class="px-4 py-3 font-semibold">Documento / Correlativo</th>
                                <th class="px-4 py-3 font-semibold">Proveedor</th>
                                <th class="px-4 py-3 font-semibold">Producto</th>
                                <th class="px-4 py-3 font-semibold text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventories ?? [] as $item)
                                @php $itemObj = is_object($item) ? $item : (object) $item; @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 text-slate-500">{{ $itemObj->fecha_recepcion ?? '' }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $itemObj->tipo_documento ?? '' }} #{{ $itemObj->numero_documento ?? '' }}
                                        @if(!empty($itemObj->correlativo_interno))
                                            <span class="text-xs text-slate-400 block">Corr: {{ $itemObj->correlativo_interno }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $itemObj->razon_social ?? '' }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $itemObj->producto ?? '' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-emerald-600">+{{ $itemObj->cantidad ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-100">
                    <span class="text-xs text-slate-400">Mostrando registros recientes</span>
                    <button type="button" onclick="closeHistory()"
                        class="px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-colors text-sm font-medium shadow-sm cursor-pointer">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <button type="button" onclick="openImport()"
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors text-sm font-medium shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Importar Carga Masiva
                </button>

                <button type="button" onclick="openDeposit()"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ingresar Producto Individual
                </button>
            </div>
        </div>

        <!-- Tabla Inventario Agrupada -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex flex-col lg:flex-row justify-between items-center gap-4">
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 text-left">
                    Catálogo
                </h3>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <form method="GET" action="{{ route('inventario.index') }}" class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por producto, lote o proveedor..."
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-slate-700">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                @php
                    // Convertimos a colección en caso de ser un paginador o array y agrupamos
                    $invCollection = collect(isset($inventories) && method_exists($inventories, 'items') ? $inventories->items() : ($inventories ?? []));
                    $groupedInventories = $invCollection->groupBy('producto');
                @endphp

                <table class="min-w-[1000px] w-full text-left text-sm font-light text-gray-800 whitespace-nowrap">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-4 w-12 text-center"></th>
                            <th scope="col" class="px-4 py-4">Producto</th>
                            <th scope="col" class="px-4 py-4">Categoría</th>
                            <th scope="col" class="px-4 py-4 text-center">N° de Lotes Activos</th>
                            <th scope="col" class="px-4 py-4 text-center">Stock Total Acumulado</th>
                            <th scope="col" class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($groupedInventories as $nombreProducto => $lotes)
                            @php
                                $stockTotalProducto = $lotes->sum('cantidad');
                                $groupKey = Str::slug($nombreProducto ?: 'producto-sin-nombre');
                                $primerLote = is_object($lotes->first()) ? $lotes->first() : (object) ($lotes->first() ?? []);
                            @endphp

                        <tr class="border-b border-neutral-200 bg-red-50/40 hover:bg-red-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-red-800">1042</td>
                            <td class="px-4 py-3 font-bold text-red-900">ERITROMICINA 500 MG CM REC.</td>
                            <td class="px-4 py-3 text-red-800">Fármacos</td>
                            <td class="px-4 py-3 text-red-800 font-medium">Pasillo A</td>
                            <td class="px-4 py-3 text-red-800">Estante 3</td>
                            <td class="px-4 py-3 text-center text-red-800 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    15 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-neutral-200 bg-white hover:bg-gray-50 transition-colors opacity-75">
                            <td class="px-4 py-3 font-medium text-gray-500">2055</td>
                            <td class="px-4 py-3 font-bold text-gray-600">AGUJA 21 G X 1,5 DESECHABLE</td>
                            <td class="px-4 py-3 text-gray-500">Insumos</td>
                            <td class="px-4 py-3 text-gray-500 font-medium">Pasillo B</td>
                            <td class="px-4 py-3 text-gray-500">Estante 2</td>
                            <td class="px-4 py-3 text-center text-gray-500 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300">
                                    0 Unidades (Agotado)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">


                                <!--botón ver (QR)-->
                                <button onclick="openQRModal()"
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>

                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <!-- Botón Desactivar -->
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-neutral-200 bg-orange-50 hover:bg-orange-100 transition-colors">
                            <td class="px-4 py-3 font-medium text-orange-800">1088</td>
                            <td class="px-4 py-3 font-bold text-orange-900">IBUPROFENO 400MG</td>
                            <td class="px-4 py-3 text-orange-800">Fármacos</td>
                            <td class="px-4 py-3 text-orange-800 font-medium">Pasillo C</td>
                            <td class="px-4 py-3 text-orange-800">Estante 1</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-200 text-orange-800 border border-orange-300">
                                    Sí
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-gray-800 border border-gray-300">
                                    320 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                            <!-- Fila Principal del Producto -->
                            <tr class="bg-white hover:bg-slate-50/80 transition-colors cursor-pointer select-none" onclick="toggleLotes('{{ $groupKey }}')">
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-slate-400 hover:text-blue-600 transition-transform duration-200" id="icon-{{ $groupKey }}">
                                        <svg class="w-5 h-5 transform transition-transform" id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800 text-base">
                                    {{ $nombreProducto ?: 'Sin Nombre' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-medium">
                                    {{ $primerLote->categoria ?? 'General' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $lotes->count() }} lote(s)
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($stockTotalProducto > 10)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                        </span>
                                    @elseif($stockTotalProducto > 0)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades (Stock Bajo)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            0 Unidades (Agotado)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                    <button onclick="toggleLotes('{{ $groupKey }}')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors">
                                        Ver Lotes
                                    </button>
                                </td>
                            </tr>

                            <!-- Subtabla Desplegable de Lotes -->
                            <tr id="lotes-{{ $groupKey }}" class="hidden bg-slate-50/60 border-t border-b border-slate-200/80">
                                <td colspan="6" class="p-4 pl-12">
                                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                        <div class="px-4 py-2.5 bg-slate-100/70 border-b border-slate-200 flex justify-between items-center">
                                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Desglose de Lotes / Recepciones de {{ $nombreProducto }}</span>
                                        </div>
                                        <table class="w-full text-left text-xs whitespace-nowrap">
                                            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                                                <tr>
                                                    <th class="px-4 py-2.5">N° Correlativo</th>
                                                    <th class="px-4 py-2.5">Lote / Vencimiento</th>
                                                    <th class="px-4 py-2.5">Proveedor / Documento</th>
                                                    <th class="px-4 py-2.5">Ubicación</th>
                                                    <th class="px-4 py-2.5 text-right">Valor Neto Unit.</th>
                                                    <th class="px-4 py-2.5 text-center">Stock del Lote</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                                @foreach($lotes as $lote)
                                                    @php
                                                        $loteObj = is_object($lote) ? $lote : (object) $lote;
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                                        <td class="px-4 py-2.5 font-bold text-teal-700">
                                                            #{{ $loteObj->correlativo_interno ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium">
                                                            <span class="text-slate-800 font-bold">Lote: {{ $loteObj->lote ?? 'S/L' }}</span> <br>
                                                            <span class="text-slate-400">Vence: {{ $loteObj->fecha_vencimiento ?? 'N/A' }}</span>
                                                        </td>
                                                        <td class="px-4 py-2.5">
                                                            <span class="font-semibold text-slate-800">{{ $loteObj->razon_social ?? 'S/N' }}</span> <br>
                                                            <span class="text-slate-400">{{ $loteObj->tipo_documento ?? '' }}: {{ $loteObj->numero_documento ?? '' }}</span>
                                                        </td>
                                                        <td class="px-4 py-2.5 text-slate-500">
                                                            {{ $loteObj->pasillo ?? 'Sin pasillo' }} / {{ $loteObj->estante ?? 'Sin estante' }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-800">
                                                            ${{ number_format($loteObj->valor_neto_unitario ?? 0, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                                                {{ number_format($loteObj->cantidad ?? 0, 0, ',', '.') }} Unid.
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">No hay registros de productos en el inventario.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
                @if(isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>


        <!-- Modal QR -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 border-b border-blue-100 px-6 py-4 flex items-center justify-between">

                    <div>
                        <h2 class="text-xl text-white font-bold text-gray-800">
                            Código QR del lote
                        </h2>

                        <p class="text-sm text-white mt-1">
                            Información del producto
                        </p>
                    </div>

                    <button type="button" onclick="closeQRModal()"
                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        title="Cerrar">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>

                    </button>

                </div>


                <!-- Contenido -->
                <div class="p-6">

                    <!-- QR -->
                    <div class="flex justify-center mb-6">

                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">

                            <div id="qrcode" class="w-48 h-48 flex items-center justify-center">
                            </div>

                        </div>

                    </div>


                    <!-- Información del producto -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">

                        <div class="bg-slate-50 px-4 py-3 border-b border-gray-200">

                            <h3 class="text-sm font-semibold text-slate-700">
                                Información del lote
                            </h3>

                        </div>


                        <div class="divide-y divide-gray-100">

                            <!-- ID -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    ID
                                </span>

                                <span id="qrId" class="text-sm font-medium text-gray-800">
                                    2055
                                </span>

                            </div>


                            <!-- Producto -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    Producto
                                </span>

                                <span id="qrProducto" class="text-sm font-medium text-gray-800 text-right">
                                    AGUJA 21 G X 1,5 DESECHABLE
                                </span>

                            </div>


                            <!-- Lote -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    Lote
                                </span>

                                <span id="qrLote" class="text-sm font-medium text-gray-800">
                                    L-2026-001
                                </span>

                            </div>


                            <!-- Vencimiento -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    Vencimiento
                                </span>

                                <span id="qrVencimiento" class="text-sm font-medium text-gray-800">
                                    12/2027
                                </span>

                            </div>


                            <!-- Stock -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    Stock disponible
                                </span>

                                <span id="qrStock" class="text-sm font-medium text-emerald-600">
                                    850 Unidades
                                </span>

                            </div>


                            <!-- Proveedor -->
                            <div class="flex justify-between gap-4 px-4 py-3">

                                <span class="text-sm text-gray-500">
                                    Proveedor
                                </span>

                                <span id="qrUbicacion" class="text-sm font-medium text-gray-800 text-right">
                                    Laboratorios Andinos S.A
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-gray-200 flex justify-end">

                    <button type="button" onclick="closeQRModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-medium transition-colors">

                        Cerrar

                    </button>

                </div>

            </div>

        </div>


        <!-- Librería para generar QR -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>


        <script>
            function openQRModal() {

                const modal = document.getElementById('qrModal');

                modal.classList.remove('hidden');
                modal.classList.add('flex');


                // Limpiar QR anterior
                const qrContainer = document.getElementById('qrcode');

                qrContainer.innerHTML = '';


                // Datos del lote
                const id = document.getElementById('qrId').textContent.trim();


                // Generar QR
                new QRCode(qrContainer, {
                    text: 'LOTE-' + id,
                    width: 192,
                    height: 192,
                    correctLevel: QRCode.CorrectLevel.H
                });

            }


            function closeQRModal() {

                const modal = document.getElementById('qrModal');

                modal.classList.add('hidden');
                modal.classList.remove('flex');

            }
        </script>

    </div>

    <!-- Modal Importar -->
    <div id="importModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between w-full max-w-md">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Importar Carga Masiva</h2>
                <p class="text-sm text-slate-500 mt-1 mb-5">Suba un archivo Excel (.xlsx o .csv) para registrar recepciones masivas.</p>

                <a href="#"
                    class="w-full mb-6 flex justify-center items-center gap-2 bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm shadow-sm cursor-pointer">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Descargar Plantilla
                </a>
            </div>

            <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg p-6 flex-grow flex flex-col justify-center items-center">
                <form action="{{ route('inventario.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center gap-4 w-full">
                    @csrf
                    <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx, .xls, .csv">

                    <label for="excel"
                        class="w-full sm:w-64 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 px-4 py-2.5 rounded-lg cursor-pointer inline-flex items-center gap-2 text-sm font-medium transition-colors shadow-sm justify-center mb-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                            </path>
                        </svg>
                        Seleccionar archivo
                    </label>

                    <button type="submit"
                        class="w-full sm:w-64 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium shadow-sm cursor-pointer">
                        Procesar Importación
                    </button>
                </form>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeImport()"
                    class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors text-sm font-medium shadow-sm cursor-pointer">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Ingreso Manual -->
    <div id="DepositModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Ingresar Producto / Recepción Manual</h2>
                <p class="text-sm text-slate-500 mt-1 mb-6">Registra un nuevo producto con su correspondiente documento de origen.</p>
            </div>

            <form action="{{ route('inventario.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="correlativo_interno" class="block text-xs font-medium text-slate-700 mb-1">N° Correlativo Interno</label>
                        <input type="number" id="correlativo_interno" name="correlativo_interno" placeholder="Ej. 1001"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="fecha_recepcion" class="block text-xs font-medium text-slate-700 mb-1">Fecha Recepción</label>
                        <input type="date" id="fecha_recepcion" name="fecha_recepcion" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="tipo_documento" class="block text-xs font-medium text-slate-700 mb-1">Tipo Documento</label>
                        <select id="tipo_documento" name="tipo_documento" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Factura">Factura</option>
                            <option value="Guía de Despacho">Guía de Despacho</option>
                            <option value="Orden de Compra">Orden de Compra</option>
                        </select>
                    </div>

                    <div>
                        <label for="numero_documento" class="block text-xs font-medium text-slate-700 mb-1">N° del Documento</label>
                        <input type="text" id="numero_documento" name="numero_documento" placeholder="Ej. F-9842" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="orden_compra" class="block text-xs font-medium text-slate-700 mb-1">Orden de Compra / Ref.</label>
                        <input type="text" id="orden_compra" name="orden_compra" placeholder="Ej. OC-4512"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="origen_mercaderia" class="block text-xs font-medium text-slate-700 mb-1">Origen Mercadería</label>
                        <input type="text" id="origen_mercaderia" name="origen_mercaderia" placeholder="Ej. Cenabast / Importación" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="rut_proveedor" class="block text-xs font-medium text-slate-700 mb-1">RUT Proveedor</label>
                        <input type="text" id="rut_proveedor" name="rut_proveedor" placeholder="Ej. 76.123.456-7" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="razon_social" class="block text-xs font-medium text-slate-700 mb-1">Razón Social / Proveedor</label>
                        <input type="text" id="razon_social" name="razon_social" placeholder="Ej. Droguería Central SpA" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="producto" class="block text-xs font-medium text-slate-700 mb-1">Producto</label>
                        <input type="text" id="producto" name="producto" placeholder="Ej. PARACETAMOL 500MG" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="categoria" class="block text-xs font-medium text-slate-700 mb-1">Categoría</label>
                        <select id="categoria" name="categoria" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="" disabled selected>Seleccionar...</option>
                            <option value="Fármacos">Fármacos</option>
                            <option value="Insumos Clínicos">Insumos Clínicos</option>
                            <option value="Equipamiento Médico">Equipamiento Médico</option>
                        </select>
                    </div>

                    <div>
                        <label for="lote" class="block text-xs font-medium text-slate-700 mb-1">Lote</label>
                        <input type="text" id="lote" name="lote" placeholder="Ej. L-4893"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="fecha_vencimiento" class="block text-xs font-medium text-slate-700 mb-1">F. Vencimiento</label>
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="cantidad" class="block text-xs font-medium text-slate-700 mb-1">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad" placeholder="0" min="1" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="valor_neto_unitario" class="block text-xs font-medium text-slate-700 mb-1">Valor Neto Unit. ($)</label>
                        <input type="number" step="0.01" id="valor_neto_unitario" name="valor_neto_unitario" placeholder="0.00" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeDeposit()"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-sm w-full sm:w-auto cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium shadow-sm w-full sm:w-auto cursor-pointer">
                        Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de Modales y Desplegables -->
    <script>
        function toggleLotes(key) {
            const row = document.getElementById('lotes-' + key);
            const arrow = document.getElementById('arrow-' + key);

            if (row) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    if(arrow) arrow.classList.add('rotate-180');
                } else {
                    row.classList.add('hidden');
                    if(arrow) arrow.classList.remove('rotate-180');
                }
            }
        }

        function openHistory() { document.getElementById('HistoryModal').classList.remove('hidden'); }
        function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }
        function openImport() { document.getElementById('importModal').classList.remove('hidden'); }
        function closeImport() { document.getElementById('importModal').classList.add('hidden'); }
        function openDeposit() { document.getElementById('DepositModal').classList.remove('hidden'); }
        function closeDeposit() { document.getElementById('DepositModal').classList.add('hidden'); }
    </script>
@endsection
