@extends('layouts.app')

@section('title', 'Recepción')
@section('subtitle', 'Registro y control de ingreso de mercadería.')

@section('actions')
    <button type="button"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Ver Historial
    </button>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Ruta de navegación -->
        <div class="hidden sm:flex items-center gap-2 text-[12.5px] text-slate-500 -mt-2">
            <span class="font-mono-das text-slate-400 font-semibold">01</span>
            <span>Abastecimiento</span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-800">Recepción</span>
        </div>

        <!-- Contenedor Principal del Formulario -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            
            <!-- Cabecera Compacta del Formulario -->
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Registro de Ingreso de Mercadería</h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">Complete los datos del documento, proveedor y productos a recepcionar.</p>
                </div>
                <div class="flex items-center gap-2 bg-teal-50 border border-teal-200 px-3 py-1.5 rounded-lg shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-teal-600 animate-pulse"></span>
                    <span class="text-[11.5px] font-mono-das font-bold text-teal-800 tracking-wide">CORRELATIVO: #REC-{{ date('Ymd-His') }}</span>
                </div>
            </div>

            <form action="#" method="POST" class="divide-y divide-slate-100">
                @csrf

                <!-- SECCIÓN 1: Datos del Documento -->
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">1. Documento y Origen</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">N° Correlativo Interno</label>
                            <input type="text" name="correlativo" placeholder="Ej: REC-10293" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Fecha de Recepción <span class="text-rose-500">*</span></label>
                            <input type="date" name="fecha_recepcion" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Tipo de Documento <span class="text-rose-500">*</span></label>
                            <select name="tipo_documento" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Factura">Factura</option>
                                <option value="Guia">Guía de Despacho</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">N° del Documento <span class="text-rose-500">*</span></label>
                            <input type="text" name="numero_documento" placeholder="Ej: 847593" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Orden de Compra / Referencia</label>
                            <input type="text" name="orden_compra" placeholder="Ej: 1234-56-SE26" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Origen de la Mercadería <span class="text-rose-500">*</span></label>
                            <select name="origen" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                                <option value="" disabled selected>Seleccione origen...</option>
                                <option value="CENABAST">CENABAST</option>
                                <option value="COMPRA DIRECTA DAS">COMPRA DIRECTA DAS</option>
                                <option value="SSTHNO">SERVICIO DE SALUD TALCAHUANO (SSTHNO)</option>
                                <option value="Traspaso">Traspaso</option>
                                <option value="Donacion">Donación</option>
                                <option value="Prestamo">Préstamo</option>
                                <option value="Canje">Canje</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: Datos del Proveedor -->
                <div class="p-6 bg-slate-50/40">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">2. Proveedor</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">RUT Proveedor</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                </div>
                                <input type="text" name="rut_proveedor" placeholder="Ej: 76.543.210-K" class="w-full pl-9 bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 pr-3 text-[13px] font-mono-das transition-colors">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Razón Social / Nombre Proveedor</label>
                            <input type="text" name="proveedor" placeholder="Ej: Laboratorios Andinos S.A." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: Detalle del Producto y Valores -->
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">3. Detalle del Producto</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Producto <span class="text-rose-500">*</span></label>
                            <select name="producto_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                                <option value="" disabled selected>Buscar producto en catálogo...</option>
                                <option value="1">Paracetamol 500mg Comprimidos</option>
                                <option value="2">Jeringa 5ml</option>
                                <option value="3">Desfibrilador Externo Automático</option>
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Categoría (Programa/Área) <span class="text-rose-500">*</span></label>
                            <select name="tipo_producto" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                                <option value="" disabled selected>Seleccione programa...</option>
                                <optgroup label="FÁRMACOS">
                                    <option value="F-PALIATIVOS">CUIDADOS PALIATIVOS</option>
                                    <option value="F-FOFAR">FOFAR</option>
                                    <option value="F-PARO">CARRO DE PARO</option>
                                    <option value="F-CARDIOVASCULAR">P.M. CARDIOVASCULAR</option>
                                </optgroup>
                                <optgroup label="INSUMO">
                                    <option value="I-CIRUGIA">CIRUGIA MENOR</option>
                                    <option value="I-URGENCIA">URGENCIA CIRUGIA</option>
                                </optgroup>
                                <optgroup label="EQUIPAMIENTO">
                                    <option value="E-COMUNAL">EQUIPAMIENTO COMUNAL</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Lote</label>
                            <input type="text" name="lote" placeholder="Ej: L-489392" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] font-mono-das transition-colors">
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] transition-colors">
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Cantidad Recepcionada <span class="text-rose-500">*</span></label>
                            <input type="number" id="cantidad" name="cantidad_unitaria" min="1" placeholder="0" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] font-mono-das font-semibold transition-colors">
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Valor Neto Unitario ($)</label>
                            <input type="number" id="valor_unitario" name="valor_unitario" step="0.01" min="0" placeholder="0.00" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 block py-2.5 px-3.5 text-[13px] font-mono-das font-semibold transition-colors">
                        </div>

                        <!-- Resumen de Valor Final (Alineado con el diseño limpio) -->
                        <div class="lg:col-span-4 mt-2">
                            <div class="bg-teal-50/60 border border-teal-200/80 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-[13.5px] font-bold text-teal-900">Valor Total Estimado (IVA Incluido)</h4>
                                    <p class="text-[12px] text-teal-600 mt-0.5">Fórmula: (Cantidad × Valor Neto) + 19% IVA</p>
                                </div>
                                <div class="relative w-full sm:w-52">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-teal-700 font-bold">$</span>
                                    <input type="text" id="valor_final_iva" name="valor_final_iva" readonly placeholder="0" class="w-full pl-8 pr-3.5 py-2.5 bg-white border border-teal-300 text-teal-900 font-mono-das font-bold text-[15px] rounded-lg focus:outline-none text-right shadow-sm cursor-default">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción (Footer del Formulario) -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="button" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-[13px] font-medium hover:bg-slate-100 transition-colors text-center shadow-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 text-white rounded-lg text-[13px] font-semibold hover:bg-teal-700 flex items-center justify-center gap-2 transition-all shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Confirmar Recepción
                    </button>
                </div>
            </form>
        </div>
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
                    const totalConIva = subtotal * 1.19; // IVA 19%
                    
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