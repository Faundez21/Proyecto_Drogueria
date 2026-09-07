@extends('layouts.app')

@section('title', 'Catálogo de Productos')
@section('header', 'Gestión del Inventario')

@section('content')
    <!-- Creacion y Importacion          -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Importación Masiva -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 border-b border-slate-200 bg-emerald-50/50">
                    <h2 class="text-sm font-bold text-emerald-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Importación Masiva
                    </h2>
                </div>
                
                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-4">Carga múltiples productos usando una plantilla Excel.</p>
                    
                    <a href="#" class="mb-5 w-full flex items-center justify-center gap-2 px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Descargar Plantilla
                    </a>

                    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-emerald-500 hover:bg-emerald-50 transition-colors cursor-pointer group" onclick="document.getElementById('file-upload').click()">
                            <svg class="mx-auto h-10 w-10 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="mt-2 flex text-sm text-slate-600 justify-center">
                                <span class="font-medium text-emerald-600 hover:text-emerald-500">Subir archivo</span>
                                <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".csv, .xlsx, .xls">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">XLSX o CSV hasta 5MB</p>
                            
                            <div id="file-name-display" class="hidden mt-3 inline-flex items-center gap-2 px-3 py-1 bg-white border border-slate-200 rounded-md text-xs font-medium text-slate-700 shadow-sm">
                                <span id="file-name-text">archivo.xlsx</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors flex justify-center items-center gap-2">
                            Procesar Importación
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!--  Columna Derecha:  Ingreso Manual -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 border-b border-slate-200 bg-blue-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Añadir Nuevo Producto
                    </h2>
                </div>

                <form action="#" method="POST" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" required placeholder="Ej: Losartán 50mg" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">SKU / Código</label>
                            <input type="text" name="codigo" placeholder="Ej: PRD-001" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Unidad <span class="text-red-500">*</span></label>
                            <select name="unidad_medida" required class="w-full border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="Caja">Caja</option>
                                <option value="Unidad">Unidad</option>
                                <option value="Ampolla">Ampolla</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Área / Programa <span class="text-red-500">*</span></label>
                            <select name="tipo_producto" required class="w-full border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <optgroup label="FÁRMACOS">
                                    <option value="F-PALIATIVOS">CUIDADOS PALIATIVOS</option>
                                    <option value="F-CARDIOVASCULAR">P.M. CARDIOVASCULAR</option>
                                </optgroup>
                                <optgroup label="INSUMOS">
                                    <option value="I-URGENCIA">URGENCIA CIRUGIA</option>
                                </optgroup>
                                <optgroup label="EQUIPAMIENTO">
                                    <option value="E-COMUNAL">EQUIPAMIENTO COMUNAL</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-5 mt-5 border-t border-slate-200">
                        <button type="reset" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">Limpiar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Seccion 2: Listado de Catalogo -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Cabecera y Filtros de la Tabla -->
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-bold text-slate-800">Catálogo Base de Productos</h2>
            
            <div class="flex w-full md:w-auto gap-3">
                <div class="relative flex-1 md:w-64">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" placeholder="Buscar por nombre o SKU..." class="pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <select class="py-2 px-3 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las Categorías</option>
                    <option value="farmacos">Fármacos</option>
                    <option value="insumos">Insumos</option>
                    <option value="equipamiento">Equipamiento</option>
                </select>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">SKU / Cód.</th>
                        <th class="px-6 py-4">Nombre del Producto</th>
                        <th class="px-6 py-4">Programa / Área</th>
                        <th class="px-6 py-4 text-center">Unidad</th>
                        <th class="px-6 py-4 text-center">Stock Mín.</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Producto 1 -->
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">PRD-001</td>
                        <td class="px-6 py-4 font-bold text-slate-800">Paracetamol 500mg</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">Fármaco - P.M. Artrosis</span>
                        </td>
                        <td class="px-6 py-4 text-center">Caja</td>
                        <td class="px-6 py-4 text-center">50</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Activo</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors tooltip" title="Editar Producto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors tooltip" title="Desactivar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Producto 2 -->
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">INS-084</td>
                        <td class="px-6 py-4 font-bold text-slate-800">Jeringa 5ml s/aguja</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">Insumo - Cirugía Menor</span>
                        </td>
                        <td class="px-6 py-4 text-center">Unidad</td>
                        <td class="px-6 py-4 text-center">100</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Activo</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between bg-white">
            <span class="text-sm text-slate-500">Mostrando 1 a 2 de 450 productos base</span>
            <div class="flex gap-1">
                <button class="px-3 py-1 border border-slate-200 rounded text-sm text-slate-400 cursor-not-allowed" disabled>Anterior</button>
                <button class="px-3 py-1 border border-blue-500 rounded text-sm text-white bg-blue-600">1</button>
                <button class="px-3 py-1 border border-slate-200 rounded text-sm text-slate-600 hover:bg-slate-50">2</button>
                <button class="px-3 py-1 border border-slate-200 rounded text-sm text-slate-600 hover:bg-slate-50">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Script Drag & Drop UI (Importador) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-upload');
            const fileNameDisplay = document.getElementById('file-name-display');

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileNameDisplay.innerHTML = `<span class="text-emerald-600 font-bold">✓</span> ${this.files[0].name}`;
                    fileNameDisplay.classList.remove('hidden');
                    fileNameDisplay.classList.add('inline-flex');
                }
            });
        });
    </script>
@endsection