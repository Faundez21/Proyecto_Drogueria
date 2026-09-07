<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layout - Almacenamiento</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="max-w-6xl mx-auto p-6">

        <div class="mb-6">

            <h1 class="text-3xl">
                Almacenamiento
            </h1>

            <p class="text-gray-500">
                Visualización del layout de la bodega
            </p>

        </div>

        <div class="flex items-center gap-3 mb-6">

            <label class="text-lg">
                Pasillo:
            </label>

            <select class="border border-gray-300 bg-white rounded-lg px-3 py-2">

                <option>Pasillo A (Insumos)</option>
                <option>Pasillo B</option>
                <option>Pasillo C</option>

            </select>

        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden p-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="md:col-span-3">

                    <div class="grid grid-cols-3 gap-5">

                        <div class="h-28 bg-green-500 border border-gray-500 rounded-lg flex items-center justify-center text-2xl text-gray-800">
                            A-01
                        </div>

                        <div class="h-28 bg-orange-400 border border-gray-500 rounded-lg flex items-center justify-center text-2xl text-gray-800">
                            B-01
                        </div>

                        <div class="h-28 bg-gray-500 border border-gray-600 rounded-lg flex items-center justify-center text-2xl text-white">
                            C-01
                        </div>

                        <div class="h-28 bg-orange-400 border border-gray-500 rounded-lg flex items-center justify-center text-2xl text-gray-800">
                            A-02
                        </div>

                        <div class="h-28 bg-green-500 border border-gray-500 rounded-lg flex items-center justify-center text-2xl text-gray-800">
                            B-02
                        </div>

                        <div class="h-28 bg-gray-500 border border-gray-600 rounded-lg flex items-center justify-center text-2xl text-white">
                            C-02
                        </div>

                    </div>

                </div>

                <div class="flex flex-col justify-center gap-4 border-l border-gray-200 pl-6">

                    <h2 class="text-lg">
                        Estado
                    </h2>

                    <div class="flex items-center gap-2">

                        <span class="w-5 h-5 bg-green-500 border border-gray-500 rounded"></span>

                        <span>
                            Disponible
                        </span>

                    </div>

                    <div class="flex items-center gap-2">

                        <span class="w-5 h-5 bg-orange-400 border border-gray-500 rounded"></span>

                        <span>
                            Lleno
                        </span>

                    </div>

                    <div class="flex items-center gap-2">

                        <span class="w-5 h-5 bg-gray-500 border border-gray-600 rounded"></span>

                        <span>
                            En cuarentena
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>
</html>
