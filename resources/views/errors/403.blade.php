@extends('layouts.app')

@section('title', 'Acceso Restringido')
@section('subtitle')
    <span class="text-rose-500 font-bold">Error 403 - Privilegios insuficientes</span>
@endsection

@section('actions')
    <a href="{{ route('dashboard.index') }}" 
        class="flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-900 text-white border border-transparent px-4 py-2 rounded-xl text-[13px] font-bold transition-all shadow-sm cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Ir al inicio
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center p-8 md:p-12 min-h-[65vh] text-center border-t-[4px] border-t-rose-500 relative overflow-hidden">
        
        <!-- fondo decorativo -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-32 bg-rose-50 rounded-full blur-3xl opacity-60 z-0"></div>

        <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center border border-rose-100 mb-6 shadow-inner relative z-10">
            <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        
        <h2 class="text-xl font-black text-slate-800 mb-2 relative z-10">No tiene autorización</h2>
        <p class="text-[13px] font-medium text-slate-500 max-w-md mx-auto mb-8 leading-relaxed relative z-10">
            Su rol actual de usuario no cuenta con los permisos necesarios para visualizar este módulo o ejecutar esta operación. Contacte a un administrador si cree que esto es un error.
        </p>
        
        <button type="button" onclick="window.history.back()" 
            class="inline-flex items-center justify-center gap-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-6 py-2.5 rounded-xl text-[13px] font-bold transition-all shadow-sm relative z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Regresar a la vista anterior
        </button>
    </div>
@endsection