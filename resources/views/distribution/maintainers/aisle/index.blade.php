@extends('layouts.app')

@section('title', 'Mantenedor de Pasillos')
@section('subtitle', 'Administración de zonas y pasillos físicos de almacenamiento en la bodega.')

@section('actions')
    <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('distribution.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-teal-700 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Mapa 3D
        </a>
        @role('Administrador')
            <button type="button" onclick="openHistory()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-[13px] font-semibold hover:bg-slate-50 hover:text-teal-700 transition-all duration-200 shadow-sm cursor-pointer">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ver Historial
            </button>
        @endrole
        <button type="button" onclick="openCreate()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all duration-200 text-[13px] font-bold shadow-sm cursor-pointer">
            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Pasillo
        </button>
    </div>
@endsection

@section('content')
<<<<<<< HEAD
    <div class="w-full">
        <!-- miga de pan -->
        <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-2 px-1 pt-2">
            <a href="{{ route('distribution.index') }}" class="hover:text-blue-900">
                Distribución
            </a>

            <span>/</span>

            <span class="text-gray-800 font-medium">
                Pasillos
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
=======
    <div class="space-y-6">
        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">02</span>
            <span>Almacenamiento</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('distribution.index') }}" class="hover:text-teal-700 transition-colors">Distribución</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-medium text-slate-800">Pasillos</span>
>>>>>>> origin/cristobal
        </div>

        <!-- Barra de Navegación Jerárquica de Bodega -->
        @include('distribution.partials.nav_tabs', ['activeTab' => 'aisle'])

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-[13px] font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Contenedor Principal Tabla Pasillos -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- Cabecera y buscador -->
            <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800">Directorio de Pasillos</h3>
                        <p class="text-[12px] text-slate-500">Total registrados: <strong class="text-slate-700 font-mono-das">{{ $aisles->count() }}</strong></p>
                    </div>
                </div>

                <div class="w-full md:w-auto flex-1 max-w-lg lg:max-w-xl">
                    <div class="relative w-full">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="aisleSearchInput" oninput="filtrarPasillos()" placeholder="Buscar por pasillo, zona o descripción..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition-all">
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            <button type="button" onclick="openCreate()"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all duration-200 text-[13px] font-bold shadow-sm hover:shadow cursor-pointer">
            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo pasillo
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

                    @foreach ($aisles as $aisle)
=======
            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-[13px] text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
