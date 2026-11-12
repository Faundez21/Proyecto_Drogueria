@extends('layouts.app')

@section('title', 'Error 404')
@section('subtitle', 'Página, lote o recurso no encontrado en el sistema.')

@section('actions')
    <button type="button" onclick="window.history.back()"
        class="flex items-center justify-center gap-2 bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-all shadow-sm cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Volver atrás
    </button>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center p-8 md:p-12 min-h-[65vh] text-center">

        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center border border-slate-100 mb-6 shadow-inner">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-xl font-black text-slate-800 mb-2">No encontramos lo que buscas</h2>
        <p class="text-[13px] font-medium text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
            El enlace podría estar roto, el producto/lote fue eliminado del inventario o ha ingresado a una dirección que no existe en la plataforma de la Droguería.
        </p>

        <a href="{{ route('dashboard.index') }}"
            class="inline-flex items-center justify-center gap-2 bg-[#0D9488] hover:bg-[#0F766E] text-white px-6 py-2.5 rounded-xl text-[13px] font-bold transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Ir al Dashboard principal
        </a>
    </div>
@endsection
