@extends('layouts.app')

@section('title', 'Recepción')
@section('subtitle', 'Registro y control de ingreso de mercadería.')

@section('actions')
@role('Administrador')
    <button type="button" onclick="openHistoryModal()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Ver Historial
    </button>
    @endrole
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

                <!-- seccion 3: multiples productos separados por Medicamentos, Insumos y Estupefacientes -->
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <div>
                                <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">3. Detalle de Producto(s) (Medicamentos, Insumos y Estupefacientes)</h3>
                                <p class="text-[11.5px] text-slate-500">Seleccione el grupo o agregue filas directamente clasificadas por tipo de producto.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="agregarFilaPorGrupo('Medicamentos')" class="text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg transition-colors border border-teal-200 cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                + Medicamento
                            </button>
                            <button type="button" onclick="agregarFilaPorGrupo('Insumos')" class="text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors border border-indigo-200 cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                + Insumo
                            </button>
                            <button type="button" onclick="agregarFilaPorGrupo('Estupefacientes')" class="text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition-colors border border-rose-200 cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                + Estupefaciente
                            </button>
                            <button type="button" id="btn_add_product" class="text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 px-3 py-1.5 rounded-lg transition-colors border border-slate-300 cursor-pointer shadow-2xs">
                                + Agregar Producto
                            </button>
                        </div>
                    </div>

                    <!-- Resumen visual por grupo de productos en recepción -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                        <div class="bg-teal-50/60 border border-teal-200 rounded-xl px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">MED</span>
                                <div>
                                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-teal-800">Medicamentos</p>
                                    <p class="text-[11px] text-teal-600">{{ isset($productosAgrupados['Medicamentos']) ? $productosAgrupados['Medicamentos']->count() : 0 }} en catálogo</p>
                                </div>
                            </div>
                            <span id="count_grupo_medicamentos" class="px-2.5 py-1 rounded-full text-[12px] font-black bg-white text-teal-700 border border-teal-200 shadow-2xs">0 ítem(s)</span>
                        </div>

                        <div class="bg-indigo-50/60 border border-indigo-200 rounded-xl px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">INS</span>
                                <div>
                                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-800">Insumos</p>
                                    <p class="text-[11px] text-indigo-600">{{ isset($productosAgrupados['Insumos']) ? $productosAgrupados['Insumos']->count() : 0 }} en catálogo</p>
                                </div>
                            </div>
                            <span id="count_grupo_insumos" class="px-2.5 py-1 rounded-full text-[12px] font-black bg-white text-indigo-700 border border-indigo-200 shadow-2xs">0 ítem(s)</span>
                        </div>

                        <div class="bg-rose-50/60 border border-rose-200 rounded-xl px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">EST</span>
                                <div>
                                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-800">Estupefacientes</p>
                                    <p class="text-[11px] text-rose-600">{{ isset($productosAgrupados['Estupefacientes']) ? $productosAgrupados['Estupefacientes']->count() : 0 }} en catálogo</p>
                                </div>
                            </div>
                            <span id="count_grupo_estupefacientes" class="px-2.5 py-1 rounded-full text-[12px] font-black bg-white text-rose-700 border border-rose-200 shadow-2xs">0 ítem(s)</span>
                        </div>
                    </div>

                    <!-- contenedor dinamico -->
                    <div id="productos_container" class="space-y-4">
                        <!-- molde de producto -->
                        <div class="producto-item relative border border-slate-200 bg-slate-50/50 p-4 rounded-xl shadow-sm transition-colors duration-200" data-grupo-actual="Todos">
                            <!-- Barra superior de clasificación visual por fila -->
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-200/80 pr-8">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mr-1">Filtrar catálogo:</span>
                                    <button type="button" onclick="filtrarGrupoFila(this, 'Todos')" class="btn-filtro-grupo px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-800 text-white transition-all cursor-pointer" data-grupo="Todos">
                                        Todos
                                    </button>
                                    <button type="button" onclick="filtrarGrupoFila(this, 'Medicamentos')" class="btn-filtro-grupo px-2.5 py-1 rounded-md text-[11px] font-bold bg-white text-teal-700 border border-teal-200 hover:bg-teal-50 transition-all cursor-pointer" data-grupo="Medicamentos">
                                        Medicamentos
                                    </button>
                                    <button type="button" onclick="filtrarGrupoFila(this, 'Insumos')" class="btn-filtro-grupo px-2.5 py-1 rounded-md text-[11px] font-bold bg-white text-indigo-700 border border-indigo-200 hover:bg-indigo-50 transition-all cursor-pointer" data-grupo="Insumos">
                                        Insumos
                                    </button>
                                    <button type="button" onclick="filtrarGrupoFila(this, 'Estupefacientes')" class="btn-filtro-grupo px-2.5 py-1 rounded-md text-[11px] font-bold bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 transition-all cursor-pointer" data-grupo="Estupefacientes">
                                        Estupefacientes
                                    </button>
                                </div>

                                <span class="badge-grupo-producto inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-extrabold uppercase tracking-wider bg-slate-200 text-slate-600">
                                    Sin clasificar
                                </span>
                            </div>

                            <button type="button" class="btn-remove-product hidden absolute top-3.5 right-3.5 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer" title="Eliminar fila">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="lg:col-span-2">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Producto <span class="text-rose-500">*</span></label>
                                    <select name="product_id[]" required onchange="onProductoReceptionChange(this)" class="select-producto-recepcion w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" disabled selected>Buscar producto por grupo...</option>
                                        <optgroup label="Medicamentos (Fármacos)" data-optgroup="Medicamentos">
                                            @foreach($productosAgrupados['Medicamentos'] ?? [] as $product)
                                                <option value="{{ $product->id }}" data-grupo="Medicamentos">
                                                    {{ $product->nombre }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Insumos y Equipamiento" data-optgroup="Insumos">
                                            @foreach($productosAgrupados['Insumos'] ?? [] as $product)
                                                <option value="{{ $product->id }}" data-grupo="Insumos">
                                                    {{ $product->nombre }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Estupefacientes y Controlados" data-optgroup="Estupefacientes">
                                            @foreach($productosAgrupados['Estupefacientes'] ?? [] as $product)
                                                <option value="{{ $product->id }}" data-grupo="Estupefacientes">
                                                    {{ $product->nombre }} (Controlado)
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Categoría / Clasificación <span class="text-rose-500">*</span></label>
                                    <select name="tipo_producto[]" required onchange="onCategoriaReceptionChange(this)" class="select-categoria-recepcion w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" disabled selected>Seleccione categoría...</option>
                                        <optgroup label="Medicamentos (Fármacos)">
                                            <option value="Medicamentos" data-grupo="Medicamentos">MEDICAMENTOS GENERALES</option>
                                            <option value="F-FOFAR" data-grupo="Medicamentos">FOFAR (FARMACIA)</option>
                                            <option value="F-PALIATIVOS" data-grupo="Medicamentos">CUIDADOS PALIATIVOS</option>
                                            <option value="F-PARO" data-grupo="Medicamentos">CARRO DE PARO</option>
                                        </optgroup>
                                        <optgroup label="Insumos Clínicos y Equipamiento">
                                            <option value="Insumos" data-grupo="Insumos">INSUMOS CLÍNICOS GENERALES</option>
                                            <option value="I-CIRUGIA" data-grupo="Insumos">CIRUGÍA MENOR (INSUMOS)</option>
                                            <option value="I-URGENCIA" data-grupo="Insumos">URGENCIA CIRUGÍA (INSUMOS)</option>
                                            <option value="E-COMUNAL" data-grupo="Insumos">EQUIPAMIENTO COMUNAL</option>
                                        </optgroup>
                                        <optgroup label="Estupefacientes y Psicotrópicos">
                                            <option value="Estupefacientes" data-grupo="Estupefacientes">ESTUPEFACIENTES / CONTROLADOS</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Programa</label>
                                    <select name="programa[]"
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" selected>Sin programa</option>
                                        <option value="Morbilidad General">Morbilidad General</option>
                                        <option value="Cardiovascular">Cardiovascular</option>
                                        <option value="IRA/ERA">Respiratorio (IRA/ERA)</option>
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
                                    <input type="number" name="cantidad_unitaria[]" min="1" step="1" required class="calc-qty w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-mono-das font-semibold">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">Valor Neto Unit. ($ CLP)</label>
                                    <input type="number" name="valor_unitario[]" step="1" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="calc-price w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-mono-das font-semibold">
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
                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">4. Firma Digital</h3>
                    </div>

                    <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                        <!-- firma receptor unica -->
                        <p class="text-[12px] font-semibold text-slate-600 mb-2">Firma Quien Recibe (DAS)</p>
                        <input type="hidden" name="firma_receptor" id="firma_receptor_input">
                        <div class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full">
                            <canvas id="pad_receptor" width="400" height="150" class="cursor-crosshair touch-none w-full"></canvas>
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
                            @php
                                $countMed = 0;
                                $countIns = 0;
                                $countEst = 0;
                                foreach ($rec->items as $rItem) {
                                    $grp = \App\Services\InventoryService::clasificarGrupoProducto($rItem->product->nombre ?? '', $rItem->tipo_producto);
                                    if ($grp === 'Estupefacientes') $countEst++;
                                    elseif ($grp === 'Insumos') $countIns++;
                                    else $countMed++;
                                }
                            @endphp
                            <!-- clase identificadora para filtrado -->
                            <tr class="hover:bg-slate-50 fila-recepcion">
                                <td class="px-6 py-4 font-bold text-slate-800">#{{ $rec->correlativo }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($rec->fecha_recepcion)->format('d-m-Y') }}</td>
                                <td class="px-6 py-4">{{ $rec->provider->name ?? $rec->provider->nombre ?? $rec->provider->razon_social ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-bold text-slate-700 mr-1">{{ $rec->items->count() }} total:</span>
                                        @if($countMed > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-teal-50 text-teal-700 border border-teal-200">{{ $countMed }} Med.</span>
                                        @endif
                                        @if($countIns > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $countIns }} Ins.</span>
                                        @endif
                                        @if($countEst > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">{{ $countEst }} Estup.</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openDetailModal({{ json_encode($rec) }}, '{{ addslashes($rec->provider->name ?? $rec->provider->nombre ?? 'N/A') }}', '{{ $rec->provider->rut ?? 'N/A' }}', '{{ route('reception.pdf', $rec->id) }}')" class="text-teal-600 hover:text-teal-800 font-medium bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md text-xs cursor-pointer">
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
                                    <th class="px-4 py-2">Producto / Clasificación</th>
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

                    <!-- Firma Modal -->
                    <div class="flex justify-center mt-4">
                        <div class="bg-white p-3 rounded-lg border border-slate-200 text-center h-24 w-64">
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
        const CONTROLADOS_LIST = @json($controlados ?? []);

        function clasificarItemJs(nombreProducto, tipoProducto) {
            const nom = (nombreProducto || '').trim().toLowerCase();
            const tipo = (tipoProducto || '').trim().toLowerCase();

            if (CONTROLADOS_LIST.some(c => c.trim().toLowerCase() === nom)) {
                return 'Estupefacientes';
            }
            if (tipo.includes('controlado') || tipo.includes('estupefaciente')) {
                return 'Estupefacientes';
            }
            if (tipo.includes('insumo') || tipo.includes('equipamiento') || tipo.startsWith('i-') || tipo.startsWith('e-')) {
                return 'Insumos';
            }
            if (tipo.includes('medicamento') || tipo.includes('farmaco') || tipo.includes('fármaco') || tipo.startsWith('f-')) {
                return 'Medicamentos';
            }

            const kwInsumos = ['jeringa', 'gasa', 'aguja', 'guante', 'mascarilla', 'apósito', 'aposito', 'venda', 'sonda', 'catéter', 'cateter', 'bajada', 'algodón', 'algodon', 'bisturí', 'bisturi', 'sutura', 'desfibrilador', 'termómetro', 'termometro', 'oxímetro', 'oximetro'];
            if (kwInsumos.some(k => nom.includes(k))) {
                return 'Insumos';
            }
            return 'Medicamentos';
        }

        function actualizarEstiloCardGrupo(card, grupo) {
            const badge = card.querySelector('.badge-grupo-producto');
            card.classList.remove(
                'border-slate-200', 'bg-slate-50/50',
                'border-teal-300', 'bg-teal-50/30',
                'border-indigo-300', 'bg-indigo-50/30',
                'border-rose-300', 'bg-rose-50/30'
            );

            badge.className = 'badge-grupo-producto inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-extrabold uppercase tracking-wider';

            if (grupo === 'Medicamentos') {
                card.classList.add('border-teal-300', 'bg-teal-50/30');
                badge.classList.add('bg-teal-100', 'text-teal-800', 'border', 'border-teal-200');
                badge.textContent = 'Medicamento';
            } else if (grupo === 'Insumos') {
                card.classList.add('border-indigo-300', 'bg-indigo-50/30');
                badge.classList.add('bg-indigo-100', 'text-indigo-800', 'border', 'border-indigo-200');
                badge.textContent = 'Insumo Clínico';
            } else if (grupo === 'Estupefacientes') {
                card.classList.add('border-rose-300', 'bg-rose-50/30');
                badge.classList.add('bg-rose-100', 'text-rose-800', 'border', 'border-rose-200');
                badge.textContent = 'Estupefaciente / Controlado';
            } else {
                card.classList.add('border-slate-200', 'bg-slate-50/50');
                badge.classList.add('bg-slate-200', 'text-slate-600');
                badge.textContent = 'Sin clasificar';
            }

            actualizarContadoresGrupos();
        }

        function filtrarGrupoFila(btn, grupo) {
            const card = btn.closest('.producto-item');
            card.setAttribute('data-grupo-actual', grupo);

            // Actualizar estado activo de los botones de filtro en esta tarjeta
            card.querySelectorAll('.btn-filtro-grupo').forEach(b => {
                const bGrupo = b.getAttribute('data-grupo');
                b.className = 'btn-filtro-grupo px-2.5 py-1 rounded-md text-[11px] font-bold transition-all cursor-pointer ';
                if (bGrupo === grupo) {
                    if (grupo === 'Medicamentos') b.className += 'bg-teal-600 text-white shadow-xs';
                    else if (grupo === 'Insumos') b.className += 'bg-indigo-600 text-white shadow-xs';
                    else if (grupo === 'Estupefacientes') b.className += 'bg-rose-600 text-white shadow-xs';
                    else b.className += 'bg-slate-800 text-white shadow-xs';
                } else {
                    if (bGrupo === 'Medicamentos') b.className += 'bg-white text-teal-700 border border-teal-200 hover:bg-teal-50';
                    else if (bGrupo === 'Insumos') b.className += 'bg-white text-indigo-700 border border-indigo-200 hover:bg-indigo-50';
                    else if (bGrupo === 'Estupefacientes') b.className += 'bg-white text-rose-700 border border-rose-200 hover:bg-rose-50';
                    else b.className += 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100';
                }
            });

            // Mostrar/ocultar optgroups del select de producto según el grupo elegido
            const selectProd = card.querySelector('.select-producto-recepcion');
            selectProd.querySelectorAll('optgroup').forEach(og => {
                const ogGrupo = og.getAttribute('data-optgroup');
                const visible = (grupo === 'Todos' || ogGrupo === grupo);
                og.disabled = !visible;
                og.hidden = !visible;
                og.querySelectorAll('option').forEach(opt => {
                    opt.disabled = !visible;
                    opt.hidden = !visible;
                });
            });

            // Si el producto actualmente seleccionado no pertenece al grupo filtrado, limpiar selección y pre-asignar categoría
            const selectedOpt = selectProd.options[selectProd.selectedIndex];
            if (grupo !== 'Todos') {
                if (!selectedOpt || !selectedOpt.value || selectedOpt.getAttribute('data-grupo') !== grupo) {
                    selectProd.selectedIndex = 0;
                    const selectCat = card.querySelector('.select-categoria-recepcion');
                    if (grupo === 'Estupefacientes') {
                        selectCat.value = 'Estupefacientes';
                    } else if (grupo === 'Insumos') {
                        selectCat.value = 'Insumos';
                    } else if (grupo === 'Medicamentos') {
                        selectCat.value = 'Medicamentos';
                    }
                }
                actualizarEstiloCardGrupo(card, grupo);
            } else if (!selectedOpt || !selectedOpt.value) {
                actualizarEstiloCardGrupo(card, 'Todos');
            }
        }

        function onProductoReceptionChange(selectProd) {
            const card = selectProd.closest('.producto-item');
            const opt = selectProd.options[selectProd.selectedIndex];
            if (!opt || !opt.value) {
                actualizarEstiloCardGrupo(card, 'Todos');
                return;
            }

            const grupo = opt.getAttribute('data-grupo') || 'Medicamentos';
            const selectCat = card.querySelector('.select-categoria-recepcion');

            if (grupo === 'Estupefacientes') {
                selectCat.value = 'Estupefacientes';
            } else {
                const catOpt = selectCat.options[selectCat.selectedIndex];
                const catGrupoActual = catOpt ? catOpt.getAttribute('data-grupo') : null;
                if (!catGrupoActual || catGrupoActual !== grupo) {
                    selectCat.value = (grupo === 'Insumos') ? 'Insumos' : 'Medicamentos';
                }
            }

            actualizarEstiloCardGrupo(card, grupo);
        }

        function onCategoriaReceptionChange(selectCat) {
            const card = selectCat.closest('.producto-item');
            const opt = selectCat.options[selectCat.selectedIndex];
            if (!opt || !opt.value) return;
            const grupo = opt.getAttribute('data-grupo') || 'Medicamentos';
            actualizarEstiloCardGrupo(card, grupo);
        }

        function actualizarContadoresGrupos() {
            let med = 0, ins = 0, est = 0;
            document.querySelectorAll('.producto-item').forEach(card => {
                const selProd = card.querySelector('.select-producto-recepcion');
                const selCat = card.querySelector('.select-categoria-recepcion');
                const optProd = selProd ? selProd.options[selProd.selectedIndex] : null;
                const optCat = selCat ? selCat.options[selCat.selectedIndex] : null;

                let grp = null;
                if (optProd && optProd.value) {
                    grp = optProd.getAttribute('data-grupo');
                } else if (optCat && optCat.value) {
                    grp = optCat.getAttribute('data-grupo');
                } else {
                    const filtroActivo = card.getAttribute('data-grupo-actual');
                    if (filtroActivo && filtroActivo !== 'Todos') grp = filtroActivo;
                }

                if (grp === 'Medicamentos') med++;
                else if (grp === 'Insumos') ins++;
                else if (grp === 'Estupefacientes') est++;
            });

            const elMed = document.getElementById('count_grupo_medicamentos');
            const elIns = document.getElementById('count_grupo_insumos');
            const elEst = document.getElementById('count_grupo_estupefacientes');
            if (elMed) elMed.textContent = `${med} ítem(s)`;
            if (elIns) elIns.textContent = `${ins} ítem(s)`;
            if (elEst) elEst.textContent = `${est} ítem(s)`;
        }

        function crearNuevaFilaProducto(grupoInicial = 'Todos') {
            const container = document.getElementById('productos_container');
            const firstItem = container.querySelector('.producto-item');
            const clone = firstItem.cloneNode(true);

            clone.querySelectorAll('input').forEach(inp => inp.value = '');
            clone.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

            const btnRemove = clone.querySelector('.btn-remove-product');
            btnRemove.classList.remove('hidden');
            btnRemove.addEventListener('click', function() {
                clone.remove();
                if (typeof window.calcularTotalGeneralRecepcion === 'function') {
                    window.calcularTotalGeneralRecepcion();
                }
                actualizarContadoresGrupos();
            });

            clone.querySelector('.calc-qty').addEventListener('input', window.calcularTotalGeneralRecepcion);
            clone.querySelector('.calc-price').addEventListener('input', window.calcularTotalGeneralRecepcion);

            container.appendChild(clone);

            const targetBtn = clone.querySelector(`.btn-filtro-grupo[data-grupo="${grupoInicial}"]`) || clone.querySelector('.btn-filtro-grupo[data-grupo="Todos"]');
            if (targetBtn) {
                filtrarGrupoFila(targetBtn, grupoInicial);
            }
            return clone;
        }

        function agregarFilaPorGrupo(grupo) {
            const container = document.getElementById('productos_container');
            const items = container.querySelectorAll('.producto-item');
            // Si la primera fila está completamente vacía, usarla directamente
            if (items.length === 1) {
                const firstSel = items[0].querySelector('.select-producto-recepcion');
                if (firstSel && !firstSel.value) {
                    const btn = items[0].querySelector(`.btn-filtro-grupo[data-grupo="${grupo}"]`);
                    if (btn) filtrarGrupoFila(btn, grupo);
                    firstSel.focus();
                    return;
                }
            }
            const nueva = crearNuevaFilaProducto(grupo);
            const sel = nueva.querySelector('.select-producto-recepcion');
            if (sel) sel.focus();
        }

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

            window.calcularTotalGeneralRecepcion = function() {
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
            };

            // asignar eventos de suma a la primera fila original
            document.querySelectorAll('.calc-qty, .calc-price').forEach(inp => {
                inp.addEventListener('input', window.calcularTotalGeneralRecepcion);
            });

            // agregar nueva fila de producto
            document.getElementById('btn_add_product').addEventListener('click', function() {
                crearNuevaFilaProducto('Todos');
            });

            // 3. funcionalidad de firma digital
            const padOptions = { penColor: 'rgb(15, 23, 42)', backgroundColor: 'rgba(255, 255, 255, 1)' };
            const signatureRec = new SignaturePad(document.getElementById('pad_receptor'), padOptions);

            // 4. limpieza del formulario completo (incluye firma)
            document.getElementById('btn_limpiar').addEventListener('click', function(e) {
                signatureRec.clear();
                document.getElementById('valor_final_iva').value = '';

                // borra los productos extra y deja solo el primero
                const items = container.querySelectorAll('.producto-item');
                for(let i = 1; i < items.length; i++) {
                    items[i].remove();
                }
                const firstBtn = items[0].querySelector('.btn-filtro-grupo[data-grupo="Todos"]');
                if (firstBtn) filtrarGrupoFila(firstBtn, 'Todos');
                actualizarContadoresGrupos();
            });

            // 5. antes de enviar, inyectar firmas base64
            document.getElementById('receptionForm').addEventListener('submit', function() {
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

            // llenar tabla de items agrupados por Medicamentos, Insumos y Estupefacientes
            const tbody = document.getElementById('m_items_table');
            tbody.innerHTML = '';

            const grupos = {
                'Medicamentos': { titulo: 'Medicamentos (Fármacos)', badgeClass: 'bg-teal-50 text-teal-800 border-teal-200', headerBg: 'bg-teal-50/70 text-teal-900', items: [] },
                'Insumos': { titulo: 'Insumos Clínicos y Equipamiento', badgeClass: 'bg-indigo-50 text-indigo-800 border-indigo-200', headerBg: 'bg-indigo-50/70 text-indigo-900', items: [] },
                'Estupefacientes': { titulo: 'Estupefacientes y Controlados', badgeClass: 'bg-rose-50 text-rose-800 border-rose-200', headerBg: 'bg-rose-50/70 text-rose-900', items: [] }
            };

            (data.items || []).forEach(item => {
                const nom = item.product ? item.product.nombre : 'Producto Eliminado';
                const grp = clasificarItemJs(nom, item.tipo_producto);
                grupos[grp].items.push(item);
            });

            Object.keys(grupos).forEach(key => {
                const g = grupos[key];
                if (g.items.length === 0) return;

                const trHeader = document.createElement('tr');
                trHeader.className = g.headerBg;
                trHeader.innerHTML = `
                    <td colspan="4" class="px-4 py-2 text-[11px] font-extrabold uppercase tracking-wider border-y border-slate-200">
                        ${g.titulo} (${g.items.length})
                    </td>
                `;
                tbody.appendChild(trHeader);

                g.items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-3">
                            <strong>${item.product ? item.product.nombre : 'Prod Eliminado'}</strong><br>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold border ${g.badgeClass}">${item.tipo_producto || key}</span>
                        </td>
                        <td class="px-4 py-3">${item.lote || 'S/L'}<br><span class="text-[10px] text-slate-500">${item.fecha_vencimiento || 'N/A'}</span></td>
                        <td class="px-4 py-3 text-center font-bold">${item.cantidad}</td>
                        <td class="px-4 py-3 text-right font-mono-das font-bold">$${Math.round(parseFloat(item.valor_total_item || 0)).toLocaleString('es-CL')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });

            document.getElementById('m_total').innerText = '$' + Math.round(parseFloat(data.valor_total_recepcion || 0)).toLocaleString('es-CL');

            // firma receptor
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
