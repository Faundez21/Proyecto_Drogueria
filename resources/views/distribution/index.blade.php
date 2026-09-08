@extends('layouts.app')

@section('content')
    <!-- Scripts requeridos -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

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
        .marker-green { background-color: #22c55e; color: white; }
        .marker-green::after { border-color: #22c55e transparent transparent transparent; }
        
        .marker-blue { background-color: #3b82f6; color: white; }
        .marker-blue::after { border-color: #3b82f6 transparent transparent transparent; }
        
        .marker-orange { background-color: #f97316; color: white; }
        .marker-orange::after { border-color: #f97316 transparent transparent transparent; }

        .marker-purple { background-color: #a855f7; color: white; }
        .marker-purple::after { border-color: #a855f7 transparent transparent transparent; }

        .marker-gray { background-color: #64748b; color: white; }
        .marker-gray::after { border-color: #64748b transparent transparent transparent; }
    </style>

    <!-- Barra Superior: Navegación -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Distribución
            </a>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto p-4 sm:p-6">
        <!-- Encabezado Principal -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Distribución de la droguería</h1>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">Distribución física y organización de la bodega</p>
        </div>

        <!-- Sección de Mantenedores (Enlaces Rápidos) -->
        <div class="mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800">Mantenedores</h2>
            <p class="text-gray-500 mt-1 mb-4 text-sm sm:text-base">Acceder a los mantenedores de la distribución física de la droguería</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('pasillos.index') }}" class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Pasillos</h3>
                    <p class="text-gray-500 text-sm mt-1">Administración de pasillos</p>
                </a>
                <a href="{{ route('shelves.index') }}" class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Estanterías</h3>
                    <p class="text-gray-500 text-sm mt-1">Administración de estanterías</p>
                </a>
                <a href="{{ route('levels.index') }}" class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Niveles</h3>
                    <p class="text-gray-500 text-sm mt-1">Administración de niveles</p>
                </a>
                <a href="{{ route('positions.index') }}" class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300 transition-colors">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Posiciones</h3>
                    <p class="text-gray-500 text-sm mt-1">Administración de posiciones</p>
                </a>
            </div>
        </div>

        <!-- Visor 3D Interactivo -->
        <div class="w-full bg-white border border-gray-200 rounded-lg p-3 sm:p-6 shadow-sm">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 hidden sm:block">Vista de almacenamiento</h2>

            <!-- Contenedor Wrapper para Three.js y Alpine.js -->
            <div id="warehouse-wrapper" x-data="warehouseApp()" class="h-[500px] sm:h-[600px] lg:h-[750px] rounded-xl overflow-hidden border border-gray-300 bg-[#eef2f6] select-none">
                
                <!-- Capa HTML para etiquetas (Markers) -->
                <div id="labels-container" class="absolute top-0 left-0 w-full h-full pointer-events-none z-10 overflow-hidden"></div>
                
                <!-- Capa WebGL 3D -->
                <div id="warehouse-3d-container"></div>

                <!-- Botonera Superior -->
                <header class="absolute top-0 left-0 w-full z-20 p-3 sm:p-5 pointer-events-none flex justify-between items-start">
                    <div class="pointer-events-auto">
                        <div class="inline-flex bg-white/90 backdrop-blur p-1 rounded-lg sm:rounded-full border border-gray-200 shadow-sm">
                            <button @click="changeView('2D')" :class="viewMode === '2D' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">2D</button>
                            <button @click="changeView('3D')" :class="viewMode === '3D' ? 'bg-blue-500 text-white shadow' : 'text-gray-500 hover:text-gray-700'" class="px-3 sm:px-5 py-1.5 text-xs sm:text-sm font-semibold rounded-md sm:rounded-full transition">3D</button>
                        </div>
                    </div>

                    <div class="pointer-events-auto flex gap-1.5 sm:gap-2">
                        <button @click="resetCamera()" title="Centrar Vista" class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </button>
                        <button @click="zoomOut()" title="Alejar" class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition hidden sm:flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </button>
                        <button @click="zoomIn()" title="Acercar" class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition hidden sm:flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                        <button @click="toggleFullScreen()" title="Pantalla Completa" class="w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-white transition">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </button>
                    </div>
                </header>

                <!-- Sidebar Colapsable (Selector, Búsqueda y Leyenda exacto a la imagen) -->
                <aside class="absolute top-16 sm:top-24 left-3 sm:left-6 w-48 sm:w-56 glass-panel text-white rounded-xl shadow-2xl z-20 flex flex-col pointer-events-auto transition-all duration-300">
                    <!-- Botón para colapsar -->
                    <div @click="sidebarOpen = !sidebarOpen" class="flex justify-between items-center p-3 cursor-pointer hover:bg-slate-800/50 rounded-t-xl" :class="{'rounded-b-xl': !sidebarOpen}">
                        <span class="text-xs sm:text-sm font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Panel de Control
                        </span>
                        <svg :class="{'rotate-180': !sidebarOpen}" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </div>
                    
                    <!-- Contenido interior del Sidebar -->
                    <div x-show="sidebarOpen" x-transition class="p-3 sm:p-4 pt-0 border-t border-slate-700/50 mt-1 flex flex-col gap-3">
                        <div class="relative">
                            <select class="w-full bg-[#1e293b] border border-slate-700 rounded-lg p-2.5 text-xs sm:text-sm text-white font-medium focus:outline-none focus:border-blue-500 cursor-pointer appearance-none">
                                <option>Nivel 1</option>
                                <option>Nivel 2</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchQuery" @input="handleSearch" placeholder="Buscar ubicación... (Ej: A-01)" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg py-2.5 pl-9 pr-3 text-xs sm:text-sm text-gray-300 focus:outline-none focus:border-blue-500 placeholder-gray-500 transition">
                        </div>

                        <!-- Leyenda de colores -->
                        <div class="flex flex-col gap-3 mt-2 text-xs sm:text-sm font-medium text-gray-300">
                            <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#22c55e]"></span> Disponible</div>
                            <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span> Ocupado</div>
                            <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#f97316]"></span> Cuarentena</div>
                            <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#a855f7]"></span> Reservado</div>
                            <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#64748b]"></span> Mantenimiento</div>
                        </div>
                    </div>
                </aside>

                <!-- Tooltip (Panel de Detalle Flotante) -->
                <div x-show="tooltipVisible"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="display: none;"
                     class="absolute bottom-4 left-4 right-4 sm:bottom-auto sm:left-auto sm:top-24 sm:right-6 sm:w-72 glass-panel text-white rounded-xl p-4 sm:p-5 shadow-2xl z-30 pointer-events-auto border border-gray-700">

                    <div class="border-b border-gray-700 pb-2 sm:pb-3 mb-2 sm:mb-3 relative flex justify-between items-center">
                        <span class="font-bold text-base sm:text-lg tracking-wide text-white" x-text="rackData.code"></span>
                        <button @click="tooltipVisible = false" class="text-gray-400 hover:text-white bg-slate-800 rounded p-1">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="text-xs sm:text-sm space-y-1 sm:space-y-2 text-gray-300">
                        <p class="font-medium text-white" x-text="rackData.product"></p>
                        <p>Lote: <span class="font-mono bg-slate-800 px-1 py-0.5 rounded" x-text="rackData.lot"></span></p>
                        <p>Venc: <span x-text="rackData.venc"></span></p>
                        <p>Stock: <span x-text="rackData.stock"></span></p>
                    </div>

                    <div class="mt-3 sm:mt-4 mb-3 sm:mb-4">
                        <span class="px-2 py-1 text-[10px] sm:text-xs font-semibold rounded-md border inline-block"
                              :class="{
                                  'bg-green-900/40 text-green-400 border-green-800': rackData.status === 'Disponible',
                                  'bg-blue-900/40 text-blue-400 border-blue-800': rackData.status === 'Ocupado',
                                  'bg-orange-900/40 text-orange-400 border-orange-800': rackData.status === 'Cuarentena',
                                  'bg-purple-900/40 text-purple-400 border-purple-800': rackData.status === 'Reservado',
                                  'bg-slate-700/60 text-slate-300 border-slate-600': rackData.status === 'Mantenimiento'
                              }" x-text="rackData.status"></span>
                    </div>
                    <button @click="verDetalle()" class="w-full py-2 bg-[#1e293b] hover:bg-slate-700 border border-gray-600 text-xs sm:text-sm text-gray-200 font-medium rounded-lg transition shadow-inner">Ver detalle completo</button>
                </div>

                <!-- Bottom Stats Bar (Estadísticas globales) -->
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-[95%] max-w-4xl glass-panel text-white rounded-xl p-4 shadow-2xl z-20 hidden lg:flex justify-between items-center px-8 divide-x divide-gray-700">
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                        <div class="p-2 bg-[#1e293b] rounded-lg text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg></div>
                        <div><p class="text-[10px] xl:text-xs text-gray-400">Ubicaciones</p><p class="text-base xl:text-lg font-bold">48</p></div>
                    </div>
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                        <div class="p-2 bg-[#1e293b] rounded-lg text-blue-400 border border-blue-900/50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></div>
                        <div class="flex-1 flex justify-between items-center">
                            <div><p class="text-[10px] xl:text-xs text-gray-400">Ocupación</p><p class="text-base xl:text-lg font-bold">68%</p></div>
                            <span class="text-xs text-gray-400">32/48</span>
                        </div>
                    </div>
                    <div class="flex-1 px-4 flex items-center gap-4 hover:scale-105 transition-transform cursor-default">
                        <div class="p-2 bg-[#1e293b] rounded-lg text-purple-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>
                        <div><p class="text-[10px] xl:text-xs text-gray-400">Cap. Total</p><p class="text-base xl:text-lg font-bold">2,500 m³</p></div>
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
                    window.dispatchEvent(new CustomEvent('update-camera-view', { detail: mode }));
                },
                zoomIn() { window.dispatchEvent(new Event('cam-zoom-in')); },
                zoomOut() { window.dispatchEvent(new Event('cam-zoom-out')); },
                resetCamera() { window.dispatchEvent(new Event('cam-reset')); },
                toggleFullScreen() {
                    const wrapper = document.getElementById('warehouse-wrapper');
                    if (!document.fullscreenElement) {
                        wrapper.requestFullscreen().catch(err => console.error(err));
                    } else {
                        document.exitFullscreen();
                    }
                },
                handleSearch() {
                    window.dispatchEvent(new CustomEvent('search-rack', { detail: this.searchQuery }));
                },
                verDetalle() {
                    alert(`Redirigiendo a detalle de la posición: ${this.rackData.code}`);
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

                    const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: "high-performance" });
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
                    const floorMat = new THREE.MeshStandardMaterial({ color: '#cbd5e1', roughness: 0.8 });
                    const floor = new THREE.Mesh(new THREE.BoxGeometry(80, 1, 50), floorMat);
                    floor.position.set(0, -0.5, 0); 
                    floor.receiveShadow = true; 
                    buildGroup.add(floor);

                    // Materiales para las paredes (Cristal semi-transparente + Zócalo sólido)
                    const wallMat = new THREE.MeshPhysicalMaterial({ 
                        color: '#94a3b8', transparent: true, opacity: 0.25, roughness: 0.1, side: THREE.DoubleSide 
                    });
                    const wallSolidMat = new THREE.MeshStandardMaterial({ color: '#64748b' });

                    function createBorderWall(w, h, d, x, y, z) {
                        // Pared transparente superior
                        const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
                        wall.position.set(x, y + 1, z); // Subir un poco para dar espacio al zócalo
                        buildGroup.add(wall);
                        
                        // Zócalo inferior (base sólida de la pared)
                        const baseHeight = 1.5;
                        const base = new THREE.Mesh(new THREE.BoxGeometry(w, baseHeight, d), wallSolidMat);
                        base.position.set(x, baseHeight/2, z);
                        buildGroup.add(base);
                    }

                    // Generar 4 paredes perimetrales alrededor del piso (80x50)
                    createBorderWall(80, 10, 1, 0, 4, -25); // Pared Norte (Atrás)
                    createBorderWall(80, 10, 1, 0, 4, 25);  // Pared Sur (Frente)
                    createBorderWall(1, 10, 50, 40, 4, 0);  // Pared Este (Derecha)
                    createBorderWall(1, 10, 50, -40, 4, 0); // Pared Oeste (Izquierda)

                    scene.add(buildGroup);
                    // ------------------------------------------------------

                    // Arrays de gestión para los Racks
                    const racks = [];
                    const markers = [];
                    const palleteGeo = new THREE.BoxGeometry(4, 3, 4);

                    // Materiales (5 Colores)
                    const matVerde = new THREE.MeshStandardMaterial({ color: '#22c55e', roughness: 0.7 });
                    const matAzul = new THREE.MeshStandardMaterial({ color: '#3b82f6', roughness: 0.7 });
                    const matNaranja = new THREE.MeshStandardMaterial({ color: '#f97316', roughness: 0.7 });
                    const matMorado = new THREE.MeshStandardMaterial({ color: '#a855f7', roughness: 0.7 });
                    const matGris = new THREE.MeshStandardMaterial({ color: '#64748b', roughness: 0.7 });

                    // Función para generar bloques de estanterías
                    function createRackCluster(x, z, cols, rows, material, data) {
                        const clusterGroup = new THREE.Group();
                        let centerVec = new THREE.Vector3(0,0,0);
                        let count = 0;

                        for(let i=0; i<cols; i++) {
                            for(let j=0; j<rows; j++) {
                                const mesh = new THREE.Mesh(palleteGeo, material.clone());
                                mesh.position.set(x + i*4.2, 1.5, z + j*4.2);
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
                        if(data.status === 'Disponible') bgClass = 'marker-green';
                        else if(data.status === 'Ocupado') bgClass = 'marker-blue';
                        else if(data.status === 'Cuarentena') bgClass = 'marker-orange';
                        else if(data.status === 'Reservado') bgClass = 'marker-purple';
                        else bgClass = 'marker-gray';

                        markerDiv.className = `rack-marker px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold shadow-md ${bgClass}`;
                        markerDiv.textContent = data.code;
                        labelsContainer.appendChild(markerDiv);
                        markers.push({ element: markerDiv, pos3D: centerVec });
                    }

                    // Poblando los racks dentro del perímetro 80x50
                    createRackCluster(-25, -15, 4, 2, matVerde, { code: 'A-01', status: 'Disponible', product: 'Ibuprofeno 400mg', stock: '1,200', lot: 'LOT-555', venc: '12/10/2025' });
                    createRackCluster(-5, -15, 2, 3, matAzul, { code: 'B-01', status: 'Ocupado', product: 'Paracetamol', stock: '850', lot: 'LOT-123', venc: '30/06/2026' });
                    createRackCluster(15, -15, 2, 3, matNaranja, { code: 'C-01', status: 'Cuarentena', product: 'Aspirina Vencida', stock: '0', lot: 'ERR-99', venc: 'VENCIDO' });
                    
                    createRackCluster(-25, 5, 2, 2, matMorado, { code: 'D-01', status: 'Reservado', product: 'Amoxicilina 500mg', stock: '400 (Req. #144)', lot: 'LOT-881', venc: '14/02/2027' });
                    createRackCluster(-10, 5, 4, 2, matAzul, { code: 'D-02', status: 'Ocupado', product: 'Loratadina', stock: '2,000', lot: 'LOT-101', venc: '10/05/2026' });
                    createRackCluster(15, 5, 3, 2, matGris, { code: 'E-01', status: 'Mantenimiento', product: 'Vacio', stock: '0', lot: 'N/A', venc: 'N/A' });

                    // Raycaster (Interacción del Ratón/Touch)
                    const raycaster = new THREE.Raycaster();
                    const mouse = new THREE.Vector2();
                    let hoveredObject = null;
                    let selectedObject = null;

                    function highlightRack(mesh, isHover) {
                        if (!mesh) return;
                        const targetColor = isHover ? 0x444444 : 0x666666;
                        racks.forEach(r => { if(r.userData.code === mesh.userData.code) r.material.emissive.setHex(targetColor); });
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
                        if (event.target.closest('header') || event.target.closest('aside') || event.target.closest('.z-30') || event.target.closest('.bottom-6')) {
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive.setHex(0x000000);
                            hoveredObject = null;
                            wrapper.style.cursor = 'default';
                            return;
                        }
                        
                        getMousePos(event);
                        raycaster.setFromCamera(mouse, camera);
                        const intersects = raycaster.intersectObjects(racks);

                        if (intersects.length > 0) {
                            if (hoveredObject !== intersects[0].object) {
                                if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive.setHex(0x000000);
                                hoveredObject = intersects[0].object;
                                wrapper.style.cursor = 'pointer';
                                if(hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                            }
                        } else {
                            if (hoveredObject && hoveredObject !== selectedObject) hoveredObject.material.emissive.setHex(0x000000);
                            hoveredObject = null;
                            wrapper.style.cursor = 'default';
                        }
                    });

                    // Clic logic
                    wrapper.addEventListener('click', (event) => {
                        if (event.target.closest('header') || event.target.closest('aside') || event.target.closest('.z-30') || event.target.closest('.bottom-6')) return;
                        
                        getMousePos(event);
                        raycaster.setFromCamera(mouse, camera);
                        const intersects = raycaster.intersectObjects(racks);
                        
                        if (intersects.length > 0) {
                            resetAllHighlights();
                            selectedObject = intersects[0].object;
                            highlightRack(selectedObject, false);
                            window.dispatchEvent(new CustomEvent('rack-selected', { detail: selectedObject.userData }));
                            controls.target.lerp(selectedObject.position, 0.5);
                        } else {
                            resetAllHighlights();
                            window.dispatchEvent(new Event('click-empty'));
                        }
                    });

                    // Búsqueda y Cámaras
                    window.addEventListener('search-rack', (e) => {
                        const query = e.detail.toLowerCase();
                        resetAllHighlights();
                        if(!query) return;
                        const found = racks.find(r => r.userData.code.toLowerCase().includes(query));
                        if(found) {
                            selectedObject = found;
                            highlightRack(found, false);
                            window.dispatchEvent(new CustomEvent('rack-selected', { detail: found.userData }));
                            controls.target.lerp(found.position, 1);
                        }
                    });

                    window.addEventListener('cam-zoom-in', () => { camera.position.lerp(controls.target, 0.3); });
                    window.addEventListener('cam-zoom-out', () => {
                        let dir = new THREE.Vector3().subVectors(camera.position, controls.target).multiplyScalar(1.3);
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
                            const { width, height } = entry.contentRect;
                            if(width > 0 && height > 0) {
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