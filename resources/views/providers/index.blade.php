@extends('layouts.app')

@section('title', 'Proveedores')
@section('subtitle', 'Directorio y gestión de proveedores registrados.')

@section('actions')
    <!-- Botón Principal: Registrar -->
    <button type="button" id="btn-new-provider"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm hover:shadow transition-all duration-200 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Registrar Proveedor
    </button>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">01</span>
            <span>Abastecimiento</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-800">Proveedores</span>
        </div>

        <!-- Tarjetas KPIs Uniformes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
            
            <!-- KPI: Total -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Total (Según filtros)</p>
                        <p class="font-mono-das text-[32px] font-bold text-slate-800 mt-1 leading-none">{{ $total ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- KPI: Activos -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Activos (Según filtros)</p>
                        <p class="font-mono-das text-[32px] font-bold text-slate-800 mt-1 leading-none">{{ $activos ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-4 pt-3 border-t border-slate-100"><span class="text-emerald-600 font-bold">{{ $porcentajeActivos ?? 0 }}%</span> del total filtrado</p>
            </div>

            <!-- KPI: Mes -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm sm:col-span-2 lg:col-span-1 transition-all hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[12.5px] font-medium text-slate-500">Agregados este mes</p>
                        <p class="font-mono-das text-[32px] font-bold text-slate-800 mt-1 leading-none">{{ $agregadosMes ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                </div>
                <p class="text-[11.5px] text-slate-400 mt-4 pt-3 border-t border-slate-100"><span class="font-medium text-slate-600">{{ $nombreMesActual ?? '' }}</span></p>
            </div>
        </div>

        <!-- Contenedor principal: Directorio y Tabla -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- Cabecera de la tabla y botón de filtros -->
            <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center">
                <h3 class="text-[15px] font-bold text-slate-800">Directorio de Proveedores</h3>
                <button type="button" id="toggle-filters-btn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filtros Avanzados
                </button>
            </div>

            <!-- Formulario de Filtros -->
            <form method="GET" action="{{ route('providers.index') }}" id="filters-panel" class="hidden bg-slate-50/50 border-b border-slate-200 p-5 shadow-inner">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    
                    <!-- Búsqueda Rápida con Autocomplete -->
                    <div class="xl:col-span-2">
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" list="provider-suggestions" placeholder="Nombre, RUT o Correo..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                        <datalist id="provider-suggestions">
                            @foreach($allProviders ?? [] as $prov)
                                <option value="{{ $prov->name }}">RUT: {{ $prov->rut }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Filtro: Categoría -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría</label>
                        <select name="category" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todas</option>
                            <option value="Fármacos y medicamentos" {{ request('category') == 'Fármacos y medicamentos' ? 'selected' : '' }}>Fármacos y medicamentos</option>
                            <option value="Insumos clínicos" {{ request('category') == 'Insumos clínicos' ? 'selected' : '' }}>Insumos clínicos</option>
                            <option value="Equipamiento mayor" {{ request('category') == 'Equipamiento mayor' ? 'selected' : '' }}>Equipamiento mayor</option>
                            <option value="Dispositivos médicos" {{ request('category') == 'Dispositivos médicos' ? 'selected' : '' }}>Dispositivos médicos</option>
                        </select>
                    </div>

                    <!-- Filtro: Estado -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Estado</label>
                        <select name="status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Filtro: Mostrar cantidad -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Mostrar</label>
                        <select name="per_page" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 registros</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 registros</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 registros</option>
                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 registros</option>
                        </select>
                    </div>

                    <!-- Filtro: Ordenar -->
                    <div>
                        <label class="block text-[11.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ordenar por</label>
                        <select name="sort" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                            <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>A-Z</option>
                            <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Z-A</option>
                        </select>
                    </div>
                </div>
                
                <!-- Botones del Filtro -->
                <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-200/60 pt-4">
                    <a href="{{ route('providers.index') }}" class="px-4 py-2 rounded-lg text-[12.5px] font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                        Limpiar Filtros
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg text-[12.5px] font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md transition-all">
                        Aplicar Filtros
                    </button>
                </div>
            </form>

            <!-- Tabla principal -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="text-[12px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 whitespace-nowrap">Proveedor</th>
                            <th class="px-6 py-4 whitespace-nowrap">Contacto</th>
                            <th class="px-6 py-4 whitespace-nowrap">Categoría</th>
                            <th class="px-6 py-4 text-center whitespace-nowrap">Estado</th>
                            <th class="px-6 py-4 text-center whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px]">
                        @forelse ($providers ?? [] as $provider)
                        <tr class="provider-row hover:bg-slate-50 transition-colors group" 
                            data-id="{{ $provider->id }}"
                            data-name="{{ $provider->name }}" 
                            data-rut="{{ $provider->rut }}" 
                            data-email="{{ $provider->email }}" 
                            data-phone="{{ $provider->phone }}" 
                            data-category="{{ $provider->category }}" 
                            data-status="{{ $provider->status }}">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-mono-das font-bold text-[13px] {{ $provider->status == 'activo' ? 'text-teal-700 bg-teal-50' : 'text-slate-500 bg-slate-100' }} shrink-0 border border-slate-200/60">
                                        {{ strtoupper(substr($provider->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 truncate">{{ $provider->name }}</p>
                                        <p class="font-mono-das text-[11.5px] text-slate-500 mt-0.5">{{ $provider->rut }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 font-medium">{{ $provider->email }}</span>
                                    <span class="font-mono-das text-[11.5px] text-slate-500">{{ $provider->phone ?: 'Sin teléfono' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-white text-slate-600 border border-slate-200 shadow-sm whitespace-nowrap">
                                    {{ $provider->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($provider->status == 'activo')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactivo
                                </span>
                                @endif
                            </td>
                            
                            <!-- Acciones Directas -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="js-view-detail p-2 text-teal-600 bg-teal-50 hover:bg-teal-100 rounded-lg transition-colors border border-transparent hover:border-teal-200" title="Ver Ficha">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button type="button" class="js-edit-provider p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-transparent hover:border-blue-200" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" class="js-toggle-status p-2 {{ $provider->status == 'activo' ? 'text-rose-600 bg-rose-50 hover:bg-rose-100 hover:border-rose-200' : 'text-slate-600 bg-slate-100 hover:bg-slate-200 hover:border-slate-300' }} rounded-lg transition-colors border border-transparent" title="{{ $provider->status == 'activo' ? 'Desactivar' : 'Reactivar' }}">
                                        @if($provider->status == 'activo')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-[14px] font-medium text-slate-600">No se encontraron proveedores.</p>
                                <p class="text-[13px] text-slate-400 mt-1">Intenta ajustando los filtros de búsqueda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- NUEVA PAGINACIÓN (Basada en la imagen adjunta) -->
            @if(isset($providers) && $providers->hasPages())
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 bg-white rounded-b-2xl">
                
                <div class="text-[13px] text-slate-500 text-center sm:text-left font-medium">
                    Mostrando del <span class="font-bold text-slate-800">{{ $providers->firstItem() ?? 0 }}</span> al <span class="font-bold text-slate-800">{{ $providers->lastItem() ?? 0 }}</span> de <span class="font-bold text-slate-800">{{ $providers->total() }}</span> proveedores
                </div>

                <div class="flex items-center gap-1.5">
                    <!-- Botón Anterior -->
                    @if ($providers->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Anterior</span>
                    @else
                        <a href="{{ $providers->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Anterior</a>
                    @endif

                    <!-- Números de Paginación Inteligentes -->
                    @php
                        $start = max($providers->currentPage() - 2, 1);
                        $end = min($providers->currentPage() + 2, $providers->lastPage());
                    @endphp

                    @if($start > 1)
                        <a href="{{ $providers->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">1</a>
                        @if($start > 2)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $providers->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-bold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">{{ $i }}</span>
                        @else
                            <a href="{{ $providers->url($i) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($end < $providers->lastPage())
                        @if($end < $providers->lastPage() - 1)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                        <a href="{{ $providers->url($providers->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-medium text-slate-600 hover:bg-slate-100 transition-colors">{{ $providers->lastPage() }}</a>
                    @endif

                    <!-- Botón Siguiente -->
                    @if ($providers->hasMorePages())
                        <a href="{{ $providers->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition-colors">Siguiente</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Siguiente</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal 1: Ficha técnica -->
    <div id="modal-detail" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm js-modal-backdrop transition-opacity" data-close="modal-detail"></div>
        <div class="relative w-full max-w-md rounded-2xl overflow-hidden opacity-0 scale-95 transition-all duration-200 bg-white shadow-2xl border border-slate-200" id="modal-detail-panel">
            <div class="flex items-start justify-between p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center gap-3.5">
                    <div id="detail-initials" class="w-12 h-12 rounded-xl flex items-center justify-center font-mono-das font-bold text-[14px] text-teal-700 bg-teal-50 border border-teal-100 shadow-sm shrink-0">PR</div>
                    <div>
                        <h3 id="detail-name" class="text-[16px] font-bold text-slate-800 leading-tight">Nombre</h3>
                        <p id="detail-rut" class="font-mono-das text-[12.5px] font-medium text-slate-500 mt-0.5">RUT</p>
                    </div>
                </div>
                <button type="button" class="js-modal-close p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" data-close="modal-detail">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Correo</p>
                        <p id="detail-email" class="text-[13.5px] font-semibold text-slate-700 break-all"></p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Teléfono</p>
                        <p id="detail-phone" class="font-mono-das text-[13.5px] font-semibold text-slate-700"></p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Categoría</p>
                        <p id="detail-category" class="text-[13.5px] font-semibold text-slate-700"></p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estado</p>
                        <span id="detail-status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[12px] font-bold mt-0.5"></span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end gap-3 p-5 border-t border-slate-100 bg-slate-50/50">
                <button type="button" class="js-modal-close px-5 py-2.5 rounded-xl text-[13px] font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 w-full sm:w-auto transition-colors" data-close="modal-detail">
                    Cerrar
                </button>
                <button type="button" id="modal-detail-edit-btn" class="px-5 py-2.5 rounded-xl text-[13px] font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md w-full sm:w-auto transition-all">
                    Editar Proveedor
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Formulario (Registrar/Editar) -->
    <div id="modal-edit" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm js-modal-backdrop transition-opacity" data-close="modal-edit"></div>
        <div class="relative w-full max-w-lg rounded-2xl overflow-hidden opacity-0 scale-95 transition-all duration-200 max-h-[90vh] flex flex-col bg-white shadow-2xl border border-slate-200" id="modal-edit-panel">
            
            <div class="flex items-start justify-between p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white shrink-0">
                <div>
                    <h3 id="modal-edit-title" class="text-[18px] font-bold text-slate-800">Proveedor</h3>
                    <p class="text-[12.5px] font-medium text-slate-500 mt-1">Completa la información oficial de la empresa.</p>
                </div>
                <button type="button" class="js-modal-close p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" data-close="modal-edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div id="form-errors" class="hidden px-6 pt-5 pb-0 text-[13px] font-medium text-red-600"></div>

            <form class="p-6 space-y-5 overflow-y-auto">
                <input type="hidden" id="provider-id" value="">
                
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Razón Social <span class="text-red-500">*</span></label>
                    <input type="text" id="provider-name" required placeholder="Ej: Laboratorios Andinos S.A." class="w-full px-4 py-2.5 rounded-xl text-[13.5px] border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all shadow-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">RUT <span class="text-red-500">*</span></label>
                        <input type="text" id="provider-rut" required placeholder="76.543.210-K" class="w-full px-4 py-2.5 rounded-xl text-[13.5px] font-mono-das border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Categoría <span class="text-red-500">*</span></label>
                        <select id="provider-category" required class="w-full px-4 py-2.5 rounded-xl text-[13.5px] border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all shadow-sm">
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Fármacos y medicamentos">Fármacos y medicamentos</option>
                            <option value="Insumos clínicos">Insumos clínicos</option>
                            <option value="Equipamiento mayor">Equipamiento mayor</option>
                            <option value="Dispositivos médicos">Dispositivos médicos</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Correo <span class="text-red-500">*</span></label>
                        <input type="email" id="provider-email" required placeholder="contacto@empresa.cl" class="w-full px-4 py-2.5 rounded-xl text-[13.5px] border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Teléfono</label>
                        <input type="text" id="provider-phone" placeholder="+56 9 1234 5678" class="w-full px-4 py-2.5 rounded-xl text-[13.5px] font-mono-das border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all shadow-sm">
                    </div>
                </div>

                <div id="status-container">
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Estado Inicial</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 rounded-xl p-3.5 border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition-colors">
                            <input type="radio" name="edit-status" value="activo" checked class="w-4 h-4 accent-teal-600">
                            <span class="text-[13.5px] font-bold text-slate-700">Activo</span>
                        </label>
                        <label class="flex items-center gap-3 rounded-xl p-3.5 border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition-colors">
                            <input type="radio" name="edit-status" value="inactivo" class="w-4 h-4 accent-rose-600">
                            <span class="text-[13.5px] font-bold text-slate-700">Inactivo</span>
                        </label>
                    </div>
                </div>
            </form>

            <div class="flex flex-col sm:flex-row justify-end gap-3 p-5 border-t border-slate-100 bg-slate-50/50 shrink-0">
                <button type="button" class="js-modal-close px-5 py-2.5 rounded-xl text-[13px] font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 w-full sm:w-auto transition-colors" data-close="modal-edit">
                    Cancelar
                </button>
                <button type="button" id="modal-edit-action" class="px-5 py-2.5 rounded-xl text-[13px] font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md w-full sm:w-auto transition-all">
                    Guardar Proveedor
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 3: Confirmación -->
    <div id="modal-confirm" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm js-modal-backdrop transition-opacity" data-close="modal-confirm"></div>
        <div class="relative w-full max-w-sm rounded-2xl overflow-hidden opacity-0 scale-95 transition-all duration-200 bg-white shadow-2xl border border-slate-200" id="modal-confirm-panel">
            <div class="p-8 text-center">
                <div id="confirm-icon-container" class="w-16 h-16 rounded-full flex items-center justify-center mb-5 mx-auto bg-slate-50 text-slate-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800">Confirmación</h3>
                <p id="confirm-message" class="text-[14px] text-slate-500 mt-2 leading-relaxed">
                    ¿Confirmas la acción?
                </p>
            </div>
            <div class="flex gap-3 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" class="js-modal-close flex-1 px-4 py-2.5 rounded-xl text-[13px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors" data-close="modal-confirm">
                    Cancelar
                </button>
                <button type="button" id="modal-confirm-action" class="flex-1 px-4 py-2.5 rounded-xl text-[13px] font-bold text-white shadow-md transition-all">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Script de Lógica -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = '{{ csrf_token() }}';
            let selectedRow = null;

            // --- Control del Panel de Filtros ---
            const btnToggleFilters = document.getElementById('toggle-filters-btn');
            const filtersPanel = document.getElementById('filters-panel');

            // Mantiene el panel abierto si hay filtros activos en la URL
            if(window.location.search.includes('category') || window.location.search.includes('status') || window.location.search.includes('month') || window.location.search.includes('sort') || window.location.search.includes('search') || window.location.search.includes('per_page')) {
                filtersPanel.classList.remove('hidden');
            }

            btnToggleFilters.addEventListener('click', () => {
                filtersPanel.classList.toggle('hidden');
            });

            // --- Control de Modales ---
            function openModal(id) {
                const modal = document.getElementById(id);
                const panel = document.getElementById(`${id}-panel`);
                if (!modal || !panel) return;
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                });
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                const panel = document.getElementById(`${id}-panel`);
                if (!modal || !panel) return;
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }

            document.querySelectorAll('[data-close]').forEach(element => {
                element.addEventListener('click', () => closeModal(element.dataset.close));
            });

            function getProviderFromRow(row) {
                return {
                    id: row.dataset.id || '',
                    name: row.dataset.name || '',
                    rut: row.dataset.rut || '',
                    email: row.dataset.email || '',
                    phone: row.dataset.phone || '',
                    category: row.dataset.category || '',
                    status: row.dataset.status || 'inactivo'
                };
            }

            // --- 1. Ver Ficha Técnica ---
            document.querySelectorAll('.js-view-detail').forEach(button => {
                button.addEventListener('click', (e) => {
                    const row = e.target.closest('.provider-row');
                    if (!row) return;
                    selectedRow = row;
                    const p = getProviderFromRow(row);
                    
                    document.getElementById('detail-name').textContent = p.name;
                    document.getElementById('detail-rut').textContent = `RUT: ${p.rut}`;
                    document.getElementById('detail-email').textContent = p.email || 'Sin correo registrado';
                    document.getElementById('detail-phone').textContent = p.phone || 'Sin teléfono registrado';
                    document.getElementById('detail-category').textContent = p.category;
                    document.getElementById('detail-initials').textContent = p.name.substring(0,2).toUpperCase();

                    const statusBadge = document.getElementById('detail-status');
                    statusBadge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${p.status === 'activo' ? 'bg-emerald-500' : 'bg-slate-400'}"></span> ${p.status === 'activo' ? 'Activo' : 'Inactivo'}`;
                    statusBadge.className = p.status === 'activo' 
                        ? 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5' 
                        : 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-slate-100 text-slate-600 border border-slate-200 mt-0.5';

                    openModal('modal-detail');
                });
            });

            // --- 2. Formulario de Guardado ---
            function openEditForm(row = null) {
                document.getElementById('form-errors').classList.add('hidden');
                
                if (row) { 
                    const p = getProviderFromRow(row);
                    document.getElementById('modal-edit-title').textContent = 'Editar Proveedor';
                    document.getElementById('provider-id').value = p.id;
                    document.getElementById('provider-name').value = p.name;
                    document.getElementById('provider-rut').value = p.rut;
                    document.getElementById('provider-email').value = p.email;
                    document.getElementById('provider-phone').value = p.phone || '+56 '; 
                    document.getElementById('provider-category').value = p.category;
                    document.querySelector(`input[name="edit-status"][value="${p.status}"]`).checked = true;
                    document.getElementById('status-container').classList.remove('hidden'); 
                } else { 
                    document.getElementById('modal-edit-title').textContent = 'Registrar Proveedor';
                    document.getElementById('provider-id').value = '';
                    document.getElementById('provider-name').value = '';
                    document.getElementById('provider-rut').value = '';
                    document.getElementById('provider-email').value = '';
                    document.getElementById('provider-phone').value = '+56 '; 
                    document.getElementById('provider-category').value = '';
                    document.querySelector('input[name="edit-status"][value="activo"]').checked = true;
                    document.getElementById('status-container').classList.add('hidden'); 
                }
                openModal('modal-edit');
            }

            document.getElementById('btn-new-provider').addEventListener('click', () => openEditForm());
            
            document.querySelectorAll('.js-edit-provider').forEach(btn => {
                btn.addEventListener('click', (e) => openEditForm(e.target.closest('.provider-row')));
            });
            
            document.getElementById('modal-detail-edit-btn').addEventListener('click', () => {
                closeModal('modal-detail');
                setTimeout(() => openEditForm(selectedRow), 200);
            });

            document.getElementById('modal-edit-action').addEventListener('click', async () => {
                const id = document.getElementById('provider-id').value;
                const data = {
                    name: document.getElementById('provider-name').value.trim(),
                    rut: document.getElementById('provider-rut').value.trim(),
                    category: document.getElementById('provider-category').value,
                    email: document.getElementById('provider-email').value.trim(),
                    phone: document.getElementById('provider-phone').value.trim(),
                    status: document.querySelector('input[name="edit-status"]:checked').value
                };

                if (data.phone === '+56' || data.phone === '+56 ') {
                    data.phone = '';
                }

                if (!data.name || !data.rut || !data.category || !data.email) {
                    const err = document.getElementById('form-errors');
                    err.innerHTML = 'Faltan campos obligatorios (*). Completa el formulario.';
                    err.classList.remove('hidden');
                    return;
                }

                // Ajustado a las rutas que me indicaste que configuraste: providers.store y providers.update
                const url = id ? `/proveedores/${id}` : '/proveedores';
                const method = id ? 'PUT' : 'POST';
                const btn = document.getElementById('modal-edit-action');
                
                btn.disabled = true;
                btn.textContent = 'Guardando...';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        window.location.reload(); 
                    } else {
                        const result = await response.json();
                        const err = document.getElementById('form-errors');
                        err.innerHTML = result.message || 'Error al guardar. Verifica que el RUT no esté duplicado.';
                        err.classList.remove('hidden');
                    }
                } catch (e) {
                    alert('Error de red. Revisa tu conexión.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Guardar Proveedor';
                }
            });

            // --- 3. Botón Activar/Desactivar ---
            document.querySelectorAll('.js-toggle-status').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const row = e.target.closest('.provider-row');
                    if (!row) return;
                    selectedRow = row;
                    const p = getProviderFromRow(row);
                    const isActivo = p.status === 'activo';

                    document.getElementById('confirm-message').innerHTML = `¿Estás seguro que deseas <b>${isActivo ? 'desactivar' : 'reactivar'}</b> a <br>${p.name}?`;
                    
                    const actionBtn = document.getElementById('modal-confirm-action');
                    actionBtn.textContent = isActivo ? 'Sí, Desactivar' : 'Sí, Reactivar';
                    actionBtn.className = isActivo 
                        ? 'flex-1 px-4 py-2.5 rounded-xl text-[13px] font-bold text-white shadow-md bg-rose-600 hover:bg-rose-700 transition-all'
                        : 'flex-1 px-4 py-2.5 rounded-xl text-[13px] font-bold text-white shadow-md bg-teal-600 hover:bg-teal-700 transition-all';
                    
                    const iconContainer = document.getElementById('confirm-icon-container');
                    iconContainer.className = isActivo
                        ? 'w-16 h-16 rounded-full flex items-center justify-center mb-5 mx-auto bg-rose-50 text-rose-600 shadow-inner'
                        : 'w-16 h-16 rounded-full flex items-center justify-center mb-5 mx-auto bg-teal-50 text-teal-600 shadow-inner';

                    openModal('modal-confirm');
                });
            });

            document.getElementById('modal-confirm-action').addEventListener('click', async () => {
                if (!selectedRow) return;
                const id = selectedRow.dataset.id;
                const btn = document.getElementById('modal-confirm-action');
                
                btn.disabled = true;
                btn.textContent = 'Procesando...';

                try {
                    const response = await fetch(`/proveedores/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Error al intentar cambiar el estado.');
                    }
                } catch (e) {
                    alert('Error de red.');
                } finally {
                    closeModal('modal-confirm');
                }
            });
        });
    </script>
@endsection