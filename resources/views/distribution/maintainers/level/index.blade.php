@extends('layouts.app')

@section('title', 'Mantenedor de Niveles')
@section('subtitle', 'Administración de repisas y niveles verticales dentro de las estanterías de la bodega.')

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
            Nuevo Nivel
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
            <span class="font-medium text-slate-800">Niveles</span>
        </div>

        <!-- navegacion entre pasillos, estantes, niveles y posiciones -->
        @include('distribution.partials.nav_tabs', ['activeTab' => 'level'])

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-[13px] font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Contenedor principal tabla Niveles -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- Cabecera y filtros -->
            <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800">Directorio de Niveles</h3>
                        <p class="text-[12px] text-slate-500">Total registrados: <strong class="text-slate-700 font-mono-das">{{ $levels->count() }}</strong></p>
                    </div>
                </div>

                <div class="w-full lg:max-w-3xl flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-end">
                    <select id="levelShelfFilter" onchange="filtrarNiveles()"
                        class="w-full sm:w-64 md:w-72 px-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl text-[13px] text-slate-700 font-medium focus:bg-white focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition-all cursor-pointer">
                        <option value="">Todas las estanterías</option>
                        @foreach ($shelves as $shelf)
                            <option value="{{ $shelf->id }}">
                                {{ $shelf->name }}{{ optional($shelf->aisle)->name ? ' (' . $shelf->aisle->name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <div class="relative flex-1 min-w-[200px] sm:min-w-[260px]">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="levelSearchInput" oninput="filtrarNiveles()" placeholder="Buscar nivel o descripción..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-2xs transition-all">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-[13px] text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 w-20">ID</th>
                            <th class="px-6 py-3.5">Nivel</th>
                            <th class="px-6 py-3.5">Estantería / Pasillo</th>
                            <th class="px-6 py-3.5">Descripción</th>
                            <th class="px-6 py-3.5 text-center">Posiciones</th>
                            <th class="px-6 py-3.5 text-center w-36">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($levels as $level)
                            @php
                                $positionsCount = $level->positions_count ?? 0;
                                $shelfName = $level->shelf->name ?? 'Sin estantería';
                                $aisleName = optional($level->shelf)->aisle->name ?? null;
                            @endphp
                            <tr class="level-row hover:bg-slate-50/70 transition-colors"
                                data-shelf="{{ $level->shelf_id }}"
                                data-search="{{ strtolower('nivel ' . $level->number . ' ' . $shelfName . ' ' . ($aisleName ?? '') . ' ' . ($level->description ?? '')) }}">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11.5px] font-semibold text-slate-600">
                                        #{{ $level->id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 border border-purple-200 font-bold text-[12.5px]">
                                        Nivel {{ $level->number }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $shelfName }}</span>
                                        @if ($aisleName)
                                            <span class="text-[11px] text-slate-400 font-medium">{{ $aisleName }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $level->description ?: 'Sin descripción' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[12px] font-bold {{ $positionsCount > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $positionsCount }} {{ $positionsCount === 1 ? 'posición' : 'posiciones' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1.5">
                                        <button type="button"
                                            onclick='openShow({{ $level->id }}, {{ $level->number }}, @json($shelfName), @json($level->description), {{ $positionsCount }})'
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                                            title="Ver nivel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick='openEdit({{ $level->id }}, @json($level->number), {{ $level->shelf_id }}, @json($level->description))'
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors cursor-pointer"
                                            title="Editar nivel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form id="delete-level-{{ $level->id }}" action="{{ route('level.destroy', $level->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteLevel('{{ $level->id }}', 'Nivel {{ $level->number }}')"
                                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                title="Eliminar nivel">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2"></path>
                                    </svg>
                                    <p class="text-[13.5px] font-bold text-slate-600">No hay niveles registrados</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Cree el primer nivel dentro de una estantería.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal crear Nivel -->
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
                        <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Nuevo Nivel</h2>
                        <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Registre un nuevo nivel dentro de una estantería.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreate()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="createLevelForm" action="{{ route('level.store') }}" method="POST" onsubmit="confirmCreateLevel(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf
                <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="number" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Número de Nivel <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="number" id="number" min="1" required placeholder="Ej: 1"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 font-mono-das focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <div>
                            <label for="shelf_id" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Estantería <span class="text-rose-500">*</span>
                            </label>
                            <select name="shelf_id" id="shelf_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                <option value="">Seleccione estantería...</option>
                                @foreach ($shelves as $shelf)
                                    <option value="{{ $shelf->id }}">
                                        {{ $shelf->name }}{{ optional($shelf->aisle)->name ? ' (' . $shelf->aisle->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Descripción Operativa
                        </label>
                        <textarea name="description" id="description" rows="3" placeholder="Ej: Nivel inferior para cajas de alto volumen..."
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
                        Guardar Nivel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal ver Nivel -->
    <div id="showModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div>
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Ficha del Nivel</h2>
                    <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Detalles del nivel seleccionado.</p>
                </div>
                <button type="button" onclick="closeShow()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nivel</p>
                        <p id="showNumber" class="text-[14px] font-bold text-purple-700 font-mono-das mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estantería</p>
                        <p id="showShelfName" class="text-[13.5px] font-bold text-slate-800 mt-1 truncate"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Posiciones</p>
                        <p id="showPositionsCount" class="text-[13.5px] font-bold text-emerald-700 font-mono-das mt-1"></p>
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

    <!-- Modal editar Nivel -->
    <div id="editModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-5">
        <div class="flex min-h-full items-center justify-center py-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
                <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-400/30 text-purple-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[16px] font-bold text-white leading-tight">Editar Nivel</h2>
                            <p class="text-slate-400 text-[12px] mt-0.5">Modifica la información del nivel.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEdit()"
                        class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-2 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="editForm" method="POST" onsubmit="confirmEditLevel(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-5 sm:p-6 space-y-4.5 overflow-y-auto flex-1 overscroll-contain">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label for="editNumber" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                    Número de Nivel <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="number" id="editNumber" min="1" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 font-mono-das focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                            </div>

                            <div>
                                <label for="editShelfId" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                    Estantería <span class="text-rose-500">*</span>
                                </label>
                                <select name="shelf_id" id="editShelfId" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                    @foreach ($shelves as $shelf)
                                        <option value="{{ $shelf->id }}">
                                            {{ $shelf->name }}{{ optional($shelf->aisle)->name ? ' (' . $shelf->aisle->name . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
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

    <!-- modal del historial de niveles -->
    <div id="HistoryModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-800 flex justify-between items-center shrink-0 border-b border-slate-700">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-400/30 text-purple-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-wide leading-tight">Historial de Niveles</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono-das font-bold bg-purple-500/20 text-purple-300 border border-purple-400/30">
                                {{ $levels->count() }} {{ $levels->count() === 1 ? 'registro' : 'registros' }}
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 font-medium truncate sm:whitespace-normal">
                            Registro de auditoría de creación y modificaciones de niveles.
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

            <!-- buscador y filtros del historial -->
            <div class="p-4 sm:px-6 sm:py-4 bg-slate-50 border-b border-slate-200 shrink-0">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Buscar en el Historial</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="historyLevelSearch" oninput="filtrarHistorialNiveles()"
                                placeholder="Buscar por número de nivel, estantería o descripción..."
                                class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-[12.5px] text-slate-800 placeholder-slate-400 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm">
                        </div>
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filtrar por Estantería</label>
                        <select id="historyLevelShelfFilter" onchange="filtrarHistorialNiveles()"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[12.5px] text-slate-700 font-medium outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Todas las estanterías</option>
                            @foreach ($shelves as $shelf)
                                <option value="{{ $shelf->id }}">
                                    {{ $shelf->name }}{{ optional($shelf->aisle)->name ? ' (' . $shelf->aisle->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="button" onclick="limpiarFiltroHistorialNiveles()"
                            class="w-full py-2 px-3 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors text-[12.5px] font-bold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Limpiar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla del Historial -->
            <div class="overflow-x-auto flex-1 min-h-[180px] bg-white">
                <table class="w-full text-left text-[12.5px] sm:text-[13px] text-slate-600 whitespace-nowrap min-w-[720px] lg:min-w-full">
                    <thead class="bg-slate-100 text-slate-600 uppercase text-[10.5px] sm:text-[11px] font-bold tracking-wider border-b border-slate-200 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 sm:px-6 w-20">ID</th>
                            <th class="px-4 py-3 sm:px-6">Nivel</th>
                            <th class="px-4 py-3 sm:px-6">Estantería / Pasillo</th>
                            <th class="px-4 py-3 sm:px-6 text-center">Posiciones</th>
                            <th class="px-4 py-3 sm:px-6">Descripción Operativa</th>
                            <th class="px-4 py-3 sm:px-6 text-center">Evento / Estado</th>
                            <th class="px-4 py-3 sm:px-6">Creación</th>
                            <th class="px-4 py-3 sm:px-6">Última Actualización</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($levels as $l)
                            @php
                                $lPositions = $l->positions_count ?? 0;
                                $lShelfName = $l->shelf->name ?? 'Sin estantería';
                                $lAisleName = optional($l->shelf)->aisle->name ?? null;
                                $fueEditado = $l->created_at && $l->updated_at && $l->updated_at->gt($l->created_at->copy()->addSeconds(2));
                            @endphp
                            <tr class="history-level-row hover:bg-slate-50/80 transition-colors"
                                data-shelf="{{ $l->shelf_id }}"
                                data-search="{{ strtolower('#' . $l->id . ' nivel ' . $l->number . ' ' . $lShelfName . ' ' . ($lAisleName ?? '') . ' ' . ($l->description ?? '')) }}">
                                <td class="px-4 py-3 sm:px-6">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11px] font-bold text-slate-600">
                                        #{{ $l->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-purple-50 text-purple-800 border border-purple-200 font-bold text-[12px]">
                                        Nivel {{ $l->number }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $lShelfName }}</span>
                                        @if ($lAisleName)
                                            <span class="text-[11px] text-slate-400 font-medium">{{ $lAisleName }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11.5px] font-bold {{ $lPositions > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $lPositions }} pos.
                                    </span>
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-slate-600 max-w-xs truncate" title="{{ $l->description ?: 'Sin descripción' }}">
                                    {{ $l->description ?: 'Sin descripción' }}
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-center">
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
                                <td class="px-4 py-3 sm:px-6 font-mono-das text-[12px] text-slate-500">
                                    {{ optional($l->created_at)->format('d/m/Y H:i') ?? 'S/F' }}
                                </td>
                                <td class="px-4 py-3 sm:px-6 font-mono-das text-[12px] text-slate-700 font-semibold">
                                    {{ optional($l->updated_at)->format('d/m/Y H:i') ?? 'Registro inicial' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">Sin registros de historial</td>
                            </tr>
                        @endforelse
                        <tr id="historyLevelEmptyRow" class="hidden">
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-9 h-9 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-[13px] font-semibold text-slate-500">No se encontraron niveles con los filtros aplicados.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie del Modal -->
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-between items-center gap-3 shrink-0">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Mostrando <strong id="historyLevelCount" class="text-teal-700 font-mono-das">{{ $levels->count() }}</strong> de <strong class="font-mono-das text-slate-700">{{ $levels->count() }}</strong> niveles
                </span>
                <button type="button" onclick="closeHistory()"
                    class="w-full sm:w-auto px-6 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl transition-colors text-xs sm:text-sm font-bold shadow-sm cursor-pointer text-center">
                    Cerrar Historial
                </button>
            </div>
        </div>
    </div>

    <script>
        function filtrarNiveles() {
            const term = (document.getElementById('levelSearchInput')?.value || '').toLowerCase().trim();
            const shelfId = document.getElementById('levelShelfFilter')?.value || '';

            document.querySelectorAll('.level-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                const rowShelf = row.getAttribute('data-shelf') || '';
                const matchText = term === '' || text.includes(term);
                const matchShelf = shelfId === '' || rowShelf === shelfId;
                row.classList.toggle('hidden', !(matchText && matchShelf));
            });
        }

        function openCreate() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openShow(id, number, shelfName, description, positionsCount = 0) {
            document.getElementById('showModal').classList.remove('hidden');
            document.getElementById('showNumber').textContent = 'Nivel ' + number;
            document.getElementById('showShelfName').textContent = shelfName;
            document.getElementById('showDescription').textContent = description || 'Sin descripción';
            document.getElementById('showPositionsCount').textContent = positionsCount;
        }

        function closeShow() {
            document.getElementById('showModal').classList.add('hidden');
        }

        function openEdit(id, number, shelfId, description) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editNumber').value = number;
            document.getElementById('editShelfId').value = shelfId;
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editForm').action = '/level/' + id;
        }

        function closeEdit() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openHistory() {
            document.getElementById('HistoryModal').classList.remove('hidden');
            filtrarHistorialNiveles();
        }

        function closeHistory() {
            document.getElementById('HistoryModal').classList.add('hidden');
        }

        function filtrarHistorialNiveles() {
            const term = (document.getElementById('historyLevelSearch')?.value || '').toLowerCase().trim();
            const shelfId = document.getElementById('historyLevelShelfFilter')?.value || '';
            const rows = document.querySelectorAll('.history-level-row');
            let visibles = 0;

            rows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                const rowShelf = row.getAttribute('data-shelf') || '';
                const matchText = term === '' || text.includes(term);
                const matchShelf = shelfId === '' || rowShelf === shelfId;
                const show = matchText && matchShelf;
                row.classList.toggle('hidden', !show);
                if (show) visibles++;
            });

            const countEl = document.getElementById('historyLevelCount');
            if (countEl) countEl.textContent = visibles;

            const emptyRow = document.getElementById('historyLevelEmptyRow');
            if (emptyRow && rows.length > 0) {
                emptyRow.classList.toggle('hidden', visibles > 0);
            }
        }

        function limpiarFiltroHistorialNiveles() {
            const s = document.getElementById('historyLevelSearch');
            const sh = document.getElementById('historyLevelShelfFilter');
            if (s) s.value = '';
            if (sh) sh.value = '';
            filtrarHistorialNiveles();
        }

        function confirmCreateLevel(e) {
            e.preventDefault();
            const form = e.target;
            const number = document.getElementById('number')?.value.trim();
            if (!number) return;
            Swal.fire({
                title: '¿Crear Nivel?',
                html: `¿Confirma registrar el <strong>Nivel ${number}</strong> en la estantería seleccionada?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, crear nivel',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmEditLevel(e) {
            e.preventDefault();
            const form = e.target;
            const number = document.getElementById('editNumber')?.value.trim();
            Swal.fire({
                title: '¿Guardar Cambios?',
                html: `¿Confirma actualizar los datos del <strong>Nivel ${number}</strong>?`,
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

        function confirmDeleteLevel(id, name) {
            Swal.fire({
                title: '¿Eliminar Nivel?',
                html: `¿Está seguro de que desea eliminar el <strong>${name}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción eliminará el nivel y sus posiciones asociadas.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-level-' + id).submit();
                }
            });
        }
    </script>
@endsection
