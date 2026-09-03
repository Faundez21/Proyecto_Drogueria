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

    <!-- Lista de Picking -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-800 mb-4">Órdenes en Cola de Picking</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 border border-slate-100 bg-slate-50 rounded-xl">
                <div>
                    <h4 class="font-bold text-slate-800">Clínica Biobío</h4>
                    <p class="text-xs text-slate-500 mt-1">Pedido #ORD-0092 • 14 Productos</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Iniciar Picking
                </button>
            </div>
            <div class="flex items-center justify-between p-4 border border-slate-100 bg-slate-50 rounded-xl">
                <div>
                    <h4 class="font-bold text-slate-800">Farmacia San Juan</h4>
                    <p class="text-xs text-slate-500 mt-1">Pedido #ORD-0093 • 5 Productos</p>
                </div>
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 transition-colors">
                    Asignar operario
                </button>
            </div>
        </div>
    </div>
@endsection