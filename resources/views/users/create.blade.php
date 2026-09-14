@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Crear Nuevo Usuario</h1>
                <p class="text-sm text-slate-500 mt-1">Completa los datos para registrar a un nuevo miembro del equipo.</p>
            </div>

        </div>

        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8">
            <form action="#" method="POST">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                        <input type="text" id="name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Ej. Juan">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Apellido</label>
                        <input type="text" id="last_name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Ej. Pérez">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="ejemplo@drogueriadas.com">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña temporal</label>
                        <input type="password" id="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="button" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
