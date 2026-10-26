@extends('layouts.app')
@section('title', 'Trazabilidad')
@section('subtitle', 'Seguimiento detallado de lotes, movimientos y ciclo de vida de productos.')

@section('actions')
    <!-- boton exportar que apunta al formulario inferior mediante su id -->
    <button type="submit" form="filterForm" name="export" value="excel" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-600 hover:text-white shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1.5 13.9l-2.4-3.4 2.3-3.3h1.8l-1.4 2.2 1.5 2.3h-1.8zM9.5 15.9l1.4-2.2-1.3-2.1h1.8l.5.9.6-.9h1.7l-2.2 3.2 2.4 3.5h-1.8l-1.5-2.4-1.5 2.4H9.5z" />
        </svg>
        Exportar Reporte Excel Visual
    </button>
@endsection

@section('content')

    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">04</span>
        <span>Control</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="font-semibold text-slate-800">Trazabilidad de Lotes</span>
    </div>

    <!-- tarjetas kpis -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Movimientos Trazados</p>
                    <p class="font-mono text-[28px] font-black text-slate-800 mt-1">{{ number_format($totalMovimientos ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Lotes en Bodega Activos</p>
                    <p class="font-mono text-[28px] font-black text-slate-800 mt-1">{{ number_format($lotesActivos ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm sm:col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Alertas Clinicas (FEFO)</p>
                    <p class="font-mono text-[28px] font-black {{ ($alertas ?? 0) > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">{{ $alertas ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- contenedor de tabla de movimientos -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

        <!-- Cabecera de la tabla y botón de filtros -->
        <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center">
            <h3 class="text-[15px] font-bold text-slate-800">Historial de Movimientos de Lotes</h3>
            <button type="button" id="toggle-filters-btn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtros Avanzados
            </button>
        </div>

        <!-- barra de busqueda y filtros -->
        <form method="GET" action="{{ route('traceability.index') }}" id="filterForm" class="{{ request()->hasAny(['search', 'tipo_movimiento', 'fecha_desde', 'fecha_hasta']) ? '' : 'hidden' }} bg-white border-b border-slate-200 p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                
                <div class="lg:col-span-2 relative">
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Búsqueda Libre</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 pt-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar Lote, Producto o Documento..."
                        class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                </div>

                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Movimiento</label>
                    <select name="tipo_movimiento" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer">
                        <option value="">Todos</option>
                        <option value="Recepción" {{ request('tipo_movimiento') == 'Recepción' ? 'selected' : '' }}>Recepción</option>
                        <option value="Despacho" {{ request('tipo_movimiento') == 'Despacho' ? 'selected' : '' }}>Despacho Normal</option>
                        @if(auth()->user()->hasRole('Administrador') || auth()->user()->can('ver.despacho_controlado'))
                            <option value="Despacho Controlado" {{ request('tipo_movimiento') == 'Despacho Controlado' ? 'selected' : '' }}>Despacho Controlado</option>
                        @endif
                        <option value="Cambio de estado" {{ request('tipo_movimiento') == 'Cambio de estado' ? 'selected' : '' }}>Cambio de Estado (Cuarentena / Merma)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                </div>

                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                </div>
                
            </div>
            
            <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                <a href="{{ route('traceability.index') }}" class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                    Limpiar Filtros
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all cursor-pointer">
                    Aplicar Filtros
                </button>
            </div>
        </form>

        <!-- tabla principal -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 whitespace-nowrap">Lote / Producto</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tipo Movimiento</th>
                        <th class="px-6 py-4 whitespace-nowrap">Origen / Destino</th>
                        <th class="px-6 py-4 whitespace-nowrap">Fecha / Hora</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Estado Lote</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]">
                    @if(count($movements) > 0)
                        @foreach($movements as $mov)
                            @php
                                $badgeBg = 'bg-amber-50'; $badgeText = 'text-amber-700'; $badgeBorder = 'border-amber-200';
                                if($mov->tipo_movimiento == 'Recepción') {
                                    $badgeBg = 'bg-emerald-50'; $badgeText = 'text-emerald-700'; $badgeBorder = 'border-emerald-200';
                                } elseif($mov->tipo_movimiento == 'Despacho') {
                                    $badgeBg = 'bg-blue-50'; $badgeText = 'text-blue-700'; $badgeBorder = 'border-blue-200';
                                } elseif($mov->tipo_movimiento == 'Despacho Controlado') {
                                    $badgeBg = 'bg-rose-50'; $badgeText = 'text-rose-700'; $badgeBorder = 'border-rose-200';
                                }
                                $responsableMov = $mov->user ? trim($mov->user->name . ' ' . ($mov->user->last_name ?? '')) : null;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-mono font-bold text-[11px] text-teal-700 bg-teal-50 shrink-0 border border-teal-100">
                                            {{ Str::limit($mov->lote, 4, '') }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-[14px] text-slate-800 truncate">{{ $mov->producto }}</p>
                                            <p class="font-mono text-[11px] font-semibold text-slate-500 mt-0.5">Lote: {{ $mov->lote }} · Vence: {{ $mov->fecha_vencimiento ? \Carbon\Carbon::parse($mov->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 rounded-lg text-[11px] font-extrabold uppercase tracking-wide {{ $badgeBg }} {{ $badgeText }} {{ $badgeBorder }} border whitespace-nowrap">
                                        {{ $mov->tipo_movimiento }} ({{ $mov->cantidad > 0 ? '+'.$mov->cantidad : $mov->cantidad }})
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-slate-800 font-bold text-[13px]">{{ $mov->origen_destino }}</span>
                                        <span class="text-[11px] font-medium text-slate-500">
                                            {{ $mov->documento_respaldo }}
                                            @if($responsableMov)
                                                · <span class="text-teal-700 font-semibold">Resp: {{ $responsableMov }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-[12px] font-semibold text-slate-600">
                                    {{ \Carbon\Carbon::parse($mov->fecha_hora)->format('d/m/Y - H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(strtolower($mov->estado_lote) == 'cuarentena')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Cuarentena
                                        </span>
                                    @elseif(strtolower($mov->estado_lote) == 'merma')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Merma
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Vigente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" onclick="openLotDetails('{{ $mov->lote }}', '{{ addslashes($mov->producto) }}')" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-teal-700 text-white rounded-xl text-[12px] font-bold transition-all hover:shadow-md hover:-translate-y-0.5 cursor-pointer shadow-sm">
                                        <svg class="w-4 h-4 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Ver Ficha Maestra
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-200 shadow-sm">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-[15px] font-bold text-slate-600">Sin movimientos trazados</p>
                                    <p class="text-[13px] text-slate-500 mt-1 max-w-sm">No existen recepciones ni despachos registrados que coincidan con la búsqueda actual.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- paginacion -->
        @if ($movements->hasPages())
        <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white rounded-b-2xl">
            <div class="text-[13px] text-slate-500 text-center sm:text-left font-medium">
                Mostrando del <span class="font-bold text-slate-800">{{ $movements->firstItem() ?? 0 }}</span> al <span class="font-bold text-slate-800">{{ $movements->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $movements->total() }}</span> registros
            </div>

            <div class="flex items-center gap-1.5">
                @if ($movements->onFirstPage())
                    <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $movements->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Anterior</a>
                @endif

                @php
                    $start = max($movements->currentPage() - 2, 1);
                    $end = min($movements->currentPage() + 2, $movements->lastPage());
                @endphp

                @if ($start > 1)
                    <a href="{{ $movements->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">1</a>
                    @if ($start > 2)
                        <span class="px-1 text-slate-400">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $movements->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-bold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">{{ $i }}</span>
                    @else
                        <a href="{{ $movements->url($i) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $i }}</a>
                    @endif
                @endfor

                @if ($end < $movements->lastPage())
                    @if ($end < $movements->lastPage() - 1)
                        <span class="px-1 text-slate-400">...</span>
                    @endif
                    <a href="{{ $movements->url($movements->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $movements->lastPage() }}</a>
                @endif

                @if ($movements->hasMorePages())
                    <a href="{{ $movements->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Siguiente</a>
                @else
                    <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- ventana modal para visualizar la ficha clinica del lote -->
    <div id="modal-lot-details" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 transition-all">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closeLotDetails()"></div>
        <div class="relative w-full max-w-5xl rounded-3xl overflow-hidden bg-white border border-slate-200 shadow-2xl flex flex-col max-h-[90vh]">

            <!-- cabecera del modal -->
            <div class="flex items-center justify-between p-6 bg-slate-800 shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-700 border border-slate-600 flex items-center justify-center text-teal-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-black text-white tracking-wide">Ficha Maestra de Trazabilidad</h3>
                        <p class="font-mono text-[13px] font-semibold text-slate-400 mt-0.5" id="modal-subtitle">Cargando...</p>
                    </div>
                </div>
                <button type="button" onclick="closeLotDetails()" class="p-2.5 rounded-xl text-slate-400 hover:text-white bg-slate-700 hover:bg-rose-600 transition-colors cursor-pointer shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- cuerpo divido en columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 overflow-y-auto bg-white h-full">
                
                <!-- columna de detalles de origen -->
                <div class="p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-50/50">
                    <h4 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Datos Comerciales de Ingreso
                    </h4>
                    <div id="lot-details-container" class="space-y-4">
                        <div class="text-center text-slate-400 py-10 text-sm">Cargando datos...</div>
                    </div>
                </div>

                <!-- columna de linea de tiempo -->
                <div class="p-6 lg:p-8 bg-white">
                    <h4 class="text-[12px] font-extrabold text-slate-800 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Linea de Vida Fisica (Kardex)
                    </h4>
                    <div id="timeline-container" class="space-y-6">
                        <div class="text-center text-slate-400 py-10 text-sm">Construyendo linea de tiempo...</div>
                    </div>
                </div>

            </div>

            <!-- pie del modal -->
            <div class="flex justify-end p-5 border-t border-slate-200 bg-white shrink-0">
                <button type="button" onclick="closeLotDetails()" class="px-6 py-2.5 rounded-xl text-[13px] font-bold text-slate-700 bg-slate-100 border border-slate-300 hover:bg-slate-200 transition-colors shadow-sm cursor-pointer">
                    Cerrar Ficha
                </button>
            </div>
        </div>
    </div>

    <!-- script de manejo de peticiones ajax para cargar informacion en modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnToggleFilters = document.getElementById('toggle-filters-btn');
            const filtersPanel = document.getElementById('filterForm');
            if (btnToggleFilters && filtersPanel) {
                btnToggleFilters.addEventListener('click', () => filtersPanel.classList.toggle('hidden'));
            }
        });

        function openLotDetails(lote, producto) {
            document.getElementById('modal-lot-details').classList.remove('hidden');
            document.getElementById('modal-subtitle').textContent = `Lote ID: ${lote} · ${producto}`;
            
            const timelineContainer = document.getElementById('timeline-container');
            const detailsContainer = document.getElementById('lot-details-container');
            
            timelineContainer.innerHTML = `<div class="text-center text-slate-500 py-10"><svg class="animate-spin h-6 w-6 text-teal-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Cargando historia...</div>`;
            detailsContainer.innerHTML = `<div class="text-center text-slate-500 py-10">Consultando base de datos...</div>`;

            const currentPath = window.location.pathname.replace(/\/$/, '');
            const fetchUrl = `${currentPath}/timeline?lote=${encodeURIComponent(lote)}&producto=${encodeURIComponent(producto)}`;

            fetch(fetchUrl)
                .then(async response => {
                    if (!response.ok) {
                        const errText = await response.text();
                        throw new Error(errText);
                    }
                    return response.json();
                })
                .then(data => {
                    // dibujar detalles en la columna izquierda
                    if(data.details) {
                        detailsContainer.innerHTML = `
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                                <div>
                                    <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Proveedor Origen</p>
                                    <p class="text-[15px] font-black text-slate-800 mt-0.5">${data.details.proveedor}</p>
                                    <p class="text-[11px] font-bold text-slate-500 font-mono mt-0.5">RUT: ${data.details.rut_proveedor}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Doc. Recepcion</p>
                                        <p class="text-[12px] font-bold text-slate-800 mt-0.5">${data.details.documento}</p>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Orden de Compra</p>
                                        <p class="text-[12px] font-bold text-slate-800 mt-0.5 font-mono">${data.details.orden_compra}</p>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Fecha Ingreso</p>
                                        <p class="text-[12px] font-bold text-slate-800 mt-0.5">${data.details.fecha_recepcion}</p>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Valor Neto Unidad</p>
                                        <p class="text-[14px] font-black text-emerald-600 mt-0.5">${data.details.valor_unitario}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-800 p-5 rounded-2xl shadow-inner flex items-center justify-between border border-slate-700">
                                <div>
                                    <p class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Stock Fisico Restante</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Ubicacion: ${data.details.ubicacion}</p>
                                </div>
                                <p class="text-[24px] font-black text-white font-mono">${data.details.stock_actual} <span class="text-[12px] text-teal-400">UNID.</span></p>
                            </div>
                        `;
                    } else {
                        detailsContainer.innerHTML = `<div class="bg-white p-6 rounded-2xl border border-slate-200 text-center text-slate-500 text-[13px] font-medium shadow-sm">No se encontro la ficha de ingreso original para este lote. Es posible que haya sido un ingreso manual antiguo.</div>`;
                    }

                    // dibujar la linea de tiempo de movimientos en la columna derecha
                    if(!data.timeline || data.timeline.length === 0) {
                        timelineContainer.innerHTML = '<div class="text-center text-slate-500 py-10 font-medium">No hay registros de trazabilidad.</div>';
                        return;
                    }

                    let html = '<div class="relative pl-6 border-l-2 border-slate-200 space-y-7">';
                    
                    data.timeline.forEach((mov) => {
                        let badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                        let dotColor = 'bg-amber-500 ring-amber-50';
                        if (mov.tipo === 'Recepción') {
                            badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            dotColor = 'bg-emerald-500 ring-emerald-50';
                        } else if (mov.tipo === 'Despacho') {
                            badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                            dotColor = 'bg-blue-500 ring-blue-50';
                        } else if (mov.tipo === 'Despacho Controlado') {
                            badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                            dotColor = 'bg-rose-500 ring-rose-50';
                        }
                        
                        html += `
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full ${dotColor} border-2 border-white ring-4"></div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest ${badgeBg} border">
                                        ${mov.tipo} (${mov.cantidad > 0 ? '+'+mov.cantidad : mov.cantidad})
                                    </span>
                                    <p class="font-mono text-[11px] font-bold text-slate-400">${mov.fecha}</p>
                                </div>
                                <h4 class="text-[13px] font-black text-slate-800 leading-tight">${mov.origen_destino}</h4>
                                <p class="text-[11.5px] font-medium text-slate-500 mt-1">Ref: <span class="text-slate-700 font-bold">${mov.documento}</span>${mov.responsable ? ` · <span class="text-teal-700 font-semibold">Resp: ${mov.responsable}</span>` : ''}</p>
                            </div>
                        </div>`;
                    });

                    html += '</div>';
                    timelineContainer.innerHTML = html;
                })
                .catch(err => {
                    console.error("error al cargar informacion: ", err);
                    timelineContainer.innerHTML = `<div class="text-center text-rose-600 py-5"><p class="font-bold">Error de Conexion</p><p class="text-xs text-slate-500">Revisa la consola para mas detalles.</p></div>`;
                    detailsContainer.innerHTML = `<div class="text-center text-rose-600 py-5"><p class="font-bold">No se pudieron cargar los datos</p></div>`;
                });
        }

        function closeLotDetails() {
            document.getElementById('modal-lot-details').classList.add('hidden');
        }
    </script>
@endsection