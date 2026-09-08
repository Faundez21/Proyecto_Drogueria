@extends('layouts.app')

    @section('content')
    <div class="max-w-full sm:max-w-[95%] mx-auto p-4 sm:p-6 mt-2 sm:mt-4">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
                        <!--importe-->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Importar Carga Masiva</h2>
                    <p class="text-sm text-slate-500 mt-1 mb-6">Suba un archivo Excel (.xlsx o .csv) para actualizar el inventario completo.</p>
                </div>

                <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg p-6 flex-grow flex flex-col justify-center items-center">
                    <form action="/inventario/subir-excel" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3 w-full justify-center">
                        @csrf
                        <input type="file" id="excel" name="excel" class="hidden" accept=".xlsx, .xls, .csv">

                        <label for="excel" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 px-4 py-2.5 rounded-lg cursor-pointer inline-flex items-center gap-2 text-sm font-medium transition-colors shadow-sm w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            Seleccionar archivo
                        </label>

                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium shadow-sm w-full sm:w-auto">
                            Subir Excel
                        </button>
                    </form>
                </div>
            </div>
                    <!--ingreso manual-->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Ingresar Producto Individual</h2>
                    <p class="text-sm text-slate-500 mt-1 mb-6">Registra un nuevo producto en el catálogo maestro del sistema.</p>
                </div>

                <form action="/inventario/guardar" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sku" class="block text-xs font-medium text-slate-700 mb-1">ID</label>
                            <input type="text" id="sku" name="sku" placeholder="Ej. COD-1005" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required />
                        </div>

                        <div>
                            <label for="nombre" class="block text-xs font-medium text-slate-700 mb-1">Nombre del Producto</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej. Paracetamol" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required />
                        </div>

                        <div>
                            <label for="categoria" class="block text-xs font-medium text-slate-700 mb-1">Categoría</label>
                            <select id="categoria" name="categoria" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                <option value="" disabled selected>Seleccionar...</option>
                                <option value="farmacos">Fármacos</option>
                                <option value="insumos">Insumos Clínicos</option>
                                <option value="equipamiento">Equipamiento Médico</option>
                            </select>
                        </div>

                        <div>
                            <label for="stock" class="block text-xs font-medium text-slate-700 mb-1">Stock Inicial</label>
                            <input type="number" id="stock" name="stock" placeholder="0" min="0" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required />
                        </div>

                        <div>
                            <label for="pasillo" class="block text-xs font-medium text-slate-700 mb-1">Pasillo</label>
                            <input type="text" id="pasillo" name="pasillo" placeholder="Ej. Pasillo 1" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label for="estante" class="block text-xs font-medium text-slate-700 mb-1">Estante</label>
                            <input type="text" id="estante" name="estante" placeholder="Ej. Estante 3" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium shadow-sm w-full sm:w-auto">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>

        </div>
        <!--Parte del inventario-->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl text-gray-800 font-semibold">Inventario Maestro</h1>
            </div>
            <!--Boton de reporte-->
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('reportes.index') }}"
                    class="flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Reporte
                </a>
            </div>
        </div>
        <!--Tabla inventario-->
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
                            <td class="px-4 py-3 text-gray-600 font-medium">Pasillo A   </td>
                            <td class="px-4 py-3 text-gray-600">Estante 1</td>
                            <td class="px-4 py-3 text-center text-gray-500 font-medium">No</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    850 Unidades
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
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
                            <td class="px-4 py-3 text-center">
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
                            <td class="px-4 py-3 text-center">
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
                            <td class="px-4 py-3 text-center">
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
            <div class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-slate-500">Mostrando del <span class="font-medium text-slate-900">1</span> al <span class="font-medium text-slate-900">10</span> de <span class="font-medium text-slate-900">142</span> proveedores</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed bg-slate-50">Anterior</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 font-bold border border-blue-100">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">3</button>
                <span class="px-1 text-slate-400">...</span>
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

        </div>
    </div>
@endsection
