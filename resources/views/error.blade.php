<!DOCTYPE html>
<html lang="es">
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡¡Error!!</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg p-8 sm:p-10 bg-white rounded-xl shadow-sm border border-gray-100 text-center">

        <div class="mb-6 flex justify-center">
            <div class="bg-red-50 text-red-500 rounded-full p-5">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">Ha ocurrido un problema</h1>

        <p class="text-gray-500 mb-8 text-sm leading-relaxed">
            No pudimos procesar tu solicitud en este momento. Por favor, verifica los datos e intenta nuevamente, o
            contacta al administrador del sistema.
        </p>


        <div class="flex flex-col sm:flex-row justify-center gap-3">
            <button onclick="window.history.back()"
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm w-full sm:w-auto">
                Volver atrás
            </button>

            <a href="/"
                class="px-5 py-2.5 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors font-medium text-sm w-full sm:w-auto">
                Ir al Inicio
            </a>
        </div>

    </div>

</body>

</html>
