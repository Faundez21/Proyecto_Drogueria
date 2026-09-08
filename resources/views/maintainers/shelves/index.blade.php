@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-4 sm:p-6">
        <!-- miga de pan -->
        <div class="flex items-center gap-2 text-gray-500 mb-4">
            <a href="{{ route('distribution.index') }}" class="hover:text-blue-900">
                Distribución
            </a>

            <span>/</span>

            <span class="text-gray-800 font-medium">
                Estanterías
            </span>
        </div>

        <!-- Encabezado -->

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Estanterías
                </h1>

                <p class="text-gray-500 mt-1">
                    Administración de las estanterías de los pasillos de la bodega
                </p>

            </div>

            <button onclick="openCreate()"
                class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg cursor-pointer shadow-sm">
                + Nueva estantería
            </button>

        </div>


        <!-- Tabla -->

        <div class="bg-white rounded-lg shadow overflow-x-auto border border-gray-100">

            <table class="w-full min-w-[600px]">

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

                    <tr class="border-t hover:bg-gray-50">

                        <td class="px-6 py-4 text-gray-600">
                            1
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            Estantería A
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            Estantería del Pasillo A
                        </td>

                        <td class="px-6 py-4 text-center">

                            <button onclick="openShow()" class="text-gray-600 hover:text-gray-800 mr-3 cursor-pointer">
                                Ver
                            </button>

                            <button onclick="openEdit()" class="text-yellow-600 hover:text-yellow-800 mr-3 cursor-pointer">
                                Editar
                            </button>

                            <button onclick="openDelete()" class="text-red-600 hover:text-red-800 mr-3 cursor-pointer">
                                Eliminar
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- Modal para crear una nueva estantería -->

        <div id="createModal" class="hidden fixed inset-0 bg-black/20 flex items-center justify-center">

            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    Nueva estantería
                </h2>

                <!-- formulario para crear una nueva estantería -->

                <form>

                    <div class="mb-4">

                        <label for="nombre" class="block text-gray-700 font-bold mb-2">
                            Nombre:
                        </label>

                        <input type="text" name="nombre" id="nombre" placeholder="Ej: Estantería A"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">

                    </div>

                    <div class="mb-6">

                        <label for="descripcion" class="block text-gray-700 font-bold mb-2">
                            Descripción:
                        </label>

                        <textarea name="descripcion" id="descripcion" rows="4" placeholder="Descripción de la estantería..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"></textarea>

                    </div>

                    <!-- Botones -->

                    <div class="flex justify-end gap-3">

                        <button type="button" onclick="closeCreate()"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg cursor-pointer">
                            Cancelar
                        </button>

                        <button type="submit"
                            class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg cursor-pointer">
                            Guardar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Modal para ver una estantería -->

    <div id="showModal" class="hidden fixed inset-0 bg-black/20 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Información de la estantería
            </h2>

            <p class="text-gray-700 mb-2">
                <strong>Nombre:</strong> Estantería A
            </p>

            <p class="text-gray-700">
                <strong>Descripción:</strong> Estantería del Pasillo A
            </p>

            <div class="flex justify-end mt-6">

                <button type="button" onclick="closeShow()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg cursor-pointer">
                    Cerrar
                </button>

            </div>

        </div>

    </div>


    <!-- Modal para editar una estantería -->

    <div id="editModal" class="hidden fixed inset-0 bg-black/20 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Editar estantería
            </h2>

            <!-- formulario para editar una estantería -->

            <form>

                <div class="mb-4">

                    <label for="nombreEditar" class="block text-gray-700 font-bold mb-2">
                        Nombre:
                    </label>

                    <input type="text" name="nombre" id="nombreEditar" value="Estantería A"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">

                </div>

                <div class="mb-6">

                    <label for="descripcionEditar" class="block text-gray-700 font-bold mb-2">
                        Descripción:
                    </label>

                    <textarea name="descripcion" id="descripcionEditar" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">Estantería del Pasillo A</textarea>

                </div>

                <!-- Botones -->

                <div class="flex justify-end gap-3">

                    <button type="button" onclick="closeEdit()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg cursor-pointer">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg cursor-pointer">
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!--Modal para eliminar pasillos-->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Eliminar pasillo
            </h2>

            <p class="text-gray-700 mb-4">
                ¿Estás seguro de que deseas eliminar este pasillo?
            </p>

            <div class="flex justify-end gap-3">

                <button type="button" onclick="closeDelete()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg cursor-pointer">
                    Cancelar
                </button>

                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg cursor-pointer">
                    Eliminar
                </button>

            </div>

        </div>

    </div>

    <!-- Funciones con JS para abrir y cerrar el modal -->

    <script>
        function openCreate() {

            document
                .getElementById('createModal')
                .classList.remove('hidden');

        }


        function closeCreate() {

            document
                .getElementById('createModal')
                .classList.add('hidden');

        }

        function openShow() {
            document
                .getElementById('showModal')
                .classList.remove('hidden');
        }

        function closeShow() {
            document
                .getElementById('showModal')
                .classList.add('hidden');
        }

        function openEdit() {
            document
                .getElementById('editModal')
                .classList.remove('hidden');
        }

        function closeEdit() {
            document
                .getElementById('editModal')
                .classList.add('hidden');
        }

        function openDelete() {
            if (confirm('¿Estás seguro de que deseas eliminar este pasillo?')) {
                alert('Pasillo eliminado');
            }
        }
    </script>
@endsection
