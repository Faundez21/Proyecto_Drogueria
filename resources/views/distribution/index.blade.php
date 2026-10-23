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

        /* Etiquetas flotantes (se reposicionan solas para no solaparse) */
        .rack-marker {
            position: absolute;
            transform: translate(-50%, -100%);
            transform-origin: 50% 100%;
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
            letter-spacing: 0.02em;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.35);
            will-change: left, top, transform;
        }

        .rack-marker::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px 5px 0;
            border-style: solid;
            border-color: inherit;
            border-color: var(--marker-color, #64748b) transparent transparent transparent;
        }

        /* Etiqueta de pasillo: estilo distinto al de las estanterías */
        .rack-marker.aisle-marker {
            opacity: 0.92;
            border-style: dashed;
        }

        /* Colores exactos */
        .marker-green {
            background-color: #22c55e;
            color: white;
        }

        .marker-blue {
            background-color: #3b82f6;
            color: white;
        }

        .marker-orange {
            background-color: #f97316;
            color: white;
        }

        .marker-purple {
            background-color: #a855f7;
            color: white;
        }

        .marker-gray {
            background-color: #64748b;
            color: white;
        }
    </style>

    <div class="w-full">
        <!-- Sección de Mantenedores (Enlaces Rápidos) -->
        <div class="mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- BOTÓN PASILLO -->
                <a href="{{ route('aisle.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">
                    <div class="flex items-center gap-3">
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
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">Pasillos</h3>
                            <p class="text-gray-500 text-sm mt-1">Administración de pasillos</p>
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
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">Estanterías</h3>
                            <p class="text-gray-500 text-sm mt-1">Administración de estanterías</p>
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
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">Niveles</h3>
                            <p class="text-gray-500 text-sm mt-1">Administración de niveles</p>
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
                            <h3 class="text-base sm:text-lg font-bold text-gray-800">Posiciones</h3>
                            <p class="text-gray-500 text-sm mt-1">Administración de posiciones</p>
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
                                    :class="viewMode === '2D' ? 'bg-blue-500 text-white shadow' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">2D</button>
                                <button @click="changeView('3D')"
                                    :class="viewMode === '3D' ? 'bg-blue-500 text-white shadow' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">3D</button>
                            </div>
                        </div>

                        <div class="pointer-events-auto flex gap-1.5 sm:gap-2">
                            <button @click="resetView()" title="Restablecer vista"
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </button>
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

                    <!-- Sidebar Colapsable (Selector, Búsqueda y Leyenda) -->
                    <aside
                        class="absolute top-16 sm:top-24 left-3 sm:left-6 w-48 sm:w-56 glass-panel text-white rounded-xl shadow-2xl z-20 flex flex-col pointer-events-auto transition-all duration-300">
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

                            <!-- Selector de nivel (dinámico: se llena con los niveles reales) -->
                            <div class="relative">
                                <select style="background-image: none;" x-model="selectedLevel" @change="changeLevel()"
                                    class="w-full bg-[#1e293b] border border-slate-700 rounded-lg p-2.5 text-xs sm:text-sm text-white font-medium focus:outline-none focus:border-blue-500 cursor-pointer appearance-none">
                                    <option value="">Todos los niveles</option>
                                    <template x-for="lvl in levelOptions" :key="lvl.value">
                                        <option :value="lvl.value" x-text="lvl.label"></option>
                                    </template>
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
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded bg-[#22c55e]"></span>
                                    Ocupación baja (&lt; 50%)
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded bg-[#f97316]"></span>
                                    Ocupación media (50-79%)
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded bg-[#ef4444]"></span>
                                    Ocupación crítica (&ge; 80%)
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span>
                                    Sin capacidad definida
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded bg-[#c9a36a]"></span>
                                    Posición con stock (cajas)
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Tooltip (Panel de Detalle Flotante) -->
                    <div x-show="tooltipVisible" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;"
                        class="absolute bottom-4 left-4 right-4 sm:bottom-auto sm:left-auto sm:top-24 sm:right-6 sm:w-72 max-h-[80%] overflow-y-auto glass-panel text-white rounded-xl p-4 sm:p-5 shadow-2xl z-30 pointer-events-auto border border-gray-700">

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
                            <p class="font-medium text-white" x-text="rackData.aisle ? 'Pasillo: ' + rackData.aisle : ''">
                            </p>

                            <!-- Ocupación -->
                            <div class="bg-slate-800/70 border border-slate-700 rounded-lg px-2.5 py-2">
                                <div class="flex justify-between items-center text-[11px] font-bold text-gray-300">
                                    <span x-text="rackData.ocupacionLabel"></span>
                                    <span class="font-mono"
                                        x-text="rackData.pct !== null ? rackData.pct + '%' : 'Sin límite'"></span>
                                </div>
                                <div class="mt-1.5 h-2 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        :style="'width:' + (rackData.pct ?? 0) + '%; background-color:' + (rackData.pct >= 80 ?
                                            '#ef4444' : (rackData.pct >= 50 ? '#f97316' : '#22c55e'))">
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 font-mono">
                                    <span x-text="(rackData.stock || 0).toLocaleString('es-CL')"></span>
                                    <span x-show="rackData.capacidad > 0"
                                        x-text="'/ ' + (rackData.capacidad || 0).toLocaleString('es-CL') + ' u'"></span>
                                </p>
                            </div>

                            <!-- Estructura: niveles y posiciones de la estantería -->
                            <template x-if="rackData.estructura && rackData.estructura.length > 0">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Niveles y
                                        posiciones</p>
                                    <div class="max-h-44 overflow-y-auto space-y-1.5 pr-1">
                                        <template x-for="(nivel, ni) in [...rackData.estructura].reverse()"
                                            :key="'n' + ni">
                                            <div class="bg-slate-800/70 border border-slate-700 rounded-lg px-2.5 py-1.5">
                                                <p class="text-[11px] font-bold text-white" x-text="nivel.label"></p>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <template x-for="(pos, pi) in nivel.posiciones" :key="'p' + pi">
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono border"
                                                            :class="pos.ocupado ?
                                                                'bg-emerald-900/40 text-emerald-300 border-emerald-800' :
                                                                'bg-slate-800 text-gray-400 border-slate-700'"
                                                            :title="pos.ocupado ? pos.producto + ' · ' + pos.cantidad + ' u' :
                                                                'Libre'"
                                                            x-text="pos.name + (pos.ocupado ? ' · ' + pos.cantidad : '')"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Productos almacenados (se muestra con 1 o más productos) -->
                            <template x-if="rackData.items && rackData.items.length > 0">
                                <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1">
                                    <template x-for="(item, idx) in rackData.items" :key="idx">
                                        <div
                                            class="flex justify-between items-center gap-2 bg-slate-800/70 border border-slate-700 rounded-lg px-2.5 py-1.5">
                                            <div class="min-w-0">
                                                <p class="font-semibold text-white truncate" x-text="item.producto"></p>
                                                <p class="text-[10px] text-gray-400 font-mono truncate">
                                                    Lote: <span x-text="item.lote"></span>
                                                    <span x-show="item.nivel"> · Niv: <span
                                                            x-text="item.nivel"></span></span>
                                                    <span x-show="item.posicion"> · Pos: <span
                                                            x-text="item.posicion"></span></span>
                                                </p>
                                                <p class="text-[10px] text-blue-400 font-semibold" x-show="item.programa"
                                                    x-text="item.programa"></p>
                                            </div>
                                            <span class="text-emerald-400 font-bold text-xs shrink-0"
                                                x-text="item.cantidad + ' u'"></span>
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
                                    'bg-green-900/40 text-green-400 border-green-800': rackData.pct !== null && rackData
                                        .pct < 50,
                                    'bg-orange-900/40 text-orange-400 border-orange-800': rackData.pct !== null &&
                                        rackData.pct >= 50 && rackData.pct < 80,
                                    'bg-rose-900/40 text-rose-400 border-rose-800': rackData.pct !== null && rackData
                                        .pct >= 80,
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

                    <!-- Bottom Stats Bar (Dinámica con Alpine.js) -->
                    <div
                        class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-[95%] max-w-4xl glass-panel text-white rounded-xl p-4 shadow-2xl z-20 hidden lg:flex justify-between items-center px-8 divide-x divide-gray-700">
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] xl:text-xs text-gray-400">Ubicaciones</p>
                                <p class="text-base xl:text-lg font-bold" x-text="stats.ubicaciones"></p>
                            </div>
                        </div>
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-blue-400 border border-blue-900/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] xl:text-xs text-gray-400">Ocupación</p>
                                    <p class="text-base xl:text-lg font-bold" x-text="stats.ocupacionPct + '%'"></p>
                                </div>
                                <span class="text-xs text-gray-400"
                                    x-text="stats.ocupadas + '/' + stats.ubicaciones"></span>
                            </div>
                        </div>
                        <div
                            class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                            <div class="p-2 bg-[#1e293b] rounded-lg text-purple-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] xl:text-xs text-gray-400">Cap. Total</p>
                                <p class="text-base xl:text-lg font-bold"
                                    x-text="stats.capacidadTotal.toLocaleString('es-CL') + ' u'"></p>
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
                    sidebarOpen: window.innerWidth >= 640,
                    tooltipVisible: false,
                    rackData: {},
                    searchQuery: '',
                    selectedLevel: '',
                    levelOptions: [],
                    stats: {
                        ubicaciones: 0,
                        ocupacionPct: 0,
                        ocupadas: 0,
                        capacidadTotal: 0
                    },
                    init() {
                        this.initThreeJS();

                        window.addEventListener('rack-selected', (e) => {
                            this.rackData = e.detail;
                            this.tooltipVisible = true;
                        });
                        window.addEventListener('click-empty', () => {
                            this.tooltipVisible = false;
                        });
                        // Escape cierra el panel de detalle
                        window.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') this.tooltipVisible = false;
                        });
                    },
                    changeView(mode) {
                        this.viewMode = mode;
                        window.dispatchEvent(new CustomEvent('update-camera-view', {
                            detail: mode
                        }));
                    },
                    changeLevel() {
                        window.dispatchEvent(new CustomEvent('filter-level', {
                            detail: this.selectedLevel
                        }));
                    },
                    resetView() {
                        this.viewMode = '3D';
                        this.selectedLevel = '';
                        this.searchQuery = '';
                        this.changeLevel();
                        window.dispatchEvent(new CustomEvent('search-rack', {
                            detail: ''
                        }));
                        window.dispatchEvent(new Event('cam-reset'));
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
                        window.location.href = "{{ route('inventario.index') }}?estante=" + encodeURIComponent(this.rackData
                            .code);
                    },
                    habilitarMovimiento() {
                        this.tooltipVisible = false; // Ocultamos el panel
                        window.dispatchEvent(new Event('start-moving')); // Avisamos a Three.js
                    },

                    // LÓGICA THREE.JS
                    initThreeJS() {
                        const wrapper = document.getElementById('warehouse-wrapper');
                        const container = document.getElementById('warehouse-3d-container');
                        const labelsContainer = document.getElementById('labels-container');

                        const scene = new THREE.Scene();
                        scene.background = new THREE.Color('#f1f5f9');

                        const camera = new THREE.PerspectiveCamera(45, wrapper.clientWidth / wrapper.clientHeight, 1, 1000);
                        const defaultCamPos = new THREE.Vector3(70, 75, 80);
                        camera.position.copy(defaultCamPos);

                        const renderer = new THREE.WebGLRenderer({
                            antialias: true,
                            powerPreference: "high-performance"
                        });
                        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
                        renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
                        renderer.shadowMap.enabled = true;
                        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
                        container.appendChild(renderer.domElement);

                        const controls = new THREE.OrbitControls(camera, renderer.domElement);
                        const defaultTarget = new THREE.Vector3(0, -5, 0);
                        controls.target.copy(defaultTarget);
                        controls.maxPolarAngle = Math.PI / 2 - 0.05;
                        controls.minDistance = 10;
                        controls.maxDistance = 220;
                        controls.enableDamping = true;
                        controls.dampingFactor = 0.05;

                        // --- Iluminación ---
                        scene.add(new THREE.AmbientLight(0xffffff, 0.45));
                        scene.add(new THREE.HemisphereLight(0xffffff, 0xcbd5e1, 0.4));
                        const dirLight = new THREE.DirectionalLight(0xffffff, 0.65);
                        dirLight.position.set(40, 70, 30);
                        dirLight.castShadow = true;
                        dirLight.shadow.mapSize.width = 2048;
                        dirLight.shadow.mapSize.height = 2048;
                        // Área de sombras que cubre toda la bodega (antes cubría solo una zona pequeña)
                        dirLight.shadow.camera.left = -60;
                        dirLight.shadow.camera.right = 60;
                        dirLight.shadow.camera.top = 60;
                        dirLight.shadow.camera.bottom = -60;
                        dirLight.shadow.camera.near = 1;
                        dirLight.shadow.camera.far = 220;
                        dirLight.shadow.bias = -0.0004;
                        scene.add(dirLight);

                        // --- ANIMACIÓN SUAVE DE CÁMARA ---
                        let camAnim = null;

                        function animateCamera(toPos, toTarget, ms) {
                            camAnim = {
                                fromPos: camera.position.clone(),
                                fromTarget: controls.target.clone(),
                                toPos: toPos.clone(),
                                toTarget: toTarget.clone(),
                                start: performance.now(),
                                ms: ms || 700
                            };
                        }

                        function stepCameraAnimation() {
                            if (!camAnim) return;
                            const t = Math.min(1, (performance.now() - camAnim.start) / camAnim.ms);
                            const e = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2; // easeInOutQuad
                            camera.position.lerpVectors(camAnim.fromPos, camAnim.toPos, e);
                            controls.target.lerpVectors(camAnim.fromTarget, camAnim.toTarget, e);
                            if (t >= 1) camAnim = null;
                        }

                        // Si el usuario toma el control, se cancela la animación
                        controls.addEventListener('start', () => {
                            camAnim = null;
                        });

                        function focusPoint(point) {
                            const dir = camera.position.clone().sub(controls.target).normalize();
                            const dist = Math.min(Math.max(camera.position.distanceTo(controls.target) * 0.7, 28), 60);
                            animateCamera(point.clone().add(dir.multiplyScalar(dist)), point, 700);
                        }

                        // --- ESTRUCTURA BASE (Suelo con cuadrícula y Paredes) ---
                        const buildGroup = new THREE.Group();

                        // Textura de cuadrícula para el piso (celdas de 2 x 2 unidades)
                        const gridCanvas = document.createElement('canvas');
                        gridCanvas.width = 64;
                        gridCanvas.height = 64;
                        const gctx = gridCanvas.getContext('2d');
                        gctx.fillStyle = '#dbe3ec';
                        gctx.fillRect(0, 0, 64, 64);
                        gctx.strokeStyle = '#c3cfdb';
                        gctx.lineWidth = 2;
                        gctx.strokeRect(0, 0, 64, 64);
                        const gridTex = new THREE.CanvasTexture(gridCanvas);
                        gridTex.wrapS = THREE.RepeatWrapping;
                        gridTex.wrapT = THREE.RepeatWrapping;
                        gridTex.repeat.set(40, 25);
                        gridTex.anisotropy = 8;

                        const floorMat = new THREE.MeshStandardMaterial({
                            map: gridTex,
                            roughness: 0.85
                        });
                        const floor = new THREE.Mesh(new THREE.BoxGeometry(80, 1, 50), floorMat);
                        floor.position.set(0, -0.5, 0);
                        floor.receiveShadow = true;
                        buildGroup.add(floor);

                        const wallMat = new THREE.MeshPhysicalMaterial({
                            color: '#94a3b8',
                            transparent: true,
                            opacity: 0.25,
                            roughness: 0.1,
                            side: THREE.DoubleSide,
                            depthWrite: false
                        });
                        const wallSolidMat = new THREE.MeshStandardMaterial({
                            color: '#64748b'
                        });

                        function createBorderWall(w, h, d, x, y, z) {
                            const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
                            wall.position.set(x, y + 1, z);
                            buildGroup.add(wall);

                            const baseHeight = 1.5;
                            const base = new THREE.Mesh(new THREE.BoxGeometry(w, baseHeight, d), wallSolidMat);
                            base.position.set(x, baseHeight / 2, z);
                            buildGroup.add(base);
                        }

                        createBorderWall(80, 10, 1, 0, 4, -25);
                        createBorderWall(80, 10, 1, 0, 4, 25);
                        createBorderWall(1, 10, 50, 40, 4, 0);
                        createBorderWall(1, 10, 50, -40, 4, 0);

                        scene.add(buildGroup);

                        // --- VARIABLES GLOBALES DE THREEJS ---
                        const racks = [];
                        const markers = [];

                        const dbShelves = @json($shelves ?? []);
                        const stockData = @json($stock ?? []);
                        const stockByShelf = {};

                        stockData.forEach(item => {
                            const key = String(item.estante ?? '').trim();
                            if (!key) return;
                            if (!stockByShelf[key]) stockByShelf[key] = [];
                            stockByShelf[key].push({
                                producto: item.producto,
                                lote: item.lote ?? 'S/L',
                                nivel: item.nivel ?? '',
                                posicion: item.posicion ?? '',
                                programa: item.programa ?? '',
                                cantidad: Number(item.cantidad ?? 0) || 0,
                                vence: item.fecha_vencimiento ?? ''
                            });
                        });

                        const COLOR_BAJO = 0x22c55e;
                        const COLOR_MEDIO = 0xf97316;
                        const COLOR_ALTO = 0xef4444;
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

                        const ALTURA_NIVEL = 2.0;
                        const ESPESOR = 0.15;
                        const MAX_NIVELES = 8;
                        const MAX_POSICIONES = 8;

                        // --- Utilidades de texto / números para comparar niveles y posiciones ---
                        // txt: texto normalizado ("Nivel 1 " -> "nivel 1")
                        const txt = (s) => String(s ?? '').trim().toLowerCase();
                        // norm: último número del texto sin ceros a la izquierda ("A-01" -> "1", "Nivel 2" -> "2")
                        const norm = (s) => {
                            const m = String(s ?? '').match(/\d+/g);
                            return m ? String(parseInt(m[m.length - 1], 10)) : '';
                        };

                        // Opciones del selector de niveles (se llenan al dibujar las estanterías)
                        const nivelesGlobales = new Map();

                        // Placa con texto (alta resolución para que se lea de cerca)
                        function texturaTexto(texto, colorFondo, colorTexto, tamanoFuente) {
                            const canvas = document.createElement('canvas');
                            canvas.width = 256;
                            canvas.height = 96;
                            const ctx = canvas.getContext('2d');
                            ctx.fillStyle = colorFondo;
                            ctx.fillRect(0, 0, 256, 96);
                            ctx.strokeStyle = 'rgba(255,255,255,0.55)';
                            ctx.lineWidth = 6;
                            ctx.strokeRect(3, 3, 250, 90);
                            ctx.fillStyle = colorTexto;
                            ctx.font = 'bold ' + ((tamanoFuente || 28) * 2) + 'px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            const cortado = String(texto).length > 9 ? String(texto).slice(0, 8) + '…' : String(texto);
                            ctx.fillText(cortado, 128, 52);
                            const tex = new THREE.CanvasTexture(canvas);
                            tex.anisotropy = 8;
                            return new THREE.MeshBasicMaterial({
                                map: tex
                            });
                        }

                        function crearEstanteriaRealista(shelf, rackData, px, pz) {
                            const grupo = new THREE.Group();

                            // Color de las vigas = color del pasillo (identifica el pasillo de un vistazo)
                            const colorPasillo = (shelf.aisle && shelf.aisle.color) ? shelf.aisle.color : '#f97316';
                            const colorEstado = colorOcupacion(rackData.pct);

                            // 1. Niveles reales de la BD (acepta "levels" o "niveles"), ordenados por número
                            const ordenNivel = (l) => parseInt(norm(l.number ?? l.name) || '0', 10) || 0;
                            const nivelesRaw = [...(shelf.levels ?? shelf.niveles ?? [])].sort((a, b) => ordenNivel(a) -
                                ordenNivel(b));

                            // Si la estantería no tiene niveles configurados se muestran 3 de referencia
                            const base = nivelesRaw.length > 0 ?
                                nivelesRaw.slice(0, MAX_NIVELES) :
                                [1, 2, 3].map(n => ({
                                    number: n,
                                    positions: []
                                }));

                            // 2. Estructura normalizada: nivel -> posiciones
                            const niveles = base.map((lv, idx) => {
                                const number = lv.number ?? lv.name ?? (idx + 1);
                                let posRaw = lv.positions ?? lv.posiciones ?? [];
                                if (posRaw.length === 0) {
                                    posRaw = [{
                                        name: 'P1'
                                    }, {
                                        name: 'P2'
                                    }, {
                                        name: 'P3'
                                    }];
                                }
                                const positions = posRaw.slice(0, MAX_POSICIONES).map((p, j) => ({
                                    name: String(p.name ?? p.code ?? p.number ?? ('P' + (j + 1)))
                                }));
                                const numeric = /^\d+$/.test(String(number).trim());
                                return {
                                    number: number,
                                    name: lv.name ?? null,
                                    key: norm(number) || txt(number),
                                    label: numeric ? 'Nivel ' + number : String(number),
                                    positions: positions
                                };
                            });

                            const nNiveles = niveles.length;
                            const alturaTotal = nNiveles * ALTURA_NIVEL + 0.8;

                            // Registrar niveles para el selector del panel
                            niveles.forEach(n => {
                                if (!nivelesGlobales.has(n.key)) nivelesGlobales.set(n.key, n.label);
                            });

                            const matPoste = new THREE.MeshStandardMaterial({
                                color: '#1e3a5f',
                                roughness: 0.45,
                                metalness: 0.3
                            });
                            const matTecho = new THREE.MeshStandardMaterial({
                                color: '#334155',
                                roughness: 0.6
                            });

                            // Pseudo-aleatorio estable (para que las cajas no cambien en cada carga)
                            const seed = (n) => {
                                const x = Math.sin(n * 12.9898 + (Number(shelf.id) || 1) * 78.233) * 43758.5453;
                                return x - Math.floor(x);
                            };

                            // Postes
                            const postes = [
                                [-1.85, -1.85],
                                [1.85, -1.85],
                                [-1.85, 1.85],
                                [1.85, 1.85]
                            ];
                            postes.forEach(([dx, dz]) => {
                                const poste = new THREE.Mesh(new THREE.BoxGeometry(0.22, alturaTotal, 0.22), matPoste);
                                poste.position.set(dx, alturaTotal / 2, dz);
                                poste.castShadow = true;
                                grupo.add(poste);
                            });

                            // Arriostres diagonales laterales (aspecto de rack industrial)
                            [-1.85, 1.85].forEach(dx => {
                                const largo = Math.sqrt(alturaTotal * alturaTotal + 3.7 * 3.7);
                                const ang = Math.atan2(alturaTotal, 3.7);
                                const brace = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.08, largo), matPoste);
                                brace.position.set(dx, alturaTotal / 2, 0);
                                brace.rotation.x = ang;
                                grupo.add(brace);
                            });

                            // 3. Asignar cada producto a su nivel y posición reales
                            const buscarNivel = (item) => {
                                const raw = item.nivel;
                                if (raw === null || raw === undefined || String(raw).trim() === '') return -1;
                                let idx = niveles.findIndex(n => txt(n.number) === txt(raw) || (n.name && txt(n
                                    .name) === txt(raw)));
                                if (idx === -1) {
                                    const k = norm(raw);
                                    if (k) idx = niveles.findIndex(n => n.key === k);
                                }
                                return idx;
                            };

                            const buscarPosicion = (nivel, item) => {
                                const raw = item.posicion;
                                if (raw === null || raw === undefined || String(raw).trim() === '') return -1;
                                let idx = nivel.positions.findIndex(p => txt(p.name) === txt(raw));
                                if (idx === -1) {
                                    const k = norm(raw);
                                    if (k) idx = nivel.positions.findIndex(p => norm(p.name) === k);
                                }
                                return idx;
                            };

                            // ocupacionMapa["nivel-posicion"] = [productos en ese hueco]
                            const ocupacionMapa = {};
                            const primerHuecoLibre = (li) => {
                                for (let j = 0; j < niveles[li].positions.length; j++) {
                                    if (!ocupacionMapa[li + '-' + j]) return j;
                                }
                                return -1;
                            };

                            (rackData.items ?? []).forEach(item => {
                                let li = buscarNivel(item);
                                let sj = -1;

                                if (li === -1) {
                                    // Sin nivel válido: se ubica en el primer hueco libre de la estantería
                                    for (let i = 0; i < niveles.length; i++) {
                                        const j = primerHuecoLibre(i);
                                        if (j !== -1) {
                                            li = i;
                                            sj = j;
                                            break;
                                        }
                                    }
                                    if (li === -1) {
                                        li = 0;
                                        sj = 0;
                                    }
                                } else {
                                    sj = buscarPosicion(niveles[li], item);
                                    if (sj === -1) sj = primerHuecoLibre(li);
                                    if (sj === -1) sj = 0;
                                }

                                const key = li + '-' + sj;
                                if (!ocupacionMapa[key]) ocupacionMapa[key] = [];
                                ocupacionMapa[key].push(item);
                            });

                            // Helper: placa visible por delante y por detrás de la estantería
                            const agregarPlaca = (ancho, alto, material, x, y, z, lista) => {
                                const frente = new THREE.Mesh(new THREE.PlaneGeometry(ancho, alto), material);
                                frente.position.set(x, y, z);
                                grupo.add(frente);
                                lista.push(frente);

                                const atras = new THREE.Mesh(new THREE.PlaneGeometry(ancho, alto), material);
                                atras.position.set(-x, y, -z);
                                atras.rotation.y = Math.PI;
                                grupo.add(atras);
                                lista.push(atras);
                            };

                            // 4. Dibujar repisas, vigas, huecos y cajas
                            const levelMeshes = [];
                            const levelKeys = [];

                            for (let i = 0; i < nNiveles; i++) {
                                const meshesNivel = [];
                                const yRepisa = 0.4 + i * ALTURA_NIVEL;
                                const nivel = niveles[i];

                                // Materiales propios de este nivel (permiten atenuarlo sin afectar a los demás)
                                const mats = {
                                    repisa: new THREE.MeshStandardMaterial({
                                        color: '#cbd5e1',
                                        roughness: 0.7
                                    }),
                                    viga: new THREE.MeshStandardMaterial({
                                        color: colorPasillo,
                                        roughness: 0.45,
                                        metalness: 0.2
                                    }),
                                    ocupado: new THREE.MeshStandardMaterial({
                                        color: '#10b981',
                                        roughness: 0.5
                                    }),
                                    libre: new THREE.MeshStandardMaterial({
                                        color: '#e2e8f0',
                                        roughness: 0.9
                                    }),
                                    caja: new THREE.MeshStandardMaterial({
                                        color: '#c9a36a',
                                        roughness: 0.85
                                    }),
                                    cajaAlt: new THREE.MeshStandardMaterial({
                                        color: '#b98f55',
                                        roughness: 0.85
                                    })
                                };

                                // Repisa base
                                const repisa = new THREE.Mesh(new THREE.BoxGeometry(4, ESPESOR, 4), mats.repisa);
                                repisa.position.set(0, yRepisa, 0);
                                repisa.castShadow = true;
                                repisa.receiveShadow = true;
                                grupo.add(repisa);
                                meshesNivel.push(repisa);

                                // Vigas frontal y trasera (color del pasillo)
                                [1.92, -1.92].forEach(zz => {
                                    const viga = new THREE.Mesh(new THREE.BoxGeometry(4, 0.18, 0.1), mats.viga);
                                    viga.position.set(0, yRepisa - 0.12, zz);
                                    viga.castShadow = true;
                                    grupo.add(viga);
                                    meshesNivel.push(viga);
                                });

                                // Placa de nivel (frente y espalda)
                                const matPlacaNivel = texturaTexto('N' + (norm(nivel.number) || nivel.number),
                                    '#1e3a5f', '#ffffff', 30);
                                agregarPlaca(0.8, 0.32, matPlacaNivel, -1.85, yRepisa + 0.24, 2.0, meshesNivel);

                                const nSlots = nivel.positions.length;
                                const anchoSlot = 3.7 / nSlots;

                                for (let j = 0; j < nSlots; j++) {
                                    const productosHueco = ocupacionMapa[i + '-' + j] ?? [];
                                    const ocupado = productosHueco.length > 0;

                                    const slot = new THREE.Mesh(
                                        new THREE.BoxGeometry(anchoSlot - 0.25, 0.14, 3.6),
                                        ocupado ? mats.ocupado : mats.libre
                                    );
                                    slot.position.set(-1.85 + anchoSlot / 2 + j * anchoSlot, yRepisa + 0.12, 0);
                                    slot.castShadow = true;
                                    slot.receiveShadow = true;
                                    grupo.add(slot);
                                    meshesNivel.push(slot);

                                    // Cajas sobre las posiciones con stock (más cantidad = más cajas)
                                    if (ocupado) {
                                        const cant = productosHueco.reduce((a, x) => a + (x.cantidad || 0), 0);
                                        const nCajas = Math.min(3, Math.max(1, Math.ceil(cant / 100)));
                                        const celda = 3.4 / nCajas;
                                        const anchoCaja = Math.max(0.3, anchoSlot - 0.5);

                                        for (let k = 0; k < nCajas; k++) {
                                            const alto = 0.65 + seed(i * 31 + j * 7 + k) * 0.55;
                                            const caja = new THREE.Mesh(
                                                new THREE.BoxGeometry(anchoCaja, alto, celda - 0.12),
                                                k % 2 === 0 ? mats.caja : mats.cajaAlt
                                            );
                                            caja.position.set(
                                                slot.position.x,
                                                yRepisa + 0.19 + alto / 2,
                                                -1.7 + celda * (k + 0.5)
                                            );
                                            caja.castShadow = true;
                                            caja.receiveShadow = true;
                                            grupo.add(caja);
                                            meshesNivel.push(caja);
                                        }
                                    }

                                    // Placa de posición (frente y espalda)
                                    const matPlacaPos = texturaTexto(nivel.positions[j].name, ocupado ? '#065f46' :
                                        '#64748b', '#ffffff', 22);
                                    agregarPlaca(
                                        Math.min(anchoSlot - 0.3, 1.2),
                                        0.27,
                                        matPlacaPos,
                                        slot.position.x,
                                        yRepisa + 0.14,
                                        1.83,
                                        meshesNivel
                                    );
                                }

                                levelMeshes.push(meshesNivel);
                                levelKeys.push(nivel.key);
                            }

                            // Techo + franja de estado (color según ocupación)
                            const techo = new THREE.Mesh(new THREE.BoxGeometry(4, ESPESOR, 4), matTecho);
                            techo.position.set(0, alturaTotal, 0);
                            techo.castShadow = true;
                            grupo.add(techo);

                            [2.0, -2.0].forEach(zz => {
                                const franja = new THREE.Mesh(
                                    new THREE.BoxGeometry(4, 0.22, 0.12),
                                    new THREE.MeshStandardMaterial({
                                        color: colorEstado,
                                        emissive: colorEstado,
                                        emissiveIntensity: 0.35,
                                        roughness: 0.4
                                    })
                                );
                                franja.position.set(0, alturaTotal, zz);
                                grupo.add(franja);
                            });

                            // Huella en el piso con el color de ocupación
                            const huella = new THREE.Mesh(
                                new THREE.BoxGeometry(4.6, 0.06, 4.6),
                                new THREE.MeshStandardMaterial({
                                    color: colorEstado,
                                    transparent: true,
                                    opacity: 0.35,
                                    roughness: 0.9,
                                    depthWrite: false
                                })
                            );
                            huella.position.set(0, 0.03, 0);
                            grupo.add(huella);

                            grupo.position.set(px, 0, pz);
                            grupo.userData.levelMeshes = levelMeshes;
                            grupo.userData.levelKeys = levelKeys;
                            scene.add(grupo);

                            // 5. Estructura para el panel de detalle (niveles y posiciones)
                            rackData.estructura = niveles.map((n, i) => ({
                                label: n.label,
                                posiciones: n.positions.map((p, j) => {
                                    const arr = ocupacionMapa[i + '-' + j] ?? [];
                                    return {
                                        name: p.name,
                                        ocupado: arr.length > 0,
                                        producto: arr.map(x => x.producto).join(', '),
                                        cantidad: arr.reduce((acc, x) => acc + (x.cantidad || 0), 0)
                                    };
                                })
                            }));

                            rackData.alturaTotal = alturaTotal;
                            rackData.nNiveles = nNiveles;
                            return grupo;
                        }

                        // Calcular estadísticas globales
                        let ubiTotal = 0;
                        let capTotal = 0;
                        let ocuTotal = 0;

                        dbShelves.forEach((shelf, idx) => {
                            const nombreEstante = String(shelf.name ?? '').trim();
                            const items = stockByShelf[nombreEstante] ?? [];
                            const totalStock = items.reduce((acc, i) => acc + (i.cantidad ?? 0), 0);
                            const capacidad = parseInt(shelf.capacidad ?? 0) || 0;
                            const pct = capacidad > 0 ? Math.min(100, Math.round((totalStock / capacidad) * 100)) :
                                null;

                            // Si la estantería aún no tiene posición guardada, se acomoda en una grilla
                            const tienePos = shelf.pos_x !== null && shelf.pos_x !== undefined && shelf.pos_z !==
                                null && shelf.pos_z !== undefined;
                            const px = tienePos ? (Number(shelf.pos_x) || 0) : -30 + (idx % 6) * 12;
                            const pz = tienePos ? (Number(shelf.pos_z) || 0) : -12 + Math.floor(idx / 6) * 14;

                            const rackData = {
                                id: shelf.id,
                                code: nombreEstante,
                                status: items.length > 0 ? 'Ocupado' : 'Disponible',
                                aisle: shelf.aisle ? shelf.aisle.name : 'Sin pasillo',
                                aisleColor: shelf.aisle ? shelf.aisle.color : '#64748b',
                                stock: totalStock,
                                capacidad: capacidad,
                                pct: pct,
                                ocupacionLabel: etiquetaOcupacion(pct),
                                product: items.length > 0 ? items[0].producto : 'Sin stock asignado',
                                lot: items.length > 0 ? items[0].lote : '-',
                                venc: items.length > 0 ? items[0].vence : '-',
                                items: items
                            };

                            // Modelo visual + hitbox invisible
                            const visualGroup = crearEstanteriaRealista(shelf, rackData, px, pz);

                            const alturaHitbox = rackData.alturaTotal + 0.5;
                            const hitMat = new THREE.MeshStandardMaterial({
                                color: colorOcupacion(pct),
                                transparent: true,
                                opacity: 0,
                                roughness: 0.3,
                                emissive: colorOcupacion(pct),
                                emissiveIntensity: 0,
                                depthWrite: false
                            });

                            const mesh = new THREE.Mesh(new THREE.BoxGeometry(4.4, alturaHitbox, 4.4), hitMat);
                            mesh.position.set(px, alturaHitbox / 2, pz);
                            mesh.userData = rackData;
                            // La referencia al grupo visual se guarda fuera de userData para no pasarla a Alpine
                            mesh.visualGroup = visualGroup;
                            racks.push(mesh);
                            scene.add(mesh);

                            const colorHex = '#' + colorOcupacion(pct).toString(16).padStart(6, '0');
                            const markerDiv = document.createElement('div');
                            markerDiv.className = 'rack-marker px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold';
                            markerDiv.style.backgroundColor = colorHex;
                            markerDiv.style.setProperty('--marker-color', colorHex);
                            markerDiv.style.color = 'white';
                            markerDiv.textContent = capacidad > 0 ? `${rackData.code} · ${pct}%` : `${rackData.code}`;

                            labelsContainer.appendChild(markerDiv);
                            markers.push({
                                element: markerDiv,
                                rackCode: rackData.code,
                                // La etiqueta queda justo encima de la estantería
                                pos3D: new THREE.Vector3(px, alturaHitbox + 0.8, pz)
                            });

                            // Sumar a estadísticas
                            ubiTotal++;
                            capTotal += capacidad;
                            if (items.length > 0) ocuTotal++;
                        });

                        // Actualizar Alpine Stats
                        this.stats.ubicaciones = ubiTotal;
                        this.stats.capacidadTotal = capTotal;
                        this.stats.ocupadas = ocuTotal;
                        this.stats.ocupacionPct = ubiTotal > 0 ? Math.round((ocuTotal / ubiTotal) * 100) : 0;

                        // Llenar el selector de niveles con los niveles reales
                        this.levelOptions = [...nivelesGlobales.entries()]
                            .map(([value, label]) => ({
                                value,
                                label
                            }))
                            .sort((a, b) => {
                                const na = parseInt(a.value, 10);
                                const nb = parseInt(b.value, 10);
                                if (!isNaN(na) && !isNaN(nb)) return na - nb;
                                return String(a.label).localeCompare(String(b.label));
                            });

                        // --- FILTRO POR NIVEL: atenúa los demás niveles de todas las estanterías ---
                        function aplicarFiltroNivel(key) {
                            const clave = String(key ?? '');
                            racks.forEach(rack => {
                                const g = rack.visualGroup;
                                if (!g) return;
                                g.userData.levelMeshes.forEach((meshes, i) => {
                                    const atenuar = clave !== '' && g.userData.levelKeys[i] !== clave;
                                    meshes.forEach(m => {
                                        m.material.transparent = atenuar;
                                        m.material.opacity = atenuar ? 0.12 : 1;
                                        m.material.depthWrite = !atenuar;
                                        m.material.needsUpdate = true;
                                    });
                                });
                            });
                        }

                        window.addEventListener('filter-level', (e) => aplicarFiltroNivel(e.detail));

                        // --- RENDERIZADO DINÁMICO DE PASILLOS ---
                        let aisleMeshes = [];
                        let aisleMarkers = [];

                        function renderAisles() {
                            // 1. Limpiar mallas antiguas de los pasillos
                            aisleMeshes.forEach(m => scene.remove(m));
                            aisleMeshes = [];

                            aisleMarkers.forEach(m => {
                                if (m.element && m.element.parentNode) m.element.parentNode.removeChild(m.element);
                                const idx = markers.indexOf(m);
                                if (idx > -1) markers.splice(idx, 1);
                            });
                            aisleMarkers = [];

                            // 2. Recalcular dimensiones en base a la posición actual de los racks
                            const zonas = {};
                            racks.forEach(rack => {
                                const data = rack.userData;
                                const nombre = data.aisle;
                                if (nombre === 'Sin pasillo') return;

                                if (!zonas[nombre]) {
                                    zonas[nombre] = {
                                        color: data.aisleColor || '#64748b',
                                        minX: rack.position.x,
                                        maxX: rack.position.x,
                                        minZ: rack.position.z,
                                        maxZ: rack.position.z
                                    };
                                } else {
                                    zonas[nombre].minX = Math.min(zonas[nombre].minX, rack.position.x);
                                    zonas[nombre].maxX = Math.max(zonas[nombre].maxX, rack.position.x);
                                    zonas[nombre].minZ = Math.min(zonas[nombre].minZ, rack.position.z);
                                    zonas[nombre].maxZ = Math.max(zonas[nombre].maxZ, rack.position.z);
                                }
                            });

                            // 3. Dibujar de nuevo
                            Object.entries(zonas).forEach(([nombre, zona]) => {
                                const pisoMat = new THREE.MeshStandardMaterial({
                                    color: zona.color,
                                    transparent: true,
                                    opacity: 0.22,
                                    roughness: 0.9,
                                    depthWrite: false
                                });
                                const bordeMat = new THREE.MeshStandardMaterial({
                                    color: zona.color,
                                    roughness: 0.6
                                });

                                const minX = zona.minX - 3;
                                const maxX = zona.maxX + 3;
                                const minZ = zona.minZ - 3;
                                const maxZ = zona.maxZ + 3;

                                const ancho = maxX - minX;
                                const profundo = maxZ - minZ;
                                const cx = (minX + maxX) / 2;
                                const cz = (minZ + maxZ) / 2;

                                const zonaPiso = new THREE.Mesh(new THREE.BoxGeometry(ancho, 0.12, profundo), pisoMat);
                                zonaPiso.position.set(cx, 0.06, cz);
                                zonaPiso.receiveShadow = true;
                                scene.add(zonaPiso);
                                aisleMeshes.push(zonaPiso);

                                const bordeZona = new THREE.Mesh(new THREE.BoxGeometry(ancho + 0.6, 0.1, 0.5),
                                    bordeMat);
                                bordeZona.position.set(cx, 0.05, maxZ + 0.55);
                                scene.add(bordeZona);
                                aisleMeshes.push(bordeZona);

                                const marcadorAisle = document.createElement('div');
                                marcadorAisle.className =
                                    'rack-marker aisle-marker px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest';
                                marcadorAisle.style.backgroundColor = zona.color;
                                marcadorAisle.style.setProperty('--marker-color', zona.color);
                                marcadorAisle.style.color = 'white';
                                marcadorAisle.textContent = nombre;
                                labelsContainer.appendChild(marcadorAisle);

                                const markerObj = {
                                    element: marcadorAisle,
                                    rackCode: null,
                                    pos3D: new THREE.Vector3(cx, 1.2, maxZ + 1.6)
                                };
                                markers.push(markerObj);
                                aisleMarkers.push(markerObj);
                            });
                        }

                        renderAisles(); // Generar pasillos por primera vez

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

                        window.addEventListener('search-rack', (e) => {
                            const query = String(e.detail ?? '').toLowerCase();
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
                                focusPoint(found.position);
                            }
                        });

                        // --- Zoom animado ---
                        window.addEventListener('cam-zoom-in', () => {
                            const offset = camera.position.clone().sub(controls.target).multiplyScalar(0.7);
                            animateCamera(controls.target.clone().add(offset), controls.target, 400);
                        });
                        window.addEventListener('cam-zoom-out', () => {
                            const offset = camera.position.clone().sub(controls.target).multiplyScalar(1.4);
                            animateCamera(controls.target.clone().add(offset), controls.target, 400);
                        });
                        window.addEventListener('cam-reset', () => {
                            controls.enableRotate = true;
                            resetAllHighlights();
                            animateCamera(defaultCamPos, defaultTarget, 800);
                        });

                        // --- Cambio 2D / 3D animado ---
                        window.addEventListener('update-camera-view', (e) => {
                            if (e.detail === '2D') {
                                controls.enableRotate = false;
                                // z = 0.01 evita la posición degenerada justo sobre el eje vertical
                                animateCamera(new THREE.Vector3(0, 110, 0.01), new THREE.Vector3(0, 0, 0), 800);
                            } else {
                                controls.enableRotate = true;
                                animateCamera(defaultCamPos, defaultTarget, 800);
                            }
                        });

                        // --- LÓGICA DE MOVIMIENTO POR CLIC ---
                        let isMoving = false;
                        const floorPlane = new THREE.Plane(new THREE.Vector3(0, 1, 0), -1.5);

                        window.addEventListener('start-moving', () => {
                            if (!selectedObject) return;
                            isMoving = true;
                            camAnim = null;
                            controls.enabled = false;
                            wrapper.style.cursor = 'move';
                        });

                        wrapper.addEventListener('pointermove', (event) => {
                            getMousePos(event);
                            raycaster.setFromCamera(mouse, camera);

                            if (isMoving && selectedObject) {
                                const intersectPoint = new THREE.Vector3();
                                const hit = raycaster.ray.intersectPlane(floorPlane, intersectPoint);

                                if (hit) {
                                    // 1. Movemos la hitbox
                                    selectedObject.position.x = intersectPoint.x;
                                    selectedObject.position.z = intersectPoint.z;

                                    // 2. Movemos el MODELO VISUAL (Estantería Realista)
                                    if (selectedObject.visualGroup) {
                                        selectedObject.visualGroup.position.x = intersectPoint.x;
                                        selectedObject.visualGroup.position.z = intersectPoint.z;
                                    }

                                    // 3. Movemos la etiqueta (se busca por código exacto, no por texto)
                                    const matchingMarker = markers.find(m => m.rackCode === selectedObject.userData
                                        .code);
                                    if (matchingMarker) {
                                        matchingMarker.pos3D.x = intersectPoint.x;
                                        matchingMarker.pos3D.z = intersectPoint.z;
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

                        wrapper.addEventListener('click', (event) => {
                            if (isMoving && selectedObject) {
                                isMoving = false;
                                controls.enabled = true;
                                wrapper.style.cursor = 'default';

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

                                // Recreamos los rectángulos del pasillo para que se ajusten a la nueva posición
                                renderAisles();

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
                                focusPoint(selectedObject.position);
                            } else {
                                resetAllHighlights();
                                window.dispatchEvent(new Event('click-empty'));
                            }
                        });

                        // --- ETIQUETAS: posicionamiento, escala por distancia y anti-solapamiento ---
                        function actualizarEtiquetas() {
                            const W = wrapper.clientWidth;
                            const H = wrapper.clientHeight;
                            const visibles = [];

                            markers.forEach(marker => {
                                const vector = marker.pos3D.clone().project(camera);

                                // Fuera del frustum (detrás de la cámara o muy lejos)
                                if (vector.z > 1 || vector.z < -1) {
                                    marker.element.style.visibility = 'hidden';
                                    return;
                                }

                                const x = (vector.x * 0.5 + 0.5) * W;
                                const y = (vector.y * -0.5 + 0.5) * H;

                                // Fuera de la pantalla
                                if (x < -80 || x > W + 80 || y < -40 || y > H + 40) {
                                    marker.element.style.visibility = 'hidden';
                                    return;
                                }

                                // Tamaño base (se mide una sola vez)
                                if (!marker.w) {
                                    marker.w = marker.element.offsetWidth || 70;
                                    marker.h = marker.element.offsetHeight || 20;
                                }

                                const dist = camera.position.distanceTo(marker.pos3D);
                                const scale = Math.min(1.1, Math.max(0.65, 70 / dist));

                                visibles.push({
                                    marker,
                                    x,
                                    y,
                                    dist,
                                    scale
                                });
                            });

                            // Las estanterías tienen prioridad sobre los pasillos; luego las más cercanas
                            visibles.sort((a, b) => {
                                const pa = a.marker.rackCode ? 0 : 1;
                                const pb = b.marker.rackCode ? 0 : 1;
                                return pa - pb || a.dist - b.dist;
                            });

                            const ocupados = [];
                            const choca = (r) => ocupados.some(p => !(r.r < p.l || r.l > p.r || r.b < p.t || r.t > p.b));

                            visibles.forEach(v => {
                                const w = v.marker.w * v.scale;
                                const h = v.marker.h * v.scale;
                                let offset = 0;
                                let colocado = false;
                                let rect;

                                // Si choca con otra etiqueta, se sube hasta 3 veces; si aun así choca, se oculta
                                for (let intento = 0; intento < 4; intento++) {
                                    rect = {
                                        l: v.x - w / 2,
                                        r: v.x + w / 2,
                                        t: v.y - offset - h,
                                        b: v.y - offset
                                    };
                                    if (!choca(rect)) {
                                        ocupados.push(rect);
                                        colocado = true;
                                        break;
                                    }
                                    offset += h + 3;
                                }

                                const el = v.marker.element;
                                if (!colocado) {
                                    el.style.visibility = 'hidden';
                                    return;
                                }
                                el.style.visibility = 'visible';
                                el.style.left = `${v.x}px`;
                                el.style.top = `${v.y - offset}px`;
                                el.style.transform = `translate(-50%, -100%) scale(${v.scale})`;
                            });
                        }

                        function animate() {
                            requestAnimationFrame(animate);
                            stepCameraAnimation();
                            controls.update();
                            renderer.render(scene, camera);
                            actualizarEtiquetas();
                        }
                        animate();

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