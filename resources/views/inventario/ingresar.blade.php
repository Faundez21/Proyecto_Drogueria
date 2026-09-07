
@extends('layouts.app')
@section('content')
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
@endsection
