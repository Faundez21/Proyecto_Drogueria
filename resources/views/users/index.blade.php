@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Usuarios</h1>
                <p class="text-sm text-slate-500 mt-1">Administra los accesos, roles y atiende las solicitudes del sistema.
                </p>
            </div>
            <!-- Corregido el error tipográfico "<<" -->
            <button type="button" onclick="openCreate()"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Usuario
            </button>
        </div>

        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl mb-8 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800">Solicitud de recuperación de cuenta</h3>
                        <p class="text-sm text-amber-700 mt-0.5">
                            <span class="font-semibold">correo.ejemplo@drogueriadas.com</span> ha solicitado restablecer su
                            contraseña.
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button"
                        class="flex-1 sm:flex-none px-4 py-2 bg-white border border-amber-300 text-amber-700 text-sm font-bold rounded-lg hover:bg-amber-100 transition-colors">Ignorar</button>
                    <button type="button"
                        class="flex-1 sm:flex-none px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition-colors">Generar
                        nueva clave</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-sm text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Datos del Usuario</th>
                            <th class="px-6 py-4 font-semibold">Rol Asignado</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">

                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors">

                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-base">{{ $user->name }}
                                        {{ $user->last_name }}</div>
                                    <div class="text-slate-500 mt-0.5">{{ $user->email }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($user->roles->count() > 0)
                                        @foreach ($user->roles as $rol)
                                            <span
                                                class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-full text-xs border border-indigo-100">
                                                {{ $rol->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span
                                            class="px-3 py-1 bg-slate-100 text-slate-700 font-bold rounded-full text-xs border border-slate-200">
                                            Sin Rol
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="flex items-center gap-2 text-emerald-600 font-medium">
                                        <span
                                            class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                        Activo
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        <button
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        @if ($user->id === auth()->id())
                                            <button class="p-2 text-slate-300 cursor-not-allowed rounded-lg"
                                                title="No puedes desactivar tu propia cuenta" disabled>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.36">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Desactivar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL (Añadido 'hidden', clases de posición fija y fondo oscuro) -->
    <div id="CreateModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 sm:p-8 w-full max-w-4xl">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Crear Nuevo Usuario</h1>
                    <p class="text-sm text-slate-500 mt-1">Completa los datos para registrar a un nuevo miembro del equipo.
                    </p>
                </div>
            </div>
            <form action="#" method="POST">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                        <input type="text" id="name"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="Ej. Juan">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Apellido</label>
                        <input type="text" id="last_name"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="Ej. Pérez">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo
                            Electrónico</label>
                        <input type="email" id="email"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="ejemplo@drogueriadas.com">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña
                            temporal</label>
                        <input type="password" id="password"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <!-- Cambiado el enlace por un botón que ejecute closeCreate() -->
                    <button type="button" onclick="closeCreate()"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreate() {
            document.getElementById('CreateModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('CreateModal').classList.add('hidden');
        }
    </script>
@endsection
