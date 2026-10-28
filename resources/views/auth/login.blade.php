<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        (function () {
            try {
                var saved = localStorage.getItem('das-theme');
                var prefers = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (!saved && prefers)) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}?v={{ @filemtime(public_path('css/dark-mode.css')) }}">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        font-family: 'Inter', sans-serif;
    }
    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-contacts-auto-fill-button {
        visibility: hidden;
        display: none !important;
        pointer-events: none;
        position: absolute;
        right: 0;
    }
</style>
</head>

<body
    class="bg-gradient-to-br from-slate-100 to-slate-300 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <div
        class="w-full max-w-md lg:max-w-7xl bg-white rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col lg:flex-row overflow-hidden min-h-[80vh] lg:min-h-[85vh]">

        <!-- VISTA MÓVIL - SOLO IMAGEN -->
        <div class="block lg:hidden w-full h-40 sm:h-48 relative bg-cover bg-center"
            style="background-image: url('{{ asset('images/bg-drogueria.png') }}');">
            <div class="absolute inset-0 bg-blue-900/10"></div>
        </div>

        <!-- PANEL IZQUIERDO - DESKTOP -->
        <div class="hidden lg:flex w-1/2 relative bg-blue-900 flex-col justify-between p-12 text-white"
            style="background-image: url('{{ asset('images/bg-drogueria.png') }}'); background-size: cover; background-position: center;">

            <div class="absolute inset-0 bg-gradient-to-r from-blue-950/95 via-blue-900/70 to-blue-900/20"></div>

            <div class="relative z-10 flex flex-col h-full">

                <!-- LOGO DESKTOP -->
                <div class="flex items-center gap-4 mb-16">
                    <div class="flex items-center gap-2 font-bold text-2xl">
                        <div class="drop-shadow-md">
                            <svg class="w-9 h-9" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M 12 12 L 4 12 A 4 4 0 0 0 0 16 A 4 4 0 0 0 4 20 L 12 20 L 12 28 A 4 4 0 0 0 16 32 A 4 4 0 0 0 20 28 L 20 12 Z"
                                    fill="#ffffff" />
                                <path d="M 12 12 L 12 4 A 4 4 0 0 1 16 0 A 4 4 0 0 1 20 4 L 20 12 Z" fill="#3b82f6" />
                                <path d="M 20 12 L 28 12 A 4 4 0 0 1 32 16 A 4 4 0 0 1 28 20 L 20 20 Z"
                                    fill="#10b981" />
                            </svg>
                        </div>
                        Drogueria <span class="text-blue-300">DAS</span>
                    </div>
                    <div class="w-px h-8 bg-blue-400/30"></div>
                    <p class="text-sm text-blue-100 leading-tight">
                        Gestión inteligente,<br>salud eficiente.
                    </p>
                </div>

                <div class="mb-12 max-w-md">
                    <h1 class="text-4xl font-bold leading-tight mb-4">
                        Sistema inteligente para una <span class="text-blue-400">gestión eficiente</span>
                    </h1>
                    <p class="text-blue-100/80">
                        Administra inventarios, despachos a establecimientos de salud y trazabilidad clínica en tiempo real desde un solo lugar.
                    </p>
                </div>

                <div class="space-y-6 flex-1">
                    <!-- 1. CONTROL TOTAL -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Control de Bodega y Lotes</h3>
                            <p class="text-sm text-blue-200">Gestiona medicamentos, insumos clínicos y estupefacientes con criterio FEFO</p>
                        </div>
                    </div>

                    <!-- 2. REPORTES -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Reportes en tiempo real</h3>
                            <p class="text-sm text-blue-200">Exporta informes valorizados de inventario y actas en PDF y Excel</p>
                        </div>
                    </div>

                    <!-- 3. TRAZABILIDAD -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Trazabilidad Sanitaria</h3>
                            <p class="text-sm text-blue-200">Kardex completo de ingresos, cuarentena ISP y despachos por lote</p>
                        </div>
                    </div>

                    <!-- 4. GENERADOR QR -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Direccionamiento QR y Mapa 3D</h3>
                            <p class="text-sm text-blue-200">Ubica pasillos, estantes, niveles y posiciones al instante</p>
                        </div>
                    </div>

                    <!-- 5. RECEPCIÓN Y DESPACHO -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Solicitudes CESFAM y Despachos</h3>
                            <p class="text-sm text-blue-200">Atiende pedidos de establecimientos y despachos controlados con respaldo</p>
                        </div>
                    </div>

                    <!-- 6. ADMINISTRACIÓN -->
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Administración Centralizada</h3>
                            <p class="text-sm text-blue-200">Gestiona usuarios, roles por vista, proveedores y zonas de cuarentena</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL DERECHO DEL LOGIN -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-10 lg:p-16 relative">
            <div class="w-full max-w-md">

                <!-- LOGO MÓVIL -->
                <div class="flex lg:hidden items-center justify-center gap-2 font-bold text-2xl mb-6">
                    <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M 12 12 L 4 12 A 4 4 0 0 0 0 16 A 4 4 0 0 0 4 20 L 12 20 L 12 28 A 4 4 0 0 0 16 32 A 4 4 0 0 0 20 28 L 20 12 Z"
                            fill="#e2e8f0" />
                        <path d="M 12 12 L 12 4 A 4 4 0 0 1 16 0 A 4 4 0 0 1 20 4 L 20 12 Z" fill="#3b82f6" />
                        <path d="M 20 12 L 28 12 A 4 4 0 0 1 32 16 A 4 4 0 0 1 28 20 L 20 20 Z" fill="#10b981" />
                    </svg>
                    <span class="text-gray-800">
                        Drogueria <span class="text-blue-600">DAS</span>
                    </span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 text-center lg:text-left">
                    Bienvenido de nuevo
                </h2>
                <p class="text-sm sm:text-base text-gray-500 mb-6 sm:mb-8 text-center lg:text-left">
                    Inicia sesión para continuar en <span class="font-semibold text-blue-700">Drogueria DAS</span>
                </p>

                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="block w-full pl-10 pr-3 py-2.5 sm:py-3 bg-white border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400 text-sm sm:text-base @error('email') border-red-500 @enderror"
                                placeholder="admin@drogueriadas.com">
                        </div>
                        @error('email')
                            <span class="text-sm text-red-600 mt-1 font-medium block">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <input type="password" name="password" id="login_password" required minlength="8"
                                maxlength="64" autocomplete="current-password"
                                oninvalid="this.setCustomValidity('La contraseña debe tener entre 8 y 64 caracteres.')"
                                oninput="this.setCustomValidity('')"
                                class="block w-full pl-10 pr-12 py-2.5 sm:py-3 bg-white border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400 text-sm sm:text-base @error('password') border-red-500 @enderror"
                                placeholder="Mínimo 8 caracteres">
                            <button type="button" id="btn_toggle_password" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 transition-colors"
                                aria-label="Mostrar u ocultar contraseña" tabindex="-1">
                                <svg id="eye_open" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <svg id="eye_closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-sm text-red-600 mt-1 font-medium block">
                                {{ $message }}
                            </span>
                        @enderror
                        <div id="caps_lock_warning" class="hidden mt-1.5 flex items-center gap-1.5 text-xs font-semibold text-amber-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Bloq Mayús (Caps Lock) está activado</span>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs sm:text-sm text-gray-600 font-medium">Recordarme</span>
                            </label>
                            <a href="#" onclick="abrirModalRecuperacion(event)"
                                class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <button type="submit" id="btn_submit_login"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-lg shadow-md text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 transition-colors cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed">
                        <span id="btn_submit_text">Iniciar sesión</span>
                        <svg id="btn_submit_arrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                        <svg id="btn_submit_spinner" class="hidden w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>
                </form>

                <p class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-600">
                    ¿No tienes una cuenta?
                    <button type="button" onclick="contactarAdministrador()"
                        class="font-semibold text-blue-700 hover:text-blue-900 underline-offset-2 hover:underline focus:outline-none cursor-pointer">
                        Contacta al administrador
                    </button>
                </p>

            </div>

            <div class="mt-8 lg:absolute lg:bottom-8 w-full text-center">
                <p class="text-[11px] sm:text-xs text-gray-400">
                    &copy; {{ date('Y') }} Drogueria DAS. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <!-- MODAL 1: ¿OLVIDASTE TU CONTRASEÑA? -->
    <div id="forgotPasswordModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
        onclick="if(event.target === this) cerrarModalRecuperacion()">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden transform transition-all">
            <!-- Cabecera -->
            <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-blue-950 text-white flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white leading-tight">Recuperar Contraseña</h3>
                        <p class="text-xs text-blue-200/80 mt-0.5">Solicitud interna de restablecimiento de clave</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalRecuperacion()"
                    class="text-slate-400 hover:text-white p-1.5 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Cuerpo del Modal (Formulario) -->
            <div id="forgotFormView" class="p-6 space-y-5">
                <!-- Flujo explicativo en 3 pasos -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">¿Cómo funciona?</p>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <span>Ingresa el correo institucional registrado en tu cuenta.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <span>El <strong>Administrador</strong> recibirá una alerta inmediata en el panel de Usuarios.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <span>Se te asignará una nueva contraseña segura (mínimo 8 caracteres).</span>
                        </div>
                    </div>
                </div>

                <form id="forgotPasswordForm" onsubmit="enviarSolicitudRecuperacion(event)" class="space-y-4" novalidate>
                    <div>
                        <label for="recovery_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Correo Electrónico Registrado <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <input type="email" id="recovery_email" required placeholder="ejemplo@drogueriadas.com"
                                class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        </div>
                        <div id="recovery_error_box" class="hidden mt-2 p-2.5 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2 text-xs text-red-700 font-medium">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="recovery_error_text"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" onclick="cerrarModalRecuperacion()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" id="btn_recovery_submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-sm transition-all cursor-pointer disabled:opacity-60">
                            <span id="btn_recovery_text">Enviar solicitud</span>
                            <svg id="btn_recovery_spinner" class="hidden w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Vista de Éxito -->
            <div id="forgotSuccessView" class="hidden p-6 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-slate-900">¡Solicitud registrada con éxito!</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Notificamos al administrador sobre la cuenta <strong id="recovery_success_email" class="text-slate-800"></strong>. Pronto se actualizará tu clave de acceso.
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" onclick="cerrarModalRecuperacion()"
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors cursor-pointer shadow-sm">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: CONTACTAR AL ADMINISTRADOR -->
    <div id="contactAdminModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
        onclick="if(event.target === this) cerrarModalContacto()">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden transform transition-all">
            <!-- Cabecera -->
            <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-blue-950 text-white flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white leading-tight">Solicitar Cuenta de Acceso</h3>
                        <p class="text-xs text-blue-200/80 mt-0.5">Administración Droguería Comunal DAS</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalContacto()"
                    class="text-slate-400 hover:text-white p-1.5 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Cuerpo -->
            <div class="p-6 space-y-4">
                <p class="text-xs text-slate-600 leading-relaxed">
                    El acceso a la plataforma es exclusivo para personal autorizado de Droguería y establecimientos de salud (CESFAM / Postas). Comunícate con el administrador para habilitar tu perfil:
                </p>

                <!-- Tarjeta de Correo con Botón Copiar -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Correo de Administración</span>
                        <p id="admin_email_text" class="text-xs sm:text-sm font-bold text-slate-800 truncate mt-0.5">drogueriatalcahuano@gmail.com</p>
                    </div>
                    <button type="button" onclick="copiarCorreoAdmin()" id="btn_copy_admin_email"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer">
                        <svg id="copy_icon_default" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <svg id="copy_icon_check" class="hidden w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span id="copy_btn_label">Copiar</span>
                    </button>
                </div>

                <!-- Datos opcionales para armar el correo automáticamente -->
                <div class="space-y-3 pt-1">
                    <div>
                        <label for="contact_name" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tu Nombre y Apellido <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                        </label>
                        <input type="text" id="contact_name" placeholder="Ej. Camila Muñoz"
                            class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    </div>
                    <div>
                        <label for="contact_unit" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Establecimiento / Unidad <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                        </label>
                        <input type="text" id="contact_unit" placeholder="Ej. CESFAM Paulina Avendaño / Bodega"
                            class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="cerrarModalContacto()"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                        Cerrar
                    </button>
                    <button type="button" onclick="abrirClienteCorreoAdmin()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-sm transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Redactar Correo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        function togglePassword() {
            const input = document.getElementById('login_password');
            const eyeOpen = document.getElementById('eye_open');
            const eyeClosed = document.getElementById('eye_closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Detector de Bloq Mayús (Caps Lock) en el campo de contraseña
        const loginPassInput = document.getElementById('login_password');
        const capsWarning = document.getElementById('caps_lock_warning');
        if (loginPassInput && capsWarning) {
            const checkCaps = (e) => {
                if (typeof e.getModifierState === 'function') {
                    capsWarning.classList.toggle('hidden', !e.getModifierState('CapsLock'));
                }
            };
            loginPassInput.addEventListener('keyup', checkCaps);
            loginPassInput.addEventListener('keydown', checkCaps);
            loginPassInput.addEventListener('blur', () => capsWarning.classList.add('hidden'));
        }

        // Validaciones básicas en cliente antes de enviar
        document.getElementById('login_password').closest('form').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('login_password').value;

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Correo inválido',
                    text: 'Ingresa un correo electrónico válido.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            if (password.length < 8 || password.length > 64) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña inválida',
                    text: 'La contraseña debe tener entre 8 y 64 caracteres.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            // Estado de carga para evitar doble envío
            const submitBtn = document.getElementById('btn_submit_login');
            const submitText = document.getElementById('btn_submit_text');
            const submitArrow = document.getElementById('btn_submit_arrow');
            const submitSpinner = document.getElementById('btn_submit_spinner');
            if (submitBtn) {
                submitBtn.disabled = true;
                if (submitText) submitText.textContent = 'Verificando acceso...';
                if (submitArrow) submitArrow.classList.add('hidden');
                if (submitSpinner) submitSpinner.classList.remove('hidden');
            }

            // Fallback de seguridad: si no se redirige en 10s, restaurar el botón
            setTimeout(resetLoginButton, 10000);
        });

        // Restaurar estado del botón de inicio de sesión
        function resetLoginButton() {
            const submitBtn = document.getElementById('btn_submit_login');
            const submitText = document.getElementById('btn_submit_text');
            const submitArrow = document.getElementById('btn_submit_arrow');
            const submitSpinner = document.getElementById('btn_submit_spinner');
            if (submitBtn) {
                submitBtn.disabled = false;
                if (submitText) submitText.textContent = 'Iniciar sesión';
                if (submitArrow) submitArrow.classList.remove('hidden');
                if (submitSpinner) submitSpinner.classList.add('hidden');
            }
        }

        // Manejar retroceso en el navegador (bfcache / back button)
        window.addEventListener('pageshow', function(event) {
            resetLoginButton();

            // Si la página fue restaurada desde el historial o bfcache tras iniciar sesión
            const navEntries = (window.performance && typeof performance.getEntriesByType === 'function')
                ? performance.getEntriesByType('navigation')
                : [];
            const isBackForward = event.persisted ||
                (navEntries.length > 0 && navEntries[0].type === 'back_forward') ||
                (window.performance && window.performance.navigation && window.performance.navigation.type === 2);

            if (isBackForward) {
                // Forzar recarga limpia para que el backend valide Auth::check() y redirija automáticamente al usuario a su módulo
                window.location.reload();
            }
        });

        // ==========================================
        // MODAL 1: RECUPERAR CONTRASEÑA
        // ==========================================
        function abrirModalRecuperacion(event) {
            if (event) event.preventDefault();
            const modal = document.getElementById('forgotPasswordModal');
            const formView = document.getElementById('forgotFormView');
            const successView = document.getElementById('forgotSuccessView');
            const errorBox = document.getElementById('recovery_error_box');
            const emailInput = document.getElementById('recovery_email');
            const loginEmail = (document.querySelector('input[name="email"]')?.value || '').trim();

            if (formView) formView.classList.remove('hidden');
            if (successView) successView.classList.add('hidden');
            if (errorBox) errorBox.classList.add('hidden');

            if (emailInput) {
                emailInput.value = loginEmail;
                setTimeout(() => emailInput.focus(), 80);
            }

            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalRecuperacion() {
            const modal = document.getElementById('forgotPasswordModal');
            if (modal) modal.classList.add('hidden');
        }

        async function enviarSolicitudRecuperacion(event) {
            event.preventDefault();
            const emailInput = document.getElementById('recovery_email');
            const errorBox = document.getElementById('recovery_error_box');
            const errorText = document.getElementById('recovery_error_text');
            const submitBtn = document.getElementById('btn_recovery_submit');
            const submitText = document.getElementById('btn_recovery_text');
            const submitSpinner = document.getElementById('btn_recovery_spinner');

            const email = (emailInput?.value || '').trim();

            const mostrarError = (msg) => {
                if (errorText) errorText.textContent = msg;
                if (errorBox) errorBox.classList.remove('hidden');
            };

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                mostrarError('Por favor, ingresa un correo electrónico válido.');
                emailInput?.focus();
                return;
            }

            if (errorBox) errorBox.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.textContent = 'Enviando...';
            if (submitSpinner) submitSpinner.classList.remove('hidden');

            try {
                const response = await fetch("{{ route('password.request.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email })
                });

                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json') ? await response.json() : {};

                if (!response.ok) {
                    if (response.status === 429 || (data.message && data.message.includes('Too Many Attempts'))) {
                        throw new Error('Demasiados intentos. Por favor, espera un minuto antes de reintentar.');
                    }
                    const validationMsg = data.errors?.email?.[0] || data.message || 'El correo electrónico no se encuentra registrado en el sistema.';
                    throw new Error(validationMsg);
                }

                // Mostrar vista de éxito dentro del modal
                document.getElementById('recovery_success_email').textContent = email;
                document.getElementById('forgotFormView').classList.add('hidden');
                document.getElementById('forgotSuccessView').classList.remove('hidden');
            } catch (err) {
                mostrarError(err.message || 'No se pudo procesar la solicitud.');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.textContent = 'Enviar solicitud';
                if (submitSpinner) submitSpinner.classList.add('hidden');
            }
        }

        // ==========================================
        // MODAL 2: CONTACTAR AL ADMINISTRADOR
        // ==========================================
        function contactarAdministrador() {
            const modal = document.getElementById('contactAdminModal');
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalContacto() {
            const modal = document.getElementById('contactAdminModal');
            if (modal) modal.classList.add('hidden');
        }

        function copiarCorreoAdmin() {
            const email = 'drogueriatalcahuano@gmail.com';
            const label = document.getElementById('copy_btn_label');
            const iconDefault = document.getElementById('copy_icon_default');
            const iconCheck = document.getElementById('copy_icon_check');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(email);
            }

            if (label) label.textContent = '¡Copiado!';
            if (iconDefault) iconDefault.classList.add('hidden');
            if (iconCheck) iconCheck.classList.remove('hidden');

            setTimeout(() => {
                if (label) label.textContent = 'Copiar';
                if (iconDefault) iconDefault.classList.remove('hidden');
                if (iconCheck) iconCheck.classList.add('hidden');
            }, 2200);
        }

        function abrirClienteCorreoAdmin() {
            const nombre = (document.getElementById('contact_name')?.value || '').trim();
            const unidad = (document.getElementById('contact_unit')?.value || '').trim();

            const subject = encodeURIComponent('Solicitud de Creación de Cuenta - Droguería DAS');
            let bodyLines = [
                'Estimado/a Administrador/a de Droguería DAS,',
                '',
                'Junto con saludar, solicito la creación y habilitación de una cuenta de acceso en la plataforma.',
            ];

            if (nombre) bodyLines.push(`- Nombre: ${nombre}`);
            if (unidad) bodyLines.push(`- Establecimiento / Unidad: ${unidad}`);

            bodyLines.push('', 'Quedo atento/a a sus comentarios.', 'Saludos cordiales.');

            const body = encodeURIComponent(bodyLines.join('\n'));
            window.location.href = `mailto:drogueriatalcahuano@gmail.com?subject=${subject}&body=${body}`;
        }

        // Cerrar modales con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalRecuperacion();
                cerrarModalContacto();
            }
        });
    </script>
<button type="button" onclick="toggleTheme()" class="theme-toggle login-theme-toggle cursor-pointer"
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
    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('das-theme', isDark ? 'dark' : 'light'); } catch (e) {}
        }
    </script>
</body>

</html>
