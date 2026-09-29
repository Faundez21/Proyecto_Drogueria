@extends('layouts.app')

@section('title', 'Recepción')
@section('subtitle', 'Registro y control de ingreso de mercadería.')

@section('actions')
    <button type="button" onclick="openHistoryModal()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Ver Historial
    </button>
@endsection

@section('content')
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">01</span>
        <span>Abastecimiento</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="font-semibold text-slate-800">Recepción</span>
    </div>

    <div class="space-y-6">
        <!-- notificaciones de exito -->
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-teal-800 rounded-xl bg-teal-50 border border-teal-200 font-medium shadow-sm" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- cabecera del formulario -->
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Registro de Ingreso de Mercaderia</h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">Complete los datos del documento, proveedor y productos a recepcionar.</p>
                </div>
            </div>

            <!-- formulario principal -->
            <form action="{{ route('reception.store') }}" method="POST" class="divide-y divide-slate-100" id="receptionForm">
                @csrf

                <!-- seccion 1: documento -->
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">1. Documento y Origen</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">N° Correlativo Interno</label>
                            <input type="text" value="{{ $nextCorrelativo ?? '1' }}" readonly class="w-full bg-slate-100 border border-slate-200 text-teal-700 font-bold rounded-lg block py-2.5 px-3.5 text-[13px] cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Fecha de Recepcion <span class="text-rose-500">*</span></label>
                            <input type="date" name="fecha_recepcion" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Tipo de Documento <span class="text-rose-500">*</span></label>
                            <select name="tipo_documento" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Factura">Factura</option>
                                <option value="Guia">Guia de Despacho</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">N° del Documento <span class="text-rose-500">*</span></label>
                            <input type="text" name="numero_documento" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Orden de Compra / Referencia</label>
                            <input type="text" name="orden_compra" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Origen de la Mercaderia <span class="text-rose-500">*</span></label>
                            <select name="origen" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                <option value="" disabled selected>Seleccione origen...</option>
                                <option value="CENABAST">CENABAST</option>
                                <option value="COMPRA DIRECTA DAS">COMPRA DIRECTA DAS</option>
                                <option value="SSTHNO">SERVICIO DE SALUD TALCAHUANO</option>
                                <option value="Traspaso">Traspaso</option>
                                <option value="Donacion">Donacion</option>
                                <option value="Prestamo">Prestamo</option>
                                <option value="Canje">Canje</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- seccion 2: proveedor -->
                <div class="p-6 bg-slate-50/40">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">2. Proveedor</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">RUT Proveedor</label>
                            <input type="text" id="rut_proveedor_display" readonly placeholder="Auto-completado" class="w-full bg-slate-100 border border-slate-200 text-slate-600 rounded-lg block py-2.5 px-3.5 text-[13px] font-mono-das cursor-not-allowed">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Razon Social / Nombre Proveedor <span class="text-rose-500">*</span></label>
                            <select name="provider_id" id="provider_select" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                <option value="" disabled selected>Seleccione proveedor...</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" data-rut="{{ $provider->rut ?? $provider->nit ?? '' }}">
                                        {{ $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- seccion 3: multiples productos -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">3. Detalle de Producto(s)</h3>
                        </div>
                        <button type="button" id="btn_add_product" class="text-xs font-bold text-teal-600 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md transition-colors border border-teal-200">
                            + Agregar Producto
                        </button>
                    </div>

                    <!-- contenedor dinamico -->
                    <div id="productos_container" class="space-y-4">
                        <!-- molde de producto -->
                        <div class="producto-item relative border border-slate-200 bg-slate-50/50 p-4 rounded-xl shadow-sm">
                            <button type="button" class="btn-remove-product hidden absolute top-3 right-3 text-slate-400 hover:text-rose-600 transition-colors" title="Eliminar fila">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="lg:col-span-2">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Producto <span class="text-rose-500">*</span></label>
                                    <select name="product_id[]" required class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" disabled selected>Buscar producto...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Categoria <span class="text-rose-500">*</span></label>
                                    <select name="tipo_producto[]" required class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="F-PALIATIVOS">CUIDADOS PALIATIVOS</option>
                                        <option value="F-FOFAR">FOFAR</option>
                                        <option value="F-PARO">CARRO DE PARO</option>
                                        <option value="I-CIRUGIA">CIRUGIA MENOR</option>
                                        <option value="I-URGENCIA">URGENCIA CIRUGIA</option>
                                        <option value="E-COMUNAL">EQUIPAMIENTO COMUNAL</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Lote</label>
                                    <input type="text" name="lote[]" placeholder="Ej: L-4893" class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px]">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">F. Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento[]" class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px]">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Cantidad <span class="text-rose-500">*</span></label>
                                    <input type="number" name="cantidad_unitaria[]" min="1" required class="calc-qty w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-mono-das font-semibold">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Valor Neto Unit. ($)</label>
                                    <input type="number" name="valor_unitario[]" step="0.01" min="0" class="calc-price w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-mono-das font-semibold">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- gran total recepcion -->
                    <div class="mt-4 bg-teal-50/80 border border-teal-200/80 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                        <div>
                            <h4 class="text-[14px] font-bold text-teal-900">Gran Total Recepcion (IVA Incluido)</h4>
                            <p class="text-[12px] text-teal-600 mt-0.5">Suma de todos los productos ingresados + 19% IVA</p>
                        </div>
                        <div class="relative w-full sm:w-52">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-teal-700 font-bold">$</span>
                            <input type="text" id="valor_final_iva" readonly placeholder="0" class="w-full pl-8 pr-3.5 py-2.5 bg-white border border-teal-300 text-teal-900 font-mono-das font-bold text-[16px] rounded-lg focus:outline-none text-right shadow-sm cursor-default">
                        </div>
                    </div>
                </div>

                <!-- seccion 4: firmas -->
                <div class="p-6 bg-slate-50/40">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">4. Firmas Digitales</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- firma proveedor -->
                        <div class="flex flex-col items-center">
                            <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Entrega (Proveedor)</p>
                            <input type="hidden" name="firma_proveedor" id="firma_proveedor_input">
                            <div class="border-2 border-dashed border-slate-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                                <canvas id="pad_proveedor" width="400" height="150" class="cursor-crosshair touch-none w-full"></canvas>
                            </div>
                        </div>

                        <!-- firma receptor -->
                        <div class="flex flex-col items-center">
                            <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Recibe (DAS)</p>
                            <input type="hidden" name="firma_receptor" id="firma_receptor_input">
                            <div class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                                <canvas id="pad_receptor" width="400" height="150" class="cursor-crosshair touch-none w-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- botones finales -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="reset" id="btn_limpiar" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-[13px] font-medium hover:bg-slate-100 transition-colors shadow-sm">Limpiar Formulario</button>
                    <button type="submit" name="action" value="save_and_download" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg text-[13px] font-semibold hover:bg-blue-700 flex items-center justify-center gap-2 transition-all shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Guardar y Descargar PDF
                    </button>
                    <button type="submit" name="action" value="save_only" class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 text-white rounded-lg text-[13px] font-semibold hover:bg-teal-700 flex items-center justify-center gap-2 transition-all shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Confirmar Recepcion
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- modal 1: historial -->
    <div id="historyModal" class="fixed inset-0 z-40 hidden bg-slate-900/50 backdrop-blur-sm transition-opacity" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 max-w-5xl w-full">

                <!-- cabecera historial -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Historial de Recepciones</h3>
                    <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- filtros dinamicos del historial -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Filtros de Busqueda</span>
                        <button type="button" onclick="limpiarFiltrosRecepcion()" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 transition-colors">Limpiar Filtros</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- filtro correlativo -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">N° Correlativo</label>
                            <input type="text" id="filtroCorrelativoRec" oninput="filtrarRecepciones()" placeholder="Ej: 123" class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm">
                        </div>

                        <!-- filtro fecha -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fecha Recepcion</label>
                            <input type="date" id="filtroFechaRec" oninput="filtrarRecepciones()" class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm cursor-pointer">
                        </div>

                        <!-- filtro proveedor con sugerencias -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Proveedor</label>
                            <input type="text" id="filtroProveedorRec" list="sugerencias-proveedores" oninput="filtrarRecepciones()" placeholder="Ej: Cenabast" class="w-full py-2 px-3 bg-white border border-slate-300 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors shadow-sm">
                            <datalist id="sugerencias-proveedores">
                                @foreach($receptions ?? [] as $rec)
                                    @if(isset($rec->provider))
                                        <option value="{{ $rec->provider->name ?? $rec->provider->nombre ?? $rec->provider->razon_social }}">
                                    @endif
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                </div>

                <!-- tabla del historial -->
                <div class="p-0 max-h-[50vh] overflow-y-auto bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-[12px] uppercase text-slate-500 sticky top-0 shadow-sm z-10">
                                <th class="px-6 py-3 font-semibold">N°</th>
                                <th class="px-6 py-3 font-semibold">Fecha</th>
                                <th class="px-6 py-3 font-semibold">Proveedor</th>
                                <th class="px-6 py-3 font-semibold">Items</th>
                                <th class="px-6 py-3 font-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px] text-slate-700">
                            @forelse($receptions ?? [] as $rec)
                            <!-- clase identificadora para filtrado -->
                            <tr class="hover:bg-slate-50 fila-recepcion">
                                <td class="px-6 py-4 font-bold text-slate-800">#{{ $rec->correlativo }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($rec->fecha_recepcion)->format('d-m-Y') }}</td>
                                <td class="px-6 py-4">{{ $rec->provider->name ?? $rec->provider->nombre ?? $rec->provider->razon_social ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-semibold text-teal-600">{{ $rec->items->count() }} producto(s)</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openDetailModal({{ json_encode($rec) }}, '{{ addslashes($rec->provider->name ?? $rec->provider->nombre ?? 'N/A') }}', '{{ $rec->provider->rut ?? 'N/A' }}', '{{ route('reception.pdf', $rec->id) }}')" class="text-teal-600 hover:text-teal-800 font-medium bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md text-xs">
                                        Ver Detalle
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No hay recepciones registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button onclick="closeHistoryModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">Cerrar Historial</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal 2: detalle -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm transition-opacity" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 max-w-4xl w-full flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white">Resumen de Recepcion</h3>
                        <p class="text-slate-300 text-xs">Correlativo Interno: <span id="m_correlativo" class="font-bold text-teal-400"></span></p>
                    </div>
                    <button onclick="closeDetailModal()" class="text-slate-400 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 bg-slate-50 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white p-4 rounded-lg border border-slate-200">
                            <h4 class="text-[11px] font-bold text-teal-600 uppercase mb-3">Informacion del Documento</h4>
                            <div class="space-y-2 text-[13px]">
                                <div class="flex justify-between"><span class="text-slate-500">Documento:</span> <span class="font-medium text-slate-800" id="m_doc"></span></div>
                                <div class="flex justify-between"><span class="text-slate-500">Fecha:</span> <span class="font-medium text-slate-800" id="m_fecha"></span></div>
                                <div class="flex justify-between"><span class="text-slate-500">Origen:</span> <span class="font-medium text-slate-800" id="m_origen"></span></div>
                                <div class="flex justify-between"><span class="text-slate-500">OC:</span> <span class="font-medium text-slate-800" id="m_orden"></span></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-slate-200">
                            <h4 class="text-[11px] font-bold text-teal-600 uppercase mb-3">Proveedor</h4>
                            <div class="space-y-2 text-[13px]">
                                <div class="flex flex-col"><span class="text-slate-500 text-xs">Razon Social:</span> <span class="font-bold text-slate-800" id="m_proveedor"></span></div>
                                <div class="flex flex-col mt-2"><span class="text-slate-500 text-xs">RUT:</span> <span class="font-medium text-slate-800 font-mono-das" id="m_rut_proveedor"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- tabla de productos modal -->
                    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-4">
                        <table class="w-full text-left text-[12px]">
                            <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-2">Producto</th>
                                    <th class="px-4 py-2">Lote/Vence</th>
                                    <th class="px-4 py-2 text-center">Cant.</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="m_items_table" class="divide-y divide-slate-100 text-slate-800">
                                <!-- filas inyectadas por js -->
                            </tbody>
                        </table>
                        <div class="bg-teal-50 border-t border-teal-100 p-3 flex justify-between items-center">
                            <span class="text-teal-800 font-bold text-sm uppercase">Gran Total (IVA Inc.)</span>
                            <span class="text-teal-700 font-bold text-lg" id="m_total"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-3 rounded-lg border border-slate-200 text-center h-24">
                            <p class="text-[11px] text-slate-500 font-semibold mb-1 uppercase">Firma Entrega</p>
                            <img id="m_img_proveedor" src="" class="hidden mx-auto max-h-12" alt="Firma">
                            <span id="m_no_firma_proveedor" class="text-xs text-slate-400 italic">No registrada</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200 text-center h-24">
                            <p class="text-[11px] text-slate-500 font-semibold mb-1 uppercase">Firma Recepcion</p>
                            <img id="m_img_receptor" src="" class="hidden mx-auto max-h-12" alt="Firma">
                            <span id="m_no_firma_receptor" class="text-xs text-slate-400 italic">No registrada</span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-200 flex justify-end gap-3 rounded-b-xl">
                    <button onclick="closeDetailModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">Cerrar</button>
                    <a href="#" id="m_btn_pdf" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Descargar PDF</a>
                </div>
            </div>
        </div>
    </div>

    <!-- importar cdn de signature pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        // limpia los filtros de la bandeja de recepciones
        function limpiarFiltrosRecepcion() {
            document.getElementById('filtroCorrelativoRec').value = '';
            document.getElementById('filtroFechaRec').value = '';
            document.getElementById('filtroProveedorRec').value = '';
            filtrarRecepciones();
        }

        // filtrado dinamico multi-criterio de la tabla de recepciones
        function filtrarRecepciones() {
            let inputCorrelativo = document.getElementById('filtroCorrelativoRec').value.toLowerCase();
            let inputFecha = document.getElementById('filtroFechaRec').value;
            let inputProveedor = document.getElementById('filtroProveedorRec').value.toLowerCase();

            let fechaFormateada = "";
            if (inputFecha) {
                let partes = inputFecha.split('-');
                if (partes.length === 3) {
                    fechaFormateada = partes[2] + '-' + partes[1] + '-' + partes[0];
                }
            }

            let filas = document.querySelectorAll('.fila-recepcion');

            filas.forEach(fila => {
                let correlativoText = fila.children[0].textContent.toLowerCase();
                let fechaText = fila.children[1].textContent.trim();
                let proveedorText = fila.children[2].textContent.toLowerCase();

                let coincideCorrelativo = correlativoText.includes(inputCorrelativo);
                let coincideFecha = fechaFormateada === "" || fechaText.includes(fechaFormateada);
                let coincideProveedor = proveedorText.includes(inputProveedor);

                if (coincideCorrelativo && coincideFecha && coincideProveedor) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. logica autocompletar proveedor
            const providerSelect = document.getElementById('provider_select');
            const rutInput = document.getElementById('rut_proveedor_display');
            providerSelect.addEventListener('change', function() {
                rutInput.value = this.options[this.selectedIndex].getAttribute('data-rut') || '';
            });

            // 2. logica dinamica de productos y totales
            const container = document.getElementById('productos_container');

            function calcularTotalGeneral() {
                let totalNeto = 0;
                document.querySelectorAll('.producto-item').forEach(item => {
                    const qty = parseFloat(item.querySelector('.calc-qty').value) || 0;
                    const price = parseFloat(item.querySelector('.calc-price').value) || 0;
                    totalNeto += (qty * price);
                });

                const inputFinal = document.getElementById('valor_final_iva');
                if(totalNeto > 0) {
                    inputFinal.value = new Intl.NumberFormat('es-CL').format(Math.round(totalNeto * 1.19));
                } else {
                    inputFinal.value = '';
                }
            }

            // asignar eventos de suma a la primera fila original
            document.querySelectorAll('.calc-qty, .calc-price').forEach(inp => {
                inp.addEventListener('input', calcularTotalGeneral);
            });

            // agregar nueva fila de producto
            document.getElementById('btn_add_product').addEventListener('click', function() {
                const firstItem = container.querySelector('.producto-item');
                const clone = firstItem.cloneNode(true);

                // limpiar valores clonados
                clone.querySelectorAll('input').forEach(inp => inp.value = '');
                clone.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

                // mostrar boton de eliminar (que en la fila 1 esta oculto)
                const btnRemove = clone.querySelector('.btn-remove-product');
                btnRemove.classList.remove('hidden');
                btnRemove.addEventListener('click', function() {
                    clone.remove();
                    calcularTotalGeneral(); // recalcular al borrar
                });

                // asignar eventos de suma a los nuevos inputs
                clone.querySelector('.calc-qty').addEventListener('input', calcularTotalGeneral);
                clone.querySelector('.calc-price').addEventListener('input', calcularTotalGeneral);

                container.appendChild(clone);
            });

            // 3. funcionalidad de firmas digitales
            const padOptions = { penColor: 'rgb(15, 23, 42)', backgroundColor: 'rgba(255, 255, 255, 1)' };
            const signatureProv = new SignaturePad(document.getElementById('pad_proveedor'), padOptions);
            const signatureRec = new SignaturePad(document.getElementById('pad_receptor'), padOptions);

            // 4. limpieza del formulario completo (incluye firmas)
            document.getElementById('btn_limpiar').addEventListener('click', function(e) {
                signatureProv.clear();
                signatureRec.clear();
                document.getElementById('valor_final_iva').value = '';

                // borra los productos extra y deja solo el primero
                const items = container.querySelectorAll('.producto-item');
                for(let i = 1; i < items.length; i++) {
                    items[i].remove();
                }
            });

            // 5. antes de enviar, inyectar firmas base64
            document.getElementById('receptionForm').addEventListener('submit', function() {
                if (!signatureProv.isEmpty()) document.getElementById('firma_proveedor_input').value = signatureProv.toDataURL('image/png');
                if (!signatureRec.isEmpty()) document.getElementById('firma_receptor_input').value = signatureRec.toDataURL('image/png');
            });
        });

        // 6. modales (historial y detalle)
        function openHistoryModal() { document.getElementById('historyModal').classList.remove('hidden'); }
        function closeHistoryModal() { document.getElementById('historyModal').classList.add('hidden'); }

        function openDetailModal(data, providerName, providerRut, pdfUrl) {
            document.getElementById('m_correlativo').innerText = '#' + data.correlativo;
            document.getElementById('m_doc').innerText = data.tipo_documento + ' #' + data.numero_documento;
            document.getElementById('m_fecha').innerText = data.fecha_recepcion;
            document.getElementById('m_origen').innerText = data.origen;
            document.getElementById('m_orden').innerText = data.orden_compra || 'N/A';
            document.getElementById('m_proveedor').innerText = providerName;
            document.getElementById('m_rut_proveedor').innerText = providerRut;

            // llenar tabla de items
            const tbody = document.getElementById('m_items_table');
            tbody.innerHTML = '';

            data.items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-3"><strong>${item.product ? item.product.nombre : 'Prod Eliminado'}</strong><br><span class="text-[10px] text-slate-500">${item.tipo_producto}</span></td>
                    <td class="px-4 py-3">${item.lote || 'S/L'}<br><span class="text-[10px] text-slate-500">${item.fecha_vencimiento || 'N/A'}</span></td>
                    <td class="px-4 py-3 text-center font-bold">${item.cantidad}</td>
                    <td class="px-4 py-3 text-right">$${parseFloat(item.valor_total_item).toLocaleString('es-CL')}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('m_total').innerText = '$' + parseFloat(data.valor_total_recepcion).toLocaleString('es-CL');

            // firmas
            const imgProv = document.getElementById('m_img_proveedor');
            const noFirmaProv = document.getElementById('m_no_firma_proveedor');
            if(data.firma_proveedor) { imgProv.src = data.firma_proveedor; imgProv.classList.remove('hidden'); noFirmaProv.classList.add('hidden'); }
            else { imgProv.classList.add('hidden'); noFirmaProv.classList.remove('hidden'); }

            const imgRec = document.getElementById('m_img_receptor');
            const noFirmaRec = document.getElementById('m_no_firma_receptor');
            if(data.firma_receptor) { imgRec.src = data.firma_receptor; imgRec.classList.remove('hidden'); noFirmaRec.classList.add('hidden'); }
            else { imgRec.classList.add('hidden'); noFirmaRec.classList.remove('hidden'); }

            document.getElementById('m_btn_pdf').href = pdfUrl;
            document.getElementById('detailModal').classList.remove('hidden');
        }
        function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
    </script>
@endsection
