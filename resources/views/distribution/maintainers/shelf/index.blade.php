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
                            Pasillo al que pertenece
                        </th>

                        <th class="px-6 py-4 text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @foreach ($shelves as $shelf)
                        <tr>
                            <td class="px-6 py-4">
                                {{ $shelf->id }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $shelf->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $shelf->description }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $shelf->aisle->name ?? 'Sin pasillo' }}</td>
                            <td class="px-6 py-4 text-center">


                                <div class="flex justify-center gap-2">

                                    <!-- Ver -->
                                    <button
                                        onclick='openShow({{ $shelf->id }}, @json($shelf->name), @json($shelf->aisle->name ?? 'Sin pasillo'), @json($shelf->description))'
                                        class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors"
                                        title="Ver estantería">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <!-- Editar -->
                                    <button
                                        onclick='openEdit({{ $shelf->id }}, @json($shelf->name), {{ $shelf->aisle_id }}, @json($shelf->description))'
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                        title="Editar estantería">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <!-- Eliminar -->
                                    <form action="{{ route('shelf.destroy', $shelf->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta estantería?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors cursor-pointer"
                                            title="Eliminar estantería">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>


        <!-- Modal para crear una nueva estantería -->
        <div id="createModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Nueva estantería
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Registra una nueva estantería de la bodega.
                        </p>
                    </div>

                    <button type="button" onclick="closeCreate()"
                        class="text-blue-100 hover:text-white hover:bg-blue-800 rounded-lg p-1.5 transition-colors cursor-pointer">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </button>

                </div>

                <!-- Formulario -->
                <form action="{{ route('shelf.store') }}" method="POST">

                    @csrf

                    <div class="px-6 py-6">

                        <!-- Nombre -->
                        <div class="mb-5">

                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nombre
                            </label>

                            <input type="text" name="name" id="name" placeholder="Ej: Estantería A"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition">

                        </div>

                        <!-- Pasillo -->
                        <div class="mb-5">

                            <label for="aisle_id" class="block text-sm font-semibold text-slate-700 mb-2">
                                Pasillo al que pertenece
                            </label>

                            <select name="aisle_id" id="aisle_id"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition bg-white">

                                @foreach ($aisles as $aisle)
                                    <option value="{{ $aisle->id }}">
                                        {{ $aisle->name }} ({{ $aisle->description }})
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <!-- Descripción -->
                        <div>

                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                                Descripción
                            </label>

                            <textarea name="description" id="description" rows="4" placeholder="Descripción de la estantería..."
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition resize-none"></textarea>

                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">

                        <button type="button" onclick="closeCreate()"
                            class="px-4 py-2 text-sm font-medium text-slate-700
                    bg-white border border-slate-300 rounded-lg
                    hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancelar
                        </button>

                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white
                    bg-blue-900 rounded-lg hover:bg-blue-800
                    transition-colors cursor-pointer">
                            Guardar estantería
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- Modal para ver una estantería -->

        <div id="showModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado azul -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Información de la estantería
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Información registrada de la estantería.
                        </p>
                    </div>

                    <button type="button" onclick="closeShow()"
                        class="text-blue-100 hover:text-white hover:bg-blue-800 rounded-lg p-1.5 transition-colors cursor-pointer">

                        ✕

                    </button>

                </div>

                <!-- Contenido blanco -->
                <div class="px-6 py-6">

                    <p class="text-sm text-slate-500 mb-1">
                        Nombre
                    </p>

                    <p id="showName" class="text-base font-semibold text-slate-800 mb-5">
                    </p>

                    <p class="text-sm text-slate-500 mb-1">
                        Pasillo
                    </p>

                    <p id="showAisle" class="text-base font-semibold text-slate-800 mb-5">
                    </p>

                    <p class="text-sm text-slate-500 mb-1">
                        Descripción
                    </p>

                    <p id="showDescription" class="text-slate-700">
                    </p>
                </div>

                <!-- Botón -->
                <div class="flex justify-end px-6 py-4 bg-slate-50 border-t border-slate-200">

                    <button type="button" onclick="closeShow()"
                        class="px-4 py-2 text-sm font-medium text-slate-700
                bg-white border border-slate-300 rounded-lg
                hover:bg-slate-100 transition-colors cursor-pointer">
                        Cerrar
                    </button>

                </div>

            </div>

        </div>


        <!-- Modal para editar una estantería -->

        <div id="editModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Editar estantería
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Modifica la información de la estantería.
                        </p>
                    </div>

                    <button type="button" onclick="closeEdit()"
                        class="text-blue-100 hover:text-white hover:bg-blue-800 rounded-lg p-1.5 transition-colors cursor-pointer">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </button>

                </div>

                <!-- Formulario -->
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-6">

                        <div class="mb-5">

                            <label for="editName" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nombre
                            </label>

                            <input type="text" name="name" id="editName" value="Estantería 1"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition">

                        </div>

                        <!-- Pasillo -->
                        <div class="mb-5">

                            <label for="editAisle" class="block text-sm font-semibold text-slate-700 mb-2">
                                Pasillo al que pertenece
                            </label>

                            <select name="aisle_id" id="editAisle"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition bg-white">

                                @foreach ($aisles as $aisle)
                                    <option value="{{ $aisle->id }}">
                                        {{ $aisle->name }} ({{ $aisle->description }})
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div>

                            <label for="editDescription" class="block text-sm font-semibold text-slate-700 mb-2">
                                Descripción
                            </label>

                            <textarea name="description" id="editDescription" rows="4"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100
                                focus:border-blue-500 transition resize-none"></textarea>

                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">

                        <button type="button" onclick="closeEdit()"
                            class="px-4 py-2 text-sm font-medium text-slate-700
                    bg-white border border-slate-300 rounded-lg
                    hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancelar
                        </button>

                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white
                    bg-blue-900 rounded-lg hover:bg-blue-800
                    transition-colors cursor-pointer">
                            Guardar cambios
                        </button>

                    </div>

                </form>

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

            function openShow(id, name, aisle, description) {
                document.getElementById('showModal').classList.remove('hidden');
                document.getElementById('showName').textContent = name;
                document.getElementById('showAisle').textContent = aisle;
                document.getElementById('showDescription').textContent = description || 'Sin descripción';
            }

            function closeShow() {
                document
                    .getElementById('showModal')
                    .classList.add('hidden');
            }

            function openEdit(id, name, aisleId, description) {
                document.getElementById('editModal').classList.remove('hidden');

                document.getElementById('editName').value = name;
                document.getElementById('editAisle').value = aisleId;
                document.getElementById('editDescription').value = description || '';

                document.getElementById('editForm').action = '/shelf/' + id;
            }

            function closeEdit() {
                document
                    .getElementById('editModal')
                    .classList.add('hidden');
            }
        </script>
    @endsection
