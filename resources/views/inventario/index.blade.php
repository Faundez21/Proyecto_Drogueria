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
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z"></path>
                    </svg>
                    Ver Historial
                </button>
            </div>
        </div>

        <!-- Modal de Historial -->
        <div id="HistoryModal"
            class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 w-full max-w-5xl overflow-hidden">
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
                                <th class="px-4 py-3 font-semibold text-center">Estado</th>
                                <th class="px-4 py-3 font-semibold text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventories ?? [] as $item)
                                @php $itemObj = is_object($item) ? $item : (object) $item; @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ !empty($itemObj->fecha_recepcion) ? \Carbon\Carbon::parse($itemObj->fecha_recepcion)->format('d-m-Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $itemObj->tipo_documento ?? '' }} #{{ $itemObj->numero_documento ?? '' }}
                                        @if(!empty($itemObj->correlativo_interno))
                                            <span class="text-xs text-slate-400 block">Corr: {{ $itemObj->correlativo_interno }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $itemObj->razon_social ?? '' }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $itemObj->producto ?? '' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php $estadoHist = strtolower($itemObj->estado ?? 'disponible'); @endphp
                                        @if($estadoHist == 'cuarentena' || $estadoHist == 'en cuarentena')
                                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500 text-white shadow-sm">Cuarentena</span>
                                        @elseif($estadoHist == 'merma' || $estadoHist == 'en merma')
                                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-sm">Merma</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-600 text-white shadow-sm">Disponible</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-emerald-600">+{{ $itemObj->cantidad ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-400">No hay movimientos registrados.</td>
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
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($groupedInventories as $nombreProducto => $lotes)
                            @php
                                $stockTotalProducto = $lotes->sum('cantidad');
                                $groupKey = Str::slug($nombreProducto ?: 'producto-sin-nombre');
                                $primerLote = is_object($lotes->first()) ? $lotes->first() : (object) ($lotes->first() ?? []);
                            @endphp

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
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-600 text-white border border-red-700 shadow-sm">
                                            0 Unidades (Agotado)
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Subtabla Desplegable de Lotes -->
                            <tr id="lotes-{{ $groupKey }}" class="hidden bg-slate-50/60 border-t border-b border-slate-200/80">
                                <td colspan="5" class="p-4 pl-12">
                                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                        <div class="px-4 py-2.5 bg-slate-100/70 border-b border-slate-200 flex justify-between items-center">
                                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Desglose de Lotes / Recepciones de {{ $nombreProducto }}</span>
                                        </div>
                                        <table class="w-full text-left text-xs whitespace-nowrap">
                                            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                                                <tr>
                                                    <th class="px-4 py-2.5">N° Correlativo</th>
                                                    <th class="px-4 py-2.5">Lote / Vencimiento</th>
                                                    <th class="px-4 py-2.5">Origen Mercadería</th>
                                                    <th class="px-4 py-2.5">Proveedor / Documento</th>
                                                    <th class="px-4 py-2.5">Ubicación</th>
                                                    <th class="px-4 py-2.5 text-center">Estado</th>
                                                    <th class="px-4 py-2.5 text-right">Valor Neto Unit.</th>
                                                    <th class="px-4 py-2.5 text-center">Stock del Lote</th>
                                                    <th class="px-4 py-2.5 text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                                @foreach($lotes as $lote)
                                                    @php
                                                        $loteObj = is_object($lote) ? $lote : (object) $lote;
                                                        $estadoVal = strtolower($loteObj->estado ?? 'disponible');

                                                        $badgeVencimientoClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                                        $textoVencimiento = !empty($loteObj->fecha_vencimiento)
                                                            ? \Carbon\Carbon::parse($loteObj->fecha_vencimiento)->format('d-m-Y')
                                                            : 'N/A';

                                                        if (!empty($loteObj->fecha_vencimiento)) {
                                                            $fechaVenc = \Carbon\Carbon::parse($loteObj->fecha_vencimiento);
                                                            $hoy = \Carbon\Carbon::now();
                                                            $mesesRestantes = $hoy->diffInMonths($fechaVenc, false);

                                                            if ($mesesRestantes > 6) {
                                                                $badgeVencimientoClass = 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold';
                                                            } elseif ($mesesRestantes >= 3 && $mesesRestantes <= 6) {
                                                                $badgeVencimientoClass = 'bg-amber-100 text-amber-800 border-amber-300 font-semibold';
                                                            } else {
                                                                $badgeVencimientoClass = 'bg-red-600 text-white font-bold border-red-700 shadow-sm';
                                                            }
                                                        }
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                                        <td class="px-4 py-2.5 font-bold text-teal-700">
                                                            #{{ $loteObj->correlativo_interno ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2.5 font-medium">
                                                            <span class="text-slate-800 font-bold">Lote: {{ $loteObj->lote ?? 'S/L' }}</span> <br>
                                                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[11px] border {{ $badgeVencimientoClass }}">
                                                                Vence: {{ $textoVencimiento }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                                {{ $loteObj->origen_mercaderia ?? $loteObj->origen ?? 'Sin Origen' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2.5">
                                                            <span class="font-semibold text-slate-800">{{ $loteObj->razon_social ?? 'S/N' }}</span> <br>
                                                            <span class="text-slate-400">{{ $loteObj->tipo_documento ?? '' }}: {{ $loteObj->numero_documento ?? '' }}</span>
                                                        </td>
                                                        <td class="px-4 py-2.5 text-slate-500">
                                                            {{ $loteObj->pasillo ?? 'Sin pasillo' }} / {{ $loteObj->estante ?? 'Sin estante' }}
                                                        </td>
                                                        <!-- Estado del Lote -->
                                                        <td class="px-4 py-2.5 text-center">
                                                            @if($estadoVal == 'cuarentena' || $estadoVal == 'en cuarentena')
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-500 text-white shadow-sm">
                                                                    Cuarentena
                                                                </span>
                                                            @elseif($estadoVal == 'merma' || $estadoVal == 'en merma')
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-red-600 text-white shadow-sm">
                                                                    Merma
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-600 text-white shadow-sm">
                                                                    Disponible
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-800">
                                                            ${{ number_format($loteObj->valor_neto_unitario ?? 0, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                                                {{ number_format($loteObj->cantidad ?? 0, 0, ',', '.') }} Unid.
                                                            </span>
                                                        </td>
                                                        <!-- Acciones de Lote -->
                                                        <td class="px-4 py-2.5 text-center" onclick="event.stopPropagation()">
                                                            <button type="button"
                                                                onclick="openEditLote('{{ $loteObj->id }}', '{{ $loteObj->lote ?? 'S/L' }}', '{{ $nombreProducto }}', '{{ $loteObj->estado ?? 'Disponible' }}')"
                                                                class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-xs font-semibold transition-colors shadow-sm cursor-pointer inline-flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                Editar
                                                            </button>
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
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">No hay registros de productos en el inventario.</td>
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
    </div>

    <!-- Modal Editar Estado de Lote -->
    <div id="editLoteModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-6 w-full max-w-md">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-4">
                <h2 class="text-lg font-bold text-slate-800">Cambiar Estado del Lote</h2>
                <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editLoteForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Producto</span>
                        <p id="modal_producto_nombre" class="text-sm font-bold text-slate-800"></p>
                    </div>

                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Lote</span>
                        <p id="modal_lote_nombre" class="text-sm font-bold text-slate-700"></p>
                    </div>

                    <div>
                        <label for="modal_estado" class="block text-xs font-bold text-slate-700 mb-1">Estado de Disponibilidad *</label>
                        <select id="modal_estado" name="estado" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="Disponible">Disponible (En Stock Normal)</option>
                            <option value="Cuarentena">En Cuarentena (Bloqueado temporalmente)</option>
                            <option value="Merma">En Merma (Dañado / Vencido)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditLote()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Actualizar Estado
                    </button>
                </div>
            </form>
        </div>
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
                        <label for="origen_mercaderia" class="block text-xs font-medium text-slate-700 mb-1">Origen de la Mercadería *</label>
                        <select id="origen_mercaderia" name="origen_mercaderia" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="" disabled selected>Seleccione origen...</option>
                            <option value="CENABAST">CENABAST</option>
                            <option value="COMPRA DIRECTA DAS">COMPRA DIRECTA DAS</option>
                            <option value="SERVICIO DE SALUD TALCAHUANO">SERVICIO DE SALUD TALCAHUANO</option>
                            <option value="Traspaso">Traspaso</option>
                            <option value="Donación">Donación</option>
                            <option value="Préstamo">Préstamo</option>
                            <option value="Canje">Canje</option>
                        </select>
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
                        <label for="estado" class="block text-xs font-medium text-slate-700 mb-1">Estado del Lote *</label>
                        <select id="estado" name="estado" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="Disponible" selected>Disponible</option>
                            <option value="Cuarentena">En Cuarentena</option>
                            <option value="Merma">En Merma (Vencido / Dañado)</option>
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
                        class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors text-sm font-medium shadow-sm cursor-pointer">
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

        function openEditLote(id, lote, producto, estado) {
            document.getElementById('editLoteForm').action = "/inventario/" + id;
            document.getElementById('modal_producto_nombre').textContent = producto;
            document.getElementById('modal_lote_nombre').textContent = lote;
            document.getElementById('modal_estado').value = estado;
            document.getElementById('editLoteModal').classList.remove('hidden');
        }

        function closeEditLote() {
            document.getElementById('editLoteModal').classList.add('hidden');
        }

        function openHistory() { document.getElementById('HistoryModal').classList.remove('hidden'); }
        function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }
        function openImport() { document.getElementById('importModal').classList.remove('hidden'); }
        function closeImport() { document.getElementById('importModal').classList.add('hidden'); }
        function openDeposit() { document.getElementById('DepositModal').classList.remove('hidden'); }
        function closeDeposit() { document.getElementById('DepositModal').classList.add('hidden'); }
    </script>
@endsection
