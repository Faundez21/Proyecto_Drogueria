@extends('layouts.app')


@section('content')
    <div class="w-full p-4 sm:p-6 space-y-6">

        <!-- encabezado acciones principales -->
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5">
            <div>
                <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 mb-1.5">
                    <span class="font-mono bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-semibold">02</span>
                    <span>Bodega</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="font-bold text-teal-700">Catálogo Maestro</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Inventario de Droguería</h1>
                <p class="text-slate-500 text-sm mt-1">Gestión centralizada de productos, lotes y ubicaciones físicas.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                <button type="button" onclick="openHistory()" class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 hover:text-teal-700 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ver Historial
                </button>
                <button type="button" onclick="openImport()" class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Carga Masiva
                </button>
                <button type="button" onclick="openDeposit()" class="flex-1 sm:flex-none px-5 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-teal-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ingreso Manual
                </button>
            </div>
        </div>

       <!-- Contenedor principal tabla inventario -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-teal-100 shadow-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Directorio de Productos</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Agrupados por categoría y stock total</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('inventario.index') }}" class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por producto, categoría o lote..."
                        class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] font-medium focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors text-slate-700 shadow-sm placeholder:text-slate-400">
                </form>
            </div>

            <div class="overflow-x-auto w-full">
                @php
                    $invRaw = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];
                    $invCollection = collect($invRaw);

                    // Agrupación blindada contra nulos
                    $groupedInventories = $invCollection->groupBy(function($item) {
                        $prod = is_object($item) ? ($item->product ?? null) : null;
                        if ($prod && isset($prod->nombre)) {
                            return $prod->nombre;
                        }
                        if (is_object($item) && isset($item->producto)) {
                            return $item->producto;
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

                    <tbody class="divide-y divide-slate-100">
                        @if($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <p class="text-[15px] font-bold text-slate-600">Bodega sin productos registrados</p>
                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">No existen medicamentos o insumos en el inventario actual. Registre una Recepción o una carga masiva para poblar la tabla.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($groupedInventories as $nombreProducto => $lotes)
                                @php
                                    $stockTotalProducto = $lotes->sum(function($i) {
                                        return is_object($i) ? ($i->stock_total ?? $i->cantidad ?? 0) : 0;
                                    });
                                    $groupKey = 'grp_' . md5($nombreProducto);
                                    $primerLote = $lotes->first();
                                    $categoriaLote = is_object($primerLote) ? ($primerLote->categoria ?? 'General') : 'General';
                                @endphp

                                <!-- Fila Maestra del Producto -->
                                <tr class="bg-white hover:bg-slate-50/80 transition-colors cursor-pointer group" onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-teal-500 transition-colors"></div>
                                        <button type="button" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-teal-200" id="icon-{{ $groupKey }}">
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
                                        <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $lotes->count() }}</span> <span class="text-slate-400 text-[11px] font-semibold uppercase">Lote(s)</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($stockTotalProducto > 10)
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

                                <!-- Subtabla Desplegable de Lotes -->
                                <tr id="lotes-{{ $groupKey }}" class="hidden bg-slate-100/50 shadow-inner">
                                    <td colspan="5" class="p-0 border-b border-slate-300">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
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
                                                            @foreach($lotes as $loteObj)
                                                                @php
                                                                    $loteData = is_object($loteObj) ? $loteObj : (object) $loteObj;
                                                                    $badgeVencimientoClass = 'text-slate-500';
                                                                    $fechaVencRaw = $loteData->fecha_vencimiento ?? null;
                                                                    $textoVencimiento = 'N/A';

                                                                    if (!empty($fechaVencRaw)) {
                                                                        try {
                                                                            $fVenc = \Carbon\Carbon::parse($fechaVencRaw);
                                                                            $textoVencimiento = $fVenc->format('d/m/Y');
                                                                            $mesesRestantes = \Carbon\Carbon::now()->diffInMonths($fVenc, false);

                                                                            if ($mesesRestantes > 6) {
                                                                                $badgeVencimientoClass = 'text-emerald-600 font-bold';
                                                                            } elseif ($mesesRestantes >= 3) {
                                                                                $badgeVencimientoClass = 'text-amber-600 font-bold';
                                                                            } else {
                                                                                $badgeVencimientoClass = 'text-rose-600 font-extrabold';
                                                                            }
                                                                        } catch (\Exception $e) {
                                                                            $textoVencimiento = $fechaVencRaw;
                                                                        }
                                                                    }

                                                                    // Ubicación segura
                                                                    $pasilloVal = $loteData->pasillo_nombre ?? (is_object($loteData->aisle ?? null) ? ($loteData->aisle->nombre ?? $loteData->aisle->name ?? null) : ($loteData->pasillo ?? 'P.A'));
                                                                    $estanteVal = $loteData->estanteria_nombre ?? (is_object($loteData->shelf ?? null) ? ($loteData->shelf->nombre ?? $loteData->shelf->name ?? null) : ($loteData->estante ?? $loteData->estanteria ?? 'P.A'));

                                                                    // Stock y variables seguras
                                                                    $stockLote = $loteData->stock_total ?? $loteData->cantidad ?? 0;
                                                                    $isCuarentena = isset($loteData->cuarentena) ? (bool)$loteData->cuarentena : (strtolower($loteData->estado ?? '') === 'cuarentena');
                                                                    $loteId = $loteData->id ?? 0;
                                                                    $loteCodigo = $loteData->lote ?? 'S/L';
                                                                    $estadoActualStr = $isCuarentena ? 'Cuarentena' : 'Disponible';
                                                                @endphp
                                                                <tr class="hover:bg-slate-50 transition-colors">
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
                                                                        @if($isCuarentena)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                        @elseif($stockLote <= 0)
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">MERMA / AGOTADO</span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">DISPONIBLE</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[14px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                                        <!-- Botón QR con parámetros sanitizados usando addslashes en Blade -->
                                                                        <button type="button"
                                                                            onclick="openQRModal('{{ addslashes($loteId) }}', '{{ addslashes($nombreProducto) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($textoVencimiento) }}', '{{ $stockLote }}', 'Droguería DAS')"
                                                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Generar Etiqueta QR">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                                        </button>

                                                                        <!-- Botón Editar con parámetros sanitizados -->
                                                                        <button type="button"
                                                                            onclick="openEditLote('{{ addslashes($loteId) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($nombreProducto) }}', '{{ $estadoActualStr }}')"
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

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                @if(isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>
        <!-- 1. MODAL: Historial de Movimientos -->
        <div id="HistoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-5xl flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Historial de Movimientos de Inventario</h2>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">Registro de auditoría de recepciones e importaciones.</p>
                    </div>
                    <button type="button" onclick="closeHistory()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-[60vh] bg-slate-50">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm">
                            <tr>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Doc / Correlativo</th>
                                <th class="px-6 py-4">Proveedor</th>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventories ?? [] as $item)
                                @php
                                    $itemObj = is_object($item) ? $item : (object) $item;
                                    $prodNombre = is_object($itemObj->product ?? null) ? ($itemObj->product->nombre ?? 'Producto') : ($itemObj->producto ?? 'Producto');
                                    $cant = $itemObj->stock_total ?? $itemObj->cantidad ?? 0;
                                @endphp
                                <tr class="hover:bg-slate-100/70 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ !empty($itemObj->fecha_recepcion) ? \Carbon\Carbon::parse($itemObj->fecha_recepcion)->format('d-m-Y') : 'N/A' }}</td>
                                    <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $itemObj->tipo_documento ?? 'Doc' }} #{{ $itemObj->numero_documento ?? '000' }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $itemObj->razon_social ?? 'Proveedor' }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ $prodNombre }}</td>
                                    <td class="px-6 py-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">Completado</span></td>
                                    <td class="px-6 py-3.5 text-center font-extrabold text-emerald-600">+{{ $cant }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No hay movimientos registrados recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                    <button type="button" onclick="closeHistory()" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">Cerrar Historial</button>
                </div>
            </div>
        </div>

        <!-- 2. MODAL: Importar Carga Masiva -->
        <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 flex flex-col w-full max-w-md overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white tracking-wide">Importar Carga Masiva</h2>
                    <button type="button" onclick="closeImport()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 bg-slate-50">
                    <p class="text-[13px] text-slate-600 mb-5 text-center leading-relaxed">Descargue la plantilla oficial, complete los datos de los productos y suba el archivo Excel (.xlsx o .csv) para registrar el inventario masivamente.</p>

                    <a href="#" class="w-full mb-6 flex justify-center items-center gap-2 bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-100 hover:text-teal-700 transition-all font-bold text-[13px] shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Descargar Plantilla Oficial
                    </a>

                    <form action="#" method="POST" enctype="multipart/form-data" class="bg-white border border-dashed border-slate-300 hover:border-teal-400 transition-colors rounded-xl p-6 flex flex-col items-center gap-4 text-center shadow-sm">
                        @csrf
                        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-teal-600 mb-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx, .xls, .csv">
                        <label for="excel" class="w-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-teal-700 px-4 py-2 rounded-lg cursor-pointer inline-flex items-center justify-center gap-2 text-[13px] font-bold transition-all shadow-sm">
                            Explorar Archivos
                        </label>
                        <span class="text-[11px] text-slate-400 font-medium">Formatos soportados: .xlsx, .csv</span>

                        <button type="submit" class="w-full px-4 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm mt-3 cursor-pointer">
                            Procesar e Importar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. MODAL: Ingreso Manual -->
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
                    <form action="#" method="POST" id="manualDepositForm">
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
                                <input type="date" name="fecha_recepcion" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm" />
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
                                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider">2. Detalle Físico e Inventario</h4>
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

        <!-- 4. MODAL: Etiqueta QR (Diseño Fotorrealista de Etiqueta de Caja) -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="relative w-full max-w-sm flex flex-col items-center">

                <!-- Etiqueta Simulada (Fondo Blanco) -->
                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full flex flex-col items-center relative overflow-hidden ring-1 ring-slate-900/5">
                    <div class="absolute inset-2 border-2 border-dashed border-slate-300 rounded-xl pointer-events-none"></div>

                    <div class="w-full flex flex-col items-center mb-4 z-10 border-b-2 border-black pb-3">
                        <h2 class="text-[17px] text-black font-black tracking-widest uppercase">DROGUERÍA DAS</h2>
                        <span class="text-[11px] text-black font-bold font-mono tracking-widest uppercase mt-0.5">Control de Trazabilidad</span>
                    </div>

                    <!-- Contenedor del QR. Dimensiones fijas para que qrcode.js lo lea correctamente -->
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
                    <button type="button" onclick="closeQRModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md cursor-pointer">
                        Cerrar Vista
                    </button>
                    <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[13px] font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Etiqueta
                    </button>
                </div>
            </div>
        </div>

        <!-- 5. MODAL: Editar Estado de Lote (Diseño Limpio) -->
        <div id="editLoteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-sm flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Actualizar Estado de Lote</h2>
                    <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-white">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-5">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Modificando Lote de:</span>
                            <p id="modal_producto_nombre" class="text-[14px] font-extrabold text-slate-800 w-full break-words leading-tight"></p>
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm">
                                <p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Seleccione Nuevo Estado Físico *</label>
                            <select id="modal_estado" name="estado" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Disponible">🟢 Disponible (Liberado para entrega)</option>
                                <option value="Cuarentena">🟠 En Cuarentena (Bloqueo preventivo)</option>
                                <option value="Merma">🔴 En Merma (Dañado / Vencido)</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            // Acordeón de tabla maestra
            function toggleLotes(key) {
                const row = document.getElementById('lotes-' + key);
                const arrow = document.getElementById('arrow-' + key);
                const iconContainer = document.getElementById('icon-' + key);

                if (row) {
                    if (row.classList.contains('hidden')) {
                        row.classList.remove('hidden');
                        if(arrow) arrow.classList.add('rotate-90');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                            iconContainer.classList.add('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
                        }
                    } else {
                        row.classList.add('hidden');
                        if(arrow) arrow.classList.remove('rotate-90');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-md');
                            iconContainer.classList.add('bg-slate-100', 'text-slate-400', 'border-slate-200');
                        }
                    }
                }
            }

            // Modal Editar Lote
            function openEditLote(id, lote, producto, estado) {
                document.getElementById('editLoteForm').action = "/inventario/" + id;
                document.getElementById('modal_producto_nombre').textContent = producto;
                document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;

                let estadoSelect = document.getElementById('modal_estado');
                let estadoFormat = (estado || '').toLowerCase();
                if(estadoFormat.includes('cuarentena')) estadoSelect.value = 'Cuarentena';
                else if(estadoFormat.includes('merma')) estadoSelect.value = 'Merma';
                else estadoSelect.value = 'Disponible';

                document.getElementById('editLoteModal').classList.remove('hidden');
            }

            function closeEditLote() {
                document.getElementById('editLoteModal').classList.add('hidden');
            }

            // Modal Etiqueta QR - LÓGICA DE DIBUJO ASÍNCRONO CORREGIDA
            function openQRModal(id, producto, lote, vencimiento, stock, proveedor) {
                // 1. Quitamos la clase 'hidden' para que el navegador asigne dimensiones al div.
                const modal = document.getElementById('qrModal');
                modal.classList.remove('hidden');

                // 2. Llenamos la información textual.
                document.getElementById('qrProducto').textContent = producto;
                document.getElementById('qrLote').textContent = lote;
                document.getElementById('qrVencimiento').textContent = vencimiento;
                document.getElementById('qrStock').textContent = stock + " UNID.";
                document.getElementById('qrProveedor').textContent = proveedor;

                // 3. Limpiamos el contenedor del QR (por si ya se había generado uno antes).
                const qrContainer = document.getElementById('qrcode');
                qrContainer.innerHTML = '';

                // 4. Esperamos 150ms. Esto es CLAVE para que Tailwind aplique los estilos Flex/Block
                //    y la librería qrcode.js no dibuje sobre un espacio en blanco de 0x0 pixeles.
                setTimeout(function() {
                    try {
                        new QRCode(qrContainer, {
                            text: 'LOTE:' + lote + '|PROD:' + producto + '|VENCE:' + vencimiento + '|STOCK:' + stock,
                            width: 140,
                            height: 140,
                            colorDark : "#0f172a",
                            colorLight : "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch (error) {
                        console.error("Error al renderizar el código QR:", error);
                    }
                }, 150);
            }

            function closeQRModal() {
                document.getElementById('qrModal').classList.add('hidden');
            }

            // Modales Generales Secundarios
            function openHistory() { document.getElementById('HistoryModal').classList.remove('hidden'); }
            function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }

            function openImport() { document.getElementById('importModal').classList.remove('hidden'); }
            function closeImport() { document.getElementById('importModal').classList.add('hidden'); }

            function openDeposit() { document.getElementById('DepositModal').classList.remove('hidden'); }
            function closeDeposit() { document.getElementById('DepositModal').classList.add('hidden'); }
        </script>
    </div>
@endsection
