<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <nav style="background-color: rgba(210, 222, 249);" class="text-black px-6 py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-xl font-bold">DAS</a>
        </div>
    </nav>
    <div class="px-6 py-4">
        <br>
        <h1 class="text-2xl font-bold mb-4">Importar</h1>
        <br>
        <div class="flex items-center gap-4">

            <form action="/subir" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
                <input type="file" id="excel" name="excel">
            </form>

            <x-button>Guardar</x-button>

        </div>
        <br>
        <hr>
        <br>
        <h1 class="text-2xl font-bold mb-4">Ingresar Manualmente</h1>

        <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>

        <div class="relative mb-12" data-twe-input-wrapper-init>
            <div class="relative mb-12" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear"
                    id="Proveedor" name="Proveedor" placeholder="Ingresa el Proveedor" />

            </div>
        </div>
        <br>
        <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Rut Proveedor</label>

        <div class="relative mb-12" data-twe-input-wrapper-init>
            <div class="relative mb-12" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear"
                    id="rut_Proveedor" name="Rut_Proveedor" placeholder="Ingresa el Rut del proveedor" />

            </div>
        </div>
        <br>
        <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Orden de compra</label>

        <div class="relative mb-12" data-twe-input-wrapper-init>
            <div class="relative mb-12" data-twe-input-wrapper-init>
                <input type="text"
                    class="peer block min-h-[auto] w-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear"
                    id="Orden" name="Orden" placeholder="Ingrese de donde fue la orden" />

            </div>
        </div>
        <br>
        <br>
        <div class="flex justify-end mt-8">
            <x-button variant="primary">
                Guardar
            </x-button>
        </div>
</body>

</html>
