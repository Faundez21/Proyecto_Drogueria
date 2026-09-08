<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - @yield('title', 'Panel')</title>

    <!-- FAVICON -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden flex h-screen">

    <!-- Overlay Móvil -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/60 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

    <!-- Sidebar con tema oscuro -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl lg:shadow-none">

        <!-- Header del Sidebar -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-sm shrink-0">
            <div class="flex items-center gap-2 font-bold text-xl text-white">
                <svg class="w-7 h-7 shrink-0 drop-shadow-sm" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 12 12 L 4 12 A 4 4 0 0 0 0 16 A 4 4 0 0 0 4 20 L 12 20 L 12 28 A 4 4 0 0 0 16 32 A 4 4 0 0 0 20 28 L 20 12 Z" fill="#ffffff" />
                    <path d="M 12 12 L 12 4 A 4 4 0 0 1 16 0 A 4 4 0 0 1 20 4 L 20 12 Z" fill="#3b82f6" />
                    <path d="M 20 12 L 28 12 A 4 4 0 0 1 32 16 A 4 4 0 0 1 28 20 L 20 20 Z" fill="#10b981" />
                </svg>
                <span>Drogueria <span class="text-blue-400">DAS</span></span>
            </div>
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menú de Navegación Principal -->
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">

            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Principal</p>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 border-l-4 {{ request()->routeIs('dashboard') ? 'bg-blue-600/10 text-blue-400 border-blue-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>

            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Operaciones</p>

            <!-- Proveedores -->
            <a href="{{ route('proveedores.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Proveedores
            </a>

            <!-- Inventario -->
            <a href="{{ route('inventario.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Inventario
            </a>

            <!-- Recepción -->
            <a href="{{ route('recepcion.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 border-l-4 {{ request()->routeIs('recepcion.*') ? 'bg-blue-600/10 text-blue-400 border-blue-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                Recepción
            </a>

            <!-- Despacho -->
            <a href="{{ route('despacho.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 border-l-4 {{ request()->routeIs('despacho.*') ? 'bg-blue-600/10 text-blue-400 border-blue-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                Despacho
            </a>

            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Control y Gestión</p>

            <!-- Trazabilidad -->
            <a href="{{ route('trazabilidad.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 border-l-4 {{ request()->routeIs('trazabilidad.*') ? 'bg-blue-600/10 text-blue-400 border-blue-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                Trazabilidad
            </a>

            <!-- Cuarentena -->
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Cuarentena
            </a>

            <!-- Reportes -->
            <a href={{ route('inventario.reporte') }} class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Reportes
            </a>

            <!-- Sistema QR -->
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Generador QR
            </a>

            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Administración</p>

            <!-- Usuarios -->
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 rounded-lg font-medium transition-all duration-200 border-l-4 border-transparent mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Usuarios
            </a>
        </nav>

        <!-- Footer del Sidebar: Navegación Secundaria (Ancla visual) -->
        <div class="p-4 bg-slate-900/50 border-t border-slate-800 shrink-0 space-y-1">
            <!-- Ajustes / Configuración -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:text-white rounded-lg font-medium transition-colors text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Configuración
            </a>

            <!-- Soporte Técnico -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:text-white rounded-lg font-medium transition-colors text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Soporte Técnico
            </a>
        </div>
    </aside>

    <!-- Contenedor principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <!-- Top Navbar con Efecto Glassmorphism -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 z-10 shrink-0 sticky top-0">

            <div class="flex items-center gap-4 w-full">
                <!-- Botón Menú Móvil -->
                <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-lg lg:hidden transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Buscador Simple -->
                <div class="relative hidden sm:block w-96 max-w-md group">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" placeholder="Buscar productos, órdenes..." class="pl-10 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-transparent w-full transition-all shadow-sm">
                </div>
            </div>

            <!-- Acciones Topbar -->
            <div class="flex items-center gap-3">

                <!-- Contenedor de Notificaciones -->
                <div class="relative" id="notifications-container">
                    <button onclick="toggleNotifications()" type="button" class="relative p-2 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none rounded-full hover:bg-blue-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>

                    <!-- Menú Desplegable de Notificaciones -->
                    <div id="notifications-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-100 z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top-right">
                        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                            <h3 class="text-sm font-bold text-slate-800">Notificaciones</h3>
                            <span class="bg-blue-100 text-blue-700 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">2 Nuevas</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition-colors bg-blue-50/30">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-800 font-semibold">Recepción completada</p>
                                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">La orden de compra #4829 fue ingresada a bodega principal.</p>
                                        <p class="text-[10px] font-medium text-blue-500 mt-1">Hace 5 min</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-800 font-semibold">Stock bajo detectado</p>
                                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">Paracetamol 500mg está por debajo del inventario mínimo.</p>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">Hace 2 horas</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <a href="#" class="block px-4 py-3 text-center text-xs text-blue-600 font-semibold hover:bg-slate-50 transition-colors border-t border-slate-100">
                            Ver todas las notificaciones
                        </a>
                    </div>
                </div>

                <!-- Divisor Vertical -->
                <div class="h-6 w-px bg-slate-200 mx-1"></div>

                <!-- Contenedor del Usuario -->
                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" type="button" class="flex items-center gap-2 focus:outline-none rounded-full transition-shadow group">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:ring-2 group-hover:ring-blue-200 transition-all">
                            AD
                        </div>
                    </button>

                    <!-- Menú Desplegable de Usuario (Solo info y cerrar sesión) -->
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-100 z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top-right">

                        <!-- Cabecera de usuario -->
                        <div class="px-4 py-4 border-b border-slate-100 bg-slate-50/50">
                            <p class="text-sm font-bold text-slate-800">Administrador General</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">admin@drogueriadas.com</p>
                        </div>

                        <!-- Botón de Cerrar Sesión -->
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Área desplazable del contenido -->
        <main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-[1400px] mx-auto">
                <!-- AQUI VA EL CONTENIDO DE CADA VISTA -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Script para el Sidebar Móvil
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Script de Menús Desplegables
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const otherMenuId = menuId === 'notifications-menu' ? 'user-menu' : 'notifications-menu';
            const otherMenu = document.getElementById(otherMenuId);

            if (otherMenu && !otherMenu.classList.contains('hidden')) {
                otherMenu.classList.add('hidden', 'opacity-0', 'scale-95');
                otherMenu.classList.remove('opacity-100', 'scale-100');
            }

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'scale-95');
                    menu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 200);
            }
        }

        function toggleNotifications() { toggleMenu('notifications-menu'); }
        function toggleUserMenu() { toggleMenu('user-menu'); }

        document.addEventListener('click', function(event) {
            const notiContainer = document.getElementById('notifications-container');
            const notiMenu = document.getElementById('notifications-menu');
            const userContainer = document.getElementById('user-menu-container');
            const userMenu = document.getElementById('user-menu');

            if (notiContainer && notiMenu && !notiContainer.contains(event.target) && !notiMenu.classList.contains('hidden')) {
                toggleMenu('notifications-menu');
            }
            if (userContainer && userMenu && !userContainer.contains(event.target) && !userMenu.classList.contains('hidden')) {
                toggleMenu('user-menu');
            }
        });
    </script>
</body>
</html>
