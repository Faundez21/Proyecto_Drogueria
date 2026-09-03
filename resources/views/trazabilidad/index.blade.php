@extends('layouts.app')

@section('title', 'Trazabilidad')
@section('header', 'Trazabilidad y Rastreo')

@section('content')
    <!-- Buscador Principal de Lotes -->
    <div class="bg-blue-900 rounded-2xl p-8 mb-6 shadow-md relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-950/80 to-blue-900/40"></div>
        <div class="relative z-10 max-w-2xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-white mb-2">Rastreo de Lotes y Productos</h2>
            <p class="text-blue-200 text-sm mb-6">Ingresa el código de lote, número de serie o escanea el código QR para ver el historial completo.</p>
            
            <div class="flex gap-2">
                <input type="text" placeholder="Ej: LOT-12345" class="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-4 focus:ring-blue-500/50 shadow-sm text-slate-900 font-medium">
                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-400 transition-colors shadow-sm">
                    Rastrear
                </button>
            </div>
        </div>
    </div>

    <!-- Resultados del Rastreo -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalles del Lote -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">Información del Lote</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-slate-500 text-xs">Producto</p>
                    <p class="font-bold text-slate-800">Paracetamol 500mg (Caja x 20)</p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs">Lote</p>
                    <p class="font-medium text-slate-800">LOT-12345</p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs">Fabricante</p>
                    <p class="font-medium text-slate-800">Laboratorios Andinos S.A.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-slate-500 text-xs">Elaboración</p>
                        <p class="font-medium text-slate-800">15/01/2026</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Vencimiento</p>
                        <p class="font-medium text-red-600">30/06/2026</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Línea de tiempo -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-6">Historial de Movimientos</h3>
            
            <div class="relative border-l border-slate-200 ml-3 space-y-6">
                <div class="relative pl-6">
                    <span class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-slate-800">Despacho Completado</h4>
                    <p class="text-xs text-slate-500 mt-1">Enviado a Clínica Santa María (Orden #ORD-0091). Cantidad: 50 cajas.</p>
                    <span class="text-[10px] font-medium text-slate-400 mt-1 block">16 May 2026, 10:15 AM</span>
                </div>
                
                <div class="relative pl-6">
                    <span class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-blue-500 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-slate-800">Almacenamiento</h4>
                    <p class="text-xs text-slate-500 mt-1">Ubicado en Bodega Principal, Pasillo 3, Estante B-02.</p>
                    <span class="text-[10px] font-medium text-slate-400 mt-1 block">15 May 2026, 09:00 AM</span>
                </div>

                <div class="relative pl-6">
                    <span class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-slate-400 ring-4 ring-white"></span>
                    <h4 class="text-sm font-bold text-slate-800">Recepción de Proveedor</h4>
                    <p class="text-xs text-slate-500 mt-1">Ingreso al sistema mediante OC-2026-001. Aprobado por Control de Calidad.</p>
                    <span class="text-[10px] font-medium text-slate-400 mt-1 block">15 May 2026, 08:30 AM</span>
                </div>
            </div>
        </div>
    </div>
@endsection