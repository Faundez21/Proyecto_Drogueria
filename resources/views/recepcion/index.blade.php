@extends('layouts.app')

@section('title', 'Recepción')
@section('header', 'Recepción de Mercadería')

@section('content')
    <!-- Acciones Rápidas -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div class="flex-1 w-full max-w-md relative">
            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Buscar orden de compra o proveedor..." class="pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full shadow-sm">
        </div>
        <button class="bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nueva Recepción
        </button>
    </div>

    <!-- Tabla de Recepciones Pendientes -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">Órdenes Pendientes de Ingreso</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">Orden C.</th>
                        <th class="px-6 py-4">Proveedor</th>
                        <th class="px-6 py-4">Fecha Esperada</th>
                        <th class="px-6 py-4">Bultos</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">OC-2026-001</td>
                        <td class="px-6 py-4">Laboratorios Andinos</td>
                        <td class="px-6 py-4">Hoy, 14:00 PM</td>
                        <td class="px-6 py-4">12 Cajas</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">En Tránsito</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 font-medium hover:underline">Recibir</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">OC-2026-002</td>
                        <td class="px-6 py-4">Pharma Corp S.A.</td>
                        <td class="px-6 py-4">Mañana, 09:00 AM</td>
                        <td class="px-6 py-4">5 Cajas</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-full">Programado</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 font-medium hover:underline">Revisar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection