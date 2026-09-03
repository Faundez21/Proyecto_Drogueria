<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasillos</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="max-w-6xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">

            <div>
                <h1 class="text-3xl">
                    Pasillos
                </h1>

                <p class="text-gray-500">
                    Administración de los pasillos de la bodega
                </p>
            </div>

            <a href="{{ route('pasillos.create') }}"
            class="bg-blue-900 text-white px-4 py-2 rounded-lg">+ Nuevo pasillo
            </a>

        </div>


        <div class="bg-white rounded-lg shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-blue-900 text-white">

                    <tr>

                        <th class="px-6 py-3 text-left">
                            ID
                        </th>

                        <th class="px-6 py-3 text-left">
                            Nombre
                        </th>

                        <th class="px-6 py-3 text-left">
                            Descripción
                        </th>

                        <th class="px-6 py-3 text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr class="border-t">

                        <td class="px-6 py-4">
                            1
                        </td>

                        <td class="px-6 py-4 font-medium">
                            Pasillo A
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            Medicamentos generales
                        </td>

                        <td class="px-6 py-4 text-center">

                            <a href="{{ route('pasillos.show', 1) }}" class="text-gray-600 mr-3">
                            Ver
                </a>

                            <a href="{{ route('pasillos.edit', 1) }}" class="text-yellow-600 mr-3">
                 Editar
                </a>

                            <button class="text-red-600">
                                Eliminar
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>