>>>>>>> origin/cristobal
                        <tr>
                            <th class="px-6 py-3.5 w-20">ID</th>
                            <th class="px-6 py-3.5">Pasillo / Identificador</th>
                            <th class="px-6 py-3.5">Descripción Operativa</th>
                            <th class="px-6 py-3.5 text-center">Color 3D</th>
                            <th class="px-6 py-3.5 text-center">Estanterías</th>
                            <th class="px-6 py-3.5 text-center">Capacidad Acumulada</th>
                            <th class="px-6 py-3.5 text-center w-36">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100" id="aisleTableBody">
                        @forelse ($aisles as $aisle)
                            @php
                                $colorHex = $aisle->color ?: '#3B82F6';
                                $shelvesCount = $aisle->shelves_count ?? 0;
                                $capTotal = (int) ($aisle->shelves_sum_capacidad ?? 0);
                            @endphp
                            <tr class="aisle-row hover:bg-slate-50/70 transition-colors"
                                data-search="{{ strtolower($aisle->name . ' ' . ($aisle->description ?? '')) }}">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11.5px] font-semibold text-slate-600">
                                        #{{ $aisle->id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full shrink-0 ring-2 ring-offset-1 ring-slate-200" style="background-color: {{ $colorHex }};"></span>
                                        <span class="font-bold text-slate-800 text-[13.5px]">{{ $aisle->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $aisle->description ?: 'Sin descripción asignada' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 font-mono-das text-[11px] font-semibold text-slate-700">
                                        <span class="w-2.5 h-2.5 rounded-sm" style="background-color: {{ $colorHex }};"></span>
                                        {{ strtoupper($colorHex) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $shelvesCount > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $shelvesCount }} {{ $shelvesCount === 1 ? 'estantería' : 'estanterías' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($capTotal > 0)
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold bg-teal-50 text-teal-700 border border-teal-200 font-mono-das">
                                            {{ number_format($capTotal, 0, ',', '.') }} uds.
                                        </span>
                                    @else
                                        <span class="text-[12px] text-slate-400 font-medium">Sin límite</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1.5">
                                        <button type="button"
                                            onclick='openShow({{ $aisle->id }}, @json($aisle->name), @json($aisle->description), {{ $shelvesCount }}, @json($colorHex))'
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                                            title="Ver detalle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick='openEdit({{ $aisle->id }}, @json($aisle->name), @json($aisle->description), @json($colorHex))'
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors cursor-pointer"
                                            title="Editar pasillo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form id="delete-aisle-{{ $aisle->id }}" action="{{ route('aisle.destroy', $aisle->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteAisle('{{ $aisle->id }}', '{{ addslashes($aisle->name) }}')"
                                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                title="Eliminar pasillo">
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
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2"></path>
                                    </svg>
                                    <p class="text-[13.5px] font-bold text-slate-600">No hay pasillos registrados</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Crea el primer pasillo para organizar tus estanterías.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear Pasillo -->
    <div id="createModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Nuevo Pasillo</h2>
                        <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Registra un nuevo pasillo físico en la bodega.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreate()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="createAisleForm" action="{{ route('aisle.store') }}" method="POST" onsubmit="confirmCreateAisle(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf
                <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label for="name" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Nombre del Pasillo <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required placeholder="Ej: Pasillo A"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>

                    <div>
                        <label for="color" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Color Identificador (Visor 3D) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <input type="color" name="color" id="color" value="#0D9488"
                                class="w-10 h-9 rounded border border-slate-300 cursor-pointer bg-white p-0.5">
                            <span class="text-[12px] text-slate-500">
                                Este color identificará a las etiquetas del pasillo en el mapa 3D.
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Descripción Operativa
                        </label>
                        <textarea name="description" id="description" rows="3" placeholder="Ej: Pasillo de medicamentos de alta rotación..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-2.5 px-4 sm:px-6 py-3 sm:py-4 bg-slate-50 border-t border-slate-200 shrink-0">
                    <button type="button" onclick="closeCreate()"
                        class="w-full sm:w-auto px-4 py-2 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer text-center">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2 text-[13px] font-bold text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors shadow-sm cursor-pointer text-center">
                        Guardar Pasillo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Pasillo -->
    <div id="showModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div>
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Ficha del Pasillo</h2>
                    <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Información registrada en la bodega.</p>
                </div>
                <button type="button" onclick="closeShow()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pasillo</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span id="showColorDot" class="w-3 h-3 rounded-full bg-teal-500"></span>
                            <p id="showName" class="text-[14px] font-bold text-slate-800"></p>
                        </div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estanterías Asociadas</p>
                        <p id="showShelvesCount" class="text-[14px] font-bold text-teal-700 font-mono-das mt-1"></p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Descripción</p>
                    <p id="showDescription" class="text-[13px] text-slate-700"></p>
                </div>
            </div>

            <div class="flex justify-end px-4 sm:px-6 py-3 sm:py-4 bg-slate-50 border-t border-slate-200 shrink-0">
                <button type="button" onclick="closeShow()"
                    class="w-full sm:w-auto px-4 py-2 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer text-center">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Pasillo -->
    <div id="editModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-5">
        <div class="flex min-h-full items-center justify-center py-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
                <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-400/30 text-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[16px] font-bold text-white leading-tight">Editar Pasillo</h2>
                            <p class="text-slate-400 text-[12px] mt-0.5">Actualiza los datos del pasillo seleccionado.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEdit()"
                        class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-2 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="editForm" method="POST" onsubmit="confirmEditAisle(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-5 sm:p-6 space-y-4.5 overflow-y-auto flex-1 overscroll-contain">
                        <div>
                            <label for="editName" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Nombre del Pasillo <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="editName" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <div>
                            <label for="editColor" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Color Identificador (Visor 3D) <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                <input type="color" name="color" id="editColor" value="#3B82F6"
                                    class="w-11 h-9 rounded-lg border border-slate-300 cursor-pointer bg-white p-0.5 shrink-0 shadow-2xs">
                                <span class="text-[12px] text-slate-500">
                                    Color aplicado a las etiquetas del pasillo en el visor 3D.
                                </span>
                            </div>
                        </div>

                        <div>
                            <label for="editDescription" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Descripción Operativa
                            </label>
                            <textarea name="description" id="editDescription" rows="3"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2.5 px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 shrink-0">
                        <button type="button" onclick="closeEdit()"
                            class="w-full sm:w-auto px-5 py-2.5 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer text-center">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 text-[13px] font-bold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition-colors shadow-sm cursor-pointer text-center">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Historial de Pasillos -->
    <div id="HistoryModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-5xl max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <!-- Encabezado -->
            <div class="px-5 py-4 sm:px-6 sm:py-4 bg-slate-900 flex justify-between items-center shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-3.5 min-w-0 pr-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/30 text-amber-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-base sm:text-[17px] font-bold text-white tracking-wide leading-tight">Historial de Pasillos</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono-das font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                {{ $aisles->count() }} {{ $aisles->count() === 1 ? 'registro' : 'registros' }}
                            </span>
                        </div>
                        <p class="text-[11.5px] sm:text-[12px] text-slate-400 mt-0.5 font-medium truncate sm:whitespace-normal">
                            Registro de auditoría de creación, capacidad y modificaciones de pasillos en bodega.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeHistory()"
                    class="text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-2 rounded-lg cursor-pointer shrink-0"
                    aria-label="Cerrar modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Barra de Búsqueda y Filtros del Historial -->
            <div class="p-3.5 sm:px-6 sm:py-3.5 bg-slate-50 border-b border-slate-200 shrink-0">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-1">Buscar en el Historial</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="historyAisleSearch" oninput="filtrarHistorialPasillos()"
                                placeholder="Buscar por nombre de pasillo, código o descripción..."
                                class="w-full pl-9 pr-3.5 py-2 bg-white border border-slate-300 rounded-lg text-[12.5px] text-slate-800 placeholder-slate-400 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-2xs">
                        </div>
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipo de Registro</label>
                        <select id="historyAisleTypeFilter" onchange="filtrarHistorialPasillos()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[12.5px] text-slate-700 font-medium outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Todos los registros</option>
                            <option value="modificado">Editados / Actualizados</option>
                            <option value="inicial">Registro Inicial</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="button" onclick="limpiarFiltroHistorialPasillos()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors text-[12.5px] font-bold shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Limpiar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla del Historial -->
            <div class="overflow-x-auto flex-1 min-h-[220px] max-h-[58vh] bg-white">
                <table class="w-full text-left text-[12.5px] sm:text-[13px] text-slate-600 whitespace-nowrap min-w-[760px]">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10.5px] sm:text-[11px] font-bold tracking-wider border-b border-slate-200 sticky top-0 z-10 shadow-2xs">
                        <tr>
                            <th class="px-5 py-3.5 w-20">ID</th>
                            <th class="px-5 py-3.5">Pasillo</th>
                            <th class="px-5 py-3.5 text-center">Estanterías</th>
                            <th class="px-5 py-3.5 text-center">Evento / Estado</th>
                            <th class="px-5 py-3.5">Descripción Operativa</th>
                            <th class="px-5 py-3.5">Creación</th>
                            <th class="px-5 py-3.5">Última Actualización</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($aisles as $a)
                            @php
                                $aColor = $a->color ?: '#3B82F6';
                                $aShelves = $a->shelves_count ?? 0;
                                $fueEditado = $a->created_at && $a->updated_at && $a->updated_at->gt($a->created_at->copy()->addSeconds(2));
                                $tipoEvento = $fueEditado ? 'modificado' : 'inicial';
                            @endphp
                            <tr class="history-aisle-row hover:bg-slate-50/80 transition-colors"
                                data-search="{{ strtolower('#' . $a->id . ' ' . $a->name . ' ' . ($a->description ?? '')) }}"
                                data-type="{{ $tipoEvento }}">
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11px] font-bold text-slate-600">
                                        #{{ $a->id }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full shrink-0 ring-2 ring-offset-1 ring-slate-200" style="background-color: {{ $aColor }};"></span>
                                        <span class="font-bold text-slate-800">{{ $a->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11.5px] font-bold {{ $aShelves > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $aShelves }} {{ $aShelves === 1 ? 'rack' : 'racks' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($fueEditado)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[11px] font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Modificado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate" title="{{ $a->description ?: 'Sin descripción' }}">
                                    {{ $a->description ?: 'Sin descripción' }}
                                </td>
                                <td class="px-5 py-3.5 font-mono-das text-[12px] text-slate-500">
                                    {{ optional($a->created_at)->format('d/m/Y H:i') ?? 'Registro inicial' }}
                                </td>
                                <td class="px-5 py-3.5 font-mono-das text-[12px] text-slate-700 font-semibold">
                                    {{ optional($a->updated_at)->format('d/m/Y H:i') ?? 'Registro inicial' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">Sin registros de historial</td>
                            </tr>
                        @endforelse
                        <tr id="historyAisleEmptyRow" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-9 h-9 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-[13px] font-semibold text-slate-500">No se encontraron pasillos con los filtros aplicados.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie del Modal -->
            <div class="px-5 py-3.5 sm:px-6 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-between items-center gap-3 shrink-0">
                <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">
                    Mostrando <strong id="historyAisleCount" class="text-teal-700 font-mono-das">{{ $aisles->count() }}</strong> de <strong class="font-mono-das text-slate-700">{{ $aisles->count() }}</strong> pasillos
                </span>
                <button type="button" onclick="closeHistory()"
                    class="w-full sm:w-auto px-6 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl transition-colors text-[13px] font-bold shadow-2xs cursor-pointer text-center">
                    Cerrar Historial
                </button>
            </div>
        </div>
    </div>

    <script>
        function filtrarPasillos() {
            const term = (document.getElementById('aisleSearchInput')?.value || '').toLowerCase().trim();
            document.querySelectorAll('.aisle-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                row.classList.toggle('hidden', term !== '' && !text.includes(term));
            });
        }

        function openCreate() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openShow(id, name, description, shelvesCount = 0, color = '#0D9488') {
            document.getElementById('showModal').classList.remove('hidden');
            document.getElementById('showName').textContent = name;
            document.getElementById('showDescription').textContent = description || 'Sin descripción';
            document.getElementById('showShelvesCount').textContent = shelvesCount + (shelvesCount === 1 ? ' estantería' : ' estanterías');
            const dot = document.getElementById('showColorDot');
            if (dot) dot.style.backgroundColor = color || '#0D9488';
        }

        function closeShow() {
            document.getElementById('showModal').classList.add('hidden');
        }

        function openEdit(id, name, description, color = '#3B82F6') {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description || '';
            const colorInput = document.getElementById('editColor');
            if (colorInput && color) colorInput.value = color;
            document.getElementById('editForm').action = '/aisle/' + id;
        }

        function closeEdit() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
            filtrarHistorialPasillos();
        }

        function closeHistory() {
            document.getElementById('HistoryModal').classList.add('hidden');
        }

        function filtrarHistorialPasillos() {
            const term = (document.getElementById('historyAisleSearch')?.value || '').toLowerCase().trim();
            const tipo = document.getElementById('historyAisleTypeFilter')?.value || '';
            const rows = document.querySelectorAll('.history-aisle-row');
            let visibles = 0;

            rows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                const rowType = row.getAttribute('data-type') || '';
                const matchText = term === '' || text.includes(term);
                const matchType = tipo === '' || rowType === tipo;
                const show = matchText && matchType;
                row.classList.toggle('hidden', !show);
                if (show) visibles++;
            });

            const countEl = document.getElementById('historyAisleCount');
            if (countEl) countEl.textContent = visibles;

            const emptyRow = document.getElementById('historyAisleEmptyRow');
            if (emptyRow && rows.length > 0) {
                emptyRow.classList.toggle('hidden', visibles > 0);
            }
        }

        function limpiarFiltroHistorialPasillos() {
            const s = document.getElementById('historyAisleSearch');
            const t = document.getElementById('historyAisleTypeFilter');
            if (s) s.value = '';
            if (t) t.value = '';
            filtrarHistorialPasillos();
        }

        function confirmCreateAisle(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('name')?.value.trim();
            if (!name) return;
            Swal.fire({
                title: '¿Crear Pasillo?',
                html: `¿Confirmas registrar el pasillo <strong>${name}</strong> en el sistema?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, crear pasillo',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmEditAisle(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('editName')?.value.trim();
            Swal.fire({
                title: '¿Guardar Cambios?',
                html: `¿Confirmas actualizar los datos del pasillo <strong>${name}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmDeleteAisle(id, name) {
            Swal.fire({
                title: '¿Eliminar Pasillo?',
                html: `¿Estás seguro de que deseas eliminar el pasillo <strong>${name}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción eliminará el pasillo del mapa de distribución.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-aisle-' + id).submit();
                }
            });
        }
    </script>
@endsection
