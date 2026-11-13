{{-- modal: solicitudes pendientes para cesfam (solo administrador) --}}
@role('Administrador')
    <div id="PendingRequestsCesfamModal"
        class="hidden fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 transition-all"
        onclick="if(event.target === this) closePendingRequestsCesfamModal()">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-7xl max-h-[94vh] flex flex-col overflow-hidden my-auto animate-fade-in text-slate-800 dark:text-slate-100">

            <!-- cabecera del modal -->
            <div class="px-6 py-5 bg-slate-800 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-teal-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-wide">
                                Solicitudes Pendientes — CESFAM
                            </h2>
                            <span class="px-2.5 py-0.5 text-[11px] font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full">
                                <span id="cesfamBadgeCount">{{ isset($solicitudesPendientesCesfam) ? $solicitudesPendientesCesfam->count() : 0 }}</span> solicitudes
                            </span>
                        </div>
                        <p class="text-[12px] text-slate-300 mt-0.5 font-medium">
                            Gestión, filtrado independiente y emisión de pedidos pendientes para Centros de Salud Familiar (Medicamentos, Insumos y Controlados).
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closePendingRequestsCesfamModal()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer"
                    title="Cerrar ventana">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- buscador y controles de la barra superior -->
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shrink-0 space-y-3">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                    <div class="relative flex-1 max-w-lg">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="pendingCesfamFilterSearch" oninput="applyPendingCesfamFilters()"
                            placeholder="Buscar N° solicitud, CESFAM, solicitante o producto..."
                            class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-[13px] text-slate-800 dark:text-slate-100 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all shadow-sm">
                    </div>

                    <!-- controles: filtros avanzados y menu exportar listado -->
                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                        <button type="button" onclick="togglePendingCesfamAdvancedFilters()" id="btnToggleCesfamAdvanced"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-300 dark:border-slate-700 rounded-lg text-[12.5px] font-semibold text-slate-700 dark:text-slate-200 transition-all cursor-pointer shadow-sm">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            <span>Filtros Avanzados</span>
                            <span id="badgeActiveCesfamFilters" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-teal-600 text-white">0</span>
                            <svg id="iconChevronCesfamAdvanced" class="text-slate-400 transition-transform duration-200 inline-block shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <!-- menu para descargar el consolidado -->
                        <div class="relative">
                            <button type="button" onclick="togglePendingCesfamExportMenu()" id="btnPendingCesfamExport"
                                class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg text-[12.5px] font-bold shadow-sm hover:shadow transition-all cursor-pointer border border-slate-700">
                                <svg class="text-teal-400 inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/></svg>
                                <span>Descargar Consolidado</span>
                                <svg class="ml-0.5 inline-block shrink-0" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <div id="dropdownPendingCesfamExport"
                                class="hidden absolute right-0 mt-1.5 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-50 py-1.5 divide-y divide-slate-100 dark:divide-slate-700/60 animate-fade-in text-[12px]">
                                <div class="py-1">
                                    <button type="button" onclick="viewPendingCesfamPdf(); closePendingCesfamExportMenu();"
                                        class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-700 dark:hover:text-teal-300 transition-colors cursor-pointer font-medium">
                                        <svg class="text-teal-600 dark:text-teal-400 text-center inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7C3.6 7.9 7.5 5 12 5s8.4 2.9 9.94 6.65a1 1 0 0 1 0 .7C20.4 16.1 16.5 19 12 19s-8.4-2.9-9.94-6.65"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span>Ver Vista Previa</span>
                                    </button>
                                    <button type="button" onclick="downloadPendingCesfamPdf(); closePendingCesfamExportMenu();"
                                        class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-700 dark:hover:text-teal-300 transition-colors cursor-pointer font-medium">
                                        <svg class="text-teal-600 dark:text-teal-400 text-center inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                                        <span>Descargar PDF</span>
                                    </button>
                                </div>
                                <div class="py-1">
                                    <button type="button" onclick="printPendingCesfamPdf(); closePendingCesfamExportMenu();"
                                        class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-700 dark:hover:text-teal-300 transition-colors cursor-pointer font-medium">
                                        <svg class="text-slate-500 dark:text-slate-400 text-center inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="7" rx="1"/></svg>
                                        <span>Imprimir Documento</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- panel de filtros avanzados -->
                <div id="pendingCesfamAdvancedPanel" class="hidden pt-3 border-t border-slate-200 dark:border-slate-800 animate-fade-in">
                    <div class="p-3.5 bg-slate-50/80 dark:bg-slate-800/40 rounded-xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs">
                        <div class="flex flex-col md:flex-row items-end gap-3 w-full">
                            <!-- filtro cesfam -->
                            <div class="flex-1 w-full md:w-auto min-w-0 md:min-w-[260px]">
                                <label for="pendingCesfamFilterCentro" class="block text-[11px] font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider">
                                    CESFAM Solicitante
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011 1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <select id="pendingCesfamFilterCentro" onchange="applyPendingCesfamFilters()"
                                        class="w-full pl-10 pr-9 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-[13px] text-slate-800 dark:text-slate-100 font-medium focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer shadow-xs appearance-none">
                                        <option value="">Todos los CESFAM</option>
                                        @php
                                            $listaCesfamOpciones = collect($centrosPendientesCesfam ?? [])
                                                ->merge(collect($establecimientos ?? [])->pluck('nombre'))
                                                ->filter()
                                                ->unique()
                                                ->sort(SORT_NATURAL)
                                                ->values();
                                        @endphp
                                        @foreach ($listaCesfamOpciones as $centro)
                                            <option value="{{ $centro }}" title="{{ $centro }}">{{ $centro }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class=" inline-block shrink-0" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full md:w-44 shrink-0">
                                <label for="pendingCesfamFilterFechaDesde" class="block text-[11px] font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider">
                                    Fecha Desde
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="date" id="pendingCesfamFilterFechaDesde" onchange="applyPendingCesfamFilters()"
                                        class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-[13px] text-slate-800 dark:text-slate-100 font-medium focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer shadow-xs">
                                </div>
                            </div>

                            <div class="w-full md:w-44 shrink-0">
                                <label for="pendingCesfamFilterFechaHasta" class="block text-[11px] font-bold text-slate-700 dark:text-slate-200 mb-1.5 uppercase tracking-wider">
                                    Fecha Hasta
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="date" id="pendingCesfamFilterFechaHasta" onchange="applyPendingCesfamFilters()"
                                        class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-[13px] text-slate-800 dark:text-slate-100 font-medium focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer shadow-xs">
                                </div>
                            </div>

                            <div class="w-full md:w-auto shrink-0">
                                <button type="button" onclick="resetPendingCesfamFilters()"
                                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700 rounded-lg text-[12.5px] font-semibold shadow-xs hover:shadow transition-all cursor-pointer group"
                                    title="Restablecer todos los filtros avanzados">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Limpiar Filtros</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- resumen de metricas y aviso de descarga -->
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-[12px]">
                <!-- metricas del encabezado -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <span class="text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">Solicitudes:</span>
                        <span id="cesfamVisibleSolicitudesCount" class="font-extrabold text-teal-600 dark:text-teal-400 text-[13px]">{{ isset($solicitudesPendientesCesfam) ? $solicitudesPendientesCesfam->count() : 0 }}</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <span class="text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">Ítems Pendientes:</span>
                        <span id="cesfamVisibleItemsCount" class="font-extrabold text-slate-800 dark:text-slate-100 text-[13px]">{{ isset($itemsPendientesCesfam) ? $itemsPendientesCesfam->count() : 0 }}</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <span class="text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">Unidades Pendientes:</span>
                        <span id="cesfamVisibleUnitsCount" class="font-extrabold text-teal-600 dark:text-teal-400 text-[13px]">{{ isset($itemsPendientesCesfam) ? number_format($itemsPendientesCesfam->sum('cantidad_pendiente'), 0, ',', '.') : 0 }}</span>
                        <span class="text-slate-400 text-[11px]">uds</span>
                    </div>
                </div>

                <!-- aviso de descarga -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 text-teal-800 dark:text-teal-300 font-medium text-[11.5px] shadow-xs">
                    <svg class="text-teal-600 dark:text-teal-400 shrink-0 inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 18c1.5-2 2.5-2 3 0s1.5 1 2.5-1 2 .5 2.5 1"/></svg>
                    <span>Cada solicitud puede ser visualizada, descargada e impresa de forma individual con la firma oficial de su solicitante.</span>
                </div>
            </div>

            <!-- tabla de solicitudes pendientes -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3">
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900">
                    <table class="w-full text-left text-[12.5px] border-collapse" id="pendingCesfamTable">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 font-bold uppercase text-[11px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                                <th class="py-3 px-3 w-12 text-center">#</th>
                                <th class="py-3 px-3">N° Solicitud</th>
                                <th class="py-3 px-3">Fecha</th>
                                <th class="py-3 px-3">CESFAM</th>
                                <th class="py-3 px-3">Solicitante</th>
                                <th class="py-3 px-3 text-center">Firma</th>
                                <th class="py-3 px-3 text-center">Total Solicitado</th>
                                <th class="py-3 px-3 text-center w-36">Descarga</th>
                                <th class="py-3 px-3 text-center w-12">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @if(isset($solicitudesPendientesCesfam) && $solicitudesPendientesCesfam->isNotEmpty())
                                @foreach($solicitudesPendientesCesfam as $idx => $sol)
                                    @php
                                        $items = $sol->itemsPendientes ?? collect();
                                        $progsList = $items->pluck('programa')->filter()->unique()->implode('|');
                                        $prodsList = $items->map(fn($i) => $i->product->nombre ?? '')->filter()->unique()->implode('|');
                                        $totalUnidades = (int) $items->sum('cantidad_pendiente');
                                        $fechaRaw = $sol->fecha_solicitud ? \Carbon\Carbon::parse($sol->fecha_solicitud)->format('Y-m-d') : '';
                                        $fechaFormat = $sol->fecha_solicitud ? \Carbon\Carbon::parse($sol->fecha_solicitud)->format('d/m/Y') : '-';
                                    @endphp
                                    <tr class="pending-cesfam-row hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
                                        id="cesfam-row-{{ $sol->id }}"
                                        data-solicitud-id="{{ $sol->id }}"
                                        data-correlativo="{{ mb_strtolower($sol->numero_solicitud ?? '') }}"
                                        data-centro="{{ mb_strtolower($sol->establecimiento ?? '') }}"
                                        data-fecha="{{ $fechaRaw }}"
                                        data-programas="{{ mb_strtolower($progsList) }}"
                                        data-productos="{{ mb_strtolower($prodsList) }}"
                                        data-items-count="{{ $items->count() }}"
                                        data-unidades-count="{{ $totalUnidades }}"
                                        data-search="{{ mb_strtolower(($sol->numero_solicitud ?? '') . ' ' . ($sol->establecimiento ?? '') . ' ' . ($sol->responsable ?? '') . ' ' . ($sol->cargo ?? '') . ' ' . $prodsList . ' ' . $progsList) }}">
                                        
                                        <td class="py-3 px-3 text-center font-bold text-slate-400 dark:text-slate-500">
                                            {{ $idx + 1 }}
                                        </td>
                                        
                                        <td class="py-3 px-3 font-mono font-bold text-teal-700 dark:text-teal-400">
                                            {{ $sol->numero_solicitud ?: ('#ID-' . $sol->id) }}
                                        </td>
                                        
                                        <td class="py-3 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                            {{ $fechaFormat }}
                                        </td>
                                        
                                        <td class="py-3 px-3 font-semibold text-slate-800 dark:text-slate-100">
                                            {{ $sol->establecimiento }}
                                        </td>
                                        
                                        <td class="py-3 px-3">
                                            <div class="font-medium text-slate-800 dark:text-slate-100">{{ $sol->responsable ?: 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $sol->cargo ?: 'Solicitante' }}</div>
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            @if(!empty($sol->firma_solicitante))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800"
                                                    title="Firma digital registrada">
                                                    <svg class=" inline-block shrink-0" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M3 17c3-6 5-9 6-8s-2 8 0 8 3-5 5-5 1 5 3 5 2-2 4-2"/></svg> Registrada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700"
                                                    title="Sin firma digital previa">
                                                    Pendiente
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-flex flex-col items-center">
                                                <strong class="text-slate-800 dark:text-slate-100 font-extrabold text-[13px]">{{ number_format($totalUnidades, 0, ',', '.') }} uds</strong>
                                                <span class="text-[10.5px] text-slate-500 dark:text-slate-400">{{ $items->count() }} {{ $items->count() === 1 ? 'producto' : 'productos' }}</span>
                                            </span>
                                        </td>

                                        <!-- acciones individuales: ver, descargar, imprimir -->
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1 bg-slate-50 dark:bg-slate-800 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
                                                <button type="button" onclick="viewSingleCesfamPdf({{ $sol->id }})"
                                                    class="p-1.5 text-teal-600 dark:text-teal-400 hover:text-white hover:bg-teal-600 rounded-md transition-colors cursor-pointer"
                                                    title="Ver PDF oficial de la Solicitud {{ $sol->numero_solicitud }}">
                                                    <svg class=" inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7C3.6 7.9 7.5 5 12 5s8.4 2.9 9.94 6.65a1 1 0 0 1 0 .7C20.4 16.1 16.5 19 12 19s-8.4-2.9-9.94-6.65"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>

                                                <button type="button" onclick="downloadSingleCesfamPdf({{ $sol->id }})"
                                                    class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-white hover:bg-slate-800 rounded-md transition-colors cursor-pointer"
                                                    title="Descargar PDF oficial de la Solicitud {{ $sol->numero_solicitud }}">
                                                    <svg class=" inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                                                </button>

                                                <button type="button" onclick="printSingleCesfamPdf({{ $sol->id }})"
                                                    class="p-1.5 text-slate-600 dark:text-slate-300 hover:text-white hover:bg-slate-800 rounded-md transition-colors cursor-pointer"
                                                    title="Imprimir directamente Solicitud {{ $sol->numero_solicitud }}">
                                                    <svg class=" inline-block shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="7" rx="1"/></svg>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center">
                                            <button type="button" onclick="toggleCesfamDetails({{ $sol->id }})"
                                                id="btn-toggle-cesfam-{{ $sol->id }}"
                                                class="p-1.5 text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors cursor-pointer"
                                                title="Ver detalle de productos solicitados">
                                                <svg id="icon-toggle-cesfam-{{ $sol->id }}" class="transform transition-transform duration-200 inline-block shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-0.125em;transition:transform .2s" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- fila desplegable con detalle de productos -->
                                    <tr id="cesfam-detail-{{ $sol->id }}" class="hidden bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
                                        <td colspan="9" class="p-3 sm:p-4">
                                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-3 sm:p-4 shadow-inner">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="text-[12px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                                        Detalle de Productos Solicitados — Solicitud {{ $sol->numero_solicitud }}
                                                    </h4>
                                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                                        {{ $sol->establecimiento }} · Solicitante: {{ $sol->responsable }}
                                                    </span>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left text-[11.5px]">
                                                        <thead>
                                                            <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 uppercase text-[10px] bg-slate-50 dark:bg-slate-800/60">
                                                                <th class="py-1.5 px-2">#</th>
                                                                <th class="py-1.5 px-2">Producto / Insumo</th>
                                                                <th class="py-1.5 px-2">Programa</th>
                                                                <th class="py-1.5 px-2 text-right">Solicitado</th>
                                                                <th class="py-1.5 px-2 text-right">Despachado</th>
                                                                <th class="py-1.5 px-2 text-right font-bold text-teal-700 dark:text-teal-400">Pendiente</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                                            @foreach($items as $iIdx => $it)
                                                                <tr>
                                                                    <td class="py-1.5 px-2 text-slate-400">{{ $iIdx + 1 }}</td>
                                                                    <td class="py-1.5 px-2 font-semibold text-slate-800 dark:text-slate-100">{{ $it->product->nombre ?? 'N/A' }}</td>
                                                                    <td class="py-1.5 px-2"><span class="px-2 py-0.5 rounded text-[10.5px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $it->programa ?: 'General' }}</span></td>
                                                                    <td class="py-1.5 px-2 text-right text-slate-600 dark:text-slate-300">{{ number_format($it->cantidad_solicitada, 0, ',', '.') }}</td>
                                                                    <td class="py-1.5 px-2 text-right text-slate-500 dark:text-slate-400">{{ number_format($it->cantidad_despachada, 0, ',', '.') }}</td>
                                                                    <td class="py-1.5 px-2 text-right font-bold text-teal-600 dark:text-teal-400">{{ number_format($it->cantidad_pendiente, 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr id="cesfamEmptyRow">
                                    <td colspan="9" class="py-12 text-center text-slate-400 dark:text-slate-500 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>No existen solicitudes pendientes para CESFAM en este momento.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr id="cesfamNoFilterResultsRow" class="hidden">
                                <td colspan="9" class="py-12 text-center text-slate-400 dark:text-slate-500 italic">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                        </svg>
                                        <span>No se encontraron solicitudes que coincidan con los filtros seleccionados.</span>
                                        <button type="button" onclick="resetPendingCesfamFilters()"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-xs hover:bg-slate-100 dark:hover:bg-slate-700 transition-all mt-2 cursor-pointer not-italic">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span>Limpiar Filtros</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- pie del modal -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/90 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center shrink-0">
                <span class="text-[11.5px] text-slate-500 dark:text-slate-400 font-medium">
                    Red de Salud DAS Talcahuano · Solicitudes de Pedido Oficiales (CESFAM)
                </span>
                <button type="button" onclick="closePendingRequestsCesfamModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg text-[12.5px] font-bold transition-all cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- iframe oculto para imprimir el pdf sin salir de la vista -->
    <iframe id="pendingCesfamPrintIframe" class="hidden" style="display:none; width:0; height:0; border:0;"></iframe>

    <script>
        function openPendingRequestsCesfamModal() {
            const modal = document.getElementById('PendingRequestsCesfamModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                applyPendingCesfamFilters();
            }
        }

        function closePendingRequestsCesfamModal() {
            const modal = document.getElementById('PendingRequestsCesfamModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function togglePendingCesfamAdvancedFilters() {
            const panel = document.getElementById('pendingCesfamAdvancedPanel');
            const icon = document.getElementById('iconChevronCesfamAdvanced');
            const btn = document.getElementById('btnToggleCesfamAdvanced');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                    if (btn) btn.classList.add('border-teal-500', 'text-teal-600', 'dark:text-teal-400');
                } else {
                    panel.classList.add('hidden');
                    if (icon) icon.classList.remove('rotate-180');
                    if (btn) btn.classList.remove('border-teal-500', 'text-teal-600', 'dark:text-teal-400');
                }
            }
        }

        function togglePendingCesfamExportMenu() {
            const dropdown = document.getElementById('dropdownPendingCesfamExport');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        function closePendingCesfamExportMenu() {
            const dropdown = document.getElementById('dropdownPendingCesfamExport');
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            const btn = document.getElementById('btnPendingCesfamExport');
            const dropdown = document.getElementById('dropdownPendingCesfamExport');
            if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function toggleCesfamDetails(id) {
            const detailRow = document.getElementById('cesfam-detail-' + id);
            const icon = document.getElementById('icon-toggle-cesfam-' + id);
            if (detailRow) {
                const isHidden = detailRow.classList.contains('hidden');
                if (isHidden) {
                    detailRow.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                } else {
                    detailRow.classList.add('hidden');
                    if (icon) icon.classList.remove('rotate-180');
                }
            }
        }

        function applyPendingCesfamFilters() {
            const centroVal = (document.getElementById('pendingCesfamFilterCentro')?.value || '').trim().toLowerCase();
            const fechaDesdeVal = (document.getElementById('pendingCesfamFilterFechaDesde')?.value || '').trim();
            const fechaHastaVal = (document.getElementById('pendingCesfamFilterFechaHasta')?.value || '').trim();
            const searchVal = (document.getElementById('pendingCesfamFilterSearch')?.value || '').trim().toLowerCase();

            let activeFiltersCount = 0;
            if (centroVal) activeFiltersCount++;
            if (fechaDesdeVal) activeFiltersCount++;
            if (fechaHastaVal) activeFiltersCount++;

            const badge = document.getElementById('badgeActiveCesfamFilters');
            if (badge) {
                if (activeFiltersCount > 0) {
                    badge.textContent = activeFiltersCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            const rows = document.querySelectorAll('.pending-cesfam-row');
            let visibleCount = 0;
            let totalItems = 0;
            let totalUnits = 0;

            rows.forEach(row => {
                const rowCentro = row.getAttribute('data-centro') || '';
                const rowFecha = row.getAttribute('data-fecha') || '';
                const rowSearch = row.getAttribute('data-search') || '';
                const itemsCount = parseInt(row.getAttribute('data-items-count') || '0', 10);
                const unitsCount = parseInt(row.getAttribute('data-unidades-count') || '0', 10);
                const solId = row.getAttribute('data-solicitud-id');

                let match = true;

                if (centroVal && rowCentro !== centroVal) {
                    match = false;
                }

                if (fechaDesdeVal && rowFecha && rowFecha < fechaDesdeVal) {
                    match = false;
                }

                if (fechaHastaVal && rowFecha && rowFecha > fechaHastaVal) {
                    match = false;
                }

                if (searchVal && !rowSearch.includes(searchVal)) {
                    match = false;
                }

                if (match) {
                    row.classList.remove('hidden');
                    visibleCount++;
                    totalItems += itemsCount;
                    totalUnits += unitsCount;
                } else {
                    row.classList.add('hidden');
                    const detailRow = document.getElementById('cesfam-detail-' + solId);
                    if (detailRow) detailRow.classList.add('hidden');
                    const icon = document.getElementById('icon-toggle-cesfam-' + solId);
                    if (icon) icon.classList.remove('rotate-180');
                }
            });

            const noResults = document.getElementById('cesfamNoFilterResultsRow');
            if (noResults) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }

            const visibleCountEl = document.getElementById('cesfamVisibleSolicitudesCount');
            if (visibleCountEl) visibleCountEl.textContent = visibleCount;

            const visibleItemsEl = document.getElementById('cesfamVisibleItemsCount');
            if (visibleItemsEl) visibleItemsEl.textContent = totalItems.toLocaleString('es-CL');

            const visibleUnitsEl = document.getElementById('cesfamVisibleUnitsCount');
            if (visibleUnitsEl) visibleUnitsEl.textContent = totalUnits.toLocaleString('es-CL');
        }

        function resetPendingCesfamFilters() {
            const centro = document.getElementById('pendingCesfamFilterCentro');
            const fechaDesde = document.getElementById('pendingCesfamFilterFechaDesde');
            const fechaHasta = document.getElementById('pendingCesfamFilterFechaHasta');
            const search = document.getElementById('pendingCesfamFilterSearch');

            if (centro) centro.value = '';
            if (fechaDesde) fechaDesde.value = '';
            if (fechaHasta) fechaHasta.value = '';
            if (search) search.value = '';

            applyPendingCesfamFilters();
        }

        function buildPendingCesfamQueryString(extra = {}) {
            const params = new URLSearchParams();
            const centro = (document.getElementById('pendingCesfamFilterCentro')?.value || '').trim();
            const fechaDesde = (document.getElementById('pendingCesfamFilterFechaDesde')?.value || '').trim();
            const fechaHasta = (document.getElementById('pendingCesfamFilterFechaHasta')?.value || '').trim();

            if (centro) params.append('centro', centro);
            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);

            for (const [k, v] of Object.entries(extra)) {
                if (v !== undefined && v !== null) {
                    params.append(k, v);
                }
            }

            const qs = params.toString();
            return qs ? ('?' + qs) : '';
        }

        // acciones individuales
        function viewSingleCesfamPdf(solicitudId) {
            const url = "{{ route('requests.pending.cesfam.pdf') }}?solicitud_id=" + solicitudId;
            window.open(url, '_blank');
        }

        function downloadSingleCesfamPdf(solicitudId) {
            const url = "{{ route('requests.pending.cesfam.pdf') }}?solicitud_id=" + solicitudId + "&download=1";
            window.location.href = url;
        }

        function printSingleCesfamPdf(solicitudId) {
            const url = "{{ route('requests.pending.cesfam.pdf') }}?solicitud_id=" + solicitudId;
            const iframe = document.getElementById('pendingCesfamPrintIframe');
            if (iframe) {
                iframe.src = url;
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        window.open(url, '_blank');
                    }
                };
            } else {
                window.open(url, '_blank');
            }
        }

        // acciones globales batch
        function viewPendingCesfamPdf() {
            const url = "{{ route('requests.pending.cesfam.pdf') }}" + buildPendingCesfamQueryString();
            window.open(url, '_blank');
        }

        function downloadPendingCesfamPdf() {
            const url = "{{ route('requests.pending.cesfam.pdf') }}" + buildPendingCesfamQueryString({ download: 1 });
            window.location.href = url;
        }

        function printPendingCesfamPdf() {
            const url = "{{ route('requests.pending.cesfam.pdf') }}" + buildPendingCesfamQueryString();
            const iframe = document.getElementById('pendingCesfamPrintIframe');
            if (iframe) {
                iframe.src = url;
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        window.open(url, '_blank');
                    }
                };
            } else {
                window.open(url, '_blank');
            }
        }
    </script>
@endrole
