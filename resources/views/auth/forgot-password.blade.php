<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Droguería DAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-slate-100 mx-4">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Recuperar Contraseña</h1>
            <p class="text-sm text-slate-500 mt-2">
                Ingresa tu correo registrado y enviaremos una solicitud al administrador para restablecer tu acceso al sistema.
            </p>
        </div>

        <form action="#" method="GET" onsubmit="event.preventDefault(); alert('¡Solicitud enviada con éxito!');">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                    Correo Electrónico
                </label>
                <input type="email" id="email" name="email" required autofocus
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                    placeholder="ejemplo@drogueriadas.com">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors shadow-sm flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Enviar solicitud al Administrador
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-blue-600 font-medium transition-colors">
                &larr; Volver al inicio de sesión
            </a>
        </div>

    </div>

</body>
</html>
