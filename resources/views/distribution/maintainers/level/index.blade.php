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
                Niveles
            </span>
            @role('Administrador')
                <div class="flex gap-2 w-full sm:w-auto ml-auto">
                    <button type="button" onclick="openHistory()"
                        class="w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 flex items-center justify-center gap-2 transition-colors shadow-sm">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ver Historial
                    </button>
                </div>
            @endrole
        </div>
        <!-- Encabezado -->

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Niveles
                </h1>

                <p class="text-gray-500 mt-1">
                    Administración de los niveles de las estanterías de la bodega
                </p>

            </div>

            <button onclick="openCreate()"
                class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg cursor-pointer shadow-sm">
                + Nuevo nivel
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
                            Número
                        </th>

                        <th class="px-6 py-4">
                            Estantería
                        </th>

                        <th class="px-6 py-4">
                            Descripción
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
                            Nivel 1
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            Estantería A
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            Lote Paracetamol
                        </td>

                        <td class="px-6 py-4 text-center">

                            <div class="flex justify-center gap-2">

                                <!-- Ver -->
                                <button onclick="openShow()"
                                    class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors"
                                    title="Ver nivel">
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
                                    title="Editar nivel">
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


        <!-- Modal para crear un nuevo nivel -->
        <div id="createModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Nuevo nivel
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Registra un nuevo nivel de la estantería.
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
                <form>

                    <div class="px-6 py-6">

                        <!-- Número -->
                        <div class="mb-5">

                            <label for="numero" class="block text-sm font-semibold text-slate-700 mb-2">
                                Número
                            </label>

                            <input type="number" name="numero" id="numero" placeholder="Ej: 1"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition">

                        </div>

                        <!-- Estantería -->
                        <div class="mb-5">

                            <label for="estanteria" class="block text-sm font-semibold text-slate-700 mb-2">
                                Estantería
                            </label>

                            <select name="estanteria" id="estanteria"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition bg-white">

                                <option value="">
                                    Seleccione una estantería
                                </option>

                                <option value="1">
                                    Estantería A
                                </option>

                                <option value="2">
                                    Estantería B
                                </option>

                                <option value="3">
                                    Estantería C
                                </option>

                            </select>

                        </div>

                        <!-- Descripción -->
                        <div>

                            <label for="descripcion" class="block text-sm font-semibold text-slate-700 mb-2">
                                Descripción
                            </label>

                            <textarea name="descripcion" id="descripcion" rows="4" placeholder="Descripción del nivel..."
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
                            Guardar nivel
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- Modal para ver un nivel -->
        <div id="showModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Información del nivel
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Detalles del nivel seleccionado.
                        </p>
                    </div>

                    <button type="button" onclick="closeShow()"
                        class="text-blue-100 hover:text-white hover:bg-blue-800 rounded-lg p-1.5 transition-colors cursor-pointer">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </button>

                </div>

                <!-- Información -->
                <div class="px-6 py-6 space-y-4">

                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">
                            Número
                        </p>

                        <p class="text-slate-800 font-medium">
                            Nivel 1
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">
                            Estantería
                        </p>

                        <p class="text-slate-800 font-medium">
                            Estantería A
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-1">
                            Descripción
                        </p>

                        <p class="text-slate-700">
                            Lote Paracetamol
                        </p>
                    </div>

                </div>

                <!-- Botones -->
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

        <!-- Modal para editar un nivel -->
        <div id="editModal"
            class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Encabezado -->
                <div class="bg-blue-900 px-6 py-5 flex items-start justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Editar nivel
                        </h2>

                        <p class="text-blue-100 text-sm mt-1">
                            Modifica la información del nivel.
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
                <form>

                    <div class="px-6 py-6">

                        <div class="mb-5">

                            <label for="numeroEditar" class="block text-sm font-semibold text-slate-700 mb-2">
                                Número
                            </label>

                            <input type="number" name="numero" id="numeroEditar" value="1"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition">

                        </div>

                        <div class="mb-5">

                            <label for="estanteriaEditar" class="block text-sm font-semibold text-slate-700 mb-2">
                                Estantería
                            </label>

                            <select name="estanteria" id="estanteriaEditar"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition bg-white">

                                <option value="1" selected>
                                    Estantería A
                                </option>

                                <option value="2">
                                    Estantería B
                                </option>

                                <option value="3">
                                    Estantería C
                                </option>

                            </select>

                        </div>

                        <div>

                            <label for="descripcionEditar" class="block text-sm font-semibold text-slate-700 mb-2">
                                Descripción
                            </label>

                            <textarea name="descripcion" id="descripcionEditar" rows="4"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-100
                        focus:border-blue-500 transition resize-none">Lote Paracetamol</textarea>

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
        <!-- modal de historial -->
        <div id="HistoryModal"
            class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 w-full max-w-4xl overflow-hidden">

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Historial de Niveles</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Registro de auditoría de creación y modificaciones de
                            niveles.</p>
                    </div>
                    <button type="button" onclick="closeHistory()"
                        class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-96 border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Fecha / Hora</th>
                                <th class="px-4 py-3 font-semibold">Acción</th>
                                <th class="px-4 py-3 font-semibold">Detalle del Nivel</th>
                                <th class="px-4 py-3 font-semibold">Usuario Responsable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-slate-500">16/09/2026 - 11:15</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold">Creación</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">Nivel 1 (Lote Paracetamol)</td>
                                <td class="px-4 py-3 text-slate-600 font-medium">Juan Pérez</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button type="button" onclick="closeHistory()"
                        class="px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-colors text-sm font-medium shadow-sm">
                        Cerrar
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
                if (confirm('¿Estás seguro de que deseas eliminar este nivel?')) {
                    alert('Nivel eliminado');
                }
            }

            function openHistory() {
                document.getElementById('HistoryModal').classList.remove('hidden');
            }

            function closeHistory() {
                document.getElementById('HistoryModal').classList.add('hidden');
            }
        </script>
    @endsection
