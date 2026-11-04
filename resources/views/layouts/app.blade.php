<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - @yield('title', 'Panel de Control')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    <!-- Modo oscuro: se aplica antes de pintar para evitar parpadeo -->
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('das-theme');
                var prefers = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (!saved && prefers)) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Gestión de estados de lote (Inventario / Cuarentena / Merma) con SweetAlert2 + AJAX -->
    <script src="{{ asset('js/lotes/lote-status.js') }}?v={{ @filemtime(public_path('js/lotes/lote-status.js')) }}"></script>

    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}?v={{ @filemtime(public_path('css/dark-mode.css')) }}">

    <style>
        /* Asegurar que SweetAlert2 siempre esté por encima de cualquier modal, drawer o backdrop */
        .swal2-container {
            z-index: 9999999 !important;
        }

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

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-main);
        }

        .font-mono-das {
            font-family: 'IBM Plex Mono', monospace;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .sidebar-scroll:hover::-webkit-scrollbar-thumb {
            background: #94a3b8;
        }

        #notifications-menu,
        #user-menu,
        #search-modal {
            transition: opacity 0.15s ease-out, transform 0.15s ease-out;
        }

        /* --- Animaciones del Sidebar --- */
        .flow-rail {
            position: relative;
        }

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

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .pulse-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }

        /* Loading spinner para el buscador */
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0d9488;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Estilo para los resultados de busqueda con flechas */
        .search-result-item:focus,
        .search-result-item.active {
            background-color: #f0fdfa !important;
            border-color: #ccfbf1 !important;
            outline: none;
        }

        .search-result-item:focus svg.shrink-0,
        .search-result-item.active svg.shrink-0 {
            color: #0f766e;
            transform: scale(1.1);
        }

        .search-result-item:focus .go-icon,
        .search-result-item.active .go-icon {
            opacity: 1;
        }

        .highlight-text {
            background-color: #ccfbf1;
            color: #0f766e;
            padding: 0 2px;
            border-radius: 3px;
        }

        /* Ajuste de fuente para modales de SweetAlert */
        .swal2-popup {
            font-family: 'IBM Plex Sans', sans-serif !important;
        }
    </style>
</head>

