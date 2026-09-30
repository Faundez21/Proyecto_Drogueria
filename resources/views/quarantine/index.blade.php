@extends('layouts.app')

@section('title', 'Productos en Cuarentena')
@section('subtitle', 'Lista de medicamentos e insumos bloqueados o retenidos preventivamente.')

@section('actions')
    <button type="button" onclick="openHistory()"
        class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 hover:text-amber-600 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Ver Historial
    </button>
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
            $totalLotes = $inventories->count();
            $totalUnidades = $inventories->sum('cantidad');
        @endphp

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total de Lotes -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[13px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Lotes Retenidos</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">
                            {{ $totalLotes }}
                        </h4>
                    </div>
                    <div class="p-3 bg-amber-500 text-white rounded-xl shadow-md shadow-amber-200">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Unidades totales -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-slate-100 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[13px] font-bold text-slate-500 uppercase tracking-wider mb-1">Unidades Físicas</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">
                            {{ number_format($totalUnidades, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="p-3 bg-slate-700 text-white rounded-xl shadow-md shadow-slate-200">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Cuarentena -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-amber-200 shadow-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Directorio Físico</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Agrupados por producto y lotes inmovilizados</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('quarantine.index') }}" class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por producto, categoría o lote..."
                        class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors text-slate-700 shadow-sm placeholder:text-slate-400">
                </form>
            </div>

            <div class="overflow-x-auto w-full">
                @php
                    $invRaw = isset($inventories) ? (method_exists($inventories, 'items') ? $inventories->items() : $inventories) : [];
                    $invCollection = collect($invRaw);

                    $groupedInventories = $invCollection->groupBy(function ($item) {
                        $prod = is_object($item) ? $item->product ?? null : null;
                        if ($prod && isset($prod->nombre)) return $prod->nombre;
                        if (is_object($item) && isset($item->producto)) return $item->producto;
                        return 'Producto General';
                    });
                @endphp

                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-extrabold">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-14 text-center">Desglose</th>
                            <th scope="col" class="px-6 py-4">Producto Principal</th>
                            <th scope="col" class="px-6 py-4">Categoría</th>
                            <th scope="col" class="px-6 py-4 text-center">Lotes en cuarentena</th>
                            <th scope="col" class="px-6 py-4 text-center">Stock Físico Inmovilizado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @if ($groupedInventories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-[15px] font-bold text-slate-600">No hay productos en cuarentena</p>
                                        <p class="text-[13px] text-slate-500 mt-1 max-w-sm">Bodega limpia de retenciones actualmente.</p>
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

                                <tr class="bg-white hover:bg-slate-50/80 transition-colors cursor-pointer group" onclick="toggleLotes('{{ $groupKey }}')">
                                    <td class="px-6 py-4 text-center relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-transparent group-hover:bg-amber-500 transition-colors"></div>
                                        <button type="button"
                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 group-hover:bg-amber-100 group-hover:text-amber-700 flex items-center justify-center transition-colors mx-auto shadow-sm border border-slate-200 group-hover:border-amber-200"
                                            id="icon-{{ $groupKey }}">
                                            <svg class="w-4 h-4 transform transition-transform duration-300" id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                        </span>
                                    </td>
                                </tr>

                                <tr id="lotes-{{ $groupKey }}" class="hidden bg-slate-100/50 shadow-inner">
                                    <td colspan="5" class="p-0 border-b border-slate-300">
                                        <div class="py-4 pr-6 pl-14">
                                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                                <div class="px-4 py-2 bg-slate-700 text-white flex justify-between items-center">
                                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                        Detalle de Lotes Retenidos
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                                                            <tr>
                                                                <th class="px-4 py-3">Lote / Vencimiento</th>
                                                                <th class="px-4 py-3">Ubicación Física</th>
                                                                <th class="px-4 py-3 text-center">Estado de Seguridad</th>
                                                                <th class="px-4 py-3 text-center">Stock de Lote</th>
                                                                <th class="px-4 py-3 text-center">Acciones y Controles</th>
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

                                                                    $pasilloVal = data_get($loteData, 'pasillo_nombre') ?? data_get($loteData, 'aisle.nombre') ?? data_get($loteData, 'pasillo') ?? 'P.A';
                                                                    $estanteVal = data_get($loteData, 'estanteria_nombre') ?? data_get($loteData, 'shelf.nombre') ?? data_get($loteData, 'estante') ?? 'P.A';
                                                                    $stockLote = data_get($loteData, 'stock_total') ?? data_get($loteData, 'cantidad') ?? 0;
                                                                    $loteId = data_get($loteData, 'id') ?? 0;
                                                                    $loteCodigo = data_get($loteData, 'lote') ?? 'S/L';
                                                                    $loteMotivo = data_get($loteData, 'motivo') ?? data_get($loteData, 'observacion') ?? 'Bloqueo preventivo';
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
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span class="font-extrabold text-slate-800 text-[14px]">{{ number_format($stockLote, 0, ',', '.') }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                                        <!-- Botón QR -->
                                                                        <button type="button" onclick="openQRModal('{{ addslashes($loteId) }}', '{{ addslashes($nombreProducto) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($textoVencimiento) }}', '{{ $stockLote }}', 'Droguería DAS')"
                                                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Generar Etiqueta QR">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                                                            </svg>
                                                                        </button>
                                                                        <!-- Botón Editar Estado -->
                                                                        <button type="button" onclick="openEditLote('{{ addslashes($loteId) }}', '{{ addslashes($loteCodigo) }}', '{{ addslashes($nombreProducto) }}', 'Cuarentena', '{{ addslashes($loteMotivo) }}')"
                                                                            class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-lg transition-all border border-teal-200 shadow-sm cursor-pointer" title="Cambiar Estado (Liberar / Merma)">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
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

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                @if (isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>

        <!-- 1. MODAL: Etiqueta QR -->
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

        <!-- 2. MODAL: Editar Estado de Lote (Liberar o Merma) -->
        <div id="editLoteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-sm flex flex-col overflow-hidden">
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
                            <div class="mt-2 inline-block bg-white border border-slate-300 px-3 py-1 rounded-lg shadow-sm">
                                <p id="modal_lote_nombre" class="text-[13px] font-mono font-bold text-slate-700"></p>
                            </div>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[13px] font-bold text-slate-700 mb-2">Decisión Técnica *</label>
                            <select id="modal_estado" name="estado" onchange="toggleMotivoField(this.value)" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-semibold text-slate-700 shadow-sm cursor-pointer transition-colors">
                                <option value="Cuarentena" selected>🟠 Mantener en Cuarentena</option>
                                <option value="Disponible">🟢 Liberar (Aprobar y regresar a Disponible)</option>
                                <option value="Merma">🔴 Declarar Merma (Desecho / Rechazo)</option>
                            </select>
                        </div>
                        
                        <div id="motivoContainer">
                            <label for="modal_motivo" class="block text-[13px] font-bold text-slate-700 mb-1.5">
                                Motivo de la Acción / Observación <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="modal_motivo" name="motivo" rows="3" required
                                placeholder="Escriba la justificación para liberar, mermar o mantener retenido..."
                                class="w-full rounded-xl border border-slate-300 bg-white p-3 text-[12px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all text-slate-700 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 text-[13px] font-bold transition-colors shadow-sm cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[13px] font-bold transition-all shadow-sm cursor-pointer">Confirmar Decisión</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. MODAL: Historial de Movimientos de Cuarentena -->
        <div id="HistoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
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

                <div class="overflow-x-auto max-h-[60vh] bg-slate-50">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-6 py-4">Fecha Movimiento</th>
                                <th class="px-6 py-4">Producto Afectado</th>
                                <th class="px-6 py-4">Lote Físico</th>
                                <th class="px-6 py-4 text-center">Acción / Estado</th>
                                <th class="px-6 py-4">Usuario Responsable</th>
                                <th class="px-6 py-4">Motivo Documentado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventories->take(15) as $movimiento)
                                @php
                                    // 1. FECHA CORREGIDA 
                                    // Se lee directamente de la instancia Carbon del modelo Eloquent
                                    $fechaMov = 'Sin Registro';
                                    if (isset($movimiento->updated_at) && $movimiento->updated_at) {
                                        $fechaMov = $movimiento->updated_at->format('d/m/Y H:i');
                                    } elseif (isset($movimiento->created_at) && $movimiento->created_at) {
                                        $fechaMov = $movimiento->created_at->format('d/m/Y H:i');
                                    }
                                    
                                    // 2. PRODUCTO Y LOTE
                                    $prodNom = $movimiento->producto ?? 'Producto Desconocido';
                                    $loteCod = $movimiento->lote ?? 'S/L';

                                    // 3. USUARIO RESPONSABLE CORREGIDO
                                    $usuarioResp = '';
                                    if (isset($movimiento->user) && $movimiento->user) {
                                        $usuarioResp = trim(($movimiento->user->name ?? '') . ' ' . ($movimiento->user->last_name ?? ''));
                                    } elseif (isset($movimiento->causer) && $movimiento->causer) {
                                        $usuarioResp = trim(($movimiento->causer->name ?? '') . ' ' . ($movimiento->causer->last_name ?? ''));
                                    }
                                    if (empty($usuarioResp)) {
                                        $usuarioResp = 'Falta Columna user_id'; // Mensaje claro para que sepas por qué no sale el usuario
                                    }

                                    // 4. ESTADO Y MOTIVO
                                    $estadoMov = strtolower($movimiento->estado ?? 'cuarentena');
                                    $motivoMov = $movimiento->motivo ?? 'Ingreso a retención preventiva';
                                    
                                    if (str_contains($estadoMov, 'disponible')) {
                                        $badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                        $badgeText  = 'LIBERADO';
                                    } elseif (str_contains($estadoMov, 'merma')) {
                                        $badgeColor = 'bg-rose-100 text-rose-800 border-rose-200';
                                        $badgeText  = 'ENVIADO A MERMA';
                                    } else {
                                        $badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
                                        $badgeText  = 'A CUARENTENA';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-100/70 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $fechaMov }}</td>
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ $prodNom }}</td>
                                    <td class="px-6 py-3.5 font-mono font-bold text-slate-700">{{ $loteCod }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded text-[10px] font-extrabold border {{ $badgeColor }} uppercase tracking-wider">
                                            {{ $badgeText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700 font-semibold flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-slate-200 text-slate-600 flex items-center justify-center text-[9px] font-bold">
                                            {{ strtoupper(substr($usuarioResp, 0, 1)) }}
                                        </div>
                                        {{ $usuarioResp }}
                                    </td>
                                    <td class="px-6 py-3.5 italic text-slate-500 max-w-xs truncate" title="{{ $motivoMov }}">
                                        {{ $motivoMov }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No hay registros de movimientos en cuarentena recientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                    <button type="button" onclick="closeHistory()" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                        Cerrar Historial
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts de interactividad y Generador QR -->
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
                    if (arrow) arrow.classList.add('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                        iconContainer.classList.add('bg-amber-500', 'text-white', 'border-amber-500', 'shadow-md');
                    }
                } else {
                    row.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-90');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-amber-500', 'text-white', 'border-amber-500', 'shadow-md');
                        iconContainer.classList.add('bg-slate-100', 'text-slate-400', 'border-slate-200');
                    }
                }
            }
        }

        // Modal Editar Lote
        function openEditLote(id, lote, producto, estado, motivo = '') {
            document.getElementById('editLoteForm').action = "/inventario/" + id;
            document.getElementById('modal_producto_nombre').textContent = producto;
            document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;

            let estadoSelect = document.getElementById('modal_estado');
            let estadoFormat = (estado || '').toLowerCase();
            
            if (estadoFormat.includes('cuarentena')) estadoSelect.value = 'Cuarentena';
            else if (estadoFormat.includes('merma')) estadoSelect.value = 'Merma';
            else estadoSelect.value = 'Disponible';

            let motivoInput = document.getElementById('modal_motivo');
            if (motivoInput) {
                motivoInput.value = motivo;
            }

            // Llamar a toggle para forzar atributo required
            toggleMotivoField(estadoSelect.value);
            document.getElementById('editLoteModal').classList.remove('hidden');
        }

        function closeEditLote() {
            document.getElementById('editLoteModal').classList.add('hidden');
        }

        // Validación dinámica del input Motivo
        function toggleMotivoField(estado) {
            const motivoInput = document.getElementById('modal_motivo');
            // Como toda acción técnica a partir de cuarentena requiere registro, siempre es required
            motivoInput.setAttribute('required', 'required');
            
            if (estado === 'Disponible') {
                motivoInput.placeholder = "Indique motivo de la liberación técnica...";
            } else if (estado === 'Merma') {
                motivoInput.placeholder = "Indique motivo del desecho o pérdida...";
            } else {
                motivoInput.placeholder = "Justificación para mantener inmovilizado...";
            }
        }

        // Modal Etiqueta QR
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
                    console.error("Error al renderizar el código QR:", error);
                }
            }, 150);
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        // Modal de Historial
        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
        }

        function closeHistory() {
            document.getElementById('HistoryModal').classList.add('hidden');
        }
    </script>
@endsection