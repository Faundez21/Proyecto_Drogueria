<!DOCTYPE html>
<<<<<<< HEAD
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel con Tailwind v4</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-sans">
    
    <div class="bg-white rounded-lg shadow-md p-8 max-w-md w-full text-center">
        
        <!-- Icono sencillo -->
        <div class="bg-blue-500 rounded-full w-16 h-16 mx-auto flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Proyecto Iniciado</h1>
        
        <p class="text-gray-600 mb-6">
            Tailwind CSS está configurado. Un diseño limpio y listo para empezar a trabajar.
        </p>
        
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
            Comenzar proyecto
        </button>
        
    </div>
    
=======
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenamiento (Mapa físico)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <style>
        #warehouse-3d-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1; 
        }
        .glass-panel {
            background-color: #0f172a; 
            border: 1px solid #1e293b;
        }
        /* Estilos para las etiquetas flotantes (Markers) */
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
        .marker-green { background-color: #22c55e; color: white; }
        .marker-green::after { border-color: #22c55e transparent transparent transparent; }
        
        .marker-blue { background-color: #3b82f6; color: white; }
        .marker-blue::after { border-color: #3b82f6 transparent transparent transparent; }

        .marker-orange { background-color: #f97316; color: white; }
        .marker-orange::after { border-color: #f97316 transparent transparent transparent; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden h-screen flex flex-col" x-data="warehouseApp()">

    <!-- Contenedor para las etiquetas HTML flotantes sincronizadas con 3D -->
    <div id="labels-container" class="absolute top-0 left-0 w-full h-full pointer-events-none z-10 overflow-hidden"></div>

    <!-- Header & Controles Superiores -->
    <header class="absolute top-0 left-0 w-full z-20 p-6 pointer-events-none flex justify-between items-start">
        <div class="pointer-events-auto">
            <h1 class="text-xl font-bold text-gray-900 mb-4">Almacenamiento (Mapa físico)</h1>
            <div class="inline-flex bg-white p-1 rounded-full border border-gray-200 shadow-sm">
                <button @click="changeView('2D')" :class="viewMode === '2D' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-1.5 text-sm font-semibold rounded-full transition">Vista 2D</button>
                <button @click="changeView('3D')" :class="viewMode === '3D' ? 'bg-blue-500 text-white shadow' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-1.5 text-sm font-semibold rounded-full transition">Vista 3D</button>
            </div>
        </div>
        
        <!-- Botones de Zoom y Pantalla Completa (Funcionales) -->
        <div class="pointer-events-auto flex gap-2">
            <button @click="resetCamera()" title="Centrar Vista" class="w-10 h-10 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
            </button>
            <button @click="zoomOut()" title="Alejar" class="w-10 h-10 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
            </button>
            <button @click="zoomIn()" title="Acercar" class="w-10 h-10 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
            <button @click="toggleFullScreen()" title="Pantalla Completa" class="w-10 h-10 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
            </button>
        </div>
    </header>

    <div class="relative flex-1 flex overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="absolute top-32 left-6 w-64 glass-panel text-white rounded-xl p-5 shadow-2xl z-20 flex flex-col gap-4 pointer-events-auto">
            <div class="relative">
                <select class="w-full bg-[#1e293b] border-none rounded-lg p-2.5 text-sm text-white font-medium focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none">
                    <option>Nivel 1</option>
                    <option>Nivel 2</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            
            <!-- Buscador Funcional -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="searchQuery" @input="handleSearch" placeholder="Buscar ubicación... (Ej: A-01)" class="w-full bg-[#1e293b] border-none rounded-lg py-2.5 pl-9 pr-3 text-sm text-gray-300 focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition">
            </div>
            
            <div class="flex flex-col gap-3 mt-2 text-sm font-medium text-gray-300">
                <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#22c55e]"></span> Disponible</div>
                <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span> Ocupado</div>
                <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#f97316]"></span> Cuarentena</div>
                <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#a855f7]"></span> Reservado</div>
                <div class="flex items-center gap-3"><span class="w-3.5 h-3.5 rounded bg-[#64748b]"></span> Mantenimiento</div>
            </div>
        </aside>

        <!-- Canvas 3D -->
        <div id="warehouse-3d-container"></div>

        <!-- Tooltip (Detalle de Ubicación) -->
        <div x-show="tooltipVisible" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;"
             class="absolute top-24 right-96 w-72 glass-panel text-white rounded-xl p-5 shadow-2xl z-30 pointer-events-auto border border-gray-700">
            
            <div class="border-b border-gray-700 pb-3 mb-3 relative flex justify-between items-center">
                <span class="font-bold text-lg tracking-wide text-white" x-text="rackData.code"></span>
                <button @click="tooltipVisible = false" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="text-sm space-y-2 text-gray-300">
                <p x-text="rackData.product"></p>
                <p>Lote: <span x-text="rackData.lot"></span></p>
                <p>Venc: <span x-text="rackData.venc"></span></p>
                <p>Stock: <span x-text="rackData.stock"></span></p>
            </div>
            
            <div class="mt-4 mb-4">
                <span class="px-3 py-1 text-xs font-semibold rounded-md border inline-block" 
                      :class="{
                          'bg-green-900/40 text-green-400 border-green-800': rackData.status === 'Disponible',
                          'bg-blue-900/40 text-blue-400 border-blue-800': rackData.status === 'Ocupado',
                          'bg-orange-900/40 text-orange-400 border-orange-800': rackData.status === 'Cuarentena'
                      }" 
                      x-text="rackData.status"></span>
            </div>
            <button @click="verDetalle()" class="w-full py-2 bg-[#1e293b] hover:bg-slate-700 border border-gray-600 text-sm text-gray-200 font-medium rounded-lg transition shadow-inner">Ver detalle</button>
        </div>

        <!-- Minimapa -->
        <div class="absolute bottom-28 right-6 w-32 h-24 bg-white/90 backdrop-blur rounded-xl border border-gray-200 shadow-lg z-20 flex items-center justify-center pointer-events-none p-2">
            <svg viewBox="0 0 100 100" class="w-full h-full text-gray-400 stroke-current" fill="none" stroke-width="2">
                <polygon points="10,30 50,20 90,40 70,80 40,90 20,60" />
                <!-- Indicador de cámara simple -->
                <circle cx="50" cy="50" r="4" fill="#3b82f6" stroke="none" />
            </svg>
        </div>

        <!-- Bottom Stats Bar -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-[95%] max-w-6xl glass-panel text-white rounded-xl p-4 shadow-2xl z-20 flex justify-between items-center px-8 divide-x divide-gray-700">
            <div class="flex-1 px-4 flex items-center gap-4">
                <div class="p-2 bg-[#1e293b] rounded-lg text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Ubicaciones totales</p>
                    <p class="text-lg font-bold">48</p>
                </div>
            </div>
            <div class="flex-1 px-4 flex items-center gap-4">
                <div class="p-2 bg-[#1e293b] rounded-lg text-green-400 border border-green-900/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-400">Ocupación</p>
                        <p class="text-lg font-bold">68%</p>
                    </div>
                    <span class="text-sm text-gray-400">32 / 48</span>
                </div>
            </div>
            <div class="flex-1 px-4 flex items-center gap-4">
                <div class="p-2 bg-[#1e293b] rounded-lg text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Capacidad total</p>
                    <p class="text-lg font-bold">2,500 m³</p>
                </div>
            </div>
            <div class="flex-1 px-4 flex items-center gap-4">
                <div class="p-2 bg-[#1e293b] rounded-lg text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Capacidad utilizada</p>
                    <p class="text-lg font-bold">1,725 m³</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function warehouseApp() {
            return {
                viewMode: '3D',
                tooltipVisible: false,
                rackData: {},
                searchQuery: '',
                init() {
                    window.addEventListener('rack-selected', (e) => {
                        this.rackData = e.detail;
                        this.tooltipVisible = true;
                    });
                    
                    // Ocultar tooltip al hacer clic en un área vacía
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
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => console.log(err));
                    } else {
                        document.exitFullscreen();
                    }
                },
                handleSearch() {
                    window.dispatchEvent(new CustomEvent('search-rack', { detail: this.searchQuery }));
                },
                verDetalle() {
                    alert(`Navegando a los detalles de la ubicación: ${this.rackData.code}\nProducto: ${this.rackData.product}`);
                }
            }
        }

        // --- THREE.JS SETUP ---
        const container = document.getElementById('warehouse-3d-container');
        const labelsContainer = document.getElementById('labels-container');
        
        const scene = new THREE.Scene();
        scene.background = new THREE.Color('#f8fafc'); 

        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 1000);
        const defaultCamPos = new THREE.Vector3(60, 55, 60);
        camera.position.copy(defaultCamPos);
        
        const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: "high-performance" });
        renderer.setSize(window.innerWidth, window.innerHeight);
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
        dirLight.position.set(30, 60, 20);
        dirLight.castShadow = true;
        dirLight.shadow.mapSize.width = 2048;
        dirLight.shadow.mapSize.height = 2048;
        scene.add(dirLight);

        // Estructura del Edificio
        const buildGroup = new THREE.Group();
        const floorMat = new THREE.MeshStandardMaterial({ color: '#e2e8f0' });
        
        const floor1 = new THREE.Mesh(new THREE.BoxGeometry(40, 1, 30), floorMat);
        floor1.position.set(0, -0.5, 0); floor1.receiveShadow = true; buildGroup.add(floor1);

        const floor2 = new THREE.Mesh(new THREE.BoxGeometry(20, 1, 20), floorMat);
        floor2.position.set(-30, -0.5, 5); floor2.receiveShadow = true; buildGroup.add(floor2);

        const wallMat = new THREE.MeshPhysicalMaterial({ color: '#cbd5e1', transparent: true, opacity: 0.5, roughness: 0.2, side: THREE.DoubleSide });
        const trimMat = new THREE.MeshStandardMaterial({ color: '#475569' }); 

        function createWall(w, h, d, x, y, z) {
            const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
            wall.position.set(x, y, z);
            buildGroup.add(wall);
            const trim = new THREE.Mesh(new THREE.BoxGeometry(w + 0.1, 0.5, d + 0.1), trimMat);
            trim.position.set(x, y + h/2, z);
            buildGroup.add(trim);
        }

        createWall(40, 5, 0.5, 0, 2, -15);
        createWall(0.5, 5, 30, 20, 2, 0);
        createWall(40, 5, 0.5, 0, 2, 15);
        createWall(0.5, 5, 10, -20, 2, -10);
        createWall(20, 5, 0.5, -30, 2, -5);
        createWall(0.5, 5, 20, -40, 2, 5);
        createWall(20, 5, 0.5, -30, 2, 15);
        scene.add(buildGroup);

        // Sistema de Racks y Marcadores HTML
        const racks = [];
        const markers = []; // Para sincronizar HTML con 3D
        const palleteGeo = new THREE.BoxGeometry(4, 3, 4);
        
        const matVerde = new THREE.MeshStandardMaterial({ color: '#4ade80', roughness: 0.7 }); 
        const matAzul = new THREE.MeshStandardMaterial({ color: '#3b82f6', roughness: 0.7 });  
        const matNaranja = new THREE.MeshStandardMaterial({ color: '#f97316', roughness: 0.7 }); 

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

            // Crear Marcador HTML Flotante para el grupo
            centerVec.divideScalar(count);
            centerVec.y += 3.5; // Altura sobre el bloque
            
            const markerDiv = document.createElement('div');
            let bgClass = data.status === 'Disponible' ? 'marker-green' : (data.status === 'Cuarentena' ? 'marker-orange' : 'marker-blue');
            markerDiv.className = `rack-marker px-2 py-0.5 rounded text-xs font-bold shadow-md ${bgClass}`;
            markerDiv.textContent = data.code;
            labelsContainer.appendChild(markerDiv);
            
            markers.push({ element: markerDiv, pos3D: centerVec });
        }

        // Poblando datos
        createRackCluster(-5, -10, 3, 2, matVerde, { code: 'A-01', status: 'Disponible', product: 'Ibuprofeno 400mg', stock: '1,200 uds', lot: 'LOT-555', venc: '12/10/2025' });
        createRackCluster(-5, 2, 3, 2, matVerde, { code: 'A-02', status: 'Disponible', product: 'Amoxicilina', stock: '850 uds', lot: 'LOT-666', venc: '05/01/2026' });
        createRackCluster(10, -8, 2, 3, matAzul, { code: 'B-02-01', status: 'Ocupado', product: 'Paracetamol 500mg', stock: '350 uds', lot: 'LOT-12345', venc: '30/06/2026' });
        createRackCluster(8, 8, 2, 1, matAzul, { code: 'C-01', status: 'Ocupado', product: 'Vitamina C', stock: '50 uds', lot: 'LOT-999', venc: '15/08/2024' });
        createRackCluster(10, 13, 1, 1, matNaranja, { code: 'C-02', status: 'Cuarentena', product: 'Aspirina', stock: '0 uds', lot: 'LOT-ERROR', venc: 'N/A' });
        createRackCluster(-25, -2, 2, 3, matAzul, { code: 'D-01', status: 'Ocupado', product: 'Loratadina', stock: '500 uds', lot: 'LOT-001', venc: '10/11/2025' });
        createRackCluster(-35, -2, 2, 3, matAzul, { code: 'D-02', status: 'Ocupado', product: 'Omeprazol', stock: '400 uds', lot: 'LOT-002', venc: '22/02/2027' });

        // Interacción: Hover & Click
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();
        let hoveredObject = null;
        let selectedObject = null;

        function highlightRack(mesh, isHover) {
            if (!mesh) return;
            const targetColor = isHover ? 0x444444 : 0x666666;
            // Resaltar todos los bloques que compartan el mismo código
            racks.forEach(r => {
                if(r.userData.code === mesh.userData.code) {
                    r.material.emissive.setHex(targetColor);
                }
            });
        }

        function resetHighlight(mesh) {
            if (!mesh) return;
            racks.forEach(r => {
                if(r.userData.code === mesh.userData.code && r !== selectedObject) {
                    r.material.emissive.setHex(0x000000);
                }
            });
        }

        function resetAllHighlights() {
            racks.forEach(r => r.material.emissive.setHex(0x000000));
            selectedObject = null;
        }

        window.addEventListener('pointermove', (event) => {
            if (event.target.closest('header') || event.target.closest('aside') || event.target.closest('.z-30') || event.target.closest('.bottom-6')) {
                if (hoveredObject && hoveredObject !== selectedObject) resetHighlight(hoveredObject);
                hoveredObject = null;
                document.body.style.cursor = 'default';
                return;
            }

            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            
            const intersects = raycaster.intersectObjects(racks);

            if (intersects.length > 0) {
                const object = intersects[0].object;
                if (hoveredObject !== object) {
                    if (hoveredObject && hoveredObject !== selectedObject) resetHighlight(hoveredObject);
                    hoveredObject = object;
                    document.body.style.cursor = 'pointer';
                    if(hoveredObject !== selectedObject) highlightRack(hoveredObject, true);
                }
            } else {
                if (hoveredObject && hoveredObject !== selectedObject) resetHighlight(hoveredObject);
                hoveredObject = null;
                document.body.style.cursor = 'default';
            }
        });

        window.addEventListener('click', (event) => {
            // Ignorar clicks en UI
            if (event.target.closest('header') || event.target.closest('aside') || event.target.closest('.z-30') || event.target.closest('.bottom-6')) return;

            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            
            const intersects = raycaster.intersectObjects(racks);
            if (intersects.length > 0) {
                resetAllHighlights();
                selectedObject = intersects[0].object;
                highlightRack(selectedObject, false);
                window.dispatchEvent(new CustomEvent('rack-selected', { detail: selectedObject.userData }));
                
                // Animar cámara suavemente hacia el objeto
                controls.target.lerp(selectedObject.position, 0.5);
            } else {
                resetAllHighlights();
                window.dispatchEvent(new Event('click-empty'));
            }
        });

        // Eventos Personalizados (Buscador, Controles UI)
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

        window.addEventListener('cam-zoom-in', () => { camera.position.lerp(controls.target, 0.2); });
        window.addEventListener('cam-zoom-out', () => { 
            let dir = new THREE.Vector3().subVectors(camera.position, controls.target).multiplyScalar(1.2);
            camera.position.copy(controls.target).add(dir); 
        });
        window.addEventListener('cam-reset', () => { 
            camera.position.lerp(defaultCamPos, 1);
            controls.target.lerp(defaultTarget, 1);
        });

        window.addEventListener('update-camera-view', (e) => {
            if (e.detail === '2D') {
                camera.position.set(0, 90, 0); 
                controls.target.set(0, 0, 0);
            } else {
                camera.position.copy(defaultCamPos); 
                controls.target.copy(defaultTarget);
            }
        });

        // Loop de Render y Sincronización de Marcadores
        function animate() {
            requestAnimationFrame(animate);
            controls.update(); 
            renderer.render(scene, camera);

            // Actualizar posiciones de los marcadores HTML
            markers.forEach(marker => {
                const vector = marker.pos3D.clone();
                vector.project(camera);
                
                // Comprobar si el marcador está detrás de la cámara
                if (vector.z > 1) {
                    marker.element.style.display = 'none';
                    return;
                }
                
                marker.element.style.display = 'block';
                const x = (vector.x * .5 + .5) * window.innerWidth;
                const y = (vector.y * -.5 + .5) * window.innerHeight;
                marker.element.style.left = `${x}px`;
                marker.element.style.top = `${y}px`;
            });
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
>>>>>>> cristobal
</body>
</html>