@extends('layouts.app')

@section('content')
    <div class="w-full max-w-[1400px] mx-auto p-4 sm:p-6">
        <!-- miga de pan -->
        <div class="flex items-center gap-2 text-gray-500 mb-4">
            <a href="{{ route('distribution.index') }}" class="hover:text-blue-900">
                Distribución
            </a>

            <span>/</span>

            <span class="text-gray-800 font-medium">
                Pasillos
            </span>
        </div>
        <!-- Encabezado -->

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Pasillos
                </h1>

                <p class="text-gray-500 mt-1">
                    Administración de los pasillos de la bodega
                </p>

            </div>

            <button onclick="openCreate()"
                class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg cursor-pointer shadow-sm">
                + Nuevo pasillo
            </button>

        </div>


        <!-- Tabla -->

        <div class="overflow-x-auto">

            <table class="w-full min-w-[600px] text-left text-sm text-slate-600">

                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs font-semibold">

                    <tr>

                        <th class="px-6 py-4">
                            ID
                        </th>

                        <th class="px-6 py-4">
                            Nombre
                        </th>

                        <th class="px-6 py-4">
                            Descripción
                        </th>

                        <th class="px-6 py-4">
                            Estanterías totales
                        </th>

                        <th class="px-6 py-4 text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <tr class="hover:bg-slate-50 transition-colors">

                        <td class="px-6 py-4 font-mono text-xs text-slate-500">
                            1
                        </td>

                        <td class="px-6 py-4 font-bold text-slate-800">
                            Pasillo A
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            Medicamentos generales
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            6
                        </td>

                        <td class="px-6 py-4 text-center">

                            <div class="flex justify-center gap-2">

                                <!-- Ver -->
                                <button onclick="openShow()"
                                    class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors"
                                    title="Ver pasillo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- Editar -->
                                <button onclick="openEdit()"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                    title="Editar pasillo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>

                                <!-- Eliminar -->
                                <button onclick="openDelete()"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- Modal para crear un nuevo pasillo -->

        <div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">

            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    Nuevo pasillo
                </h2>


                <!-- formulario para crear un nuevo pasillo -->

                <form action="{{ route('pasillos.store') }}" method="POST">

                    @csrf


                    <div class="mb-4">

                        <label for="nombre" class="block text-gray-700 font-bold mb-2">
                            Nombre:
                        </label>

                        <input type="text" name="nombre" id="nombre" placeholder="Ej: Pasillo A"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">

                    </div>




                    <div class="mb-6">

                        <label for="descripcion" class="block text-gray-700 font-bold mb-2">
                            Descripción:
                        </label>

                        <textarea name="descripcion" id="descripcion" rows="4" placeholder="Descripción del pasillo..."
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

    <!-- Modal para ver un pasillo -->

    <div id="showModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Información del pasillo
            </h2>

            <p class="text-gray-700 mb-2">
                <strong>Nombre:</strong> Pasillo A
            </p>

            <p class="text-gray-700 mb-4">
                <strong>Descripción:</strong> Medicamentos generales
            </p>

            <p class="text-gray-700">
                <strong>Estanterías totales:</strong> 6
            </p>

            <div class="flex justify-end mt-6">

                <button type="button" onclick="closeShow()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg cursor-pointer">
                    Cerrar
                </button>

            </div>

        </div>

    </div>

    <!-- Modal para editar un pasillo -->

    <div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Editar pasillo
            </h2>

            <!-- formulario para editar un pasillo -->

            <form>

                <div class="mb-4">

                    <label for="nombreEditar" class="block text-gray-700 font-bold mb-2">
                        Nombre:
                    </label>

                    <input type="text" name="nombre" id="nombreEditar" value="Pasillo A"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">

                </div>

                <div class="mb-6">

                    <label for="descripcionEditar" class="block text-gray-700 font-bold mb-2">
                        Descripción:
                    </label>

                    <textarea name="descripcion" id="descripcionEditar" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">Medicamentos generales</textarea>

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
