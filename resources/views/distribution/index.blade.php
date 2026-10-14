@extends('layouts.app')

@section('title', 'Distribución física de la droguería')
@section('subtitle', 'Mapa interactivo de la distribución física del almacén.')
@section('content')
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

    <!-- Barra Superior: Navegación -->

    <div class="w-full">
        <!-- Encabezado Principal -->



        <!-- migas de pan -->
        <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-2 px-1 pt-2">
            <span class="text-slate-400 font-semibold">03</span>
            <span>Salida</span>
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-semibold text-slate-800">Distribución</span>
        </div>


        <!-- BOTONES MANTENEDORES -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- BOTÓN PASILLO -->
            <a href="{{ route('aisle.index') }}"
                class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">

                <div class="flex items-center gap-3">

                    <!-- ICONO PASILLO -->
                    <div
                        class="w-16 h-16 bg-orange-100 border border-orange-200 rounded-full flex items-center justify-center shrink-0">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 403 403" fill="none" stroke="currentColor"
                            stroke-width="9" stroke-linecap="round" stroke-linejoin="round"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                        <button @click="toggleFullScreen()" title="Pantalla Completa"
                            class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <select
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


                    </div>
                </aside>

                <!-- Tooltip (Panel de Detalle Flotante) -->
                <div x-show="tooltipVisible" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" style="display: none;"
                    class="absolute bottom-4 left-4 right-4 sm:bottom-auto sm:left-auto sm:top-24 sm:right-6 sm:w-72 glass-panel text-white rounded-xl p-4 sm:p-5 shadow-2xl z-30 pointer-events-auto border border-gray-700">

                    <div
                        class="border-b border-gray-700 pb-2 sm:pb-3 mb-2 sm:mb-3 relative flex justify-between items-center">
                        <span class="font-bold text-base sm:text-lg tracking-wide text-white"
                            x-text="rackData.code"></span>
                        <button @click="tooltipVisible = false"
                            class="text-gray-400 hover:text-white bg-slate-800 rounded p-1">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs sm:text-sm space-y-1 sm:space-y-2 text-gray-300">
                        <p class="font-medium text-white" x-text="rackData.product"></p>
                        <p>Lote: <span class="font-mono bg-slate-800 px-1 py-0.5 rounded" x-text="rackData.lot"></span>
                        </p>
                        <p>Venc: <span x-text="rackData.venc"></span></p>
                        <p>Stock: <span x-text="rackData.stock"></span></p>
                    </div>

                    <div class="mt-3 sm:mt-4 mb-3 sm:mb-4">
                        <span class="px-2 py-1 text-[10px] sm:text-xs font-semibold rounded-md border inline-block"
                            :class="{
                                'bg-green-900/40 text-green-400 border-green-800': rackData
                                    .status === 'Disponible',
                                'bg-blue-900/40 text-blue-400 border-blue-800': rackData.status === 'Ocupado',
                                'bg-orange-900/40 text-orange-400 border-orange-800': rackData
                                    .status === 'Cuarentena',
                                'bg-purple-900/40 text-purple-400 border-purple-800': rackData
                                    .status === 'Reservado',
                                'bg-slate-700/60 text-slate-300 border-slate-600': rackData
                                    .status === 'Mantenimiento'
                            }"
                            x-text="rackData.status"></span>
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
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
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
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
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
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
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
                    alert(`Redirigiendo a detalle de la posición: ${this.rackData.code}`);
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
                    const palleteGeo = new THREE.BoxGeometry(4, 3, 4);




                    // Convertimos la variable de Laravel a un objeto de JavaScript
                    const dbShelves = @json($shelves);

                    function createSingleRack(x, z, material, data) {
                        // Crear la malla 3D individual
                        const mesh = new THREE.Mesh(palleteGeo, material.clone());
                        mesh.position.set(x, 1.5, z); // Altura fija en Y = 1.5
                        mesh.castShadow = true;
                        mesh.receiveShadow = true;
                        mesh.userData = data;
                        mesh.userData.originalEmissive = mesh.material.emissive.getHex();

                        racks.push(mesh);
                        scene.add(mesh);

                        // Crear el marcador HTML flotante (la etiqueta con el nombre)
                        const centerVec = new THREE.Vector3(x, 4.5, z);
                        const markerDiv = document.createElement('div');

                        let bgClass = 'marker-blue'; // Color por defecto
                        if (data.status === 'Disponible') bgClass = 'marker-green';
                        else if (data.status === 'Ocupado') bgClass = 'marker-blue';
                        else if (data.status === 'Cuarentena') bgClass = 'marker-orange';
                        else if (data.status === 'Reservado') bgClass = 'marker-purple';
                        else if (data.status === 'Mantenimiento') bgClass = 'marker-gray';

                        markerDiv.className =
                            `rack-marker px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold shadow-md ${bgClass}`;
                        markerDiv.textContent = data.code;

                        labelsContainer.appendChild(markerDiv);
                        markers.push({
                            element: markerDiv,
                            pos3D: centerVec
                        });
                    }

                    // Recorrer los datos reales de la BD y dibujarlos
                    dbShelves.forEach(shelf => {

                        const materialPasillo = new THREE.MeshStandardMaterial({
                            color: shelf.aisle ? shelf.aisle.color : '#64748b',
                            roughness: 0.7
                        });

                        const rackData = {
                            id: shelf.id,
                            code: shelf.name,
                            status: 'Disponible',
                            aisle: shelf.aisle ? shelf.aisle.name : 'Sin pasillo'
                        };

                        createSingleRack(
                            shelf.pos_x,
                            shelf.pos_z,
                            materialPasillo,
                            rackData
                        );
                    });

                    // Función para generar bloques de estanterías
                    function createRackCluster(x, z, cols, rows, material, data) {
                        const clusterGroup = new THREE.Group();
                        let centerVec = new THREE.Vector3(0, 0, 0);
                        let count = 0;

                        for (let i = 0; i < cols; i++) {
                            for (let j = 0; j < rows; j++) {
                                const mesh = new THREE.Mesh(palleteGeo, material.clone());
                                mesh.position.set(x + i * 4.2, 1.5, z + j * 4.2);
                                mesh.castShadow = true;
                                mesh.receiveShadow = true;
                                mesh.userData = data;
                                mesh.userData.originalEmissive = mesh.material.emissive.getHex();
                                racks.push(mesh);
                                clusterGroup.add(mesh);
                                centerVec.add(mesh.position);
                                count++;
                            }
                        }
                        scene.add(clusterGroup);

                        // Crear marcador HTML flotante
                        centerVec.divideScalar(count);
                        centerVec.y += 3.5;

                        const markerDiv = document.createElement('div');
                        let bgClass = '';
                        if (data.status === 'Disponible') bgClass = 'marker-green';
                        else if (data.status === 'Ocupado') bgClass = 'marker-blue';
                        else if (data.status === 'Cuarentena') bgClass = 'marker-orange';
                        else if (data.status === 'Reservado') bgClass = 'marker-purple';
                        else bgClass = 'marker-gray';

                        markerDiv.className =
                            `rack-marker px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold shadow-md ${bgClass}`;
                        markerDiv.textContent = data.code;
                        labelsContainer.appendChild(markerDiv);
                        markers.push({
                            element: markerDiv,
                            pos3D: centerVec
                        });
                    }

                    // Poblando los racks dentro del perímetro 80x50


                    // Raycaster (Interacción del Ratón/Touch)
                    const raycaster = new THREE.Raycaster();
                    const mouse = new THREE.Vector2();
                    let hoveredObject = null;
                    let selectedObject = null;

                    function highlightRack(mesh, isHover) {
                        if (!mesh) return;
                        const targetColor = isHover ? 0x444444 : 0x666666;
                        racks.forEach(r => {
                            if (r.userData.code === mesh.userData.code) r.material.emissive.setHex(targetColor);
                        });
                    }

                    function resetAllHighlights() {
                        racks.forEach(r => r.material.emissive.setHex(0x000000));
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
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive
                                .setHex(0x000000);
                            hoveredObject = null;
                            wrapper.style.cursor = 'default';
                            return;
                        }

                        getMousePos(event);
                        raycaster.setFromCamera(mouse, camera);
                        const intersects = raycaster.intersectObjects(racks);

                        if (intersects.length > 0) {
                            if (hoveredObject !== intersects[0].object) {
                                if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material
                                    .emissive.setHex(0x000000);
                                hoveredObject = intersects[0].object;
                                wrapper.style.cursor = 'pointer';
                                if (hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                            }
                        } else {
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive
                                .setHex(0x000000);
                            hoveredObject = null;
                            wrapper.style.cursor = 'default';
                        }
                    });



                    // Búsqueda y Cámaras
                    window.addEventListener('search-rack', (e) => {
                        const query = e.detail.toLowerCase();
                        resetAllHighlights();
                        if (!query) return;
                        const found = racks.find(r => r.userData.code.toLowerCase().includes(query));
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
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive
                                .setHex(0x000000);
                            hoveredObject = null;
                            wrapper.style.cursor = 'default';
                            return;
                        }

                        const intersects = raycaster.intersectObjects(racks);
                        if (intersects.length > 0) {
                            if (hoveredObject !== intersects[0].object) {
                                if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material
                                    .emissive.setHex(0x000000);
                                hoveredObject = intersects[0].object;
                                wrapper.style.cursor = 'pointer';
                                if (hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                            }
                        } else {
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive
                                .setHex(0x000000);
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
