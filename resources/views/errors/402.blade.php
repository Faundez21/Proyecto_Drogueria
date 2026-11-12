@extends('layouts.app')
@section('content')
    <div class="w-full px-4 sm:px-6 pt-0 pb-6 space-y-5">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Límite Alcanzado</h1>
                <p class="text-[13px] font-medium text-amber-600 mt-0.5">Error 402 - Pago o renovación requerida</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center p-12 min-h-[55vh] text-center border-t-[4px] border-t-amber-500">
            
            <div class="w-20 h-20 bg-amber-50 rounded-3xl flex items-center justify-center border border-amber-100 mb-6 shadow-inner">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            
            <h2 class="text-xl font-black text-slate-800 mb-2">Renueve su suscripción</h2>
            <p class="text-[13px] font-medium text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                Ha superado el límite de operaciones o necesita actualizar su plan para seguir utilizando este módulo administrativo.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('dashboard.index') }}" 
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors shadow-sm">
                    Volver al inicio
                </a>
            </div>
        </div>
        
    </div>
@endsection