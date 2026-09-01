<!DOCTYPE html>
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
    
</body>
</html>