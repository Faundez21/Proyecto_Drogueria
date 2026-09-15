<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - @yield('title', 'Panel de Control')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0F172A;
            --sidebar-hover: #1E293B;
            --sidebar-text: #94A3B8;
            --sidebar-active-text: #F8FAFC;
            --nav-bg: #FFFFFF;
            --main-bg: #F1F5F9;
            --border-light: #E2E8F0;
            --text-main: #334155;
            --text-muted: #64748B;
            --accent: #0D9488;
            --accent-hover: #0F766E;
            --alert: #D97706;
            --danger: #E11D48;
        }

        body { font-family: 'IBM Plex Sans', sans-serif; background-color: var(--main-bg); color: var(--text-main); }
        .font-mono-das { font-family: 'IBM Plex Mono', monospace; }

        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: #475569; }

        #notifications-menu, #user-menu, #search-modal {
            transition: opacity 0.15s ease-out, transform 0.15s ease-out;
        }

        /* --- Animaciones del Sidebar --- */
        .flow-rail { position: relative; }
        .flow-rail::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 4px;
            bottom: 4px;
            width: 1px;
            background: #334155;
            transition: background-color 0.3s ease;
        }

        .flow-section:hover .flow-rail::before {
            background: var(--accent);
        }

        .nav-row {
            position: relative;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--sidebar-text);
        }
        
        .nav-row svg {
            transition: transform 0.22s ease, color 0.22s ease;
        }

        .nav-row:hover:not(.is-active) {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-active-text);
            transform: translateX(4px);
        }

        .nav-row:hover svg {
            transform: scale(1.12);
            color: var(--accent);
        }
        
        .nav-row.is-active {
            background-color: var(--sidebar-hover);
            color: var(--accent);
            font-weight: 600;
        }

        .nav-row.is-active svg {
            color: var(--accent);
        }

        .nav-row.is-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            bottom: 10%;
            width: 3px;
            background-color: var(--accent);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(13, 148, 136, 0.5);
            transition: all 0.3s ease;
        }

        .step-badge {
            transition: all 0.25s ease;
        }

        .flow-section:hover .step-badge {
            background-color: var(--accent);
            color: #FFFFFF;
            transform: scale(1.05);
        }

        .brand-logo-img {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .brand-container:hover .brand-logo-img {
            transform: rotate(-6deg) scale(1.08);
        }

        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>

<body class="antialiased overflow-hidden flex h-screen">

    <!-- Overlay móvil -->
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300"
        onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 w-[260px] flex flex-col z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 transition-transform duration-300 ease-in-out"
        style="background-color: var(--sidebar-bg); box-shadow: 2px 0 8px rgba(0,0,0,0.15);">

        <!-- Encabezado del sidebar (Favicon sin fondo blanco) -->
        <div class="h-[75px] flex items-center px-5 shrink-0 relative border-b border-slate-800 bg-[#0F172A]">
            <a href="{{ route('dashboard.index') }}" class="brand-container flex items-center gap-3.5 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-[#0F172A]">
                    <img src="{{ asset('images/favicon.svg') }}" alt="Logo DAS" class="brand-logo-img w-full h-full object-contain">
                </div>
                <div class="flex flex-col justify-center mt-0.5">
                    <span class="font-mono-das font-bold text-[19px] text-white tracking-widest leading-none group-hover:text-teal-400 transition-colors">DAS</span>
                    <span class="text-[10px] font-medium text-slate-400 tracking-[0.2em] uppercase mt-1.5 leading-none">Droguería</span>
                </div>
            </a>
            
            <button onclick="toggleSidebar()" class="absolute right-4 lg:hidden p-1.5 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-3 py-5 space-y-6 overflow-y-auto sidebar-scroll">
            <div>
                <a href="{{ route('dashboard.index') }}"
                    class="nav-row {{ request()->routeIs('dashboard.*') ? 'is-active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-md text-[13.5px]">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Panel Principal
                </a>
            </div>

            <!-- Paso 1: Abastecimiento -->
            <div class="flow-section">
                <div class="flex items-center gap-2 mb-2 px-2">
                    <span class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">01</span>
                    <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Abastecimiento</p>
                </div>
                <div class="flow-rail pl-[27px] space-y-0.5">
                    <a href="{{ route('proveedores.index') }}"
                        class="nav-row {{ request()->routeIs('proveedores.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Proveedores
                    </a>
                    <a href="{{ route('recepcion.index') }}"
                        class="nav-row {{ request()->routeIs('recepcion.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        Recepción
                    </a>
                </div>
            </div>

            <!-- Paso 2: Almacenamiento -->
            <div class="flow-section">
                <div class="flex items-center gap-2 mb-2 px-2">
                    <span class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">02</span>
                    <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Almacenamiento</p>
                </div>
                <div class="flow-rail pl-[27px] space-y-0.5">
                    <a href="{{ route('inventario.index') }}"
                        class="nav-row {{ request()->routeIs('inventario.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Inventario
                    </a>
                    <a href="{{ route('quarantine.index') }}"
                        class="nav-row {{ request()->routeIs('quarantine.*') ? 'is-active' : '' }} flex items-center justify-between px-3 py-2 rounded-md text-[13px]">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Cuarentena
                        </div>
                        <span class="flex items-center gap-1.5 text-[11px] font-mono-das font-semibold" style="color: var(--alert);">
                            <span class="w-1.5 h-1.5 rounded-full pulse-dot" style="background-color: var(--alert);"></span>
                            3
                        </span>
                    </a>
                </div>
            </div>

            <!-- Paso 3: Salida -->
            <div class="flow-section">
                <div class="flex items-center gap-2 mb-2 px-2">
                    <span class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">03</span>
                    <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Salida</p>
                </div>
                <div class="flow-rail pl-[27px] space-y-0.5">
                    <a href="{{ route('despacho.index') }}"
                        class="nav-row {{ request()->routeIs('despacho.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                        Despacho
                    </a>
                    <a href="{{ route('distribution.index') }}"
                        class="nav-row {{ request()->routeIs('distribution.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Distribución
                    </a>
                </div>
            </div>

            <!-- Paso 4: Control -->
            <div class="flow-section">
                <div class="flex items-center gap-2 mb-2 px-2">
                    <span class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">04</span>
                    <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Control</p>
                </div>
                <div class="flow-rail pl-[27px] space-y-0.5">
                    <a href="{{ route('trazabilidad.index') }}"
                        class="nav-row {{ request()->routeIs('trazabilidad.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Trazabilidad
                    </a>
                    <a href="{{ route('reportes.index') }}"
                        class="nav-row {{ request()->routeIs('reportes.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Reportes
                    </a>
                </div>
            </div>

            <!-- Administración -->
            @auth
                @if(method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Administrador'))
                <div class="pt-6 pb-4 border-t border-slate-800">
                    <p class="text-[11.5px] font-semibold tracking-wide uppercase px-2 mb-2 text-slate-500">Configuración</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('users.index') }}"
                            class="nav-row {{ request()->routeIs('users.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Usuarios y Permisos
                        </a>
                    </div>
                </div>
                @endif
            @endauth
        </nav>
    </aside>

    <!-- Contenedor principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <!-- Barra superior -->
        <header class="h-[70px] flex items-center justify-between px-4 sm:px-6 z-10 shrink-0 sticky top-0"
            style="background-color: var(--nav-bg); border-bottom: 1px solid var(--border-light); box-shadow: 0 1px 2px rgba(0,0,0,0.02);">

            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 -ml-2 rounded-md lg:hidden text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            <!-- Buscador Interactivo (Ctrl + K) -->
            <div class="flex-1 max-w-2xl mx-4">
                <div onclick="openSearchModal()" 
                    class="hidden sm:flex items-center justify-between w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white hover:border-teal-500/50 hover:shadow-md transition-all duration-200 text-slate-500 cursor-pointer group">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span class="text-[13px] group-hover:text-slate-700 transition-colors">Buscar en inventario, lotes o reportes...</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <kbd class="font-mono-das text-[10px] px-1.5 py-0.5 rounded border border-slate-300 bg-white text-slate-400 group-hover:border-slate-400 transition-colors">Ctrl</kbd>
                        <kbd class="font-mono-das text-[10px] px-1.5 py-0.5 rounded border border-slate-300 bg-white text-slate-400 group-hover:border-slate-400 transition-colors">K</kbd>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <div class="hidden xl:flex items-center gap-2.5 pr-4 border-r border-slate-200">
                    <svg class="w-[18px] h-[18px]" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="text-[13px] font-mono-das tracking-tight" id="live-clock"></div>
                </div>

                <!-- Notificaciones -->
                <div class="relative" id="notifications-container">
                    <button onclick="toggleNotifications()" type="button" class="relative p-2 rounded-md text-slate-500 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full pulse-dot border-2 border-white" style="background-color: var(--alert);"></span>
                    </button>

                    <div id="notifications-menu" class="hidden absolute right-0 mt-2 w-[340px] rounded-lg z-50 overflow-hidden opacity-0 scale-95 origin-top-right bg-white border border-slate-200 shadow-lg">
                        <div class="px-4 py-3.5 flex justify-between items-center border-b border-slate-100 bg-slate-50">
                            <div class="flex items-center gap-2">
                                <h3 class="text-[13px] font-semibold text-slate-800">Notificaciones</h3>
                                <span class="text-white text-[10px] font-mono-das font-semibold px-1.5 py-0.5 rounded" style="background-color: var(--accent);">2</span>
                            </div>
                            <button class="text-[11px] font-medium px-2 py-1 rounded transition-colors hover:bg-slate-200 text-slate-600">Marcar leídas</button>
                        </div>
                        <div class="max-h-[320px] overflow-y-auto p-2 space-y-1 sidebar-scroll">
                            <a href="#" class="flex gap-3 p-2.5 rounded-md transition-colors hover:bg-slate-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-amber-100" style="color: var(--alert);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-medium text-slate-800">Stock bajo en Paracetamol</p>
                                    <p class="text-[12px] mt-0.5 leading-snug text-slate-500">Quedan 12 unidades en Bodega A.</p>
                                    <p class="text-[11px] font-mono-das mt-1.5 text-slate-400">Hace 2 horas</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block h-6 w-px mx-1 bg-slate-200"></div>

                <!-- Perfil -->
                @auth
                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" type="button" class="flex items-center gap-2.5 p-1 rounded-md hover:bg-slate-100 transition-colors">
                        <div class="relative">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center font-mono-das text-[11px] font-semibold text-white shadow-sm" style="background-color: var(--accent);">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white bg-green-500"></div>
                        </div>
                        <div class="hidden md:flex flex-col items-start text-left leading-tight">
                            <span class="text-[12.5px] font-medium text-slate-700">{{ auth()->user()->name ?? 'Usuario' }}</span>
                            <span class="text-[10.5px] mt-0.5 text-slate-400">
                                @if(method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Administrador'))
                                    Administrador
                                @else
                                    Operador
                                @endif
                            </span>
                        </div>
                        <svg class="w-3.5 h-3.5 hidden md:block text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-[240px] rounded-lg z-50 overflow-hidden opacity-0 scale-95 origin-top-right bg-white border border-slate-200 shadow-lg">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <p class="text-[13px] font-semibold text-slate-800">{{ auth()->user()->name ?? 'Usuario' }}</p>
                            <p class="text-[12px] truncate mt-0.5 text-slate-500">{{ auth()->user()->email ?? 'usuario@ejemplo.com' }}</p>
                        </div>
                        <div class="p-1.5">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-md text-[12.5px] font-medium text-left transition-colors hover:bg-rose-50 text-rose-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-[1600px] mx-auto space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        @yield('actions')
                    </div>
                </div>

                <div class="w-full">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Búsqueda Global (Comandos Ctrl + K) -->
    <div id="search-modal" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-16 sm:pt-24 px-4 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="if(event.target === this) closeSearchModal()">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden transform scale-95 transition-transform duration-200" id="search-modal-card">
            <div class="flex items-center px-4 border-b border-slate-100 bg-slate-50/50">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="global-search-input" placeholder="Escribe para buscar productos, lotes, proveedores..." class="w-full px-3 py-4 text-[14px] bg-transparent border-0 focus:outline-none text-slate-800 placeholder-slate-400 font-medium">
                <kbd onclick="closeSearchModal()" class="cursor-pointer font-mono-das text-[10px] px-2 py-1 rounded border border-slate-200 bg-white text-slate-400 hover:bg-slate-100 transition-colors">ESC</kbd>
            </div>
            <div class="p-2 max-h-[360px] overflow-y-auto space-y-1 sidebar-scroll">
                <div class="px-3 py-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider font-mono-das">Accesos Rápidos</div>
                
                <a href="{{ route('inventario.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-teal-50 text-slate-700 hover:text-teal-900 group transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="p-1.5 rounded-md bg-slate-100 group-hover:bg-teal-100 text-slate-500 group-hover:text-teal-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></span>
                        <span class="text-[13px] font-medium">Buscar en Inventario</span>
                    </div>
                    <span class="text-[11px] font-mono-das text-slate-400">Ir a página</span>
                </a>

                <a href="{{ route('proveedores.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-teal-50 text-slate-700 hover:text-teal-900 group transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="p-1.5 rounded-md bg-slate-100 group-hover:bg-teal-100 text-slate-500 group-hover:text-teal-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></span>
                        <span class="text-[13px] font-medium">Gestión de Proveedores</span>
                    </div>
                    <span class="text-[11px] font-mono-das text-slate-400">Ir a página</span>
                </a>

                <a href="{{ route('quarantine.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-amber-50 text-slate-700 hover:text-amber-900 group transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="p-1.5 rounded-md bg-slate-100 group-hover:bg-amber-100 text-slate-500 group-hover:text-amber-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></span>
                        <span class="text-[13px] font-medium">Revisar Cuarentena (3)</span>
                    </div>
                    <span class="text-[11px] font-mono-das text-amber-600 font-semibold">Alerta</span>
                </a>
            </div>
            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Navega con la consola de búsqueda</span>
                <span class="font-mono-das">Droguería DAS</span>
            </div>
        </div>
    </div>

    <script>
        /* --- Control del Sidebar --- */
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        /* --- Control de Menús Desplegables --- */
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const otherMenuId = menuId === 'notifications-menu' ? 'user-menu' : 'notifications-menu';
            const otherMenu = document.getElementById(otherMenuId);

            if (otherMenu && !otherMenu.classList.contains('hidden')) {
                closeMenu(otherMenu);
            }

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'scale-95');
                    menu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                closeMenu(menu);
            }
        }

        function closeMenu(menu) {
            menu.classList.remove('opacity-100', 'scale-100');
            menu.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 150);
        }

        function toggleNotifications() { toggleMenu('notifications-menu'); }
        function toggleUserMenu() { toggleMenu('user-menu'); }

        /* --- Búsqueda Ctrl + K --- */
        function openSearchModal() {
            const modal = document.getElementById('search-modal');
            const card = document.getElementById('search-modal-card');
            const input = document.getElementById('global-search-input');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
                input.focus();
            }, 10);
        }

        function closeSearchModal() {
            const modal = document.getElementById('search-modal');
            const card = document.getElementById('search-modal-card');
            
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const modal = document.getElementById('search-modal');
                if (modal.classList.contains('hidden')) {
                    openSearchModal();
                } else {
                    closeSearchModal();
                }
            }
            if (e.key === 'Escape') {
                const modal = document.getElementById('search-modal');
                if (!modal.classList.contains('hidden')) {
                    closeSearchModal();
                }
            }
        });

        /* --- Cierre por clic fuera del contenedor --- */
        document.addEventListener('click', (event) => {
            ['notifications', 'user'].forEach(type => {
                const container = document.getElementById(`${type}-menu-container`);
                const menu = document.getElementById(`${type}-menu`);
                if (container && menu && !container.contains(event.target) && !menu.classList.contains('hidden')) {
                    closeMenu(menu);
                }
            });
        });

        document.querySelectorAll('#sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) toggleSidebar();
            });
        });

        /* --- Reloj en tiempo real --- */
        function updateClock() {
            const clockElement = document.getElementById('live-clock');
            if (clockElement) {
                const now = new Date();
                const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                
                const dayName = days[now.getDay()];
                const day = now.getDate();
                const month = months[now.getMonth()];
                
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                clockElement.innerHTML = `
                    <span style="color: var(--text-muted); margin-right: 6px;">${dayName}, ${day} ${month}</span> 
                    <span style="color: var(--border-light);">|</span> 
                    <span style="color: var(--text-main); font-weight: 600; margin-left: 6px;">${hours}:${minutes}:${seconds}</span>
                `;
            }
        }
        
        setInterval(updateClock, 1000); 
        updateClock(); 
    </script>
</body>
</html>