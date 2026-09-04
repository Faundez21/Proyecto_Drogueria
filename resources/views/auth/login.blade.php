<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droguería DAS - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-slate-100 to-slate-300 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <!-- Contenedor Principal -->
    <div class="w-full max-w-md lg:max-w-7xl bg-white rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col lg:flex-row overflow-hidden min-h-[80vh] lg:min-h-[85vh]">
        
        <!-- Vista movil (Solo Imagen) -->
        <div class="block lg:hidden w-full h-40 sm:h-48 relative bg-cover bg-center" 
             style="background-image: url('{{ asset('images/bg-drogueria.jpg') }}');">
            <div class="absolute inset-0 bg-blue-900/10"></div>
        </div>

        <!-- Panel Izquierdo parte Desktop -->
        <div class="hidden lg:flex w-1/2 relative bg-blue-900 flex-col justify-between p-12 text-white" 
             style="background-image: url('{{ asset('images/bg-drogueria.jpg') }}'); background-size: cover; background-position: center;">
            
            <div class="absolute inset-0 bg-gradient-to-r from-blue-950/95 via-blue-900/70 to-blue-900/20"></div>

            <div class="relative z-10 flex flex-col h-full">
                <!-- Logo Desktop -->
                <div class="flex items-center gap-4 mb-16">
                    <div class="flex items-center gap-2 font-bold text-2xl">
                        <div class="drop-shadow-md">
                            <svg class="w-9 h-9" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M 12 12 L 4 12 A 4 4 0 0 0 0 16 A 4 4 0 0 0 4 20 L 12 20 L 12 28 A 4 4 0 0 0 16 32 A 4 4 0 0 0 20 28 L 20 12 Z" fill="#ffffff" />
                                <path d="M 12 12 L 12 4 A 4 4 0 0 1 16 0 A 4 4 0 0 1 20 4 L 20 12 Z" fill="#3b82f6" />
                                <path d="M 20 12 L 28 12 A 4 4 0 0 1 32 16 A 4 4 0 0 1 28 20 L 20 20 Z" fill="#10b981" />
                            </svg>
                        </div>
                        Drogueria <span class="text-blue-300">DAS</span>
                    </div>
                    <div class="w-px h-8 bg-blue-400/30"></div>
                    <p class="text-sm text-blue-100 leading-tight">Gestión inteligente,<br>salud eficiente.</p>
                </div>

                <!-- Textos Principales Desktop -->
                <div class="mb-12 max-w-md">
                    <h1 class="text-4xl font-bold leading-tight mb-4">Sistema inteligente para una <span class="text-blue-400">gestión eficiente</span></h1>
                    <p class="text-blue-100/80">Administra inventarios, ventas y operaciones en tiempo real desde un solo lugar.</p>
                </div>

                <!-- Lista de Beneficios Desktop -->
                <div class="space-y-6 flex-1">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Control total</h3>
                            <p class="text-sm text-blue-200">Gestiona inventarios y productos fácilmente</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Reportes en tiempo real</h3>
                            <p class="text-sm text-blue-200">Toma decisiones basadas en datos actualizados</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-800/50 p-3 rounded-full border border-blue-600/30 shrink-0">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Trazabilidad</h3>
                            <p class="text-sm text-blue-200">Rastrea y gestiona tu inventario de manera eficiente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  Panel Derecho del login  -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-10 lg:p-16 relative">
            
            <div class="w-full max-w-md">
                <!-- Logo solo visible en móviles (Ajustado para fondo blanco) -->
                <div class="flex lg:hidden items-center justify-center gap-2 font-bold text-2xl mb-6">
                    <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 12 12 L 4 12 A 4 4 0 0 0 0 16 A 4 4 0 0 0 4 20 L 12 20 L 12 28 A 4 4 0 0 0 16 32 A 4 4 0 0 0 20 28 L 20 12 Z" fill="#e2e8f0" />
                        <path d="M 12 12 L 12 4 A 4 4 0 0 1 16 0 A 4 4 0 0 1 20 4 L 20 12 Z" fill="#3b82f6" />
                        <path d="M 20 12 L 28 12 A 4 4 0 0 1 32 16 A 4 4 0 0 1 28 20 L 20 20 Z" fill="#10b981" />
                    </svg>
                    <span class="text-gray-800">Drogueria <span class="text-blue-600">DAS</span></span>
                </div>

                <!-- Textos del Formulario -->
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 text-center lg:text-left">Bienvenido de nuevo</h2>
                <p class="text-sm sm:text-base text-gray-500 mb-6 sm:mb-8 text-center lg:text-left">Inicia sesión para continuar en <span class="font-semibold text-blue-700">Drogueria DAS</span></p>

                <!-- Formulario -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" required class="block w-full pl-10 pr-3 py-2.5 sm:py-3 bg-white border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400 text-sm sm:text-base" placeholder="admin@drogueriadas.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input type="password" name="password" required class="block w-full pl-10 pr-10 py-2.5 sm:py-3 bg-white border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400 text-sm sm:text-base" placeholder="Ingresa tu contraseña">
                        </div>
                        <div class="flex justify-end mt-2">
                            <a href="{{ route('password.request') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-lg shadow-md text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        Iniciar sesión
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>

                <p class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-600">
                    ¿No tienes una cuenta? <a href="#" class="font-medium text-blue-700 hover:text-blue-900">Contacta al administrador</a>
                </p>
            </div>
            
            <div class="mt-8 lg:absolute lg:bottom-8 w-full text-center">
                <p class="text-[11px] sm:text-xs text-gray-400">&copy; {{ date('Y') }} Drogueria DAS. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>

</body>
</html>