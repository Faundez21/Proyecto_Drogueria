@extends('layouts.app')

@section('title', 'Recepción')
@section('header', 'Recepción de Mercadería')

@section('content')
    <!-- Acciones Rápidas y Buscador -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div class="flex-1 w-full max-w-lg relative group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" placeholder="Buscar orden de compra o proveedor..." class="block w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-slate-700 placeholder-slate-400">
        </div>
        <button class="bg-white text-slate-700 border border-slate-200 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-slate-50 hover:border-slate-300 flex items-center gap-2 shadow-sm transition-all focus:ring-2 focus:ring-slate-200">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Ver Historial
        </button>
    </div>

    <!-- Contenedor Principal del Formulario -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        
        <!-- Cabecera del Formulario -->
        <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2.5">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                Nueva Recepción de Mercadería
            </h2>
            <div class="flex items-center gap-2 bg-blue-50/50 border border-blue-100 px-4 py-1.5 rounded-full">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-blue-700">Auto: #REC-{{ date('Ymd-His') }}</span>
            </div>
        </div>

        <form action="#" method="POST" class="p-6 sm:p-8 space-y-10">
            @csrf

            <!-- SECCIÓN 1: Datos del Documento -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">1</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Datos del Documento y Origen</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 pl-0 sm:pl-11">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">N° Correlativo</label>
                        <input type="text" name="correlativo" placeholder="Ej: REC-10293" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Fecha de Recepción</label>
                        <div class="relative">
                            <input type="date" name="fecha_recepcion" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipo de Documento</label>
                        <select name="tipo_documento" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccione tipo...</option>
                            <option value="Factura">Factura</option>
                            <option value="Guia">Guía de Despacho</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">N° del Documento</label>
                        <input type="text" name="numero_documento" placeholder="Ej: 847593" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Orden de Compra</label>
                        <input type="text" name="orden_compra" placeholder="Ej: 1234-56-SE26" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Origen</label>
                        <select name="origen" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccione origen...</option>
                            <option value="CENABAST">CENABAST</option>
                            <option value="COMPRA DIRECTA DAS">COMPRA DIRECTA DAS</option>
                            <option value="SERVICIO DE SALUD TALCAHUANO">SERVICIO DE SALUD TALCAHUANO (SSTHNO)</option>
                            <option value="Traspaso">Traspaso</option>
                            <option value="Donacion">Donación</option>
                            <option value="Prestamo">Préstamo</option>
                            <option value="Canje">Canje</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- SECCIÓN 2: Datos del Proveedor -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">2</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Datos del Proveedor</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 sm:pl-11">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">RUT Proveedor</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            </div>
                            <input type="text" name="rut_proveedor" placeholder="Ej: 76.543.210-K" class="w-full pl-10 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Razón Social / Nombre Proveedor</label>
                        <input type="text" name="proveedor" placeholder="Ej: Laboratorios Andinos S.A." class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: Datos del Producto -->
            <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center gap-3 mb-6">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-sm shadow-md shadow-blue-200">3</span>
                    <h3 class="text-base font-bold text-slate-800 tracking-tight">Detalle del Producto a Ingresar</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 pl-0 sm:pl-11">
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Producto</label>
                        <select name="producto_id" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Buscar producto en catálogo...</option>
                            <option value="1">Paracetamol 500mg Comprimidos</option>
                            <option value="2">Jeringa 5ml</option>
                            <option value="3">Desfibrilador Externo Automático</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Categoría (Programa/Área)</label>
                        <select name="tipo_producto" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                            <option value="" disabled selected>Seleccione programa...</option>
                            <optgroup label="FÁRMACOS" class="font-bold text-slate-700">
                                <option value="F-PALIATIVOS" class="font-normal">CUIDADOS PALIATIVOS</option>
                                <option value="F-FOFAR" class="font-normal">FOFAR</option>
                                <option value="F-PARO" class="font-normal">CARRO DE PARO</option>
                                <option value="F-UAPO" class="font-normal">UAPO</option>
                                <!-- Más opciones omitidas por brevedad -->
                            </optgroup>
                            <optgroup label="INSUMO" class="font-bold text-slate-700">
                                <option value="I-PARO" class="font-normal">CARRO DE PARO</option>
                                <option value="I-CIRUGIA" class="font-normal">CIRUGIA MENOR</option>
                                <option value="I-PALIATIVOS" class="font-normal">CUIDADOS PALIATIVOS</option>
                            </optgroup>
                            <optgroup label="EQUIPAMIENTO" class="font-bold text-slate-700">
                                <option value="E-COMUNAL" class="font-normal">EQUIPAMIENTO COMUNAL</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lote</label>
                        <input type="text" name="lote" placeholder="Ej: L-489392" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">F. Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad_unitaria" min="1" placeholder="0" class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Valor Neto Unitario</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium">$</span>
                            <input type="number" id="valor_unitario" name="valor_unitario" step="0.01" min="0" placeholder="0" class="w-full pl-8 bg-white border border-slate-300 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-colors sm:text-sm">
                        </div>
                    </div>

                    <!-- Resumen de Valor Final -->
                    <div class="md:col-span-2 lg:col-span-4 mt-2">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="bg-emerald-100 p-3 rounded-full text-emerald-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-emerald-900">Valor Total (IVA Incluido)</h4>
                                    <p class="text-sm text-emerald-600 font-medium">Calculado aut: (Cant. × Neto) + 19% IVA</p>
                                </div>
                            </div>
                            
                            <div class="relative w-full md:w-64">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-700 font-bold text-xl">$</span>
                                <input type="text" id="valor_final_iva" name="valor_final_iva" readonly placeholder="0" class="w-full pl-10 pr-4 py-3 bg-white/60 border border-emerald-300 text-emerald-800 font-bold rounded-xl focus:outline-none focus:ring-0 text-xl md:text-2xl text-right transition-colors cursor-default">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-slate-200">
                <button type="button" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-50 hover:text-slate-900 transition-all focus:ring-2 focus:ring-slate-200 order-2 sm:order-1">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-200 flex items-center justify-center gap-2 transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 order-1 sm:order-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Confirmar e Ingresar
                </button>
            </div>
        </form>
    </div>

    <!-- Script para cálculo automático del IVA -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cantidadInput = document.getElementById('cantidad');
            const valorUnitarioInput = document.getElementById('valor_unitario');
            const valorFinalInput = document.getElementById('valor_final_iva');

            function calcularTotal() {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const valorUnitario = parseFloat(valorUnitarioInput.value) || 0;
                
                if(cantidad > 0 && valorUnitario > 0) {
                    const subtotal = cantidad * valorUnitario;
                    const totalConIva = subtotal * 1.19; // IVA 19% Chile
                    
                    // Formatear visualmente el número con separadores de miles
                    valorFinalInput.value = new Intl.NumberFormat('es-CL').format(Math.round(totalConIva));
                } else {
                    valorFinalInput.value = '';
                }
            }

            cantidadInput.addEventListener('input', calcularTotal);
            valorUnitarioInput.addEventListener('input', calcularTotal);
        });
    </script>
@endsection