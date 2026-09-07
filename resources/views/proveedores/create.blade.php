@extends('layouts.app')

@section('title', 'Nuevo Proveedor')
@section('header', 'Proveedores')

@section('content')
    <!-- Botón Volver -->
    <div class="mb-6">
        <a href="{{ route('proveedores.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver al listado
        </a>
    </div>

    <!-- Contenedor Principal del Formulario -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        
        <!-- Cabecera del Formulario -->
        <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2.5">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                Ficha de Nuevo Proveedor
            </h2>
            <p class="text-sm text-slate-500 mt-1 ml-11">Ingresa los datos fiscales, de contacto y bancarios del proveedor.</p>
        </div>

        <form action="#" class="p-6 sm:p-8 space-y-10">
            <!-- SECCIÓN 1: Datos de la Empresa -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">1</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Datos Fiscales y Empresa</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pl-0 sm:pl-11">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">RUT Proveedor *</label>
                        <input type="text" placeholder="Ej: 76.123.456-7" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Razón Social o Nombre *</label>
                        <input type="text" placeholder="Nombre legal de la empresa" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Giro Comercial</label>
                        <input type="text" placeholder="Ej: Venta al por mayor de productos farmacéuticos" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Categoría Principal</label>
                        <select class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccionar...</option>
                            <option value="farmacos">Fármacos y Medicamentos</option>
                            <option value="insumos">Insumos Clínicos</option>
                            <option value="equipamiento">Equipamiento Médico</option>
                            <option value="servicios">Servicios Generales</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- SECCIÓN 2: Contacto y Ubicación -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">2</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Información de Contacto</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 sm:pl-11">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correo Electrónico *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" placeholder="contacto@empresa.cl" class="w-full pl-10 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono Principal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <input type="text" placeholder="+56 9 1234 5678" class="w-full pl-10 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dirección Física</label>
                        <input type="text" placeholder="Calle, número, oficina/depto" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- SECCIÓN 3: Datos Bancarios -->
            <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center gap-3 mb-6">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">3</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Datos Bancarios para Transferencias</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pl-0 sm:pl-11">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Banco</label>
                        <select class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccione banco...</option>
                            <option value="banco_estado">Banco Estado</option>
                            <option value="banco_chile">Banco de Chile</option>
                            <option value="banco_santander">Banco Santander</option>
                            <option value="bci">BCI</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipo de Cuenta</label>
                        <select class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccione tipo...</option>
                            <option value="corriente">Cuenta Corriente</option>
                            <option value="vista">Cuenta Vista / RUT</option>
                            <option value="ahorro">Cuenta de Ahorro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">N° de Cuenta</label>
                        <input type="text" placeholder="Ej: 123456789" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('proveedores.index') }}" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl text-center text-sm font-bold hover:bg-slate-50 hover:text-slate-900 transition-all focus:ring-2 focus:ring-slate-200 order-2 sm:order-1">
                    Cancelar
                </a>
                <button type="button" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-200 flex items-center justify-center gap-2 transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 order-1 sm:order-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
@endsection