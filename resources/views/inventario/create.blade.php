<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex justify-between items-center w-full shadow-sm">
        <div class="flex items-center gap-6 w-full max-w-3xl">
            <button class="p-2 bg-slate-50 text-slate-500 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <a href="#" class="text-2xl font-extrabold text-slate-800 tracking-wider">DAS</a>
        </div>
    </nav>
    <br>
    <div class="w-full flex justify-center py-8 px-4">

        <div class="w-full max-w-4xl p-8 sm:p-12 bg-white rounded-xl shadow-sm border border-gray-100">

            <h1 class="text-2xl font-bold mb-6">Importar Recepcion</h1>

            <div class="flex items-center gap-4 mb-8">

                <form action="/subir" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">

                    <input type="file" id="excel" name="excel" class="hidden">


                    <label for="excel"
                        class="bg-white border border-gray-800 text-gray-800 hover:bg-gray-100 px-4 py-2 rounded-md cursor-pointer inline-block text-sm font-medium transition-colors">
                        Subir archivo
                    </label>

                    <x-button type="submit">
                        Guardar
                    </x-button>

                </form>
            </div>

            <hr class="my-8">
            <br>
            <h1 class="text-2xl font-bold mb-6">Ingresar Manualmente</h1>

            <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
            <div class="relative mb-8" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded-md border border-gray-300 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 focus:border-blue-500"
                    id="Proveedor" name="Proveedor" placeholder="Ingresa el Proveedor" />
            </div>

            <label for="rut_Proveedor" class="block text-sm font-medium text-gray-700 mb-1">Rut Proveedor</label>
            <div class="relative mb-8" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded-md border border-gray-300 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 focus:border-blue-500"
                    id="rut_Proveedor" name="Rut_Proveedor" placeholder="Ingresa el Rut del proveedor" />
            </div>

            <label for="Orden" class="block text-sm font-medium text-gray-700 mb-1">Orden de compra</label>
            <div class="relative mb-8" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded-md border border-gray-300 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 focus:border-blue-500"
                    id="Orden" name="Orden" placeholder="Ingrese de donde fue la orden" />
            </div>

            <div class="flex justify-end mt-10 gap-3">
                <x-button variant="danger">
                    Cancelar
                </x-button>
                <x-button variant="primary">
                    Guardar
                </x-button>
            </div>
        </div>
    </div>
</body>

</html>