<body class="antialiased min-h-screen bg-slate-100">

    @auth
        <div class="flex h-screen overflow-hidden">

            <!-- Overlay móvil -->
            <div id="sidebar-overlay"
                class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300"
                onclick="toggleSidebar()"></div>

            <!-- Sidebar -->
            <aside id="sidebar"
                class="fixed inset-y-0 left-0 w-[260px] flex flex-col z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:shrink-0 transition-transform duration-300 ease-in-out"
                style="background-color: var(--sidebar-bg); box-shadow: 2px 0 8px rgba(0,0,0,0.15);">

                <div class="h-[70px] flex items-center px-5 shrink-0 relative border-b border-slate-800 bg-[#0F172A]">
                    <a href="{{ route('dashboard.index') }}" class="brand-container flex items-center gap-3.5 group">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-[#0F172A]">
                            <img src="{{ asset('images/favicon.svg') }}" alt="Logo DAS"
                                class="brand-logo-img w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col justify-center mt-0.5">
                            <span
                                class="font-mono-das font-bold text-[19px] text-white tracking-widest leading-none group-hover:text-teal-400 transition-colors">DAS</span>
                            <span
                                class="text-[10px] font-medium text-slate-400 tracking-[0.2em] uppercase mt-1.5 leading-none">Droguería</span>
                        </div>
                    </a>

                    <button onclick="toggleSidebar()"
                        class="absolute right-4 lg:hidden p-1.5 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                @php
                    $sidebarCuarentenaCount = 0;
                    $sidebarDespachosPendCount = 0;
                    $sidebarDespachosControladosPendCount = 0;
                    $sidebarClavesPendCount = 0;
                    $rolUsuarioActual = 'Operador';
                    try {
                        if (auth()->check()) {
                            $rolUsuarioActual = auth()->user()->roles->first()?->name
                                ?? (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Administrador') ? 'Administrador' : 'Operador');

                            if (auth()->user()->can('ver.cuarentena')) {
                                $sidebarCuarentenaCount = \Illuminate\Support\Facades\DB::table('inventories')
                                    ->where('estado', 'Cuarentena')
                                    ->where('cantidad', '>', 0)
                                    ->count();
                            }
                            $controladosList = \App\Services\InventoryService::controlados();
                            if (auth()->user()->can('ver.despacho')) {
                                $sidebarDespachosPendCount = \App\Models\EstablishmentRequest::whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
                                    ->whereHas('items.product', fn ($q) => $q->whereNotIn('nombre', $controladosList))
                                    ->count();
                            }
                            if (auth()->user()->can('ver.despacho_controlado')) {
                                $sidebarDespachosControladosPendCount = \App\Models\EstablishmentRequest::whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
                                    ->whereHas('items.product', fn ($q) => $q->whereIn('nombre', $controladosList))
                                    ->count();
                            }
                            if (auth()->user()->can('ver.usuarios')) {
                                $sidebarClavesPendCount = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                                    ->where('status', 'pending')
                                    ->count();
                            }
                        }
                    } catch (\Throwable $e) {
                    }
                @endphp

                <nav class="flex-1 px-3 py-5 space-y-6 overflow-y-auto sidebar-scroll">
                    <div>
                        @can('ver.dashboard')
                            <a href="{{ route('dashboard.index') }}"
                                class="nav-row {{ request()->routeIs('dashboard.*') ? 'is-active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-md text-[13.5px]">
                                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                Panel Principal
                            </a>
                        @endcan
                    </div>
                    @canany(['ver.proveedores', 'ver.recepcion'])
                    <div class="flow-section">
                        <div class="flex items-center gap-2 mb-2 px-2">
                            <span
                                class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">01</span>
                            <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Abastecimiento</p>
                        </div>
                        <div class="flow-rail pl-[27px] space-y-0.5">
                            @can('ver.proveedores')
                            <a href="{{ route('providers.index') }}"
                                class="nav-row {{ request()->routeIs('providers.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Proveedores
                            </a>
                            @endcan
                            @can('ver.recepcion')
                            <a href="{{ route('reception.index') }}"
                                class="nav-row {{ request()->routeIs('reception.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                Recepción
                            </a>
                            @endcan
                        </div>
                    </div>

                    @endcanany

                    <!-- Paso 2: Almacenamiento -->
                    @canany(['ver.inventario', 'ver.distribucion', 'ver.cuarentena'])
                    <div class="flow-section">
                        <div class="flex items-center gap-2 mb-2 px-2">
                            <span
                                class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">02</span>
                            <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Almacenamiento</p>
                        </div>
                        <div class="flow-rail pl-[27px] space-y-0.5">
                            @can('ver.inventario')
                            <a href="{{ route('inventory.index') }}"
                                class="nav-row {{ request()->routeIs('inventory.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                    </path>
                                </svg>
                                Inventario
                            </a>
                            @endcan
                            @can('ver.distribucion')
                            <a href="{{ route('distribution.index') }}"
                                class="nav-row {{ request()->routeIs('distribution.*') || request()->routeIs('shelf.*') || request()->routeIs('aisle.*') || request()->routeIs('level.*') || request()->routeIs('position.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Distribución
                            </a>
                            @endcan
                            @can('ver.cuarentena')
                            <a href="{{ route('quarantine.index') }}"
                                class="nav-row {{ request()->routeIs('quarantine.*') ? 'is-active' : '' }} flex items-center justify-between px-3 py-2 rounded-md text-[13px]">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    Cuarentena
                                </div>
                                @if ($sidebarCuarentenaCount > 0)
                                    <span class="font-mono-das text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Lotes retenidos en cuarentena">
                                        {{ $sidebarCuarentenaCount }}
                                    </span>
                                @endif
                            </a>
                            @endcan
                        </div>

                    </div>

                    @endcanany

                    <!-- Paso 3: Salida -->
                    @canany(['ver.despacho', 'ver.despacho_controlado', 'ver.solicitudes', 'ver.solicitudes_cesfam'])
                    <div class="flow-section">
                        <div class="flex items-center gap-2 mb-2 px-2">
                            <span
                                class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">03</span>
                            <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Salida</p>
                        </div>
                        <div class="flow-rail pl-[27px] space-y-0.5">
                            <div class="flex flex-col space-y-0.5">
                                @can('ver.despacho')
                                <a href="{{ route('dispatch.index') }}"
                                    class="nav-row {{ request()->routeIs('dispatch.index') ? 'is-active' : '' }} flex items-center justify-between px-3 py-2 rounded-md text-[13px]">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path>
                                        </svg>
                                        Despacho Normal
                                    </div>
                                    @if ($sidebarDespachosPendCount > 0)
                                        <span class="font-mono-das text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30" title="Solicitudes pendientes o parciales por despachar">
                                            {{ $sidebarDespachosPendCount }}
                                        </span>
                                    @endif
                                </a>
                                @endcan
                                @can('ver.despacho_controlado')
                                    <!-- Submenú Controlado -->
                                    <div class="relative pl-6">
                                        <div class="absolute left-3.5 top-0 bottom-4 w-px bg-slate-700/60"></div>
                                        <div class="absolute left-3.5 top-1/2 w-2.5 h-px bg-slate-700/60"></div>

                                        <a href="{{ route('controlled_dispatch.index') }}"
                                            class="nav-row {{ request()->routeIs('controlled_dispatch.*') ? 'is-active' : '' }} flex items-center justify-between px-3 py-1.5 rounded-md text-[12px]">
                                            <div class="flex items-center gap-2.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                    </path>
                                                </svg>
                                                Despacho Controlado
                                            </div>
                                            @if ($sidebarDespachosControladosPendCount > 0)
                                                <span class="font-mono-das text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Solicitudes con fármacos controlados por despachar">
                                                    {{ $sidebarDespachosControladosPendCount }}
                                                </span>
                                            @endif
                                        </a>
                                    </div>
                                @endcan
                            </div>

                            @can('ver.solicitudes')
                                <a href="{{ route('requests.index') }}"
                                    class="nav-row {{ request()->routeIs('requests.index') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2zM13 3v4h4">
                                        </path>
                                    </svg>
                                    Solicitudes Establecimientos
                                </a>
                            @endcan

                            @can('ver.solicitudes_cesfam')
                                <a href="{{ route('requests.cesfam') }}"
                                    class="nav-row {{ request()->routeIs('requests.cesfam') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011 1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    Solicitudes Cesfam
                                </a>
                            @endcan
                        </div>
                    </div>
                    @endcanany
                    <!-- Paso 4: Control -->
                    @canany(['ver.trazabilidad', 'ver.reportes'])
                    <div class="flow-section">
                        <div class="flex items-center gap-2 mb-2 px-2">
                            <span
                                class="step-badge font-mono-das text-[10px] w-5 h-5 rounded flex items-center justify-center bg-slate-800 text-slate-400">04</span>
                            <p class="text-[11.5px] font-semibold tracking-wide uppercase text-slate-500">Control</p>
                        </div>
                        <div class="flow-rail pl-[27px] space-y-0.5">
                            @can('ver.trazabilidad')
                            <a href="{{ route('traceability.index') }}"
                                class="nav-row {{ request()->routeIs('traceability.*') || request()->routeIs('trazabilidad.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                    </path>
                                </svg>
                                Trazabilidad
                            </a>
                            @endcan
                            @can('ver.reportes')
                            <a href="{{ route('reports.index') }}"
                                class="nav-row {{ request()->routeIs('reports.*') ? 'is-active' : '' }} flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Reportes
                            </a>
                            @endcan
                        </div>
                    </div>

                    @endcanany

                    <!-- Administración -->
                    @can('ver.usuarios')
                        <div class="pt-6 pb-4 border-t border-slate-800">
                            <p class="text-[11.5px] font-semibold tracking-wide uppercase px-2 mb-2 text-slate-500">
                                Configuración</p>
                            <div class="space-y-0.5">
                                <a href="{{ route('users.index') }}"
                                    class="nav-row {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'is-active' : '' }} flex items-center justify-between px-3 py-2 rounded-md text-[13px]">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        Usuarios y Permisos
                                    </div>
                                    @if ($sidebarClavesPendCount > 0)
                                        <span class="font-mono-das text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-purple-500/25 text-purple-300 border border-purple-400/30 animate-pulse" title="Solicitudes de recuperación de contraseña pendientes">
                                            {{ $sidebarClavesPendCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endcan
                </nav>
            </aside>
            <!-- Contenedor principal -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

                <!-- Barra superior -->
                <header class="h-[70px] flex items-center justify-between px-4 sm:px-6 z-10 shrink-0 sticky top-0"
                    style="background-color: var(--nav-bg); border-bottom: 1px solid var(--border-light); box-shadow: 0 1px 2px rgba(0,0,0,0.02);">

                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()"
                            class="p-2 -ml-2 rounded-md lg:hidden text-slate-500 hover:bg-slate-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Buscador Interactivo (Ctrl + K) -->
                    <div class="flex-1 max-w-2xl mx-4">
                        <div onclick="openSearchModal()"
                            class="hidden sm:flex items-center justify-between w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white hover:border-teal-500/50 hover:shadow-md transition-all duration-200 text-slate-500 cursor-pointer group">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="text-[13px] group-hover:text-slate-700 transition-colors">Buscar en
                                    inventario,
                                    lotes o catálogo...</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <kbd
                                    class="font-mono-das text-[10px] px-1.5 py-0.5 rounded border border-slate-300 bg-white text-slate-400 group-hover:border-slate-400 transition-colors">Ctrl</kbd>
                                <kbd
                                    class="font-mono-das text-[10px] px-1.5 py-0.5 rounded border border-slate-300 bg-white text-slate-400 group-hover:border-slate-400 transition-colors">K</kbd>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                        <!-- Botón de búsqueda móvil (< sm) -->
                        <button onclick="openSearchModal()" type="button"
                            class="sm:hidden p-2 rounded-md text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Buscar en el sistema">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        <!-- Botón modo claro / oscuro -->
                        <button onclick="toggleTheme()" type="button" id="theme-toggle-btn"
                            class="theme-toggle shrink-0 cursor-pointer"
                            title="Cambiar modo claro / oscuro" aria-label="Cambiar modo claro / oscuro">
                            <svg class="theme-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg class="theme-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>

                        <div class="hidden xl:flex items-center gap-2.5 pr-4 border-r border-slate-200">
                            <svg class="w-[18px] h-[18px]" style="color: var(--accent);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-[13px] font-mono-das tracking-tight" id="live-clock"></div>
                        </div>
                        <!-- Notificaciones Dinámicas -->
                        <div class="relative" id="notifications-menu-container">
                            <button onclick="toggleNotifications()" type="button"
                                class="relative p-2 rounded-md text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <span id="notif-badge"
                                    class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold text-white flex items-center justify-center border-2 border-white shadow-xs"
                                    style="background-color: var(--alert);">0</span>
                            </button>

                            <div id="notifications-menu"
                                class="hidden absolute right-0 mt-2 w-[350px] sm:w-[370px] rounded-2xl z-50 overflow-hidden opacity-0 scale-95 origin-top-right bg-white border border-slate-200 shadow-2xl transition-all">
                                <div class="px-4 py-3 flex justify-between items-center border-b border-slate-100 bg-slate-50">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-[13px] font-extrabold text-slate-800">Centro de Alertas</h3>
                                    </div>
                                    <button id="btn-marcar-todas-notif" onclick="marcarTodasNotificacionesVistas(event)" type="button"
                                        class="hidden text-[11px] font-bold text-teal-600 hover:text-teal-800 transition-colors flex items-center gap-1 cursor-pointer"
                                        title="Marcar todas las alertas pendientes como vistas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Marcar todas
                                    </button>
                                </div>

                                <!-- Selector de Pestañas: Pendientes y Vistas -->
                                <div class="flex border-b border-slate-200/80 bg-slate-50/70 p-1.5 gap-1.5 text-[12px] font-bold">
                                    <button id="tab-notif-pendientes" type="button" onclick="cambiarTabNotificaciones('pendientes')"
                                        class="notif-tab-active flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 bg-white text-teal-700 shadow-2xs border border-slate-200/60 cursor-pointer">
                                        <span>Pendientes</span>
                                        <span id="notif-count" class="text-white text-[10px] font-mono-das font-extrabold px-1.5 py-0.2 rounded-full bg-rose-500">0</span>
                                    </button>
                                    <button id="tab-notif-vistas" type="button" onclick="cambiarTabNotificaciones('vistas')"
                                        class="notif-tab-inactive flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 hover:bg-white/60 cursor-pointer">
                                        <span>Vistas</span>
                                        <span id="notif-read-count" class="text-slate-600 text-[10px] font-mono-das font-bold px-1.5 py-0.2 rounded-full bg-slate-200">0</span>
                                    </button>
                                </div>

                                <!-- Lista Pendientes -->
                                <div id="notif-list" class="max-h-[340px] overflow-y-auto p-2 space-y-1 sidebar-scroll">
                                    <div class="p-4 text-center text-slate-400 text-xs">Cargando alertas...</div>
                                </div>

                                <!-- Lista Vistas -->
                                <div id="notif-read-list" class="hidden max-h-[340px] overflow-y-auto p-2 space-y-1 sidebar-scroll bg-slate-50/30">
                                    <div class="p-4 text-center text-slate-400 text-xs">No hay notificaciones vistas.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Perfil -->
                        <div class="relative" id="user-menu-container">
                            <button onclick="toggleUserMenu()" type="button"
                                class="flex items-center gap-2.5 p-1 rounded-md hover:bg-slate-100 transition-colors">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-md flex items-center justify-center font-mono-das text-[11px] font-semibold text-white shadow-sm"
                                        style="background-color: var(--accent);">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div
                                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white bg-green-500">
                                    </div>
                                </div>
                                <div class="hidden md:flex flex-col items-start text-left leading-tight">
                                    <span
                                        class="text-[12.5px] font-medium text-slate-700">{{ auth()->user()->name ?? 'Usuario' }}</span>
                                    <span class="text-[10.5px] mt-0.5 text-slate-400">
                                        {{ $rolUsuarioActual }}
                                    </span>
                                </div>
                                <svg class="w-3.5 h-3.5 hidden md:block text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>

                            <div id="user-menu"
                                class="hidden absolute right-0 mt-2 w-[250px] rounded-lg z-50 overflow-hidden opacity-0 scale-95 origin-top-right bg-white border border-slate-200 shadow-lg">
                                <div class="p-4 border-b border-slate-100 bg-slate-50">
                                    <p class="text-[13px] font-semibold text-slate-800">
                                        {{ trim((auth()->user()->name ?? 'Usuario') . ' ' . (auth()->user()->last_name ?? '')) }}</p>
                                    <p class="text-[12px] truncate mt-0.5 text-slate-500">
                                        {{ auth()->user()->email ?? 'usuario@ejemplo.com' }}</p>
                                    <span class="inline-flex items-center gap-1.5 mt-2 px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                        {{ $rolUsuarioActual }}
                                    </span>
                                </div>
                                <div class="p-1.5">
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}"
                                        class="w-full">
                                        @csrf
                                        <!-- Botón de logout modificado para disparar SweetAlert -->
                                        <button type="button" onclick="confirmLogout()"
                                            class="flex w-full items-center gap-3 px-3 py-2 rounded-md text-[12.5px] font-medium text-left transition-colors hover:bg-rose-50 text-rose-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Contenido principal -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    <div class="max-w-[1600px] mx-auto space-y-6">


                        <!-- ENCABEZADO DINÁMICO DE PÁGINA (Resuelve el problema de los botones) -->

                        @if (View::hasSection('title') || View::hasSection('actions'))
                            <div
                                class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-5">
                                <div>
                                    @hasSection('title')
                                        <h1 class="text-[22px] font-bold text-slate-800 tracking-tight leading-none">
                                            @yield('title')</h1>
                                    @endif
                                    @hasSection('subtitle')
                                        <p class="text-[13px] text-slate-500 mt-2 font-medium">@yield('subtitle')</p>
                                    @endif
                                </div>

                                @hasSection('actions')
                                    <div class="flex items-center gap-3 shrink-0">
                                        @yield('actions')
                                    </div>
                                @endif
                            </div>
                        @endif


                        <div class="w-full">
                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>

            <!-- MODAL DE BÚSQUEDA GLOBAL "NIVEL INSANO" (Navegación por Teclado) -->

            <div id="search-modal"
                class="fixed inset-0 z-50 hidden flex items-start justify-center p-3 sm:p-4 pt-8 sm:pt-16 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200"
                onclick="if(event.target === this) closeSearchModal()">
                <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden transform scale-95 transition-transform duration-200 flex flex-col max-h-[88vh]"
                    id="search-modal-card">

                    <div class="flex items-center px-4 border-b border-slate-100 bg-slate-50/50 shrink-0 relative">
                        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="global-search-input" oninput="performGlobalSearch(this.value)"
                            onkeydown="handleSearchKeydown(event)" placeholder="Buscar Lote, Fármaco o RUT Proveedor..."
                            class="w-full px-3 py-4 text-[14px] bg-transparent border-0 focus:outline-none text-slate-800 placeholder-slate-400 font-medium pr-12">

                        <!-- Botón Limpiar (X) -->
                        <button id="search-clear-btn" onclick="clearSearch()"
                            class="hidden absolute right-16 p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition-colors focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Loader -->
                        <div id="search-loader" class="hidden absolute right-16 loader"></div>

                        <kbd onclick="closeSearchModal()"
                            class="cursor-pointer font-mono-das text-[10px] px-2 py-1 rounded border border-slate-200 bg-white text-slate-400 hover:bg-slate-100 transition-colors ml-2">ESC</kbd>
                    </div>

                    <div id="search-results" class="p-2 overflow-y-auto space-y-1 sidebar-scroll flex-1 pb-4">
                        <div class="px-3 py-6 text-center text-[12px] text-slate-400">Escribe al menos 2 letras para buscar
                            en
                            toda la base de datos...</div>
                    </div>

                    <div
                        class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center gap-1"><kbd
                                    class="px-1 py-0.5 rounded border border-slate-200 bg-white">↑</kbd><kbd
                                    class="px-1 py-0.5 rounded border border-slate-200 bg-white">↓</kbd> Navegar</span>
                            <span class="flex items-center gap-1 ml-2"><kbd
                                    class="px-1 py-0.5 rounded border border-slate-200 bg-white">↵</kbd> Entrar</span>
                        </div>
                        <span class="font-mono-das">Droguería DAS</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <main class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-2xl">
                @yield('content')
            </div>
        </main>
    @endauth

    <!-- SCRIPT MAESTRO DE FUNCIONALIDAD GLOBAL -->
    <script>
        // --- SWEETALERT2: CONFIGURACIÓN GLOBAL ---

        // 1. Instancia global para notificaciones tipo Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // 2. Interceptor de Flash Messages de Laravel con SweetAlert2 Modal
        @if (session('lote_status_success'))
            if (window.LoteStatus && window.LoteStatus.core) {
                window.LoteStatus.core.mostrarExito(@json(session('lote_status_success')));
            }
        @elseif (session('success') && !session('reception_success_detail') && !session('dispatch_success_detail') && !session('controlled_dispatch_success_detail') && !session('lote_moved_success') && !session('lote_status_success'))
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: @json(session('success')),
                confirmButtonColor: '#0f766e',
                timer: 3500,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#e11d48'
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: @json(session('warning')),
                confirmButtonColor: '#d97706'
            });
        @endif

        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: @json(session('info')),
                confirmButtonColor: '#3b82f6'
            });
        @endif

        // 3. Confirmación de Cierre de Sesión con SweetAlert2
        function confirmLogout() {
            // Cerramos el menú flotante para que no interfiera visualmente
            const userMenu = document.getElementById('user-menu');
            if (userMenu) closeMenu(userMenu);

            Swal.fire({
                title: '¿Cerrar sesión?',
                text: "Tendrás que volver a ingresar tus credenciales para acceder al sistema.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0D9488', // Teal 600 (var--accent)
                cancelButtonColor: '#E11D48', // Rose 600 (var--danger)
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
                reverseButtons: true, // Pone el botón de confirmar a la derecha
                focusCancel: true // Foco en el botón cancelar para evitar clics accidentales
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // --- BÚSQUEDA GLOBAL "NIVEL INSANO" ---
        let searchTimeout = null;
        let selectedSearchIndex = -1;
        let searchResultsCount = 0;
        let searchAbortController = null; // Nuevo controlador de interrupción

        // Helper para escapar caracteres especiales de Regex
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Limpiar búsqueda
        function clearSearch() {
            const input = document.getElementById('global-search-input');
            input.value = '';
            input.focus();
            performGlobalSearch('');
        }

        function performGlobalSearch(query) {
            const resultsContainer = document.getElementById('search-results');
            const loader = document.getElementById('search-loader');
            const clearBtn = document.getElementById('search-clear-btn');
            selectedSearchIndex = -1; // Reset flechas
            const cleanQuery = query.trim();

            // Mostrar u ocultar el botón "X"
            if (cleanQuery.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            // Cancelar la petición anterior si existe
            if (searchAbortController) {
                searchAbortController.abort();
            }

            if (cleanQuery.length < 2) {
                loader.classList.add('hidden');
                resultsContainer.innerHTML =
                    '<div class="px-3 py-6 text-center text-[12px] text-slate-400">Escribe al menos 2 letras para iniciar la búsqueda.</div>';
                return;
            }

            // Ocultar X y mostrar loader temporalmente
            clearBtn.classList.add('hidden');
            loader.classList.remove('hidden');

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Instanciar el nuevo AbortController
                searchAbortController = new AbortController();

                fetch(`/api/global-search?q=${encodeURIComponent(cleanQuery)}`, {
                        signal: searchAbortController.signal
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Error en red');
                        return res.json();
                    })
                    .then(data => {
                        loader.classList.add('hidden');
                        clearBtn.classList.remove('hidden'); // Restaurar botón X
                        searchResultsCount = data.length;

                        if (data.length === 0) {
                            resultsContainer.innerHTML = `
                                <div class="px-3 py-10 flex flex-col items-center justify-center text-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[13px] text-slate-500 font-medium">No se encontraron coincidencias.</span>
                                    <span class="text-[11px] text-slate-400 mt-1">Intenta con otro término o código.</span>
                                </div>`;
                            return;
                        }

                        let html = '';
                        let currentType = '';
                        // Usar función escapeRegExp para prevenir errores con caracteres especiales
                        const safeQuery = escapeRegExp(cleanQuery);
                        const regex = new RegExp(`(${safeQuery})`, 'gi');

                        data.forEach((item, index) => {
                            if (currentType !== item.type) {
                                html += `
                                    <div class="px-3 py-2 mt-2 flex items-center gap-2">
                                        <div class="h-px bg-slate-100 flex-1"></div>
                                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider font-mono-das">${item.type}</span>
                                        <div class="h-px bg-slate-100 flex-1"></div>
                                    </div>`;
                                currentType = item.type;
                            }

                            // Resaltador Mágico Seguro
                            const highlightedTitle = item.title ? item.title.replace(regex,
                                '<span class="highlight-text">$1</span>') : 'N/A';
                            const highlightedSubtitle = item.subtitle ? item.subtitle.replace(regex,
                                '<span class="highlight-text">$1</span>') : '';

                            html += `
                                <a href="${item.url}" id="search-item-${index}" class="search-result-item flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-teal-50 text-slate-700 hover:text-teal-900 transition-all duration-200 border border-transparent mt-0.5 group">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <span class="p-1.5 rounded-md bg-slate-100 text-slate-500 transition-colors group-hover:bg-teal-100/50 group-hover:text-teal-600 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${item.icon}</svg>
                                        </span>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-[13px] font-bold truncate">${highlightedTitle}</span>
                                            <span class="text-[11px] text-slate-500 font-mono truncate">${highlightedSubtitle}</span>
                                        </div>
                                    </div>
                                    <span class="go-icon text-[11px] font-mono-das text-teal-600 opacity-0 transition-opacity flex items-center gap-1.5 shrink-0 pl-2">
                                        <span class="hidden sm:inline">Entrar</span> <kbd class="px-1.5 py-0.5 rounded border border-teal-200 bg-white">↵</kbd>
                                    </span>
                                </a>
                            `;
                        });

                        resultsContainer.innerHTML = html;
                    })
                    .catch((error) => {
                        // Ignorar el error si la petición fue abortada por tipear rápido
                        if (error.name === 'AbortError') return;

                        loader.classList.add('hidden');
                        clearBtn.classList.remove('hidden');
                        resultsContainer.innerHTML = `
                            <div class="px-3 py-8 text-center flex flex-col items-center">
                                <svg class="w-8 h-8 text-rose-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span class="text-[12px] text-rose-500 font-medium">Error de conexión al buscar.</span>
                            </div>`;
                    });
            }, 300); // 300ms de debounce es un estándar ideal
        }

        // --- NAVEGACIÓN POR TECLADO EN BÚSQUEDA ---
        function handleSearchKeydown(e) {
            if (searchResultsCount === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedSearchIndex = (selectedSearchIndex + 1) % searchResultsCount;
                updateSearchSelection();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedSearchIndex = (selectedSearchIndex - 1 + searchResultsCount) % searchResultsCount;
                updateSearchSelection();
            } else if (e.key === 'Enter' && selectedSearchIndex >= 0) {
                e.preventDefault();
                document.getElementById(`search-item-${selectedSearchIndex}`).click();
            }
        }

        function updateSearchSelection() {
            document.querySelectorAll('.search-result-item').forEach(el => el.classList.remove('active'));
            if (selectedSearchIndex >= 0) {
                const activeEl = document.getElementById(`search-item-${selectedSearchIndex}`);
                if (activeEl) {
                    activeEl.classList.add('active');
                    // Scroll inteligente centrado al navegar
                    activeEl.scrollIntoView({
                        block: 'center',
                        behavior: 'smooth'
                    });
                }
            }
        }

        // --- NOTIFICACIONES AJAX ---
        let currentNotifKeys = [];
        let currentNotifTab = 'pendientes';

        function cambiarTabNotificaciones(tab) {
            currentNotifTab = tab;
            const tabPend = document.getElementById('tab-notif-pendientes');
            const tabVist = document.getElementById('tab-notif-vistas');
            const listPend = document.getElementById('notif-list');
            const listVist = document.getElementById('notif-read-list');
            const btnMarcarTodas = document.getElementById('btn-marcar-todas-notif');

            if (tab === 'pendientes') {
                tabPend.className = 'notif-tab-active flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 bg-white text-teal-700 shadow-2xs border border-slate-200/60 cursor-pointer';
                tabVist.className = 'notif-tab-inactive flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 hover:bg-white/60 cursor-pointer';
                listPend.classList.remove('hidden');
                listVist.classList.add('hidden');
                if (btnMarcarTodas && currentNotifKeys.length > 0) {
                    btnMarcarTodas.classList.remove('hidden');
                }
            } else {
                tabVist.className = 'notif-tab-active flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 bg-white text-teal-700 shadow-2xs border border-slate-200/60 cursor-pointer';
                tabPend.className = 'notif-tab-inactive flex-1 py-1.5 px-3 rounded-xl text-center transition-all flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 hover:bg-white/60 cursor-pointer';
                listPend.classList.add('hidden');
                listVist.classList.remove('hidden');
                if (btnMarcarTodas) {
                    btnMarcarTodas.classList.add('hidden');
                }
            }
        }

        function loadNotifications() {
            fetch('/api/notifications')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notif-badge');
                    const count = document.getElementById('notif-count');
                    const readCount = document.getElementById('notif-read-count');
                    const list = document.getElementById('notif-list');
                    const readList = document.getElementById('notif-read-list');
                    const btnMarcarTodas = document.getElementById('btn-marcar-todas-notif');

                    currentNotifKeys = (data.data || []).map(n => n.id).filter(Boolean);

                    // 1. Contador del badge campana
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                    } else {
                        badge.classList.add('hidden');
                        badge.textContent = '0';
                    }

                    // 2. Contadores en las pestañas
                    if (count) count.textContent = data.count;
                    if (readCount) readCount.textContent = data.read_count || 0;

                    // 3. Botón "Marcar todas"
                    if (btnMarcarTodas) {
                        if (data.count > 0 && currentNotifTab === 'pendientes') {
                            btnMarcarTodas.classList.remove('hidden');
                        } else {
                            btnMarcarTodas.classList.add('hidden');
                        }
                    }

                    // 4. Renderizar lista PENDIENTES
                    if (data.count > 0) {
                        let html = '';
                        data.data.forEach(n => {
                            html += `
                            <div class="group relative flex items-start gap-2 p-2 rounded-xl transition-colors hover:bg-slate-50 border-b border-slate-100 last:border-0" data-notif-id="${n.id}">
                                <a href="${n.url}" onclick="onNotifClick('${n.id}')" class="flex-1 flex gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${n.bg} ${n.color}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${n.icon}</svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12.5px] font-bold text-slate-800 leading-snug">${n.title}</p>
                                        <p class="text-[11px] font-medium mt-0.5 leading-snug text-slate-500">${n.message}</p>
                                        <p class="text-[10px] font-mono font-bold mt-1 text-slate-400">${n.time}</p>
                                    </div>
                                </a>
                                <button onclick="marcarNotificacionVista(event, '${n.id}')" type="button"
                                    class="shrink-0 p-1.5 text-slate-400 hover:text-teal-700 hover:bg-teal-50 rounded-lg transition-colors cursor-pointer"
                                    title="Marcar como vista">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </div>`;
                        });
                        list.innerHTML = html;
                    } else {
                        list.innerHTML =
                            '<div class="p-6 text-center text-slate-400 text-[12px] font-medium flex flex-col items-center justify-center gap-1.5"><svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>¡Estás al día! No hay alertas pendientes.</span></div>';
                    }

                    // 5. Renderizar lista VISTAS (Apartado de Notificaciones Vistas)
                    if (data.read_count > 0 && Array.isArray(data.read_data)) {
                        let htmlVistas = '';
                        data.read_data.forEach(n => {
                            htmlVistas += `
                            <div class="group relative flex items-start gap-2 p-2 rounded-xl transition-colors hover:bg-slate-100/70 border-b border-slate-100 last:border-0 opacity-80 hover:opacity-100" data-notif-read-id="${n.id}">
                                <a href="${n.url}" class="flex-1 flex gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-slate-100 text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${n.icon}</svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-[12px] font-bold text-slate-700 leading-snug">${n.title}</p>
                                            <span class="inline-block text-[9.5px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-semibold border border-slate-200/60">Vista</span>
                                        </div>
                                        <p class="text-[11px] font-normal mt-0.5 leading-snug text-slate-500">${n.message}</p>
                                        <p class="text-[10px] font-mono font-medium mt-1 text-slate-400">${n.read_at_human || 'Vista recientemente'}</p>
                                    </div>
                                </a>
                                <button onclick="desmarcarNotificacionVista(event, '${n.id}')" type="button"
                                    class="shrink-0 p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer"
                                    title="Restaurar a pendientes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                </button>
                            </div>`;
                        });
                        readList.innerHTML = htmlVistas;
                    } else {
                        readList.innerHTML =
                            '<div class="p-6 text-center text-slate-400 text-[12px] font-medium flex flex-col items-center justify-center gap-1.5"><svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg><span>No tienes notificaciones en el apartado de vistas.</span></div>';
                    }
                })
                .catch(() => {});
        }

        function onNotifClick(id) {
            if (!id) return;
            fetch('/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            }).catch(() => {});
        }

        function marcarNotificacionVista(event, id) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const itemEl = document.querySelector(`[data-notif-id="${id}"]`);
            if (itemEl) {
                itemEl.classList.add('opacity-40', 'pointer-events-none');
            }

            fetch('/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(() => {
                loadNotifications();
            })
            .catch(() => {
                if (itemEl) itemEl.classList.remove('opacity-40', 'pointer-events-none');
            });
        }

        function desmarcarNotificacionVista(event, id) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const itemEl = document.querySelector(`[data-notif-read-id="${id}"]`);
            if (itemEl) {
                itemEl.classList.add('opacity-40', 'pointer-events-none');
            }

            fetch('/api/notifications/unmark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(() => {
                loadNotifications();
            })
            .catch(() => {
                if (itemEl) itemEl.classList.remove('opacity-40', 'pointer-events-none');
            });
        }

        function marcarTodasNotificacionesVistas(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (!currentNotifKeys.length) return;

            const btn = document.getElementById('btn-marcar-todas-notif');
            if (btn) btn.disabled = true;

            fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ keys: currentNotifKeys })
            })
            .then(res => res.json())
            .then(() => {
                loadNotifications();
            })
            .catch(() => {})
            .finally(() => {
                if (btn) btn.disabled = false;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadNotifications();
            setInterval(loadNotifications, 45000);
        });


        /* --- Control del Sidebar --- */
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('das-theme', isDark ? 'dark' : 'light'); } catch (e) {}
            window.dispatchEvent(new CustomEvent('das-theme-change', { detail: { dark: isDark } }));
        }

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

        function toggleNotifications() {
            toggleMenu('notifications-menu');
            loadNotifications();
        }

        function toggleUserMenu() {
            toggleMenu('user-menu');
        }

        /* --- Búsqueda Ctrl + K Atajos --- */
        function openSearchModal() {
            const modal = document.getElementById('search-modal');
            const card = document.getElementById('search-modal-card');
            const input = document.getElementById('global-search-input');
            const clearBtn = document.getElementById('search-clear-btn');

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
                input.value = '';
                clearBtn.classList.add('hidden');
                document.getElementById('search-results').innerHTML =
                    '<div class="px-3 py-6 text-center text-[12px] text-slate-400">Escribe al menos 2 letras para buscar en toda la base de datos...</div>';
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
                if (container && menu && !container.contains(event.target) && !menu.classList.contains(
                        'hidden')) {
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
