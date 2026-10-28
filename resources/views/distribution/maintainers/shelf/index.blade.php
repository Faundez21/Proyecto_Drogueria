@extends('layouts.app')

@section('title', 'Mantenedor de Estanterías')
@section('subtitle', 'Administración de racks, capacidad máxima de unidades y asignación por pasillo.')

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
            Nueva Estantería
        </button>
    </div>
@endsection

@section('content')
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
            <span class="font-medium text-slate-800">Estanterías</span>
        </div>

        <!-- Barra de Navegación Jerárquica de Bodega -->
        @include('distribution.partials.nav_tabs', ['activeTab' => 'shelf'])

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-[13px] font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Contenedor Principal Tabla Estanterías -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- Cabecera y filtros -->
            <div class="p-4 sm:px-6 border-b border-slate-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16M6 4v16M18 4v16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800">Directorio de Estanterías</h3>
                        <p class="text-[12px] text-slate-500">Total registradas: <strong class="text-slate-700 font-mono-das">{{ $shelves->count() }}</strong></p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <select id="shelfAisleFilter" onchange="filtrarEstanterias()"
                        class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-700 font-medium focus:bg-white focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                        <option value="">Todos los pasillos</option>
                        @foreach ($aisles as $aisle)
                            <option value="{{ $aisle->id }}">{{ $aisle->name }}</option>
                        @endforeach
                    </select>

                    <div class="relative w-full sm:w-64">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="shelfSearchInput" oninput="filtrarEstanterias()" placeholder="Buscar estantería..."
                            class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[750px] text-left text-[13px] text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 w-20">ID</th>
                            <th class="px-6 py-3.5">Estantería</th>
                            <th class="px-6 py-3.5">Pasillo Asignado</th>
                            <th class="px-6 py-3.5">Descripción</th>
                            <th class="px-6 py-3.5 text-center">Niveles</th>
                            <th class="px-6 py-3.5 text-center">Capacidad (uds)</th>
                            <th class="px-6 py-3.5 text-center w-36">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($shelves as $shelf)
                            @php
                                $aisleColor = optional($shelf->aisle)->color ?: '#3B82F6';
                                $levelsCount = $shelf->levels_count ?? 0;
                            @endphp
                            <tr class="shelf-row hover:bg-slate-50/70 transition-colors"
                                data-aisle="{{ $shelf->aisle_id }}"
                                data-search="{{ strtolower($shelf->name . ' ' . ($shelf->description ?? '') . ' ' . ($shelf->aisle->name ?? '')) }}">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11.5px] font-semibold text-slate-600">
                                        #{{ $shelf->id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 text-[13.5px]">{{ $shelf->name }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[12px] font-semibold text-slate-700">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $aisleColor }};"></span>
                                        {{ $shelf->aisle->name ?? 'Sin pasillo' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $shelf->description ?: 'Sin descripción' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $levelsCount > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $levelsCount }} {{ $levelsCount === 1 ? 'nivel' : 'niveles' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($shelf->capacidad > 0)
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[12px] font-bold bg-teal-50 text-teal-700 border border-teal-200 font-mono-das">
                                            {{ number_format($shelf->capacidad, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-[12px] text-slate-400 font-medium">Sin límite</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1.5">
                                        <button type="button"
                                            onclick='openShow({{ $shelf->id }}, @json($shelf->name), @json($shelf->aisle->name ?? "Sin pasillo"), @json($shelf->description), {{ $shelf->capacidad ?? 0 }}, {{ $levelsCount }})'
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                                            title="Ver estantería">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick='openEdit({{ $shelf->id }}, @json($shelf->name), {{ $shelf->aisle_id }}, @json($shelf->description), {{ $shelf->capacidad ?? 0 }})'
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors cursor-pointer"
                                            title="Editar estantería">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form id="delete-shelf-{{ $shelf->id }}" action="{{ route('shelf.destroy', $shelf->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteShelf('{{ $shelf->id }}', '{{ addslashes($shelf->name) }}')"
                                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
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
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16M6 4v16M18 4v16"></path>
                                    </svg>
                                    <p class="text-[13.5px] font-bold text-slate-600">No hay estanterías registradas</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Crea una nueva estantería asociada a un pasillo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear Estantería -->
    <div id="createModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="bg-slate-900 px-6 py-4.5 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[16px] font-bold text-white leading-tight">Nueva Estantería</h2>
                        <p class="text-slate-400 text-[12px] mt-0.5">Registra un nuevo rack dentro de un pasillo.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreate()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="createShelfForm" action="{{ route('shelf.store') }}" method="POST" onsubmit="confirmCreateShelf(event)">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Nombre de la Estantería <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required placeholder="Ej: Estantería A"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="aisle_id" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Pasillo Asignado <span class="text-rose-500">*</span>
                            </label>
                            <select name="aisle_id" id="aisle_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                @foreach ($aisles as $aisle)
                                    <option value="{{ $aisle->id }}">
                                        {{ $aisle->name }}{{ $aisle->description ? ' (' . $aisle->description . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="capacidad" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Capacidad Máxima (uds)
                            </label>
                            <input type="number" name="capacidad" id="capacidad" min="0" step="1" value="0"
                                placeholder="0 = sin límite"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 font-mono-das focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 -mt-2">Define la capacidad máxima para calcular el % de ocupación en el visor 3D (usa 0 si no tiene límite).</p>

                    <div>
                        <label for="description" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Descripción Operativa
                        </label>
                        <textarea name="description" id="description" rows="3" placeholder="Descripción de la estantería..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="button" onclick="closeCreate()"
                        class="px-4 py-2 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-[13px] font-bold text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors shadow-sm cursor-pointer">
                        Guardar Estantería
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Estantería -->
    <div id="showModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
            <div class="bg-slate-900 px-6 py-4.5 flex items-center justify-between border-b border-slate-800">
                <div>
                    <h2 class="text-[16px] font-bold text-white leading-tight">Ficha de la Estantería</h2>
                    <p class="text-slate-400 text-[12px] mt-0.5">Información registrada en la bodega.</p>
                </div>
                <button type="button" onclick="closeShow()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3.5">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estantería</p>
                        <p id="showName" class="text-[14px] font-bold text-slate-800 mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pasillo</p>
                        <p id="showAisle" class="text-[14px] font-bold text-teal-700 mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Capacidad</p>
                        <p id="showCapacidad" class="text-[13px] font-bold text-slate-800 font-mono-das mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Niveles</p>
                        <p id="showLevelsCount" class="text-[13px] font-bold text-purple-700 font-mono-das mt-1"></p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Descripción</p>
                    <p id="showDescription" class="text-[13px] text-slate-700"></p>
                </div>
            </div>

            <div class="flex justify-end px-6 py-4 bg-slate-50 border-t border-slate-200">
                <button type="button" onclick="closeShow()"
                    class="px-4 py-2 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Estantería -->
    <div id="editModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="bg-slate-900 px-6 py-4.5 flex items-center justify-between border-b border-slate-800">
                <div>
                    <h2 class="text-[16px] font-bold text-white leading-tight">Editar Estantería</h2>
                    <p class="text-slate-400 text-[12px] mt-0.5">Modifica la información de la estantería.</p>
                </div>
                <button type="button" onclick="closeEdit()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" onsubmit="confirmEditShelf(event)">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="editName" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Nombre de la Estantería <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="editName" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="editAisle" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Pasillo Asignado <span class="text-rose-500">*</span>
                            </label>
                            <select name="aisle_id" id="editAisle" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                @foreach ($aisles as $aisle)
                                    <option value="{{ $aisle->id }}">
                                        {{ $aisle->name }}{{ $aisle->description ? ' (' . $aisle->description . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="editCapacidad" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Capacidad Máxima (uds)
                            </label>
                            <input type="number" name="capacidad" id="editCapacidad" min="0" step="1" value="0"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 font-mono-das focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>
                    </div>

                    <div>
                        <label for="editDescription" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Descripción Operativa
                        </label>
                        <textarea name="description" id="editDescription" rows="3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button type="button" onclick="closeEdit()"
                        class="px-4 py-2 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-[13px] font-bold text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors shadow-sm cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Historial -->
    <div id="HistoryModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-3xl overflow-hidden">
            <div class="bg-slate-900 px-6 py-4.5 flex justify-between items-center border-b border-slate-800">
                <div>
                    <h2 class="text-[16px] font-bold text-white">Historial de Estanterías</h2>
                    <p class="text-[12px] text-slate-400 mt-0.5">Registro de auditoría de creación y modificaciones de estanterías.</p>
                </div>
                <button type="button" onclick="closeHistory()"
                    class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto max-h-96 border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Estantería</th>
                                <th class="px-4 py-3">Pasillo</th>
                                <th class="px-4 py-3 text-center">Capacidad</th>
                                <th class="px-4 py-3">Última Actualización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($shelves as $s)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $s->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $s->aisle->name ?? 'Sin pasillo' }}</td>
                                    <td class="px-4 py-3 text-center font-mono-das">{{ $s->capacidad > 0 ? number_format($s->capacidad, 0, ',', '.') . ' uds.' : 'Sin límite' }}</td>
                                    <td class="px-4 py-3 font-mono-das text-[12px] text-slate-500">{{ optional($s->updated_at)->format('d/m/Y H:i') ?? 'Registro inicial' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">Sin registros de historial</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end px-6 py-4 bg-slate-50 border-t border-slate-200">
                <button type="button" onclick="closeHistory()"
                    class="px-5 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        function filtrarEstanterias() {
            const term = (document.getElementById('shelfSearchInput')?.value || '').toLowerCase().trim();
            const aisleId = document.getElementById('shelfAisleFilter')?.value || '';

            document.querySelectorAll('.shelf-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                const rowAisle = row.getAttribute('data-aisle') || '';
                const matchText = term === '' || text.includes(term);
                const matchAisle = aisleId === '' || rowAisle === aisleId;
                row.classList.toggle('hidden', !(matchText && matchAisle));
            });
        }

        function openCreate() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openShow(id, name, aisle, description, capacidad = 0, levelsCount = 0) {
            document.getElementById('showModal').classList.remove('hidden');
            document.getElementById('showName').textContent = name;
            document.getElementById('showAisle').textContent = aisle;
            document.getElementById('showDescription').textContent = description || 'Sin descripción';
            document.getElementById('showCapacidad').textContent = capacidad > 0 ? Number(capacidad).toLocaleString('es-CL') + ' uds.' : 'Sin límite';
            document.getElementById('showLevelsCount').textContent = levelsCount + (levelsCount === 1 ? ' nivel' : ' niveles');
        }

        function closeShow() {
            document.getElementById('showModal').classList.add('hidden');
        }

        function openEdit(id, name, aisleId, description, capacidad = 0) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editName').value = name;
            document.getElementById('editAisle').value = aisleId;
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editCapacidad').value = capacidad || 0;
            document.getElementById('editForm').action = '/shelf/' + id;
        }

        function closeEdit() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
        }

        function closeHistory() {
            document.getElementById('HistoryModal').classList.add('hidden');
        }

        function confirmCreateShelf(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('name')?.value.trim();
            if (!name) return;
            Swal.fire({
                title: '¿Crear Estantería?',
                html: `¿Confirmas registrar la estantería <strong>${name}</strong> en el pasillo seleccionado?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, crear estantería',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmEditShelf(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('editName')?.value.trim();
            Swal.fire({
                title: '¿Guardar Cambios?',
                html: `¿Confirmas actualizar los datos de la estantería <strong>${name}</strong>?`,
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

        function confirmDeleteShelf(id, name) {
            Swal.fire({
                title: '¿Eliminar Estantería?',
                html: `¿Estás seguro de que deseas eliminar la estantería <strong>${name}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción eliminará la estantería y sus niveles asociados.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-shelf-' + id).submit();
                }
            });
        }
    </script>
@endsection
