@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 sm:p-6 mt-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl text-gray-800 font-semibold">Reportes</h1>
            </div>
            <!-- Ambos botones para exportar -->
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button
                    class="flex justify-center items-center gap-2 bg-red-600 border border-transparent text-white px-4 py-2.5 sm:py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-sm shadow-sm w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h6m-4-8h.01">
                        </path>
                    </svg>
                    Exportar PDF
                </button>

                <button
                    class="flex justify-center items-center gap-2 bg-emerald-600 border border-transparent text-white px-4 py-2.5 sm:py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium text-sm shadow-sm w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exportar Excel
                </button>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-8">
            <!-- Barra de busqueda -->
            <div class="mb-5">
                <label for="busqueda" class="block text-sm font-medium text-slate-700 mb-1">Búsqueda</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="busqueda" placeholder="Buscar por ID o Nombre de Producto..."
                        class="pl-10 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <!-- div de los filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

                <div>
                    <label for="filtro_categoria" class="block text-xs font-medium text-slate-600 mb-1">Categoría</label>
                    <select id="filtro_categoria"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-all bg-white">
                        <option value="">Todas las categorías</option>
                        <option value="farmacos">Fármacos</option>
                        <option value="insumos">Insumos</option>
                    </select>
                </div>

                <div>
                    <label for="filtro_pasillo" class="block text-xs font-medium text-slate-600 mb-1">Pasillo</label>
                    <select id="filtro_pasillo"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-all bg-white">
                        <option value="">Todos los pasillos</option>
                        <option value="A">Pasillo A</option>
                        <option value="B">Pasillo B</option>
                        <option value="C">Pasillo C</option>
                    </select>
                </div>

                <div>
                    <label for="filtro_estante" class="block text-xs font-medium text-slate-600 mb-1">Estante</label>
                    <select id="filtro_estante"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-all bg-white">
                        <option value="">Todos los estantes</option>
                        <option value="1">Estante 1</option>
                        <option value="2">Estante 2</option>
                        <option value="3">Estante 3</option>
                    </select>
                </div>

                <div>
                    <label for="filtro_stock" class="block text-xs font-medium text-slate-600 mb-1">Nivel de Stock</label>
                    <select id="filtro_stock"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-all bg-white">
                        <option value="">Cualquier cantidad</option>
                        <option value="disponible">Disponible (Normal)</option>
                        <option value="pocas">Pocas unidades (Crítico)</option>
                        <option value="agotado">Agotado (0 unidades)</option>
                    </select>
                </div>

                <div>
                    <label for="filtro_cuarentena" class="block text-xs font-medium text-slate-600 mb-1">Estado /
                        Cuarentena</label>
                    <select id="filtro_cuarentena"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-all bg-white">
                        <option value="">Cualquier estado</option>
                        <option value="no">Libre (No)</option>
                        <option value="si">Retenido (Sí)</option>
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-4 flex justify-end gap-3 mt-4 pt-4 border-t border-slate-100 w-full">
                    <button type="reset"
                        class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium shadow-sm">
                        Limpiar Filtros
                    </button>
                    <button type="button"
                        class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors font-medium text-sm flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Aplicar Filtros
                    </button>
                </div>

            </div>
        </div>
        <!-- Tabla (misma q la de inventario)-->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-[1100px] w-full text-left text-sm font-light text-gray-800 whitespace-nowrap">

                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-4">ID</th>
                            <th scope="col" class="px-4 py-4">Producto</th>
                            <th scope="col" class="px-4 py-4">Categoría</th>
                            <th scope="col" class="px-4 py-4">Pasillo</th>
                            <th scope="col" class="px-4 py-4">Estante</th>
                            <th scope="col" class="px-4 py-4 text-center">Cuarentena</th>
                            <th scope="col" class="px-4 py-4">Stock Total</th>
                            <th scope="col" class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="border-b border-neutral-200 bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-600">1001</td>
                            <td class="px-4 py-3 font-bold text-slate-800">PARACETAMOL 500MG</td>
                            <td class="px-4 py-3 text-gray-600">Fármacos</td>
                            <td class="px-4 py-3 text-gray-600 font-medium">Pasillo A </td>
                            <td class="px-4 py-3 text-gray-600">Estante 1</td>
                            <td class="px-4 py-3 text-center text-gray-500 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    850 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-neutral-200 bg-red-50/40 hover:bg-red-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-red-800">1042</td>
                            <td class="px-4 py-3 font-bold text-red-900">ERITROMICINA 500 MG CM REC.</td>
                            <td class="px-4 py-3 text-red-800">Fármacos</td>
                            <td class="px-4 py-3 text-red-800 font-medium">Pasillo A</td>
                            <td class="px-4 py-3 text-red-800">Estante 3</td>
                            <td class="px-4 py-3 text-center text-red-800 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    15 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-neutral-200 bg-white hover:bg-gray-50 transition-colors opacity-75">
                            <td class="px-4 py-3 font-medium text-gray-500">2055</td>
                            <td class="px-4 py-3 font-bold text-gray-600">AGUJA 21 G X 1,5 DESECHABLE</td>
                            <td class="px-4 py-3 text-gray-500">Insumos</td>
                            <td class="px-4 py-3 text-gray-500 font-medium">Pasillo B</td>
                            <td class="px-4 py-3 text-gray-500">Estante 2</td>
                            <td class="px-4 py-3 text-center text-gray-500 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300">
                                    0 Unidades (Agotado)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <!-- Botón Desactivar -->
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-neutral-200 bg-orange-50 hover:bg-orange-100 transition-colors">
                            <td class="px-4 py-3 font-medium text-orange-800">1088</td>
                            <td class="px-4 py-3 font-bold text-orange-900">IBUPROFENO 400MG</td>
                            <td class="px-4 py-3 text-orange-800">Fármacos</td>
                            <td class="px-4 py-3 text-orange-800 font-medium">Pasillo C</td>
                            <td class="px-4 py-3 text-orange-800">Estante 1</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-200 text-orange-800 border border-orange-300">
                                    Sí
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-gray-800 border border-gray-300">
                                    320 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-1">
                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Desactivar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div
                class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-b-2xl">
                <span class="text-sm text-slate-500">Mostrando del <span class="font-medium text-slate-900">1</span> al
                    <span class="font-medium text-slate-900">10</span> de <span
                        class="font-medium text-slate-900">142</span> productos</span>
                <div class="flex items-center gap-1">
                    <button
                        class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed bg-slate-50">Anterior</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 font-bold border border-blue-100">1</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">2</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">3</button>
                    <span class="px-1 text-slate-400">...</span>
                    <button
                        class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Siguiente</button>
                </div>
            </div>
        </div>
    @endsection
