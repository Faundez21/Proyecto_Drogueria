@extends('layouts.app')

@section('title', 'Distribución de la Droguería')

@section('subtitle', 'Distribución física y organización de la bodega')

@section('actions')
    <a href="{{ route('shelf.index') }}"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-[#0f968c] hover:bg-teal-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M4 4h16M4 12h16">
            </path>
        </svg>
        Gestionar Estanterías
    </a>
@endsection

@section('content')
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">03</span>
        <span>Salida</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Distribución</span>
    </div>

    <!-- Scripts requeridos -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <!--nueva libreria para mover el layout-->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/DragControls.js"></script>

    <style>
        #warehouse-wrapper {
            position: relative;
            width: 100%;
        }

        #warehouse-3d-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .glass-panel {
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid #1e293b;
        }

        /* Estilos para Etiquetas Flotantes (Markers) y sus colores según imagen */
        .rack-marker {
            position: absolute;
            transform: translate(-50%, -100%);
            pointer-events: none;
            z-index: 10;
            transition: opacity 0.2s;
        }

        .rack-marker::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px 5px 0;
            border-style: solid;
        }

        /* Colores exactos */
        .marker-green {
            background-color: #22c55e;
            color: white;
        }

        .marker-green::after {
            border-color: #22c55e transparent transparent transparent;
        }

        .marker-blue {
            background-color: #3b82f6;
            color: white;
        }

        .marker-blue::after {
            border-color: #3b82f6 transparent transparent transparent;
        }

        .marker-orange {
            background-color: #f97316;
            color: white;
        }

        .marker-orange::after {
            border-color: #f97316 transparent transparent transparent;
        }

        .marker-purple {
            background-color: #a855f7;
            color: white;
        }

        .marker-purple::after {
            border-color: #a855f7 transparent transparent transparent;
        }

        .marker-gray {
            background-color: #64748b;
            color: white;
        }

        .marker-gray::after {
            border-color: #64748b transparent transparent transparent;
        }
    </style>

    <div class="w-full">
        <!-- Sección de Mantenedores (Enlaces Rápidos) -->
        <div class="mb-8">
            <!-- BOTONES MANTENEDORES -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- BOTÓN PASILLO -->
                <a href="{{ route('aisle.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">

                    <div class="flex items-center gap-3">

                        <!-- ICONO PASILLO -->
                        <div
                            class="w-16 h-16 bg-orange-100 border border-orange-200 rounded-full flex items-center justify-center shrink-0">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 403 403" fill="none"
                                stroke="currentColor" stroke-width="9" stroke-linecap="round" stroke-linejoin="round"
                                class="w-10 h-10 text-orange-600">

                                <path d="M64 115 H116 L153 153 V323 H64 Z" />
                                <path d="M116 115 V323" />
                                <path d="M116 183 L153 201" />
                                <path d="M116 248 L153 267" />
                                <path d="M80 180 H99" />
                                <path d="M80 256 H99" />

                                <path d="M339 115 H287 L250 153 V323 H339 Z" />
                                <path d="M287 115 V323" />
                                <path d="M287 183 L250 201" />
                                <path d="M287 248 L250 267" />
                                <path d="M304 180 H323" />
                                <path d="M304 256 H323" />

                                <path d="M155 71 H229 V117 H155 Z" />
                                <path d="M183 117 V101 H201 V117" />
                                <path d="M183 117 L192 132 L201 117" />

                            </svg>

                        </div>

                        <div class="w-[70%]">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">
                                Pasillos
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                Administración de pasillos
                            </p>
                        </div>

                    </div>

                </a>

                <!-- BOTÓN ESTANTERÍA -->
                <a href="{{ route('shelf.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">

                    <div class="flex items-center gap-3">

                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-16 h-16 bg-blue-200 border border-blue-300 rounded-full p-3 text-blue-600">

                            <path d="M12 12V9a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3" />
                            <path d="M16 20v-3a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v3" />
                            <path d="M20 22V2" />
                            <path d="M4 12h16" />
                            <path d="M4 20h16" />
                            <path d="M4 2v20" />
                            <path d="M4 4h16" />

                        </svg>

                        <div class="w-[70%]">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">
                                Estanterías
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                Administración de estanterías
                            </p>
                        </div>

                    </div>

                </a>


                <!-- BOTÓN NIVEL -->
                <a href="{{ route('level.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">

                    <div class="flex items-center gap-3">

                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-16 h-16 bg-purple-200 border border-purple-300 rounded-full p-3 text-purple-600">

                            <path d="M2 8h20" />
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="M6 16h12" />

                        </svg>

                        <div class="w-[70%]">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">
                                Niveles
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                Administración de niveles
                            </p>
                        </div>

                    </div>

                </a>


                <!-- BOTÓN POSICIÓN -->
                <a href="{{ route('position.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">

                    <div class="flex items-center gap-3">

                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-16 h-16 bg-orange-200 border border-orange-300 rounded-full p-3 text-orange-600">

                            <path
                                d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                            <circle cx="12" cy="10" r="3" />

                        </svg>

                        <div class="w-[70%]">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">
                                Posiciones
                            </h3>

                            <p class="text-gray-500 text-sm mt-1">
                                Administración de posiciones
                            </p>
                        </div>

                    </div>

                </a>

            </div>


            <!-- Visor 3D Interactivo -->
            <div class="w-full bg-white border border-gray-200 rounded-lg p-3 sm:p-6 shadow-sm mt-3">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 hidden sm:block">Vista de almacenamiento</h2>

                <!-- Contenedor Wrapper para Three.js y Alpine.js -->
                <div id="warehouse-wrapper" x-data="warehouseApp()"
                    class="h-[500px] sm:h-[600px] lg:h-[750px] rounded-xl overflow-hidden border border-gray-300 bg-[#eef2f6] select-none">

                    <!-- Capa HTML para etiquetas (Markers) -->
                    <div id="labels-container"
                        class="absolute top-0 left-0 w-full h-full pointer-events-none z-10 overflow-hidden">
                    </div>

                    <!-- Capa WebGL 3D -->
                    <div id="warehouse-3d-container"></div>

                    <!-- Botonera Superior -->
                    <header
                        class="absolute top-0 left-0 w-full z-20 p-3 sm:p-5 pointer-events-none flex justify-between items-start">
                        <div class="pointer-events-auto">
                            <div
                                class="inline-flex bg-white/90 backdrop-blur p-1 rounded-lg sm:rounded-full border border-gray-200 shadow-sm">
                                <button @click="changeView('2D')"
                                    :class="viewMode === '2D' ? 'bg-white shadow text-gray-800' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">2D</button>
                                <button @click="changeView('3D')"
                                    :class="viewMode === '3D' ? 'bg-blue-500 text-white shadow' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">3D</button>
                            </div>
                        </div>

                        <div class="pointer-events-auto flex gap-1.5 sm:gap-2">
                            <button @click="zoomOut()" title="Alejar"
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition hidden sm:flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                    </path>
                                </svg>
                            </button>
                            <button @click="zoomIn()" title="Acercar"
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition hidden sm:flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4">
                                    </path>
                                </svg>
                            </button>
                            <button @click="toggleFullScreen()" title="Pantalla Completa"
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </header>

                    <!-- Sidebar Colapsable (Selector, Búsqueda y Leyenda exacto a la imagen) -->
                    <aside
                        class="absolute top-16 sm:top-24 left-3 sm:left-6 w-48 sm:w-56 glass-panel text-white rounded-xl shadow-2xl z-20 flex flex-col pointer-events-auto transition-all duration-300">
                        <!-- Botón para colapsar -->
                        <div @click="sidebarOpen = !sidebarOpen"
                            class="flex justify-between items-center p-3 cursor-pointer hover:bg-slate-800/50 rounded-t-xl"
                            :class="{ 'rounded-b-xl': !sidebarOpen }">
                            <span class="text-xs sm:text-sm font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                    </path>
                                </svg>
                                Panel de Control
                            </span>
                            <svg :class="{ 'rotate-180': !sidebarOpen }"
                                class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                                </path>
                            </svg>
                        </div>

                        <!-- Contenido interior del Sidebar -->
                        <div x-show="sidebarOpen" x-transition
                            class="p-3 sm:p-4 pt-0 border-t border-slate-700/50 mt-1 flex flex-col gap-3">
                            <div class="relative">
                                <select style="background-image: none;"
                                    class="w-full bg-[#1e293b] border border-slate-700 rounded-lg p-2.5 text-xs sm:text-sm text-white font-medium focus:outline-none focus:border-blue-500 cursor-pointer appearance-none">
                                    <option>Nivel 1</option>
                                    <option>Nivel 2</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" x-model="searchQuery" @input="handleSearch"
                                    placeholder="Buscar ubicación... (Ej: A-01)"
                                    class="w-full bg-[#1e293b] border border-slate-700 rounded-lg py-2.5 pl-9 pr-3 text-xs sm:text-sm text-gray-300 focus:outline-none focus:border-blue-500 placeholder-gray-500 transition">
                            </div>

                            <!-- Leyenda de ocupación -->
                            <div class="flex flex-col gap-3 mt-2 text-xs sm:text-sm font-medium text-gray-300">
                                <div class="flex items-center gap-3"><span
                                        class="w-3.5 h-3.5 rounded bg-[#22c55e]"></span>
                                    Ocupación baja (< 50%)</div>
                                <div class="flex items-center gap-3"><span
                                        class="w-3.5 h-3.5 rounded bg-[#f97316]"></span>
                                    Ocupación media (50-79%)</div>
                                <div class="flex items-center gap-3"><span
                                        class="w-3.5 h-3.5 rounded bg-[#ef4444]"></span>
                                    Ocupación crítica (&ge; 80%)</div>
                                <div class="flex items-center gap-3"><span
                                        class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span>
                                    Sin capacidad definida</div>
                            </div>
                        </div>
                    </aside>

                    <!-- Tooltip (Panel de Detalle Flotante) -->
                    <div x-show="tooltipVisible" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;"
                        class="absolute bottom-4 left-4 right-4 sm:bottom-auto sm:left-auto sm:top-24 sm:right-6 sm:w-72 glass-panel text-white rounded-xl p-4 sm:p-5 shadow-2xl z-30 pointer-events-auto border border-gray-700">

                        <div
                            class="border-b border-gray-700 pb-2 sm:pb-3 mb-2 sm:mb-3 relative flex justify-between items-center">
                            <span class="font-bold text-base sm:text-lg tracking-wide text-white"
                                x-text="rackData.code"></span>
                            <button @click="tooltipVisible = false"
                                class="text-gray-400 hover:text-white bg-slate-800 rounded p-1">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-xs sm:text-sm space-y-1 sm:space-y-2 text-gray-300">
                            <p class="font-medium text-white" x-text="rackData.aisle ? 'Pasillo: ' + rackData.aisle : ''"></p>

                            <!-- Ocupación -->
                            <div class="bg-slate-800/70 border border-slate-700 rounded-lg px-2.5 py-2">
                                <div class="flex justify-between items-center text-[11px] font-bold text-gray-300">
                                    <span x-text="rackData.ocupacionLabel"></span>
                                    <span class="font-mono" x-text="rackData.pct !== null ? rackData.pct + '%' : 'Sin límite'"></span>
                                </div>
                                <div class="mt-1.5 h-2 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        :style="'width:' + (rackData.pct ?? 0) + '%; background-color:' + (rackData.pct >= 80 ? '#ef4444' : (rackData.pct >= 50 ? '#f97316' : '#22c55e'))"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 font-mono">
                                    <span x-text="rackData.stock.toLocaleString('es-CL')"></span>
                                    <span x-show="rackData.capacidad > 0" x-text="'/ ' + rackData.capacidad.toLocaleString('es-CL') + ' u'"></span>
                                </p>
                            </div>

                            <template x-if="rackData.items && rackData.items.length > 1">
                                <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1">
                                    <template x-for="(item, idx) in rackData.items" :key="idx">
                                        <div class="flex justify-between items-center gap-2 bg-slate-800/70 border border-slate-700 rounded-lg px-2.5 py-1.5">
                                            <div class="min-w-0">
                                                <p class="font-semibold text-white truncate" x-text="item.producto"></p>
                                                <p class="text-[10px] text-gray-400 font-mono truncate">
                                                    Lote: <span x-text="item.lote"></span>
                                                    <span x-show="item.nivel"> · Niv: <span x-text="item.nivel"></span></span>
                                                    <span x-show="item.posicion"> · Pos: <span x-text="item.posicion"></span></span>
                                                </p>
                                                <p class="text-[10px] text-blue-400 font-semibold" x-show="item.programa" x-text="item.programa"></p>
                                            </div>
                                            <span class="text-emerald-400 font-bold text-xs shrink-0" x-text="item.cantidad + ' u'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!rackData.items || rackData.items.length === 0">
                                <p class="text-gray-400 text-xs">No hay medicamentos disponibles en esta estantería.</p>
                            </template>
                        </div>

                        <div class="mt-3 sm:mt-4 mb-3 sm:mb-4">
                            <span class="px-2 py-1 text-[10px] sm:text-xs font-semibold rounded-md border inline-block"
                                :class="{
                                    'bg-green-900/40 text-green-400 border-green-800': rackData.pct !== null && rackData.pct < 50,
                                    'bg-orange-900/40 text-orange-400 border-orange-800': rackData.pct !== null && rackData.pct >= 50 && rackData.pct < 80,
                                    'bg-rose-900/40 text-rose-400 border-rose-800': rackData.pct !== null && rackData.pct >= 80,
                                    'bg-blue-900/40 text-blue-400 border-blue-800': rackData.pct === null
                                }"
                                x-text="rackData.ocupacionLabel"></span>
                        </div>
                        <div class="flex gap-2">
                            <button @click.stop="habilitarMovimiento()"
                                class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 border border-blue-800 text-xs sm:text-sm text-white font-medium rounded-lg transition shadow">
                                Mover
                            </button>
                            <button @click="verDetalle()"
                                class="flex-1 py-2 bg-[#1e293b] hover:bg-slate-700 border border-gray-600 text-xs sm:text-sm text-gray-200 font-medium rounded-lg transition shadow-inner">
                                Ver detalle
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Stats Bar (Estadísticas globales) -->
                    <div
                        class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-[95%] max-w-4xl glass-panel text-white rounded-xl p-4 shadow-2xl z-20 hidden lg:flex justify-between items-center px-8 divide-x divide-gray-700">
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-gray-400"><svg class="w-5 h-5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg></div>
                            <div>
                                <p class="text-[10px] xl:text-xs text-gray-400">Ubicaciones</p>
                                <p class="text-base xl:text-lg font-bold">48</p>
                            </div>
                        </div>
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-blue-400 border border-blue-900/50"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg></div>
                            <div class="flex-1 flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] xl:text-xs text-gray-400">Ocupación</p>
                                    <p class="text-base xl:text-lg font-bold">68%</p>
                                </div>
                                <span class="text-xs text-gray-400">32/48</span>
                            </div>
                        </div>
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-purple-400"><svg class="w-5 h-5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg></div>
                            <div>
                                <p class="text-[10px] xl:text-xs text-gray-400">Cap. Total</p>
                                <p class="text-base xl:text-lg font-bold">2,500 m³</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script de Integración de Lógica (Alpine.js y Three.js) -->
        <script>
            function warehouseApp() {
                return {
                    viewMode: '3D',
                    sidebarOpen: window.innerWidth >= 640, // Abierto en PC/Tablet, cerrado en celular
                    tooltipVisible: false,
                    rackData: {},
                    searchQuery: '',
                    init() {
                        this.initThreeJS();

                        window.addEventListener('rack-selected', (e) => {
                            this.rackData = e.detail;
                            this.tooltipVisible = true;
                        });
                        window.addEventListener('click-empty', () => {
                            this.tooltipVisible = false;
                        });
                    },
                    changeView(mode) {
                        this.viewMode = mode;
                        window.dispatchEvent(new CustomEvent('update-camera-view', {
                            detail: mode
                        }));
                    },
                    zoomIn() {
                        window.dispatchEvent(new Event('cam-zoom-in'));
                    },
                    zoomOut() {
                        window.dispatchEvent(new Event('cam-zoom-out'));
                    },
                    toggleFullScreen() {
                        const wrapper = document.getElementById('warehouse-wrapper');
                        if (!document.fullscreenElement) {
                            wrapper.requestFullscreen().catch(err => console.error(err));
                        } else {
                            document.exitFullscreen();
                        }
                    },
                    handleSearch() {
                        window.dispatchEvent(new CustomEvent('search-rack', {
                            detail: this.searchQuery
                        }));
                    },
                    verDetalle() {
                        window.location.href = "{{ route('inventario.index') }}?estante=" + encodeURIComponent(this.rackData.code);
                    },
                    habilitarMovimiento() {
                        this.tooltipVisible = false; // Ocultamos el panel para que no estorbe
                        window.dispatchEvent(new Event('start-moving')); // Le avisamos a Three.js
                    },

                    // LÓGICA THREE.JS
                    initThreeJS() {
                        const wrapper = document.getElementById('warehouse-wrapper');
                        const container = document.getElementById('warehouse-3d-container');
                        const labelsContainer = document.getElementById('labels-container');

                        const scene = new THREE.Scene();
                        scene.background = new THREE.Color('#f8fafc');

                        const camera = new THREE.PerspectiveCamera(45, wrapper.clientWidth / wrapper.clientHeight, 1, 1000);
                        const defaultCamPos = new THREE.Vector3(70, 75, 80);
                        camera.position.copy(defaultCamPos);

                        const renderer = new THREE.WebGLRenderer({
                            antialias: true,
                            powerPreference: "high-performance"
                        });
                        renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
                        renderer.shadowMap.enabled = true;
                        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
                        container.appendChild(renderer.domElement);

                        const controls = new THREE.OrbitControls(camera, renderer.domElement);
                        const defaultTarget = new THREE.Vector3(0, -5, 0);
                        controls.target.copy(defaultTarget);
                        controls.maxPolarAngle = Math.PI / 2 - 0.05;
                        controls.enableDamping = true;
                        controls.dampingFactor = 0.05;

                        // Iluminación
                        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
                        scene.add(ambientLight);
                        const dirLight = new THREE.DirectionalLight(0xffffff, 0.6);
                        dirLight.position.set(40, 70, 30);
                        dirLight.castShadow = true;
                        dirLight.shadow.mapSize.width = 2048;
                        dirLight.shadow.mapSize.height = 2048;
                        scene.add(dirLight);

                        // --- ESTRUCTURA BASE (BORDES Y SUELO DE LA BODEGA) ---
                        const buildGroup = new THREE.Group();

                        // Suelo unificado (80x50)
                        const floorMat = new THREE.MeshStandardMaterial({
                            color: '#cbd5e1',
                            roughness: 0.8
                        });
                        const floor = new THREE.Mesh(new THREE.BoxGeometry(80, 1, 50), floorMat);
                        floor.position.set(0, -0.5, 0);
                        floor.receiveShadow = true;
                        buildGroup.add(floor);

                        // Materiales para las paredes (Cristal semi-transparente + Zócalo sólido)
                        const wallMat = new THREE.MeshPhysicalMaterial({
                            color: '#94a3b8',
                            transparent: true,
                            opacity: 0.25,
                            roughness: 0.1,
                            side: THREE.DoubleSide
                        });
                        const wallSolidMat = new THREE.MeshStandardMaterial({
                            color: '#64748b'
                        });

                        function createBorderWall(w, h, d, x, y, z) {
                            // Pared transparente superior
                            const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
                            wall.position.set(x, y + 1, z); // Subir un poco para dar espacio al zócalo
                            buildGroup.add(wall);

                            // Zócalo inferior (base sólida de la pared)
                            const baseHeight = 1.5;
                            const base = new THREE.Mesh(new THREE.BoxGeometry(w, baseHeight, d), wallSolidMat);
                            base.position.set(x, baseHeight / 2, z);
                            buildGroup.add(base);
                        }

                        // Generar 4 paredes perimetrales alrededor del piso (80x50)
                        createBorderWall(80, 10, 1, 0, 4, -25); // Pared Norte (Atrás)
                        createBorderWall(80, 10, 1, 0, 4, 25); // Pared Sur (Frente)
                        createBorderWall(1, 10, 50, 40, 4, 0); // Pared Este (Derecha)
                        createBorderWall(1, 10, 50, -40, 4, 0); // Pared Oeste (Izquierda)

                        scene.add(buildGroup);
                        // ------------------------------------------------------

                        // Arrays de gestión para los Racks
                        const racks = [];
                        const markers = [];




                        // Convertimos la variable de Laravel a un objeto de JavaScript
                        const dbShelves = @json($shelves);

                        // Stock real del inventario agrupado por estante (ubicacion fisica)
                        const stockData = @json($stock);
                        const stockByShelf = {};
                        stockData.forEach(item => {
                            const key = (item.estante ?? '').trim();
                            if (!key) return;
                            if (!stockByShelf[key]) stockByShelf[key] = [];
                            stockByShelf[key].push({
                                producto: item.producto,
                                lote: item.lote ?? 'S/L',
                                nivel: item.nivel ?? '',
                                posicion: item.posicion ?? '',
                                programa: item.programa ?? '',
                                cantidad: item.cantidad ?? 0,
                                vence: item.fecha_vencimiento ?? ''
                            });
                        });

                        // Colores por nivel de ocupación
                        const COLOR_BAJO = 0x22c55e;   // < 50%
                        const COLOR_MEDIO = 0xf97316;  // 50-79%
                        const COLOR_ALTO = 0xef4444;   // >= 80%
                        const COLOR_SIN_LIMITE = 0x3b82f6;

                        function colorOcupacion(pct) {
                            if (pct === null) return COLOR_SIN_LIMITE;
                            if (pct >= 80) return COLOR_ALTO;
                            if (pct >= 50) return COLOR_MEDIO;
                            return COLOR_BAJO;
                        }

                        function etiquetaOcupacion(pct) {
                            if (pct === null) return 'Sin capacidad';
                            if (pct >= 80) return 'Ocupación Crítica';
                            if (pct >= 50) return 'Ocupación Media';
                            return 'Ocupación Baja';
                        }

                        // Pasillos: zonas de piso coloreadas (pasillos visibles)
                        const zonasPasillo = {};
                        dbShelves.forEach(shelf => {
                            const nombrePasillo = shelf.aisle ? shelf.aisle.name : 'Sin pasillo';
                            if (!zonasPasillo[nombrePasillo]) {
                                zonasPasillo[nombrePasillo] = {
                                    color: shelf.aisle ? shelf.aisle.color : '#64748b',
                                    shelves: []
                                };
                            }
                            zonasPasillo[nombrePasillo].shelves.push(shelf);
                        });

                        Object.entries(zonasPasillo).forEach(([nombrePasillo, zona]) => {
                            const pisoMat = new THREE.MeshStandardMaterial({
                                color: zona.color,
                                transparent: true,
                                opacity: 0.22,
                                roughness: 0.9
                            });
                            const bordeMat = new THREE.MeshStandardMaterial({
                                color: zona.color,
                                roughness: 0.6
                            });

                            const shelfxs = zona.shelves.map(s => s.pos_x);
                            const shelfzs = zona.shelves.map(s => s.pos_z);
                            const minX = Math.min(...shelfxs) - 3;
                            const maxX = Math.max(...shelfxs) + 3;
                            const minZ = Math.min(...shelfzs) - 3;
                            const maxZ = Math.max(...shelfzs) + 3;
                            const ancho = maxX - minX;
                            const profundo = maxZ - minZ;
                            const cx = (minX + maxX) / 2;
                            const cz = (minZ + maxZ) / 2;

                            const zonaPiso = new THREE.Mesh(new THREE.BoxGeometry(ancho, 0.12, profundo), pisoMat);
                            zonaPiso.position.set(cx, 0.06, cz);
                            zonaPiso.receiveShadow = true;
                            scene.add(zonaPiso);

                            const bordeZona = new THREE.Mesh(new THREE.BoxGeometry(ancho + 0.6, 0.1, 0.5), bordeMat);
                            bordeZona.position.set(cx, 0.05, maxZ + 0.55);
                            scene.add(bordeZona);

                            const marcadorAisle = document.createElement('div');
                            marcadorAisle.className = 'rack-marker px-2 py-0.5 rounded text-[9px] font-bold shadow-md uppercase tracking-widest';
                            marcadorAisle.style.backgroundColor = zona.color;
                            marcadorAisle.style.color = 'white';
                            marcadorAisle.textContent = nombrePasillo;
                            labelsContainer.appendChild(marcadorAisle);
                            markers.push({
                                element: marcadorAisle,
                                pos3D: new THREE.Vector3(cx, 1.2, maxZ + 1.6)
                            });
                        });

                        // Estanterías realistas: postes + niveles + posiciones
                        const ALTURA_NIVEL = 2.0;
                        const ESPESOR = 0.15;

                        function texturaTexto(texto, colorFondo, colorTexto, tamanoFuente) {
                            const canvas = document.createElement('canvas');
                            canvas.width = 128;
                            canvas.height = 48;
                            const ctx = canvas.getContext('2d');
                            ctx.fillStyle = colorFondo;
                            ctx.fillRect(0, 0, 128, 48);
                            ctx.fillStyle = colorTexto;
                            ctx.font = 'bold ' + (tamanoFuente || 28) + 'px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            const cortado = String(texto).length > 9 ? String(texto).slice(0, 8) + '…' : String(texto);
                            ctx.fillText(cortado, 64, 26);
                            const tex = new THREE.CanvasTexture(canvas);
                            tex.anisotropy = 4;
                            return new THREE.MeshBasicMaterial({ map: tex });
                        }

                        function crearEstanteriaRealista(shelf, rackData) {
                            const grupo = new THREE.Group();
                            const nivelesData = [...(shelf.levels ?? [])]
                                .sort((a, b) => (parseInt(a.number ?? 0) || 0) - (parseInt(b.number ?? 0) || 0));
                            const nNiveles = Math.min(Math.max(nivelesData.length, 3), 5);
                            const alturaTotal = nNiveles * ALTURA_NIVEL + 0.8;

                            const colorPasillo = shelf.aisle ? shelf.aisle.color : '#64748b';
                            const matPoste = new THREE.MeshStandardMaterial({ color: '#334155', roughness: 0.5 });
                            const matRepisa = new THREE.MeshStandardMaterial({ color: '#94a3b8', roughness: 0.7 });
                            const matOcupado = new THREE.MeshStandardMaterial({ color: '#10b981', roughness: 0.4 });
                            const matLibre = new THREE.MeshStandardMaterial({ color: '#e2e8f0', roughness: 0.85 });

                            const postes = [
                                [-1.85, -1.85], [1.85, -1.85],
                                [-1.85, 1.85], [1.85, 1.85]
                            ];
                            postes.forEach(([dx, dz]) => {
                                const poste = new THREE.Mesh(new THREE.BoxGeometry(0.22, alturaTotal, 0.22), matPoste);
                                poste.position.set(dx, alturaTotal / 2, dz);
                                poste.castShadow = true;
                                grupo.add(poste);
                            });

                            // Estructura de niveles/posiciones reales (con fallback)
                            const posicionesPorNivel = [];
                            for (let i = 0; i < nNiveles; i++) {
                                const nivelReal = nivelesData[i] ?? null;
                                const positionsData = nivelReal && (nivelReal.positions ?? []).length > 0
                                    ? nivelReal.positions
                                    : [{ name: 'P1' }, { name: 'P2' }, { name: 'P3' }];
                                posicionesPorNivel.push(positionsData.slice(0, 4));
                            }

                            // Colocación REAL del stock: cada item en su nivel/posición
                            const digitos = (s) => (String(s ?? '').match(/\d+/g) || []).join('');
                            const ocupacionMapa = {};
                            let colocados = 0;

                            (rackData.items ?? []).forEach(item => {
                                const nivDig = digitos(item.nivel);
                                let li = -1;

                                if (nivDig) {
                                    li = posicionesPorNivel.findIndex((_, idx) => {
                                        const numReal = nivelesData[idx] ? digitos(nivelesData[idx].number) : String(idx + 1);
                                        return numReal === nivDig;
                                    });
                                    if (li === -1) {
                                        const idx = parseInt(nivDig, 10) - 1;
                                        if (idx >= 0 && idx < nNiveles) li = idx;
                                    }
                                }

                                if (li === -1 && rackData.pct === null) {
                                    for (let i = 0; i < nNiveles && li === -1; i++) {
                                        for (let j = 0; j < posicionesPorNivel[i].length; j++) {
                                            if (!ocupacionMapa[i + '-' + j]) { li = i; break; }
                                        }
                                    }
                                }

                                if (li === -1) return;

                                const posList = posicionesPorNivel[li];
                                let sj = -1;
                                const posDig = digitos(item.posicion);

                                if (posDig) {
                                    sj = posList.findIndex(p => digitos(p.name) === posDig);
                                }
                                if (sj === -1) {
                                    for (let j = 0; j < posList.length; j++) {
                                        if (!ocupacionMapa[li + '-' + j]) { sj = j; break; }
                                    }
                                }
                                if (sj === -1) return;

                                const key = li + '-' + sj;
                                if (!ocupacionMapa[key]) {
                                    ocupacionMapa[key] = item;
                                    colocados++;
                                }
                            });

                            // Total de slots para repartir el resto según el % de ocupación
                            const totalSlots = posicionesPorNivel.reduce((acc, p) => acc + p.length, 0);
                            const slotsMeta = rackData.pct !== null ? Math.round(totalSlots * rackData.pct / 100) : colocados;
                            let extrasPorColocar = Math.max(0, slotsMeta - colocados);

                            for (let i = 0; i < nNiveles; i++) {
                                const yRepisa = 0.4 + i * ALTURA_NIVEL;

                                const repisa = new THREE.Mesh(new THREE.BoxGeometry(4, ESPESOR, 4), matRepisa);
                                repisa.position.set(0, yRepisa, 0);
                                repisa.castShadow = true;
                                repisa.receiveShadow = true;
                                grupo.add(repisa);

                                // Placa con el NÚMERO real del nivel (frente del nivel)
                                const numNivel = nivelesData[i]
                                    ? (nivelesData[i].number ?? (i + 1))
                                    : (i + 1);
                                const placaNivel = new THREE.Mesh(
                                    new THREE.PlaneGeometry(0.75, 0.3),
                                    texturaTexto('N' + numNivel, '#334155', '#ffffff', 30)
                                );
                                placaNivel.position.set(-1.85, yRepisa + 0.22, 2.03);
                                grupo.add(placaNivel);

                                const posList = posicionesPorNivel[i];
                                const nSlots = posList.length;
                                const anchoSlot = 3.7 / nSlots;

                                for (let j = 0; j < nSlots; j++) {
                                    const key = i + '-' + j;
                                    const itemReal = ocupacionMapa[key];
                                    let ocupado = Boolean(itemReal);

                                    if (!ocupado && extrasPorColocar > 0) {
                                        ocupado = true;
                                        extrasPorColocar--;
                                    }

                                    const slot = new THREE.Mesh(
                                        new THREE.BoxGeometry(anchoSlot - 0.25, 0.14, 3.6),
                                        ocupado ? matOcupado.clone() : matLibre.clone()
                                    );
                                    slot.position.set(
                                        -1.85 + anchoSlot / 2 + j * anchoSlot,
                                        yRepisa + 0.12,
                                        0
                                    );
                                    slot.castShadow = true;
                                    grupo.add(slot);

                                    // Placa con el NOMBRE real de la posición (frente del slot)
                                    const nombrePos = posList[j].name ?? ('P' + (j + 1));
                                    const placaPos = new THREE.Mesh(
                                        new THREE.PlaneGeometry(Math.min(anchoSlot - 0.35, 1.1), 0.22),
                                        texturaTexto(nombrePos, ocupado ? '#065f46' : '#64748b', '#ffffff', 22)
                                    );
                                    placaPos.position.set(
                                        slot.position.x,
                                        yRepisa + 0.12,
                                        1.82
                                    );
                                    grupo.add(placaPos);
                                }
                            }

                            const techo = new THREE.Mesh(new THREE.BoxGeometry(4, ESPESOR, 4), matPoste);
                            techo.position.set(0, alturaTotal, 0);
                            grupo.add(techo);

                            grupo.position.set(shelf.pos_x, 0, shelf.pos_z);
                            scene.add(grupo);
                            rackData.alturaTotal = alturaTotal;
                            rackData.nNiveles = nNiveles;

                            return grupo;
                        }

                        // Recorrer los datos reales de la BD y dibujarlos
                        dbShelves.forEach(shelf => {
                            const items = stockByShelf[shelf.name.trim()] ?? [];
                            const totalStock = items.reduce((acc, i) => acc + (i.cantidad ?? 0), 0);
                            const capacidad = parseInt(shelf.capacidad ?? 0) || 0;
                            const pct = capacidad > 0 ? Math.min(100, Math.round((totalStock / capacidad) * 100)) : null;

                            const rackData = {
                                id: shelf.id,
                                code: shelf.name,
                                status: items.length > 0 ? 'Ocupado' : 'Disponible',
                                aisle: shelf.aisle ? shelf.aisle.name : 'Sin pasillo',
                                stock: totalStock,
                                capacidad: capacidad,
                                pct: pct,
                                ocupacionLabel: etiquetaOcupacion(pct),
                                product: items.length > 0 ? items[0].producto : 'Sin stock asignado',
                                lot: items.length > 0 ? items[0].lote : '-',
                                venc: items.length > 0 ? items[0].vence : '-',
                                items: items
                            };

                            // Estantería realista (postes, niveles, posiciones)
                            crearEstanteriaRealista(shelf, rackData);

                            // Hitbox invisible para interacción (raycast, drag, selección)
                            const alturaHitbox = rackData.alturaTotal + 0.5;
                            const hitMat = new THREE.MeshStandardMaterial({
                                color: colorOcupacion(pct),
                                transparent: true,
                                opacity: 0,
                                roughness: 0.3,
                                emissive: colorOcupacion(pct),
                                emissiveIntensity: 0
                            });
                            const mesh = new THREE.Mesh(new THREE.BoxGeometry(4.4, alturaHitbox, 4.4), hitMat);
                            mesh.position.set(shelf.pos_x, alturaHitbox / 2, shelf.pos_z);
                            mesh.userData = rackData;
                            mesh.userData.originalEmissive = 0;
                            racks.push(mesh);
                            scene.add(mesh);

                            // Marcador HTML flotante: código + % ocupación + unidades
                            const markerDiv = document.createElement('div');
                            markerDiv.className =
                                `rack-marker px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold shadow-md marker-ocu`;
                            markerDiv.style.backgroundColor = '#' + colorOcupacion(pct).toString(16).padStart(6, '0');
                            markerDiv.style.color = 'white';
                            markerDiv.textContent = capacidad > 0
                                ? `${rackData.code} · ${pct}% (${totalStock.toLocaleString('es-CL')}u)`
                                : `${rackData.code} · ${totalStock.toLocaleString('es-CL')}u`;

                            labelsContainer.appendChild(markerDiv);
                            markers.push({
                                element: markerDiv,
                                pos3D: new THREE.Vector3(shelf.pos_x, alturaHitbox + 1.6, shelf.pos_z)
                            });
                        });

                        // Poblando los racks dentro del perímetro 80x50


                        // Raycaster (Interacción del Ratón/Touch)
                        const raycaster = new THREE.Raycaster();
                        const mouse = new THREE.Vector2();
                        let hoveredObject = null;
                        let selectedObject = null;

                        function highlightRack(mesh, isHover) {
                            if (!mesh) return;
                            racks.forEach(r => {
                                if (r.userData.code === mesh.userData.code) {
                                    r.material.opacity = isHover ? 0.16 : 0.3;
                                    r.material.emissiveIntensity = isHover ? 0.4 : 0.75;
                                }
                            });
                        }

                        function quitarBrillo(mesh) {
                            if (!mesh || mesh === selectedObject) return;
                            mesh.material.opacity = 0;
                            mesh.material.emissiveIntensity = 0;
                        }

                        function resetAllHighlights() {
                            racks.forEach(r => {
                                r.material.opacity = 0;
                                r.material.emissiveIntensity = 0;
                            });
                            selectedObject = null;
                        }

                        function getMousePos(event) {
                            const rect = renderer.domElement.getBoundingClientRect();
                            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
                            const clientY = event.touches ? event.touches[0].clientY : event.clientY;
                            mouse.x = ((clientX - rect.left) / rect.width) * 2 - 1;
                            mouse.y = -((clientY - rect.top) / rect.height) * 2 + 1;
                        }

                        // Hover logic
                        wrapper.addEventListener('pointermove', (event) => {
                            if (event.target.closest('header') || event.target.closest('aside') || event.target.closest(
                                    '.z-30') || event.target.closest('.bottom-6')) {
                                quitarBrillo(hoveredObject);
                                hoveredObject = null;
                                wrapper.style.cursor = 'default';
                                return;
                            }

                            getMousePos(event);
                            raycaster.setFromCamera(mouse, camera);
                            const intersects = raycaster.intersectObjects(racks);

                            if (intersects.length > 0) {
                                if (hoveredObject !== intersects[0].object) {
                                    quitarBrillo(hoveredObject);
                                    hoveredObject = intersects[0].object;
                                    wrapper.style.cursor = 'pointer';
                                    if (hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                                }
                            } else {
                                quitarBrillo(hoveredObject);
                                hoveredObject = null;
                                wrapper.style.cursor = 'default';
                            }
                        });



                        // Búsqueda y Cámaras
                        window.addEventListener('search-rack', (e) => {
                            const query = e.detail.toLowerCase();
                            resetAllHighlights();
                            if (!query) return;
                            const found = racks.find(r =>
                                r.userData.code.toLowerCase().includes(query) ||
                                (r.userData.items ?? []).some(i => (i.producto ?? '').toLowerCase().includes(query))
                            );
                            if (found) {
                                selectedObject = found;
                                highlightRack(found, false);
                                window.dispatchEvent(new CustomEvent('rack-selected', {
                                    detail: found.userData
                                }));
                                controls.target.lerp(found.position, 1);
                            }
                        });

                        window.addEventListener('cam-zoom-in', () => {
                            camera.position.lerp(controls.target, 0.3);
                        });
                        window.addEventListener('cam-zoom-out', () => {
                            let dir = new THREE.Vector3().subVectors(camera.position, controls.target).multiplyScalar(
                                1.3);
                            camera.position.copy(controls.target).add(dir);
                        });
                        window.addEventListener('cam-reset', () => {
                            camera.position.lerp(defaultCamPos, 0.1);
                            controls.target.lerp(defaultTarget, 0.1);
                        });

                        window.addEventListener('update-camera-view', (e) => {
                            if (e.detail === '2D') {
                                camera.position.set(0, 110, 0);
                                controls.target.set(0, 0, 0);
                            } else {
                                camera.position.copy(defaultCamPos);
                                controls.target.copy(defaultTarget);
                            }
                        });

                        // --- LÓGICA DE MOVIMIENTO POR CLIC ---
                        let isMoving = false;
                        // Un plano matemático exacto a la altura Y = 1.5 donde flotan las estanterías
                        const floorPlane = new THREE.Plane(new THREE.Vector3(0, 1, 0), -1.5);

                        window.addEventListener('start-moving', () => {
                            if (!selectedObject) return;
                            isMoving = true;
                            controls.enabled = false; // Bloquea la cámara
                            wrapper.style.cursor = 'move';
                        });

                        // Hover logic modificado
                        wrapper.addEventListener('pointermove', (event) => {
                            getMousePos(event);
                            raycaster.setFromCamera(mouse, camera);

                            // Si estamos en "Modo Mover", la estantería seleccionada sigue al mouse
                            if (isMoving && selectedObject) {
                                const intersectPoint = new THREE.Vector3();
                                raycaster.ray.intersectPlane(floorPlane, intersectPoint);

                                if (intersectPoint) {
                                    selectedObject.position.x = intersectPoint.x;
                                    selectedObject.position.z = intersectPoint.z;

                                    // Mueve la etiqueta flotante al mismo tiempo
                                    const matchingMarker = markers.find(m => m.element.textContent === selectedObject
                                        .userData.code);
                                    if (matchingMarker) {
                                        matchingMarker.pos3D.copy(selectedObject.position);
                                        matchingMarker.pos3D.y = 4.5;
                                    }
                                }
                                return;
                            }


                            if (event.target.closest('header') || event.target.closest('aside') || event.target.closest(
                                    '.z-30') || event.target.closest('.bottom-6')) {
                                quitarBrillo(hoveredObject);
                                hoveredObject = null;
                                wrapper.style.cursor = 'default';
                                return;
                            }

                            const intersects = raycaster.intersectObjects(racks);
                            if (intersects.length > 0) {
                                if (hoveredObject !== intersects[0].object) {
                                    quitarBrillo(hoveredObject);
                                    hoveredObject = intersects[0].object;
                                    wrapper.style.cursor = 'pointer';
                                    if (hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                                }
                            } else {
                                quitarBrillo(hoveredObject);
                                hoveredObject = null;
                                wrapper.style.cursor = 'default';
                            }
                        });

                        // Clic logic modificado
                        wrapper.addEventListener('click', (event) => {
                            // Si estamos moviendo una estantería, un clic significa "Soltar y Guardar"
                            if (isMoving && selectedObject) {
                                isMoving = false;
                                controls.enabled = true; // Reactivamos giro de cámara
                                wrapper.style.cursor = 'default';

                                // Guardamos en la base de datos de Laravel
                                fetch(`/shelf/${selectedObject.userData.id}/update-position`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            pos_x: selectedObject.position.x,
                                            pos_z: selectedObject.position.z
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => console.log('Posición guardada exitosamente'))
                                    .catch(error => console.error('Error guardando posición:', error));

                                // Reabrimos el menú de opciones automáticamente
                                window.dispatchEvent(new CustomEvent('rack-selected', {
                                    detail: selectedObject.userData
                                }));
                                return;
                            }

                            if (event.target.closest('header') || event.target.closest('aside') || event.target.closest(
                                    '.z-30') || event.target.closest('.bottom-6')) return;

                            getMousePos(event);
                            raycaster.setFromCamera(mouse, camera);
                            const intersects = raycaster.intersectObjects(racks);

                            if (intersects.length > 0) {
                                resetAllHighlights();
                                selectedObject = intersects[0].object;
                                highlightRack(selectedObject, false);
                                window.dispatchEvent(new CustomEvent('rack-selected', {
                                    detail: selectedObject.userData
                                }));
                                controls.target.lerp(selectedObject.position, 0.5);
                            } else {
                                resetAllHighlights();
                                window.dispatchEvent(new Event('click-empty'));
                            }
                        });

                        // Ciclo de renderizado
                        function animate() {
                            requestAnimationFrame(animate);
                            controls.update();
                            renderer.render(scene, camera);

                            // Sincronizar etiquetas HTML en pantalla 2D sobre la escena 3D
                            markers.forEach(marker => {
                                const vector = marker.pos3D.clone();
                                vector.project(camera);

                                if (vector.z > 1) {
                                    marker.element.style.display = 'none';
                                    return;
                                }

                                marker.element.style.display = 'block';
                                const x = (vector.x * 0.5 + 0.5) * wrapper.clientWidth;
                                const y = (vector.y * -0.5 + 0.5) * wrapper.clientHeight;
                                marker.element.style.left = `${x}px`;
                                marker.element.style.top = `${y}px`;
                            });
                        }
                        animate();

                        // ResizeObserver
                        const resizeObserver = new ResizeObserver(entries => {
                            for (let entry of entries) {
                                const {
                                    width,
                                    height
                                } = entry.contentRect;
                                if (width > 0 && height > 0) {
                                    camera.aspect = width / height;
                                    camera.updateProjectionMatrix();
                                    renderer.setSize(width, height);
                                }
                            }
                        });
                        resizeObserver.observe(wrapper);
                    }
                }
            }
        </script>
    @endsection
