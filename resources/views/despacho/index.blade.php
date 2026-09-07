@extends('layouts.app')

@section('title', 'Despacho')
@section('header', 'Despacho de Pedidos')

@section('content')
    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Para hoy</p>
                <h3 class="text-2xl font-bold text-slate-800">45</h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-50 rounded-lg text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">En Preparación (Picking)</p>
                <h3 class="text-2xl font-bold text-slate-800">12</h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Despachados</p>
                <h3 class="text-2xl font-bold text-slate-800">28</h3>
            </div>
        </div>
    </div>

    <!-- Módulo de Emisión de Guía de Despacho -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        
        <!-- Módulo Controlados Switch (Identificador visual de módulo exclusivo) -->
        <div class="flex justify-end mb-4">
            <label class="flex items-center cursor-pointer bg-red-50 p-2 rounded-lg border border-red-200">
                <div class="relative">
                    <input type="checkbox" class="sr-only">
                    <div class="block bg-red-200 w-10 h-6 rounded-full"></div>
                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                </div>
                <div class="ml-3 text-red-700 font-bold text-sm">
                    Módulo Exclusivo Despacho Controlados
                </div>
            </label>
        </div>

        <!-- Encabezado de la Guía (Razón Social y Dirección) -->
        <div class="border-b border-slate-200 pb-4 mb-6 flex justify-between items-start">
            <div>
                <h2 class="text-lg font-bold text-slate-800 uppercase">Ilustre Municipalidad de Talcahuano</h2>
                <h3 class="text-md font-semibold text-slate-600">Droguería Comunal Talcahuano</h3>
                <p class="text-sm text-slate-500 mt-1">Dirección: Blanco Encalada 450, Talcahuano</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-slate-800">GUÍA DE DESPACHO</h2>
                <div class="mt-2 flex items-center justify-end gap-2">
                    <label class="text-sm font-bold text-slate-700">N° Correlativo:</label>
                    <input type="text" value="GD-2023-0899" readonly class="bg-slate-100 border border-slate-300 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2 text-center font-bold">
                </div>
            </div>
        </div>

        <form action="#" method="POST">
            <!-- Información General del Despacho -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Fecha -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Despacho</label>
                    <input type="date" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Condición de Liberación -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Condición del Despacho</label>
                    <select class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="liberada">Liberada para distribución</option>
                        <option value="rechazado">Rechazado</option>
                        <option value="cuarentena">En Cuarentena</option>
                    </select>
                </div>

                <!-- Establecimiento de Destino -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Establecimiento de Destino</label>
                    <select class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Seleccione un establecimiento...</option>
                        <option value="cesfam_paul">CESFAM Paulina Avendaño</option>
                        <option value="cesfam_sv">CESFAM San Vicente</option>
                        <option value="cesfam_la">CESFAM Leocán Portus</option>
                        <option value="cecosf_8m">CECOSF 8 de Mayo</option>
                    </select>
                </div>

                <!-- Dirección de Destino -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dirección del Establecimiento</label>
                    <input type="text" placeholder="Ej: Calle Los Robles 123, Sector Medio Camino" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Condiciones de Transporte y Almacenamiento -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Condiciones de Transporte y Almacenamiento</label>
                    <input type="text" placeholder="Ej: Mantener cadena de frío (2°C - 8°C), Proteger de la luz solar directa" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Detalle de Productos Despachados -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-md font-bold text-slate-800">Productos Despachados</h3>
                    <button type="button" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg border border-slate-200 hover:bg-slate-200 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Añadir Producto
                    </button>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-4 py-3">Producto (Fármaco, Conc. y Forma)</th>
                                <th scope="col" class="px-4 py-3">Lote</th>
                                <th scope="col" class="px-4 py-3">F. Vencimiento</th>
                                <th scope="col" class="px-4 py-3">Programa</th>
                                <th scope="col" class="px-4 py-3 text-center">Cantidad</th>
                                <th scope="col" class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Fila de ejemplo 1 -->
                            <tr class="bg-white border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-800">Paracetamol</div>
                                    <div class="text-xs text-slate-500">500 mg, Comprimido oral</div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" value="L-90210" class="w-24 bg-transparent border-slate-300 rounded text-sm p-1">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="date" value="2025-10-15" class="w-32 bg-transparent border-slate-300 rounded text-sm p-1">
                                </td>
                                <td class="px-4 py-3">
                                    <select class="w-32 bg-transparent border-slate-300 rounded text-sm p-1">
                                        <option>Cardiovascular</option>
                                        <option selected>Morbilidad</option>
                                        <option>Salud Mental</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" value="5000" class="w-20 bg-transparent border-slate-300 rounded text-sm p-1 text-center">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            <!-- Fila de ejemplo 2 (En blanco para llenar) -->
                            <tr class="bg-white hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <input type="text" placeholder="Ej: Amoxicilina 500mg Cápsula..." class="w-full bg-transparent border-slate-300 rounded text-sm p-1">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" placeholder="Lote..." class="w-24 bg-transparent border-slate-300 rounded text-sm p-1">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="date" class="w-32 bg-transparent border-slate-300 rounded text-sm p-1">
                                </td>
                                <td class="px-4 py-3">
                                    <select class="w-32 bg-transparent border-slate-300 rounded text-sm p-1">
                                        <option value="">Programa...</option>
                                        <option>IRA / ERA</option>
                                        <option>Morbilidad</option>
                                        <option>Salud Mental</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" placeholder="0" class="w-20 bg-transparent border-slate-300 rounded text-sm p-1 text-center">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 mt-8 border-t border-slate-200 pt-6">
                <button type="button" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Generar e Imprimir Guía
                </button>
            </div>
        </form>
    </div>
@endsection