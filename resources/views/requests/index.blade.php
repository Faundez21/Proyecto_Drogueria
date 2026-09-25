@extends('layouts.app')


@section('content')
    <div class="w-full p-4 sm:p-6 space-y-6">

        <!-- encabezado acciones principales -->
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5">
            <div>
                <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 mb-1.5">
                    <span class="font-mono bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-semibold">03</span>
                    <span>Salida</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-bold text-teal-700">Solicitudes</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Solicitudes de productos</h1>
                <p class="text-slate-500 text-sm mt-1">Solicitudes de establecimientos para pedir un producto.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                <button type="button" onclick="openHistory()"
                    class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 hover:text-teal-700 transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Ver Historial
                </button>
                <button type="button" onclick="openImport()"
                    class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Carga Masiva
                </button>
                <button type="button" onclick="openDeposit()"
                    class="flex-1 sm:flex-none px-5 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-teal-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ingreso Manual
                </button>
            </div>
        </div>
    @endsection
