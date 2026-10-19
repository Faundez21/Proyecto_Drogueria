<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
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
                        Administra inventarios, ventas y operaciones en tiempo real desde un solo lugar.
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
                            <h3 class="font-semibold text-lg">Control total</h3>
                            <p class="text-sm text-blue-200">Gestiona inventarios y productos fácilmente</p>
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
                            <p class="text-sm text-blue-200">Toma decisiones basadas en datos actualizados</p>
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
                            <h3 class="font-semibold text-lg">Trazabilidad</h3>
                            <p class="text-sm text-blue-200">Rastrea y gestiona tu inventario de manera eficiente</p>
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
                            <h3 class="font-semibold text-lg">Generador QR</h3>
                            <p class="text-sm text-blue-200">Crea etiquetas para agilizar la identificación de productos
                            </p>
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
                            <h3 class="font-semibold text-lg">Recepción y Despacho</h3>
                            <p class="text-sm text-blue-200">Controla el flujo de mercancía entrante y saliente al
                                instante</p>
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
                            <p class="text-sm text-blue-200">Gestiona usuarios, proveedores y zonas de cuarentena de
                                forma segura</p>
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
                        <div class="flex justify-end mt-2">
                            <a href="#" onclick="abrirModalRecuperacion(event)"
                                class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-lg shadow-md text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        Iniciar sesión
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <p class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-600">
                    ¿No tienes una cuenta?
                    <button onclick="contactarAdministrador()"
                        class="font-medium text-blue-700 hover:text-blue-900 focus:outline-none">
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
        });

        // Modal con SweetAlert2 para Recuperar Contraseña
        function abrirModalRecuperacion(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Recuperar Contraseña',
                text: 'Solicita al administrador restablecer tu acceso.',
                input: 'email',
                inputPlaceholder: 'ejemplo@drogueriadas.com',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Enviar solicitud',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                inputValidator: (value) => {
                    if (!value) return '¡Debes ingresar un correo electrónico!';
                },
                preConfirm: async (email) => {
                    try {
                        const response = await fetch("{{ route('password.request.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json', // <--- PIDE JSON OBLIGATORIAMENTE
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        });

                        const contentType = response.headers.get("content-type");

                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            const data = await response.json();
                            if (!response.ok) {
                                if (response.status === 429 || (data.message && data.message.includes(
                                        'Too Many Attempts'))) {
                                    throw new Error(
                                        'Demasiados intentos. Por favor, espera un momento antes de reintentar.'
                                    );
                                }
                                if (data.errors && data.errors.email) {
                                    throw new Error('Debes ingresar un correo electrónico válido.');
                                }
                                throw new Error(data.message || data.errors?.email?.[0] ||
                                    'El correo no existe en el sistema.');
                            }
                            return data;
                        } else {
                            if (!response.ok) {
                                throw new Error('El correo electrónico no se encuentra registrado.');
                            }
                        }
                    } catch (error) {
                        Swal.showValidationMessage(`${error.message}`);
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¡Solicitud enviada!',
                        text: 'El administrador ha sido notificado.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    });
                }
            });
        }

        // Modal con SweetAlert2 para Contactar Administrador
        function contactarAdministrador() {
            Swal.fire({
                title: 'Contactar al Administrador',
                text: 'Escribe a drogueriatalcahuano@gmail.com o usa tu aplicación de correos para solicitar tu cuenta.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Abrir Correo',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'mailto:drogueriatalcahuano@gmail.com';
                }
            });
        }
    </script>
</body>

</html>
