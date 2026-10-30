@extends('layouts.app')

@section('title', 'Mantenedor de Posiciones')
@section('subtitle', 'Administración de casillas y ubicaciones específicas dentro de los niveles de la bodega.')

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
            Nueva Posición
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
            <span class="font-medium text-slate-800">Posiciones</span>
        </div>

        <!-- Barra de Navegación Jerárquica de Bodega -->
        @include('distribution.partials.nav_tabs', ['activeTab' => 'position'])

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-[13px] font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Contenedor Principal Tabla Posiciones -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- Cabecera y filtros -->
            <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800">Directorio de Posiciones</h3>
                        <p class="text-[12px] text-slate-500">Total registradas: <strong class="text-slate-700 font-mono-das">{{ $positions->count() }}</strong></p>
                    </div>
                </div>

                <div class="w-full lg:max-w-3xl flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-end">
                    <select id="positionLevelFilter" onchange="filtrarPosiciones()"
                        class="w-full sm:w-64 md:w-72 px-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl text-[13px] text-slate-700 font-medium focus:bg-white focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 shadow-2xs transition-all cursor-pointer">
                        <option value="">Todos los niveles</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">
                                Nivel {{ $level->number }} · {{ $level->shelf->name ?? 'Sin estantería' }}
                            </option>
                        @endforeach
                    </select>

                    <div class="relative flex-1 min-w-[200px] sm:min-w-[260px]">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="positionSearchInput" oninput="filtrarPosiciones()" placeholder="Buscar posición o descripción..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-2xs transition-all">
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-[13px] text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 w-20">ID</th>
                            <th class="px-6 py-3.5">Posición / Casilla</th>
                            <th class="px-6 py-3.5">Nivel Asignado</th>
                            <th class="px-6 py-3.5">Estantería / Pasillo</th>
                            <th class="px-6 py-3.5">Descripción</th>
                            <th class="px-6 py-3.5 text-center w-36">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($positions as $position)
                            @php
                                $lvlNumber = $position->level->number ?? null;
                                $shelfName = optional($position->level)->shelf->name ?? 'Sin estantería';
                                $aisleName = optional(optional($position->level)->shelf)->aisle->name ?? null;
                            @endphp
                            <tr class="position-row hover:bg-slate-50/70 transition-colors"
                                data-level="{{ $position->level_id }}"
                                data-search="{{ strtolower($position->name . ' ' . ($position->description ?? '') . ' nivel ' . ($lvlNumber ?? '') . ' ' . $shelfName . ' ' . ($aisleName ?? '')) }}">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono-das text-[11.5px] font-semibold text-slate-600">
                                        #{{ $position->id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono-das font-bold text-[12.5px]">
                                        {{ $position->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 text-[12px] font-bold">
                                        {{ $lvlNumber ? 'Nivel ' . $lvlNumber : 'Sin nivel' }}
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
                                    {{ $position->description ?: 'Sin descripción' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1.5">
                                        <button type="button"
                                            onclick='openShow({{ $position->id }}, @json($position->name), @json($lvlNumber ?? "Sin nivel"), @json($position->description), @json($shelfName))'
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                                            title="Ver posición">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick='openEdit({{ $position->id }}, @json($position->name), {{ $position->level_id }}, @json($position->description))'
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded-lg transition-colors cursor-pointer"
                                            title="Editar posición">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <form id="delete-position-{{ $position->id }}" action="{{ route('position.destroy', $position->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeletePosition('{{ $position->id }}', '{{ addslashes($position->name) }}')"
                                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                title="Eliminar posición">
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
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <p class="text-[13.5px] font-bold text-slate-600">No hay posiciones registradas</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Crea la primera posición dentro de un nivel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear Posición -->
    <div id="createModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-4 sm:px-6 py-3.5 sm:py-4.5 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Nueva Posición</h2>
                        <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Registra una nueva posición dentro de un nivel.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreate()"
                    class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="createPositionForm" action="{{ route('position.store') }}" method="POST" onsubmit="confirmCreatePosition(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf
                <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label for="name" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Nombre / Código de Posición <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required placeholder="Ej: Posición A-01"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>

                    <div>
                        <label for="level_id" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Nivel Asignado <span class="text-rose-500">*</span>
                        </label>
                        <select name="level_id" id="level_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                            <option value="">Seleccione un nivel...</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}">
                                    Nivel {{ $level->number }} · {{ $level->shelf->name ?? 'Sin estantería' }}{{ $level->description ? ' (' . $level->description . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                            Descripción Operativa
                        </label>
                        <textarea name="description" id="description" rows="3" placeholder="Descripción de la posición..."
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
                        Guardar Posición
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Posición -->
    <div id="showModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-4 sm:px-6 py-3.5 sm:py-4.5 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div>
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-white leading-tight">Ficha de la Posición</h2>
                    <p class="text-slate-400 text-[11px] sm:text-[12px] mt-0.5">Información registrada de la casilla.</p>
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
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Posición</p>
                        <p id="showName" class="text-[13.5px] font-bold text-emerald-700 font-mono-das mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nivel</p>
                        <p id="showLevel" class="text-[13.5px] font-bold text-purple-700 mt-1"></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estantería</p>
                        <p id="showShelf" class="text-[13.5px] font-bold text-slate-800 mt-1 truncate"></p>
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

    <!-- Modal Editar Posición -->
    <div id="editModal"
        class="hidden fixed inset-0 z-[9999] !m-0 overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-5">
        <div class="flex min-h-full items-center justify-center py-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
                <div class="bg-slate-900 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[16px] font-bold text-white leading-tight">Editar Posición</h2>
                            <p class="text-slate-400 text-[12px] mt-0.5">Modifica la información de la posición.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEdit()"
                        class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg p-2 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="editForm" method="POST" onsubmit="confirmEditPosition(event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-5 sm:p-6 space-y-4.5 overflow-y-auto flex-1 overscroll-contain">
                        <div>
                            <label for="editName" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Nombre / Código de Posición <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="editName" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <div>
                            <label for="editLevel" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">
                                Nivel Asignado <span class="text-rose-500">*</span>
                            </label>
                            <select name="level_id" id="editLevel" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">
                                        Nivel {{ $level->number }} · {{ $level->shelf->name ?? 'Sin estantería' }}{{ $level->description ? ' (' . $level->description . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
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

    <!-- Modal de Historial -->
    <div id="HistoryModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden my-auto">
            <div class="bg-slate-900 px-6 py-4.5 flex justify-between items-center border-b border-slate-800 shrink-0">
                <div>
                    <h2 class="text-[16px] font-bold text-white">Historial de Posiciones</h2>
                    <p class="text-[12px] text-slate-400 mt-0.5">Registro de auditoría de creación y modificaciones de posiciones.</p>
                </div>
                <button type="button" onclick="closeHistory()"
                    class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 overflow-y-auto flex-1">
                <div class="overflow-x-auto max-h-96 border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-[13px] text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Posición</th>
                                <th class="px-4 py-3">Nivel / Estantería</th>
                                <th class="px-4 py-3">Descripción</th>
                                <th class="px-4 py-3">Última Actualización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($positions as $p)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-800 font-mono-das">{{ $p->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">
                                        Nivel {{ $p->level->number ?? '?' }} ({{ optional($p->level)->shelf->name ?? 'Sin estantería' }})
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $p->description ?: 'Sin descripción' }}</td>
                                    <td class="px-4 py-3 font-mono-das text-[12px] text-slate-500">{{ optional($p->updated_at)->format('d/m/Y H:i') ?? 'Registro inicial' }}</td>
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

            <div class="flex justify-end px-6 py-4 bg-slate-50 border-t border-slate-200 shrink-0">
                <button type="button" onclick="closeHistory()"
                    class="px-5 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-colors text-[13px] font-bold shadow-sm cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        function filtrarPosiciones() {
            const term = (document.getElementById('positionSearchInput')?.value || '').toLowerCase().trim();
            const levelId = document.getElementById('positionLevelFilter')?.value || '';

            document.querySelectorAll('.position-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                const rowLevel = row.getAttribute('data-level') || '';
                const matchText = term === '' || text.includes(term);
                const matchLevel = levelId === '' || rowLevel === levelId;
                row.classList.toggle('hidden', !(matchText && matchLevel));
            });
        }

        function openCreate() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openShow(id, name, level, description, shelfName = 'Sin estantería') {
            document.getElementById('showModal').classList.remove('hidden');
            document.getElementById('showName').textContent = name;
            document.getElementById('showLevel').textContent = 'Nivel ' + level;
            document.getElementById('showShelf').textContent = shelfName;
            document.getElementById('showDescription').textContent = description || 'Sin descripción';
        }

        function closeShow() {
            document.getElementById('showModal').classList.add('hidden');
        }

        function openEdit(id, name, levelId, description) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editName').value = name;
            document.getElementById('editLevel').value = levelId;
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editForm').action = '/position/' + id;
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

        function confirmCreatePosition(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('name')?.value.trim();
            if (!name) return;
            Swal.fire({
                title: '¿Crear Posición?',
                html: `¿Confirmas registrar la posición <strong>${name}</strong> en el nivel seleccionado?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, crear posición',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmEditPosition(e) {
            e.preventDefault();
            const form = e.target;
            const name = document.getElementById('editName')?.value.trim();
            Swal.fire({
                title: '¿Guardar Cambios?',
                html: `¿Confirmas actualizar los datos de la posición <strong>${name}</strong>?`,
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

        function confirmDeletePosition(id, name) {
            Swal.fire({
                title: '¿Eliminar Posición?',
                html: `¿Estás seguro de que deseas eliminar la posición <strong>${name}</strong>?<br><span class="text-[12px] text-slate-500 mt-1 block">Esta acción eliminará la posición física del mapa de distribución.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-position-' + id).submit();
                }
            });
        }
    </script>
@endsection
