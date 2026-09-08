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
                    Editar pasillo
                </h1>

                <p class="text-gray-500">
                </p>
            </div>

        </div>


        <div class="bg-white rounded-lg shadow overflow-hidden">

            <form action="{{ route('pasillos.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('pasillos.index') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg">+ Guardar
                    </a>
                    <a href="{{ route('pasillos.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</a>
                </div>
            </form>

        </div>

    </div>

</body>
</html>
