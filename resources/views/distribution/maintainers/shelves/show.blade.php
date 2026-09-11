<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalle del Pasillo</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-6">

        <div class="mb-6">

            <a
                href="{{ route('pasillos.index') }}"
                class="text-blue-600 hover:underline"
            >
                ← Volver a Pasillos
            </a>

            <h1 class="text-3xl font-bold text-gray-800 mt-3">
                Detalle del Pasillo
            </h1>

        </div>



        <div class="bg-white rounded-lg shadow overflow-hidden">

            <div class="bg-blue-900 text-white px-6 py-4">

                <h2 class="text-xl font-bold">
                    {{ $pasillo['nombre'] }}
                </h2>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <div>

                        <p class="text-sm text-gray-500">
                            ID
                        </p>

                        <p class="text-lg font-semibold text-gray-800">
                            {{ $pasillo['id'] }}
                        </p>

                    </div>



                    <div>

                        <p class="text-sm text-gray-500">
                            Nombre
                        </p>

                        <p class="text-lg font-semibold text-gray-800">
                            {{ $pasillo['nombre'] }}
                        </p>

                    </div>



                    <div class="md:col-span-2">

                        <p class="text-sm text-gray-500">
                            Descripción
                        </p>

                        <p class="text-gray-800 mt-1">
                            {{ $pasillo['descripcion'] }}
                        </p>

                    </div>

                </div>


                <!-- Acciones -->

                <div class="flex justify-end gap-3 mt-8">

                    <a
                        href="{{ route('pasillos.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                    >
                        Volver
                    </a>

                    <a
                        href="{{ route('pasillos.edit', $pasillo['id']) }}"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"
                    >
                        Editar
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
