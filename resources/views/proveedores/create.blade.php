@extends('layouts.app')

@section('title', 'Nuevo Proveedor')
@section('header', 'Gestión de Proveedores')

@section('content')
    <!-- Navegación / Breadcrumb -->
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="{{ route('proveedores.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Proveedores 
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800">Nuevo Registro</span>
        </div>
    </div>

    <!-- Contenedor Principal -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Cabecera Compacta -->
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Ficha de Nuevo Proveedor</h2>
                <p class="text-xs text-slate-500 mt-0.5">Los campos marcados con (*) son obligatorios.</p>
            </div>
        </div>

        <form action="#" method="POST" class="divide-y divide-slate-100">
            @csrf

            <!-- SECCIÓN 1: Perfil de la Empresa -->
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">1. Perfil de la Empresa</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- RUT -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">RUT Proveedor <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="Ej: 76.123.456-7" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                    </div>

                    <!-- Razón Social -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Razón Social o Nombre Legal <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="Nombre completo de la empresa" required class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                    </div>

                    <!-- Giro -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Giro Comercial</label>
                        <input type="text" placeholder="Ej: Venta de insumos médicos" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                    </div>

                    <!-- Categoría -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Categoría Principal <span class="text-red-500">*</span></label>
                        <select required class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="farmacos">Fármacos y Medicamentos</option>
                            <option value="insumos">Insumos Clínicos</option>
                            <option value="equipamiento">Equipamiento Médico</option>
                            <option value="servicios">Servicios Generales</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Contacto y Ubicación -->
            <div class="p-6 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">2. Contacto y Ubicación</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Correo -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </div>
                            <input type="email" placeholder="ventas@empresa.cl" required class="w-full pl-9 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 pr-3 text-sm transition-colors">
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Principal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <input type="text" placeholder="+56 9 1234 5678" class="w-full pl-9 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 pr-3 text-sm transition-colors">
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dirección Física</label>
                        <input type="text" placeholder="Calle, número, comuna" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: Datos Bancarios -->
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">3. Datos Bancarios</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Banco -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Institución Bancaria</label>
                        <select class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                            <option value="" disabled selected>Seleccione banco...</option>
                            <option value="banco_estado">Banco Estado</option>
                            <option value="banco_chile">Banco de Chile</option>
                            <option value="banco_santander">Banco Santander</option>
                            <option value="bci">Banco BCI</option>
                            <option value="itau">Banco Itaú</option>
                        </select>
                    </div>

                    <!-- Tipo Cuenta -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Cuenta</label>
                        <select class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                            <option value="" disabled selected>Seleccione tipo...</option>
                            <option value="corriente">Cuenta Corriente</option>
                            <option value="vista">Cuenta Vista / RUT</option>
                            <option value="ahorro">Cuenta de Ahorro</option>
                        </select>
                    </div>

                    <!-- N° Cuenta -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">N° de Cuenta</label>
                        <input type="text" placeholder="Ej: 1234567890" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 text-sm transition-colors">
                    </div>
                </div>
            </div>

            <!-- Botones de Acción (Footer) -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('proveedores.index') }}" class="w-full sm:w-auto px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-100 transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800 flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Registrar Proveedor
                </button>
            </div>
        </form>
    </div>
@endsection