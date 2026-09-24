@extends('layouts.app')

@section('content')
    @php
        $totalLotes = $inventories->count();
        $totalUnidades = $inventories->sum('cantidad');
    @endphp
    <div class="w-full">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl text-gray-800 font-semibold">Cuarentena</h1>
                <p class="text-gray-500 mt-1">
                    Lista de lotes en cuarentena
                </p>
            </div>

        </div>
        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Total de lotes</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">
                            {{ $totalLotes }}
                        </h4>
                    </div>
                    <div class="p-3 bg-yellow-400 text-white rounded-xl shadow-md shadow-blue-200">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Unidades totales -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Unidades totales</p>

                        <h4 class="text-3xl font-extrabold text-slate-800">
                            {{ number_format($totalUnidades, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="p-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Nuevos -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Alertas sanitarias</p>

                        <h4 class="text-3xl font-extrabold text-slate-800">

                        </h4>
                    </div>
                    <div class="p-3 bg-purple-600 text-white rounded-xl shadow-md shadow-purple-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!--Tabla cuarentena-->

        <div class="overflow-x-auto w-full">
            @php
                $invRaw = isset($inventories)
                    ? (method_exists($inventories, 'items')
                        ? $inventories->items()
                        : $inventories)
                    : [];
                $invCollection = collect($invRaw);

                // Agrupación blindada contra nulos
                $groupedInventories = $invCollection->groupBy(function ($item) {
                    $prod = is_object($item) ? $item->product ?? null : null;
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
                <thead
                    class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-extrabold">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-14 text-center">Detalle</th>
                        <th scope="col" class="px-6 py-4">Producto Principal</th>
                        <th scope="col" class="px-6 py-4">Categoría</th>
                        <th scope="col" class="px-6 py-4 text-center">Lotes en cuarentena</th>
                        <th scope="col" class="px-6 py-4 text-center">Stock retenido</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @if ($groupedInventories->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[15px] font-bold text-slate-600">Bodega sin productos registrados</p>
                                    <p class="text-[13px] text-slate-500 mt-1 max-w-sm">No existen medicamentos o insumos en
                                        el inventario actual. Registre una Recepción o una carga masiva para poblar la
                                        tabla.</p>
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

                            <!-- Fila Maestra del Producto -->
                            <tr class="bg-white hover:bg-slate-50/80 transition-colors cursor-pointer group"
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
                                    <span class="font-extrabold text-slate-800 text-[14px]">{{ $nombreProducto }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">{{ $categoriaLote }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $lotes->count() }}</span>
                                    <span class="text-slate-400 text-[11px] font-semibold uppercase">Lote(s)</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($stockTotalProducto > 10)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                        </span>
                                    @elseif($stockTotalProducto > 0)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unid. (Bajo)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
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
                                                <table class="w-full text-left text-xs whitespace-nowrap">
                                                    <thead
                                                        class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
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
                                                                $loteData = is_object($loteObj)
                                                                    ? $loteObj
                                                                    : (object) $loteObj;
                                                                $badgeVencimientoClass = 'text-slate-500';
                                                                $fechaVencRaw = $loteData->fecha_vencimiento ?? null;
                                                                $textoVencimiento = 'N/A';

                                                                if (!empty($fechaVencRaw)) {
                                                                    try {
                                                                        $fVenc = \Carbon\Carbon::parse($fechaVencRaw);
                                                                        $textoVencimiento = $fVenc->format('d/m/Y');
                                                                        $mesesRestantes = \Carbon\Carbon::now()->diffInMonths(
                                                                            $fVenc,
                                                                            false,
                                                                        );

                                                                        if ($mesesRestantes > 6) {
                                                                            $badgeVencimientoClass =
                                                                                'text-emerald-600 font-bold';
                                                                        } elseif ($mesesRestantes >= 3) {
                                                                            $badgeVencimientoClass =
                                                                                'text-amber-600 font-bold';
                                                                        } else {
                                                                            $badgeVencimientoClass =
                                                                                'text-rose-600 font-extrabold';
                                                                        }
                                                                    } catch (\Exception $e) {
                                                                        $textoVencimiento = $fechaVencRaw;
                                                                    }
                                                                }

                                                                // Ubicación segura
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

                                                                // Stock y variables seguras
                                                                $stockLote =
                                                                    $loteData->stock_total ??
                                                                    ($loteData->cantidad ?? 0);
                                                                $isCuarentena = isset($loteData->cuarentena)
                                                                    ? (bool) $loteData->cuarentena
                                                                    : strtolower($loteData->estado ?? '') ===
                                                                        'cuarentena';
                                                                $loteId = $loteData->id ?? 0;
                                                                $loteCodigo = $loteData->lote ?? 'S/L';
                                                                $estadoActualStr = $isCuarentena
                                                                    ? 'Cuarentena'
                                                                    : 'Disponible';
                                                            @endphp
                                                            <tr class="hover:bg-slate-50 transition-colors">
                                                                <td class="px-4 py-3">
                                                                    <span
                                                                        class="font-mono font-extrabold text-slate-800 text-[13px]">{{ $loteCodigo }}</span><br>
                                                                    <span
                                                                        class="text-[10px] uppercase font-semibold {{ $badgeVencimientoClass }}">Vence:
                                                                        {{ $textoVencimiento }}</span>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="flex items-center gap-1.5 text-[11px]">
                                                                        <span
                                                                            class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span
                                                                                class="text-slate-400">P:</span> <b
                                                                                class="text-slate-800">{{ $pasilloVal }}</b></span>
                                                                        <span
                                                                            class="bg-white text-slate-600 px-2 py-1 rounded shadow-sm border border-slate-200"><span
                                                                                class="text-slate-400">E:</span> <b
                                                                                class="text-slate-800">{{ $estanteVal }}</b></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    @if ($isCuarentena)
                                                                        <span
                                                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                    @elseif($stockLote <= 0)
                                                                        <span
                                                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">MERMA
                                                                            / AGOTADO</span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">DISPONIBLE</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    <span
                                                                        class="font-extrabold text-slate-800 text-[14px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
                                                                </td>
                                                                <td
                                                                    class="px-4 py-3 text-center flex justify-center gap-2">
                                                                    <!-- Botón QR con parámetros sanitizados usando addslashes en Blade -->
                                                                    <button type="button"
                                                                        onclick="openQRModal('{{ addslashes($loteId) }}', '{{ addslashes($nombreProducto) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($textoVencimiento) }}', '{{ $stockLote }}', 'Droguería DAS')"
                                                                        class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer"
                                                                        title="Generar Etiqueta QR">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                                                            </path>
                                                                        </svg>
                                                                    </button>

                                                                    <!-- Botón Editar con parámetros sanitizados -->
                                                                    <button type="button"
                                                                        onclick="openEditLote('{{ addslashes($loteId) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($nombreProducto) }}', '{{ $estadoActualStr }}')"
                                                                        class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer"
                                                                        title="Cambiar Estado">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                            </path>
                                                                        </svg>
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
        <!-- 5. MODAL: Editar Estado de Lote (Diseño Limpio) -->
        <div id="editLoteModal"
            class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div
                class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-sm flex flex-col overflow-hidden">
                <div class="px-6 py-5 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[16px] font-bold text-white tracking-wide">Actualizar Estado de Lote</h2>
                    <button type="button" onclick="closeEditLote()"
                        class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-white">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-5">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <span
                                class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Modificando
                                Lote de:</span>
                            <p id="modal_producto_nombre"
                                class="text-[14px] font-extrabold text-slate-800 w-full break-words leading-tight"></p>
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm">
                                <p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Seleccione
                                Nuevo Estado Físico *</label>
                            <select id="modal_estado" name="estado" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Disponible">🟢 Disponible (Liberado para entrega)</option>
                                <option value="Cuarentena">🟠 En Cuarentena (Bloqueo preventivo)</option>
                                <option value="Merma">🔴 En Merma (Dañado / Vencido)</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()"
                            class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar
                            Cambio</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-slate-200 bg-white">
            @if (isset($inventories) && method_exists($inventories, 'links'))
                {{ $inventories->links() }}
            @endif
        </div>
    </div>
    <script>
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

        // Modal Editar Lote
        function openEditLote(id, lote, producto, estado) {
            document.getElementById('editLoteForm').action = "/inventario/" + id;
            document.getElementById('modal_producto_nombre').textContent = producto;
            document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;

            let estadoSelect = document.getElementById('modal_estado');
            let estadoFormat = (estado || '').toLowerCase();
            if (estadoFormat.includes('cuarentena')) estadoSelect.value = 'Cuarentena';
            else if (estadoFormat.includes('merma')) estadoSelect.value = 'Merma';
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
                        text: 'LOTE:' + lote + '|PROD:' + producto + '|VENCE:' + vencimiento + '|STOCK:' +
                            stock,
                        width: 140,
                        height: 140,
                        colorDark: "#0f172a",
                        colorLight: "#ffffff",
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

        function openDeposit() {
            document.getElementById('DepositModal').classList.remove('hidden');
        }

        function closeDeposit() {
            document.getElementById('DepositModal').classList.add('hidden');
        }
    </script>



@endsection
