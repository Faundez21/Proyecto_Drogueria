@extends('layouts.app')

@section('title', 'Solicitudes CESFAM')
@section('subtitle', 'Registro y gestión de solicitudes de medicamentos, insumos y estupefacientes para CESFAM.')

@section('actions')
    @role('Administrador')
        <button type="button" onclick="openPendingRequestsCesfamModal()"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-all duration-200 text-[13px] font-bold shadow-sm hover:shadow cursor-pointer border border-slate-700">
            <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Solicitudes Pendientes CESFAM
            @if(isset($pendientesCount) && $pendientesCount > 0)
                <span class="px-2 py-0.5 text-[11px] font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full">
                    {{ $pendientesCount }}
                </span>
            @endif
        </button>

        <button type="button" onclick="openRequestInventoryModal()"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-all duration-200 text-[13px] font-bold shadow-sm hover:shadow cursor-pointer">
            <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            Ver Inventario CESFAM
        </button>
    @endrole
@endsection

@section('content')

    <div class="space-y-6">

        <!-- notificacion de exito -->
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-teal-800 rounded-xl bg-teal-50 border border-teal-200 font-medium shadow-sm flex items-center justify-between"
                role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Solicitud CESFAM Registrada con Éxito!',
                            html: `<p class="text-sm text-slate-600 font-medium">{{ session('success') }}</p>`,
                            confirmButtonColor: '#0d9488',
                            confirmButtonText: 'Aceptar',
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Verifique los datos de la solicitud',
                            html: `<ul class="text-left text-xs space-y-1 text-rose-700">@foreach ($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>`,
                            confirmButtonColor: '#e11d48',
                            confirmButtonText: 'Entendido'
                        });
                    }
                });
            </script>
        @endif

        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- cabecera del formulario -->
            <div
                class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">
                        Registro de Solicitud CESFAM
                    </h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">
                        Complete los datos del CESFAM y los medicamentos, insumos o estupefacientes requeridos.
                    </p>
                </div>
            </div>

            <!-- formulario principal -->
            <form action="{{ route('requests.store') }}" method="POST" class="divide-y divide-slate-100" id="requestForm">
                @csrf
                <input type="hidden" name="tipo_vista" value="cesfam">

                <!-- seccion 1: establecimiento -->
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>

                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">
                            1. Datos del CESFAM
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                        <div class="lg:col-span-3 min-w-0">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                CESFAM <span class="text-rose-500">*</span>
                            </label>

                            <select name="establecimiento" id="establecimiento_select" required
                                class="w-full max-w-full truncate bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                <option value="" disabled selected>Seleccione CESFAM...</option>
                                @foreach ($establecimientos as $est)
                                    @php $nomEst = trim(preg_replace('/\s+/', ' ', $est['nombre'] ?? '')); @endphp
                                    @if($nomEst !== '')
                                        <option value="{{ $nomEst }}">{{ $nomEst }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                Fecha de Solicitud <span class="text-rose-500">*</span>
                            </label>

                            <input type="date" name="fecha_solicitud" value="{{ date('Y-m-d') }}" required
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>

                        <!-- N° Solicitud oculto pero funcional -->
                        <div class="hidden">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                N° Solicitud
                            </label>

                            <input type="text" name="numero_solicitud" value="#00001" readonly
                                class="w-full bg-slate-100 border border-slate-200 text-teal-700 font-bold rounded-lg block py-2.5 px-3.5 text-[13px] cursor-not-allowed">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                Responsable de la Solicitud <span class="text-rose-500">*</span>
                            </label>

                            <input type="text" name="responsable" placeholder="Nombre y apellido" required
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                Cargo / Función
                            </label>

                            <input type="text" name="cargo" placeholder="Ej: Químico Farmacéutico / Encargado de farmacia"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>
                    </div>
                </div>

                <!-- seccion 2: productos -->
                <div class="p-6 bg-slate-50/40">

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                </path>
                            </svg>

                            <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">
                                2. Productos Solicitados (Medicamentos, Insumos y Estupefacientes)
                            </h3>
                        </div>

                        <button type="button" id="btn_add_product"
                            class="text-xs font-bold text-teal-600 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md transition-colors border border-teal-200 cursor-pointer">
                            + Agregar Producto
                        </button>
                    </div>

                    <!-- contenedor dinamico -->
                    <div id="productos_container" class="space-y-4">

                        <div class="producto-item relative border border-slate-200 bg-white p-4 rounded-xl shadow-sm">

                            <button type="button"
                                class="btn-remove-product hidden absolute top-3 right-3 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer"
                                title="Eliminar fila">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 01-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="min-w-0">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Programa <span class="text-rose-500">*</span>
                                    </label>

                                    <select name="programa[]" required
                                        class="w-full max-w-full truncate bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px] select-programa">
                                        <option value="" disabled selected>Seleccione...</option>
                                        @foreach ($programasLista ?? [] as $progOpt)
                                            @php $progLimpio = trim(preg_replace('/\s+/', ' ', $progOpt ?? '')); @endphp
                                            @if($progLimpio !== '')
                                                <option value="{{ $progLimpio }}">{{ $progLimpio }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="lg:col-span-2 min-w-0">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Producto <span class="text-rose-500">*</span>
                                    </label>

                                    <select name="producto[]" required
                                        class="w-full max-w-full truncate bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px] select-producto">
                                        <option value="" disabled selected>Buscar producto...</option>
                                        <optgroup label="Medicamentos e Insumos">
                                            @foreach ($products as $product)
                                                @if (!in_array($product->nombre, $controlados ?? []))
                                                    @php $nomLimpio = trim(preg_replace('/\s+/', ' ', $product->nombre ?? '')); @endphp
                                                    @if($nomLimpio !== '')
                                                        <option value="{{ $product->id }}" title="{{ $nomLimpio }}" data-nombre-completo="{{ $nomLimpio }}" data-programa="{{ trim($product->programa_sugerido ?? '') }}" data-categoria="{{ trim($product->categoria_sugerida ?? '') }}">{{ \Illuminate\Support\Str::limit($nomLimpio, 85) }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Estupefacientes y Controlados">
                                            @foreach ($products as $product)
                                                @if (in_array($product->nombre, $controlados ?? []))
                                                    @php $nomLimpio = trim(preg_replace('/\s+/', ' ', $product->nombre ?? '')); @endphp
                                                    @if($nomLimpio !== '')
                                                        <option value="{{ $product->id }}" title="{{ $nomLimpio }}" data-nombre-completo="{{ $nomLimpio }}" data-programa="{{ trim($product->programa_sugerido ?? '') }}" data-categoria="{{ trim($product->categoria_sugerida ?? 'Estupefacientes') }}">{{ \Illuminate\Support\Str::limit($nomLimpio, 85) }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <span class="block text-[10.5px] text-teal-600 font-medium mt-1 hidden aviso-programa"></span>
                                </div>

                                <div class="hidden">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Consumo mensual del mes anterior <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="number" name="consumo_anterior[]" min="0" readonly
                                        value="0"
                                        placeholder="Se calculará automáticamente"
                                        class="w-full bg-slate-100 border border-slate-200 text-slate-700 rounded-lg py-2.5 px-3.5 text-[13px] font-bold cursor-not-allowed input-consumo-anterior">
                                    <span class="block text-[10.5px] text-slate-400 mt-1 info-consumo-anterior">Calculado según despachos del mes anterior</span>
                                </div>

                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Cantidad en stock actualmente <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="number" name="stock_actual[]" min="0" required
                                        placeholder="Ej: 20"
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-semibold input-stock-actual">
                                    <span class="block text-[10.5px] text-teal-600 font-medium mt-1 hidden info-ultimo-stock"></span>
                                </div>

                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Cantidad Solicitada <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="number" name="cantidad_solicitada[]" min="1" required
                                        placeholder="Ej: 50"
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-semibold">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- seccion 3: observaciones -->
                <div class="p-6">

                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M8 10h8m-8 4h5m8-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>

                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">
                            3. Observaciones Generales
                        </h3>
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                            Observaciones de la Solicitud
                        </label>

                        <textarea name="observaciones" rows="4"
                            placeholder="Indique alguna observación, requerimiento especial o información adicional..."
                            class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px] resize-none"></textarea>
                    </div>
                </div>

                <!-- seccion de firmas digitales -->
                <div class="p-6 bg-slate-50/60">

                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>

                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">
                            4. Firma de Respaldo
                        </h3>
                    </div>

                    <!-- firma solicita (cesfam) -->
                    <div class="flex flex-col items-center">

                        <p class="text-[12px] font-semibold text-slate-600 mb-2">
                            Firma de quien solicita (CESFAM)
                        </p>

                        <input type="hidden" name="firma_recibe" id="firma_recibe_input">

                        <div
                            class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_recibe" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>

                        <button type="button" onclick="signatureRecibe.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors cursor-pointer">
                            Limpiar Firma
                        </button>
                    </div>
                </div>

                <!-- resumen -->
                <div class="p-6 bg-slate-50/40">

                    <div
                        class="bg-teal-50/80 border border-teal-200/80 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">

                        <div>
                            <h4 class="text-[14px] font-bold text-teal-900">
                                Resumen de Solicitud
                            </h4>

                            <p class="text-[12px] text-teal-600 mt-0.5">
                                Verifique los productos y cantidades antes de enviar.
                            </p>
                        </div>

                        <div class="flex items-center gap-6">

                            <div class="text-center">
                                <span class="block text-[11px] text-teal-600 uppercase font-semibold">
                                    Productos
                                </span>

                                <span id="total_productos" class="block text-lg font-bold text-teal-900">
                                    1
                                </span>
                            </div>

                            <div class="text-center">
                                <span class="block text-[11px] text-teal-600 uppercase font-semibold">
                                    Unidades
                                </span>

                                <span id="total_unidades" class="block text-lg font-bold text-teal-900">
                                    0
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- botones finales -->
                <div
                    class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">

                    <button type="reset" id="btn_limpiar"
                        class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-[13px] font-medium hover:bg-slate-100 transition-colors shadow-sm cursor-pointer">
                        Limpiar Formulario
                    </button>

                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 text-white rounded-lg text-[13px] font-semibold hover:bg-teal-700 flex items-center justify-center gap-2 transition-all shadow-sm cursor-pointer">

                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 13l4 4L19 7">
                            </path>
                        </svg>

                        Enviar Solicitud
                    </button>
                </div>

            </form>
        </div>
    </div>

    @include('requests.partials.inventory_modal', [
        'modalTitle' => 'Inventario de CESFAM (Solicitudes)',
        'modalSubtitle' => 'Directorio de medicamentos, insumos y estupefacientes declarados por los CESFAM en sus solicitudes.',
        'esVistaCesfam' => true,
    ])

    @include('requests.partials.pending_cesfam_modal')

    <!-- libreria para las firmas -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        let signatureRecibe = null;
        const consumoMapa = @json($consumoMapa ?? []);

        function obtenerNombreCompletoProducto(opt) {
            if (!opt) return '';
            return (opt.getAttribute('data-nombre-completo') || opt.getAttribute('title') || opt.textContent || '').trim();
        }

        // ---- Filtrado de productos según el Programa seleccionado ----
        const PRODUCTOS_POR_PROGRAMA = @json($productosPorPrograma ?? []);
        let OPCIONES_PRODUCTO_HTML = null;

        function guardarOpcionesProducto(select) {
            if (OPCIONES_PRODUCTO_HTML === null && select) {
                OPCIONES_PRODUCTO_HTML = select.innerHTML;
            }
        }

        function filtrarProductosPorPrograma(row) {
            if (!row || OPCIONES_PRODUCTO_HTML === null) return;

            const progSelect = row.querySelector('.select-programa');
            const prodSelect = row.querySelector('.select-producto');
            if (!progSelect || !prodSelect) return;

            const seleccionado = prodSelect.value;
            const prog = (progSelect.value || '').trim().toLowerCase();
            const permitidos = PRODUCTOS_POR_PROGRAMA[prog];
            const aviso = row.querySelector('.aviso-programa');

            // se parte siempre del listado completo
            prodSelect.innerHTML = OPCIONES_PRODUCTO_HTML;

            if (prog && Array.isArray(permitidos) && permitidos.length) {
                const permitidosSet = new Set(permitidos.map(String));

                prodSelect.querySelectorAll('optgroup').forEach(grupo => {
                    grupo.querySelectorAll('option').forEach(op => {
                        if (op.value && !permitidosSet.has(String(op.value))) {
                            op.remove();
                        }
                    });
                    if (!grupo.querySelector('option')) {
                        grupo.remove();
                    }
                });

                const n = prodSelect.querySelectorAll('optgroup option').length;

                if (n === 0) {
                    // El programa existe pero ninguno de sus productos aplica en esta vista:
                    // se vuelve al listado completo para no dejar el selector vacío.
                    prodSelect.innerHTML = OPCIONES_PRODUCTO_HTML;
                    if (aviso) {
                        aviso.textContent = 'Ningún producto de este programa está disponible aquí: se muestran todos.';
                        aviso.classList.remove('hidden');
                    }
                } else if (aviso) {
                    aviso.textContent = n === 1
                        ? '1 producto registrado en este programa'
                        : n + ' productos registrados en este programa';
                    aviso.classList.remove('hidden');
                }
            } else if (aviso) {
                if (prog) {
                    aviso.textContent = 'Este programa aún no tiene productos registrados: se muestran todos.';
                    aviso.classList.remove('hidden');
                } else {
                    aviso.textContent = '';
                    aviso.classList.add('hidden');
                }
            }

            // se conserva la selección anterior si el producto sigue disponible
            const sigueDisponible = seleccionado &&
                Array.from(prodSelect.options).some(op => op.value === seleccionado);

            if (sigueDisponible) {
                prodSelect.value = seleccionado;
            } else {
                prodSelect.selectedIndex = 0;
            }
        }

        function engancharFiltroPrograma(row) {
            const progSelect = row ? row.querySelector('.select-programa') : null;
            if (!progSelect || progSelect.dataset.filtroListo === '1') return;
            progSelect.dataset.filtroListo = '1';
            progSelect.addEventListener('change', function() {
                filtrarProductosPorPrograma(row);
            });
        }


        document.addEventListener('DOMContentLoaded', function() {

            const padRecibeElem = document.getElementById('pad_recibe');

            const padOptions = {
                penColor: 'rgb(15, 23, 42)',
                backgroundColor: 'rgba(255, 255, 255, 1)'
            };

            signatureRecibe = new SignaturePad(padRecibeElem, padOptions);

            const container = document.getElementById('productos_container');
            const establecimientoSelect = document.getElementById('establecimiento_select');

            function actualizarConsumoFila(itemRow) {
                const est = (establecimientoSelect?.value || '').trim();
                const prodSelect = itemRow.querySelector('.select-producto');
                const prodId = prodSelect ? prodSelect.value : '';
                const progSelect = itemRow.querySelector('select[name="programa[]"]');
                const inputConsumo = itemRow.querySelector('input[name="consumo_anterior[]"]');
                const infoConsumo = itemRow.querySelector('.info-consumo-anterior');
                const infoStock = itemRow.querySelector('.info-ultimo-stock');

                if (prodSelect && progSelect && prodSelect.selectedIndex >= 0 && !progSelect.value) {
                    const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
                    const progSugerido = (selectedOpt?.getAttribute('data-programa') || '').trim();
                    if (progSugerido) {
                        let matchedProg = Array.from(progSelect.options).find(
                            o => o.value && o.value.toLowerCase() === progSugerido.toLowerCase()
                        );
                        if (!matchedProg) {
                            const newOpt = document.createElement('option');
                            newOpt.value = progSugerido;
                            newOpt.textContent = progSugerido;
                            progSelect.appendChild(newOpt);
                            matchedProg = newOpt;
                        }
                        progSelect.value = matchedProg.value;
                    }
                }

                if (!inputConsumo) return;

                if (!est || !prodId) {
                    inputConsumo.value = 0;
                    if (infoConsumo) infoConsumo.textContent = 'Seleccione CESFAM y producto';
                    if (infoStock) {
                        infoStock.classList.add('hidden');
                        infoStock.textContent = '';
                    }
                    return;
                }

                const datos = (consumoMapa[est] && consumoMapa[est][prodId]) ? consumoMapa[est][prodId] : null;
                const consumo = datos ? (parseInt(datos.consumo_anterior) || 0) : 0;
                inputConsumo.value = consumo;

                if (infoConsumo) {
                    infoConsumo.textContent = consumo > 0
                        ? `Consumo anterior registrado: ${consumo} uds.`
                        : 'Sin despachos el mes anterior (0 uds.)';
                }

                if (infoStock) {
                    if (datos && datos.ultimo_stock !== null && datos.ultimo_stock !== undefined) {
                        infoStock.textContent = `Último stock declarado: ${datos.ultimo_stock} uds.`;
                        infoStock.classList.remove('hidden');
                    } else {
                        infoStock.classList.add('hidden');
                        infoStock.textContent = '';
                    }
                }
            }

            function actualizarTodosLosConsumos() {
                container.querySelectorAll('.producto-item').forEach(item => actualizarConsumoFila(item));
            }

            if (establecimientoSelect) {
                establecimientoSelect.addEventListener('change', actualizarTodosLosConsumos);
            }

            guardarOpcionesProducto(container.querySelector('.select-producto'));

            container.querySelectorAll('.select-producto').forEach(sel => {
                sel.addEventListener('change', function() {
                    const row = sel.closest('.producto-item');
                    if (row) actualizarConsumoFila(row);
                });
            });

            container.querySelectorAll('.producto-item').forEach(row => engancharFiltroPrograma(row));

            function actualizarResumen() {
                const items = container.querySelectorAll('.producto-item');
                let totalUnidades = 0;

                items.forEach(item => {
                    const cantidad_solicitada = parseInt(
                        item.querySelector('input[name="cantidad_solicitada[]"]').value
                    ) || 0;

                    totalUnidades += cantidad_solicitada;
                });

                document.getElementById('total_productos').innerText = items.length;
                document.getElementById('total_unidades').innerText = totalUnidades;
            }

            document.querySelectorAll('input[name="cantidad_solicitada[]"]').forEach(input => {
                input.addEventListener('input', actualizarResumen);
            });

            document.getElementById('btn_add_product').addEventListener('click', function() {

                const firstItem = container.querySelector('.producto-item');
                const clone = firstItem.cloneNode(true);

                clone.querySelectorAll('input').forEach(input => {
                    input.value = input.name === 'consumo_anterior[]' ? '0' : '';
                });

                clone.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                const infoStock = clone.querySelector('.info-ultimo-stock');
                if (infoStock) {
                    infoStock.classList.add('hidden');
                    infoStock.textContent = '';
                }

                const btnRemove = clone.querySelector('.btn-remove-product');

                btnRemove.classList.remove('hidden');

                btnRemove.addEventListener('click', function() {
                    clone.remove();
                    actualizarResumen();
                });

                const inputCant = clone.querySelector('input[name="cantidad_solicitada[]"]');
                if (inputCant) {
                    inputCant.addEventListener('input', actualizarResumen);
                }

                const selectProd = clone.querySelector('.select-producto');
                if (selectProd) {
                    if (OPCIONES_PRODUCTO_HTML !== null) {
                        selectProd.innerHTML = OPCIONES_PRODUCTO_HTML;
                        selectProd.selectedIndex = 0;
                    }
                    selectProd.addEventListener('change', function() {
                        actualizarConsumoFila(clone);
                    });
                }

                const avisoClone = clone.querySelector('.aviso-programa');
                if (avisoClone) {
                    avisoClone.textContent = '';
                    avisoClone.classList.add('hidden');
                }

                const progClone = clone.querySelector('.select-programa');
                if (progClone) {
                    delete progClone.dataset.filtroListo;
                }

                engancharFiltroPrograma(clone);

                container.appendChild(clone);
                actualizarConsumoFila(clone);
                actualizarResumen();
            });

            document.getElementById('btn_limpiar').addEventListener('click', function() {

                const items = container.querySelectorAll('.producto-item');

                for (let i = 1; i < items.length; i++) {
                    items[i].remove();
                }

                signatureRecibe.clear();

                setTimeout(() => {
                    actualizarTodosLosConsumos();
                    actualizarResumen();
                }, 10);
            });

            function mostrarSweetAlertSolicitudExitosa(solData, recargarAlCerrar = false) {
                if (!solData) return;

                const htmlDetail = `
                    <div class="text-left text-xs text-slate-700">
                        <div class="p-3.5 bg-teal-50/70 border border-teal-200 rounded-xl mb-3 space-y-1.5">
                            <div class="flex items-center justify-between pb-2 mb-1.5 border-b border-teal-200/80">
                                <span class="font-extrabold text-teal-900 text-sm">Solicitud N° ${solData.numero_solicitud || ''}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-300">SOLICITUD CESFAM REGISTRADA</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-[12px]">
                                <p><strong>Establecimiento:</strong> ${solData.establecimiento || 'N/A'}</p>
                                <p><strong>Tipo:</strong> ${solData.tipo || 'CESFAM'}</p>
                                <p><strong>Fecha Solicitud:</strong> ${solData.fecha_solicitud || 'N/A'}</p>
                                <p><strong>Responsable:</strong> ${solData.responsable || 'N/A'}${solData.cargo && solData.cargo !== 'N/A' ? ` (${solData.cargo})` : ''}</p>
                                <p><strong>Total Productos:</strong> ${solData.total_items || 0} producto(s)</p>
                                <p><strong>Total Unidades:</strong> <span class="font-bold text-teal-800">${Number(solData.total_unidades || 0).toLocaleString('es-CL')} unidades</span></p>
                                ${solData.observaciones ? `<p class="col-span-1 sm:col-span-2 text-slate-500 italic mt-0.5"><strong>Obs:</strong> ${solData.observaciones}</p>` : ''}
                            </div>
                        </div>

                        <p class="font-bold text-slate-700 mb-1.5 text-[11px] uppercase tracking-wide">Resumen de Productos Solicitados:</p>
                        <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-lg shadow-inner">
                            <table class="w-full text-[11.5px] text-left">
                                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 border-b border-slate-200">
                                    <tr>
                                        <th class="p-2">Producto</th>
                                        <th class="p-2 text-center">Programa</th>
                                        <th class="p-2 text-center">Consumo Ant.</th>
                                        <th class="p-2 text-center">Stock Act.</th>
                                        <th class="p-2 text-right">Cant. Solicitada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    ${(solData.items || []).map(it => `
                                        <tr class="hover:bg-slate-50">
                                            <td class="p-2 font-medium text-slate-800">${it.producto}</td>
                                            <td class="p-2 text-center text-slate-600 font-mono text-[11px]">${it.programa || 'General'}</td>
                                            <td class="p-2 text-center text-slate-500">${Number(it.consumo_anterior || 0).toLocaleString('es-CL')}</td>
                                            <td class="p-2 text-center text-slate-500">${Number(it.stock_actual || 0).toLocaleString('es-CL')}</td>
                                            <td class="p-2 text-right font-bold text-teal-700">${Number(it.cantidad_solicitada || 0).toLocaleString('es-CL')} u.</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: '¡Solicitud CESFAM Creada con Éxito!',
                    html: htmlDetail,
                    icon: 'success',
                    confirmButtonColor: '#0d9488',
                    confirmButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Aceptar y Finalizar',
                    width: '660px',
                    allowOutsideClick: false
                }).then(() => {
                    if (recargarAlCerrar) {
                        window.location.href = "{{ route('requests.cesfam') }}";
                    }
                });
            }

            @if (session('solicitud_success_detail'))
                mostrarSweetAlertSolicitudExitosa(@json(session('solicitud_success_detail')), false);
            @endif

            document.getElementById('requestForm').addEventListener('submit', function(e) {
                const form = this;

                if (signatureRecibe.isEmpty()) {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Firma obligatoria',
                        text: 'Debe firmar la solicitud antes de enviarla.',
                        confirmButtonColor: '#0d9488'
                    });

                    return;
                }

                if (!form.checkValidity()) {
                    return;
                }

                e.preventDefault();

                const estSelect = document.getElementById('establecimiento_select');
                const estNombre = estSelect && estSelect.selectedIndex >= 0 ? estSelect.options[estSelect.selectedIndex]?.text.trim() : 'CESFAM';
                const totalProds = document.getElementById('total_productos')?.textContent.trim() || '0';
                const totalUnits = document.getElementById('total_unidades')?.textContent.trim() || '0';

                // Resumen de respaldo por si el backend responde sin detail
                const fallbackItems = [];
                form.querySelectorAll('.producto-item').forEach(row => {
                    const selProd = row.querySelector('.select-producto');
                    const selProg = row.querySelector('.select-programa');
                    const inpCons = row.querySelector('input[name="consumo_anterior[]"]');
                    const inpStock = row.querySelector('input[name="stock_actual[]"]');
                    const inpCant = row.querySelector('input[name="cantidad_solicitada[]"]');
                    if (selProd && selProd.value) {
                        fallbackItems.push({
                            producto: obtenerNombreCompletoProducto(selProd.options[selProd.selectedIndex]) || 'Producto',
                            programa: selProg && selProg.value ? selProg.value : 'General',
                            consumo_anterior: parseInt(inpCons?.value) || 0,
                            stock_actual: parseInt(inpStock?.value) || 0,
                            cantidad_solicitada: parseInt(inpCant?.value) || 0
                        });
                    }
                });

                const fallbackDetail = {
                    numero_solicitud: form.querySelector('input[name="numero_solicitud"]')?.value || '',
                    establecimiento: estNombre,
                    tipo: 'CESFAM',
                    fecha_solicitud: form.querySelector('input[name="fecha_solicitud"]')?.value || '',
                    responsable: form.querySelector('input[name="responsable"]')?.value || 'N/A',
                    cargo: form.querySelector('input[name="cargo"]')?.value || 'N/A',
                    observaciones: form.querySelector('textarea[name="observaciones"]')?.value || '',
                    total_items: fallbackItems.length || parseInt(totalProds) || 0,
                    total_unidades: parseInt(totalUnits) || 0,
                    items: fallbackItems
                };

                Swal.fire({
                    title: '¿Confirmar Envío de Solicitud CESFAM?',
                    html: `Se emitirá la solicitud para <strong>${estNombre}</strong> con <strong>${totalProds}</strong> producto(s) y un total de <strong>${totalUnits}</strong> unidad(es).`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, enviar solicitud',
                    cancelButtonText: 'Revisar',
                    reverseButtons: true
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    document.getElementById('firma_recibe_input').value = signatureRecibe.toDataURL('image/png');

                    Swal.fire({
                        title: 'Registrando Solicitud CESFAM...',
                        text: 'Por favor espere mientras se guarda la solicitud en el sistema.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const contentType = response.headers.get('content-type') || '';

                        if (contentType.includes('application/json')) {
                            const data = await response.json();

                            if (response.ok && data.ok) {
                                mostrarSweetAlertSolicitudExitosa(data.detail || fallbackDetail, true);
                                return;
                            }

                            let errorMsg = data.message || 'Verifique los datos ingresados.';
                            if (data.errors) {
                                const firstKey = Object.keys(data.errors)[0];
                                if (firstKey && data.errors[firstKey][0]) {
                                    errorMsg = data.errors[firstKey][0];
                                }
                            }

                            Swal.fire({
                                title: 'No se pudo registrar la solicitud',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#e11d48',
                                confirmButtonText: 'Revisar formulario'
                            });
                        } else if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error('Error en la respuesta del servidor.');
                        }
                    } catch (err) {
                        Swal.fire({
                            title: 'Error al enviar solicitud',
                            text: 'Ocurrió un problema de conexión o validación. Intente nuevamente.',
                            icon: 'error',
                            confirmButtonColor: '#e11d48',
                            confirmButtonText: 'Entendido'
                        });
                    }
                });
            });

            actualizarResumen();

        });
    </script>
@endsection