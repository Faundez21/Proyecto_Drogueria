@extends('layouts.app')

@section('title', 'Inventario y Stock')
@section('subtitle', 'Control físico de medicamentos e insumos en bodega.')

@section('content')
    <div class="w-full p-4 sm:p-6 space-y-6">

        <!-- Encabezado y Acciones Principales -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 mb-1">
                    <span class="font-mono-das text-slate-400 font-semibold">02</span>
                    <span>Bodega</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="font-medium text-slate-800">Catálogo General</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">Inventario de Droguería</h1>
                <p class="text-slate-500 text-sm mt-1">Control maestro de productos, lotes y ubicaciones físicas.</p>
            </div>
            
            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button type="button" onclick="openHistory()" class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-semibold hover:bg-slate-50 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Historial
                </button>
                <button type="button" onclick="openImport()" class="flex-1 md:flex-none px-4 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-colors text-[13px] font-semibold shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Carga Masiva
                </button>
                <button type="button" onclick="openDeposit()" class="flex-1 md:flex-none px-4 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors text-[13px] font-semibold shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ingreso Manual
                </button>
            </div>
        </div>

        <!-- Tabla Inventario Agrupada (Catálogo Maestro) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Catálogo de Productos</h3>
                </div>

                <form method="GET" action="{{ route('inventario.index') }}" class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar producto o lote..."
                        class="block w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors text-slate-700 shadow-sm">
                </form>
            </div>

            <div class="overflow-x-auto w-full">
                @php
                    $invCollection = collect(isset($inventories) && method_exists($inventories, 'items') ? $inventories->items() : ($inventories ?? []));
                    $groupedInventories = $invCollection->groupBy('producto');
                @endphp

                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-12 text-center">Detalle</th>
                            <th scope="col" class="px-6 py-4">Producto Principal</th>
                            <th scope="col" class="px-6 py-4">Categoría</th>
                            <th scope="col" class="px-6 py-4 text-center">Lotes Activos</th>
                            <th scope="col" class="px-6 py-4 text-center">Stock Físico Acumulado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($groupedInventories as $nombreProducto => $lotes)
                            @php
                                $stockTotalProducto = $lotes->sum('cantidad');
                                $groupKey = Str::slug($nombreProducto ?: 'producto-sin-nombre');
                                $primerLote = is_object($lotes->first()) ? $lotes->first() : (object) ($lotes->first() ?? []);
                            @endphp

                            <!-- Fila Maestra del Producto -->
                            <tr class="bg-white hover:bg-slate-50/80 transition-colors cursor-pointer group" onclick="toggleLotes('{{ $groupKey }}')">
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="w-6 h-6 rounded-full bg-slate-100 text-slate-400 group-hover:bg-teal-100 group-hover:text-teal-600 flex items-center justify-center transition-colors" id="icon-{{ $groupKey }}">
                                        <svg class="w-4 h-4 transform transition-transform duration-200" id="arrow-{{ $groupKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 text-[14px]">{{ $nombreProducto ?: 'Sin Nombre' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-[11px] font-medium border border-slate-200">{{ $primerLote->categoria ?? 'General' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-semibold text-slate-600">{{ $lotes->count() }}</span> <span class="text-slate-400 text-xs">Lote(s)</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($stockTotalProducto > 10)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unidades
                                        </span>
                                    @elseif($stockTotalProducto > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ number_format($stockTotalProducto, 0, ',', '.') }} Unid. (Bajo)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            0 Unidades (Agotado)
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Subtabla Desplegable de Lotes -->
                            <tr id="lotes-{{ $groupKey }}" class="hidden bg-slate-50/50">
                                <td colspan="5" class="p-0 border-b border-slate-200">
                                    <div class="px-6 py-4 pl-16">
                                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                            <div class="px-4 py-2.5 bg-slate-800 text-white flex justify-between items-center">
                                                <span class="text-[11px] font-bold uppercase tracking-wider flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    Desglose de Lotes
                                                </span>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-left text-xs whitespace-nowrap">
                                                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                                                        <tr>
                                                            <th class="px-4 py-3">Lote / Vence</th>
                                                            <th class="px-4 py-3">Ubicación (P-E)</th>
                                                            <th class="px-4 py-3 text-center">Estado</th>
                                                            <th class="px-4 py-3 text-right">Valor U.</th>
                                                            <th class="px-4 py-3 text-center">Stock Lote</th>
                                                            <th class="px-4 py-3 text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                                        @foreach($lotes as $lote)
                                                            @php
                                                                $loteObj = is_object($lote) ? $lote : (object) $lote;
                                                                $estadoVal = strtolower($loteObj->estado ?? 'disponible');

                                                                $badgeVencimientoClass = 'text-slate-500';
                                                                $textoVencimiento = !empty($loteObj->fecha_vencimiento) ? \Carbon\Carbon::parse($loteObj->fecha_vencimiento)->format('d/m/Y') : 'N/A';

                                                                if (!empty($loteObj->fecha_vencimiento)) {
                                                                    $fechaVenc = \Carbon\Carbon::parse($loteObj->fecha_vencimiento);
                                                                    $hoy = \Carbon\Carbon::now();
                                                                    $mesesRestantes = $hoy->diffInMonths($fechaVenc, false);

                                                                    if ($mesesRestantes > 6) {
                                                                        $badgeVencimientoClass = 'text-emerald-600 font-medium';
                                                                    } elseif ($mesesRestantes >= 3 && $mesesRestantes <= 6) {
                                                                        $badgeVencimientoClass = 'text-amber-600 font-bold';
                                                                    } else {
                                                                        $badgeVencimientoClass = 'text-rose-600 font-bold';
                                                                    }
                                                                }
                                                            @endphp
                                                            <tr class="hover:bg-slate-50 transition-colors">
                                                                <td class="px-4 py-3">
                                                                    <span class="font-mono-das font-bold text-slate-800 text-[13px]">{{ $loteObj->lote ?? 'S/L' }}</span><br>
                                                                    <span class="text-[10.5px] {{ $badgeVencimientoClass }}">Vence: {{ $textoVencimiento }}</span>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="flex items-center gap-1 text-[11px]">
                                                                        <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">P: <b class="text-slate-800">{{ $loteObj->pasillo ?? 'P.A' }}</b></span>
                                                                        <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">E: <b class="text-slate-800">{{ $loteObj->estante ?? 'P.A' }}</b></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    @if($estadoVal == 'cuarentena' || $estadoVal == 'en cuarentena')
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">CUARENTENA</span>
                                                                    @elseif($estadoVal == 'merma' || $estadoVal == 'en merma')
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">MERMA</span>
                                                                    @else
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">DISPONIBLE</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 text-right font-mono-das font-medium text-slate-600">
                                                                    ${{ number_format($loteObj->valor_neto_unitario ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    <span class="font-bold text-slate-800 text-[13px]">{{ number_format($loteObj->cantidad ?? 0, 0, ',', '.') }}</span>
                                                                </td>
                                                                <td class="px-4 py-3 text-center flex justify-center gap-1.5">
                                                                    <button type="button" onclick="openQRModal('{{ $loteObj->id ?? '' }}', '{{ addslashes($nombreProducto) }}', '{{ $loteObj->lote ?? 'S/L' }}', '{{ $textoVencimiento }}', '{{ $loteObj->cantidad ?? 0 }}', '{{ addslashes($loteObj->razon_social ?? 'S/N') }}')" class="p-1.5 text-teal-600 bg-teal-50 hover:bg-teal-100 hover:text-teal-800 rounded-md transition-colors border border-teal-200/50" title="Ver QR">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                                    </button>
                                                                    <button type="button" onclick="openEditLote('{{ $loteObj->id }}', '{{ $loteObj->lote ?? 'S/L' }}', '{{ addslashes($nombreProducto) }}', '{{ $loteObj->estado ?? 'Disponible' }}')" class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 rounded-md transition-colors border border-blue-200/50" title="Cambiar Estado">
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
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <p class="text-[14px] font-medium text-slate-500">No hay productos registrados en el inventario.</p>
                                        <p class="text-[12px] mt-1">Realice un ingreso manual o carga masiva para comenzar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                @if(isset($inventories) && method_exists($inventories, 'links'))
                    {{ $inventories->links() }}
                @endif
            </div>
        </div>


        <!-- MODAL: Historial de Movimientos -->
        <div id="HistoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-0 w-full max-w-5xl overflow-hidden flex flex-col">
                <div class="px-6 py-4 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white">Historial de Movimientos de Inventario</h2>
                        <p class="text-xs text-slate-300 mt-0.5">Registro de auditoría de recepciones e importaciones.</p>
                    </div>
                    <button type="button" onclick="closeHistory()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-[60vh] bg-slate-50">
                    <table class="w-full text-left text-[12.5px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-white text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-200 sticky top-0 shadow-sm">
                            <tr>
                                <th class="px-5 py-3">Fecha Recepción</th>
                                <th class="px-5 py-3">Documento / Correlativo</th>
                                <th class="px-5 py-3">Proveedor</th>
                                <th class="px-5 py-3">Producto</th>
                                <th class="px-5 py-3 text-center">Estado</th>
                                <th class="px-5 py-3 text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Aquí podrías iterar un $historial_recepciones real. Por ahora uso $inventories como mockup -->
                            @forelse($inventories ?? [] as $item)
                                @php $itemObj = is_object($item) ? $item : (object) $item; @endphp
                                <tr class="hover:bg-slate-100/50 transition-colors">
                                    <td class="px-5 py-3 text-slate-500">{{ !empty($itemObj->fecha_recepcion) ? \Carbon\Carbon::parse($itemObj->fecha_recepcion)->format('d-m-Y') : 'N/A' }}</td>
                                    <td class="px-5 py-3 font-medium text-slate-800">{{ $itemObj->tipo_documento ?? 'Doc' }} #{{ $itemObj->numero_documento ?? '000' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $itemObj->razon_social ?? 'Proveedor' }}</td>
                                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $itemObj->producto ?? 'Producto' }}</td>
                                    <td class="px-5 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Completado</span></td>
                                    <td class="px-5 py-3 text-center font-bold text-emerald-600">+{{ $itemObj->cantidad ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">No hay movimientos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end">
                    <button type="button" onclick="closeHistory()" class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-medium shadow-sm">Cerrar Historial</button>
                </div>
            </div>
        </div>

        <!-- MODAL: Importar Carga Masiva -->
        <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 flex flex-col w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white">Importar Carga Masiva</h2>
                    </div>
                    <button type="button" onclick="closeImport()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-[13px] text-slate-500 mb-4">Descargue la plantilla oficial, complete los datos de los productos y suba el archivo Excel (.xlsx o .csv) para registrar el inventario masivamente.</p>

                    <a href="#" class="w-full mb-6 flex justify-center items-center gap-2 bg-teal-50 border border-teal-200 text-teal-700 px-4 py-2.5 rounded-xl hover:bg-teal-100 transition-colors font-semibold text-[13px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Descargar Plantilla Excel
                    </a>

                    <form action="{{ route('inventario.import') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center gap-4">
                        @csrf
                        <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx, .xls, .csv">
                        <label for="excel" class="w-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2.5 rounded-lg cursor-pointer inline-flex items-center justify-center gap-2 text-[13px] font-medium transition-colors shadow-sm">
                            Seleccionar Archivo
                        </label>
                        <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-colors text-[13px] font-semibold shadow-sm mt-2">
                            Procesar Importación
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Ingreso Manual -->
        <div id="DepositModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-6 py-4 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white">Ingreso Directo de Producto</h2>
                        <p class="text-xs text-slate-300 mt-0.5">Registre un producto individual sin pasar por recepción formal.</p>
                    </div>
                    <button type="button" onclick="closeDeposit()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto bg-slate-50">
                    <form action="{{ route('inventario.store') }}" method="POST" id="manualDepositForm">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                            
                            <div class="sm:col-span-2">
                                <h4 class="text-[11px] font-bold text-teal-600 uppercase tracking-wider mb-2 border-b border-teal-100 pb-1">1. Datos del Documento</h4>
                            </div>

                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">N° Correlativo Interno</label>
                                <input type="number" name="correlativo_interno" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Fecha Recepción *</label>
                                <input type="date" name="fecha_recepcion" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Tipo Documento *</label>
                                <select name="tipo_documento" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Factura">Factura</option>
                                    <option value="Guía de Despacho">Guía de Despacho</option>
                                    <option value="Orden de Compra">Orden de Compra</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">N° Documento *</label>
                                <input type="text" name="numero_documento" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" />
                            </div>

                            <div class="sm:col-span-2 mt-2">
                                <h4 class="text-[11px] font-bold text-teal-600 uppercase tracking-wider mb-2 border-b border-teal-100 pb-1">2. Producto e Inventario</h4>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Producto *</label>
                                <input type="text" name="producto" placeholder="Ej. PARACETAMOL 500MG" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Categoría *</label>
                                <select name="categoria" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="Fármacos">Fármacos</option>
                                    <option value="Insumos">Insumos Clínicos</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Estado de Ingreso *</label>
                                <select name="estado" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                    <option value="Disponible">Disponible Directo</option>
                                    <option value="Cuarentena" selected>A Cuarentena (Recomendado)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Lote</label>
                                <input type="text" name="lote" placeholder="Ej. L-4893" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-mono-das" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Cantidad a Ingresar *</label>
                                <input type="number" name="cantidad" placeholder="0" min="1" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-bold" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeDeposit()" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-[13px] font-medium shadow-sm">Cancelar</button>
                    <button type="button" onclick="document.getElementById('manualDepositForm').submit()" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-[13px] font-semibold shadow-sm">Guardar Registro</button>
                </div>
            </div>
        </div>

        <!-- MODAL: Etiqueta QR -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-sm overflow-hidden flex flex-col">
                <div class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-[15px] text-white font-bold tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            ETIQUETA DE LOTE
                        </h2>
                    </div>
                    <button type="button" onclick="closeQRModal()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 bg-slate-50 flex flex-col items-center">
                    <div class="bg-white border-2 border-slate-200 rounded-xl p-4 shadow-sm mb-5">
                        <div id="qrcode" class="w-40 h-40 flex items-center justify-center"></div>
                    </div>

                    <div class="w-full bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                        <div class="bg-slate-100 px-4 py-2 border-b border-slate-200 text-center">
                            <span id="qrProducto" class="text-[13px] font-bold text-slate-800 block truncate w-full">Producto Name</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div class="flex justify-between px-4 py-2 text-[12px]">
                                <span class="text-slate-500 font-medium">Lote:</span>
                                <span id="qrLote" class="font-bold font-mono-das text-slate-800">L-000</span>
                            </div>
                            <div class="flex justify-between px-4 py-2 text-[12px]">
                                <span class="text-slate-500 font-medium">Vence:</span>
                                <span id="qrVencimiento" class="font-bold text-slate-800">00/00/00</span>
                            </div>
                            <div class="flex justify-between px-4 py-2 text-[12px]">
                                <span class="text-slate-500 font-medium">Stock Físico:</span>
                                <span id="qrStock" class="font-bold text-teal-700">0 Unid.</span>
                            </div>
                            <div class="flex justify-between px-4 py-2 text-[11px] bg-slate-50">
                                <span class="text-slate-400">Prov:</span>
                                <span id="qrProveedor" class="font-medium text-slate-600 truncate max-w-[150px]">Proveedor</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-between gap-3">
                    <button type="button" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Editar Estado de Lote -->
        <div id="editLoteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-0 w-full max-w-sm flex flex-col overflow-hidden">
                <div class="px-6 py-4 bg-slate-800 flex justify-between items-center">
                    <h2 class="text-[15px] font-bold text-white tracking-wide">Actualizar Estado</h2>
                    <button type="button" onclick="closeEditLote()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="editLoteForm" method="POST" action="" class="bg-slate-50">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Producto Seleccionado</span>
                            <p id="modal_producto_nombre" class="text-[13px] font-bold text-slate-800 truncate w-full"></p>
                            <p id="modal_lote_nombre" class="text-[11px] font-mono-das font-bold text-teal-600 mt-1"></p>
                        </div>

                        <div>
                            <label for="modal_estado" class="block text-[12px] font-bold text-slate-700 mb-1.5">Nuevo Estado Físico *</label>
                            <select id="modal_estado" name="estado" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-[13px] outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-medium">
                                <option value="Disponible">🟢 Disponible (Listo para entrega)</option>
                                <option value="Cuarentena">🟠 En Cuarentena (Bloqueo preventivo)</option>
                                <option value="Merma">🔴 En Merma (Dañado / Vencido)</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeEditLote()" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-[13px] font-medium transition-colors shadow-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[13px] font-semibold transition-colors shadow-sm">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            function toggleLotes(key) {
                const row = document.getElementById('lotes-' + key);
                const arrow = document.getElementById('arrow-' + key);
                const iconContainer = document.getElementById('icon-' + key);

                if (row) {
                    if (row.classList.contains('hidden')) {
                        row.classList.remove('hidden');
                        if(arrow) arrow.classList.add('rotate-90');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-400');
                            iconContainer.classList.add('bg-teal-600', 'text-white');
                        }
                    } else {
                        row.classList.add('hidden');
                        if(arrow) arrow.classList.remove('rotate-90');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-teal-600', 'text-white');
                            iconContainer.classList.add('bg-slate-100', 'text-slate-400');
                        }
                    }
                }
            }

            function openEditLote(id, lote, producto, estado) {
                document.getElementById('editLoteForm').action = "/inventario/" + id;
                document.getElementById('modal_producto_nombre').textContent = producto;
                document.getElementById('modal_lote_nombre').textContent = "LOTE: " + lote;
                
                let estadoSelect = document.getElementById('modal_estado');
                let estadoFormat = estado.toLowerCase();
                if(estadoFormat.includes('cuarentena')) estadoSelect.value = 'Cuarentena';
                else if(estadoFormat.includes('merma')) estadoSelect.value = 'Merma';
                else estadoSelect.value = 'Disponible';
                
                document.getElementById('editLoteModal').classList.remove('hidden');
            }
            function closeEditLote() { document.getElementById('editLoteModal').classList.add('hidden'); }

            function openQRModal(id, producto, lote, vencimiento, stock, proveedor) {
                document.getElementById('qrProducto').textContent = producto;
                document.getElementById('qrLote').textContent = lote;
                document.getElementById('qrVencimiento').textContent = vencimiento;
                document.getElementById('qrStock').textContent = stock + " Unid.";
                document.getElementById('qrProveedor').textContent = proveedor;

                const qrContainer = document.getElementById('qrcode');
                qrContainer.innerHTML = '';
                new QRCode(qrContainer, {
                    text: 'LOTE:' + lote + '|PROD:' + producto + '|VENCE:' + vencimiento + '|STOCK:' + stock,
                    width: 140,
                    height: 140,
                    colorDark : "#0f172a",
                    colorLight : "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });

                document.getElementById('qrModal').classList.remove('hidden');
            }
            function closeQRModal() { document.getElementById('qrModal').classList.add('hidden'); }

            function openHistory() { document.getElementById('HistoryModal').classList.remove('hidden'); }
            function closeHistory() { document.getElementById('HistoryModal').classList.add('hidden'); }
            function openImport() { document.getElementById('importModal').classList.remove('hidden'); }
            function closeImport() { document.getElementById('importModal').classList.add('hidden'); }
            function openDeposit() { document.getElementById('DepositModal').classList.remove('hidden'); }
            function closeDeposit() { document.getElementById('DepositModal').classList.add('hidden'); }
        </script>
    </div>
@endsection