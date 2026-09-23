@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Encabezado con Botón de Notificación y Nuevo Usuario -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Usuarios</h1>
                <p class="text-sm text-slate-500 mt-1">Administra los accesos, roles y atiende las solicitudes del sistema.</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <!-- Botón de Solicitudes con Contador -->
                @php
                    $passwordRequests = $passwordRequests ?? [
                        ['id' => 1, 'email' => 'correo.ejemplo@drogueriadas.com', 'time' => 'Hace 10 min'],
                        ['id' => 2, 'email' => 'correo.ejemplo2@drogueriadas.com', 'time' => 'Hace 25 min'],
                    ];
                @endphp

                <button type="button" onclick="openRecoveryModal()"
                    class="relative bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-semibold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-all shadow-sm cursor-pointer">
                    @if(count($passwordRequests) > 0)
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                    @endif
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <span>Solicitudes</span>
                    <span class="bg-amber-200 text-amber-900 text-xs px-2 py-0.5 rounded-full font-bold ml-1">
                        {{ count($passwordRequests) }}
                    </span>
                </button>

                <button type="button" onclick="openCreate()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-all shadow-sm cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Usuario
                </button>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
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
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
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
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
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

    <!-- Modal Múltiples Solicitudes de Recuperación de Cuenta -->
    <div id="RecoveryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 sm:p-8 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Solicitudes de Recuperación</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Usuarios que han solicitado restablecer su contraseña de acceso</p>
                    </div>
                </div>
                <button type="button" onclick="closeRecoveryModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Listado de Solicitudes (Solo Vista) -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse ($passwordRequests as $req)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-amber-50/50 border border-amber-200/60 rounded-xl gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $req['email'] }}</p>
                            <span class="text-xs text-amber-700 font-medium">Solicitado: {{ $req['time'] }}</span>
                        </div>
                        <button type="button"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path>
                            </svg>
                            Generar nueva clave
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">No hay solicitudes pendientes de restablecimiento.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-6 mt-4 border-t border-slate-100">
                <button type="button" onclick="closeRecoveryModal()"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Crear Nuevo Usuario -->
    <div id="CreateModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 sm:p-8 w-full max-w-4xl">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Crear Nuevo Usuario</h1>
                    <p class="text-sm text-slate-500 mt-1">Completa los datos para registrar a un nuevo miembro del equipo.</p>
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
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="ejemplo@drogueriadas.com">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña temporal</label>
                        <input type="password" id="password"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <button type="button" onclick="closeCreate()"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Controladores JS -->
    <script>
        function openRecoveryModal() {
            document.getElementById('RecoveryModal').classList.remove('hidden');
        }

        function closeRecoveryModal() {
            document.getElementById('RecoveryModal').classList.add('hidden');
        }

        function openCreate() {
            document.getElementById('CreateModal').classList.remove('hidden');
        }

        function closeCreate() {
            document.getElementById('CreateModal').classList.add('hidden');
        }
    </script>
@endsection
