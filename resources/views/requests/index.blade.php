@extends('layouts.app')

@section('title', 'Solicitudes')
@section('subtitle', 'Registro y gestión de solicitudes de establecimientos.')

@section('actions')
@role('Administrador')
    <button type="button"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded text-[13px] font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-all duration-150 w-full sm:w-auto">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Ver Historial
    </button>
@endrole
@endsection

@section('content')
    <!-- migas de pan -->
    <div class="flex items-center gap-2 text-[13px] text-slate-500 mb-6 px-1">
        <span class="text-slate-400 font-semibold">03</span>
        <span>Salida</span>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="font-semibold text-slate-800">Solicitudes</span>
    </div>

    <div class="space-y-6">

        <!-- notificacion de exito -->
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-teal-800 rounded-xl bg-teal-50 border border-teal-200 font-medium shadow-sm"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

            <!-- cabecera del formulario -->
            <div
                class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">
                        Registro de Solicitud
                    </h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">
                        Complete los datos del establecimiento y los productos requeridos.
                    </p>
                </div>
            </div>

            <!-- formulario principal -->
            <form action="{{ route('requests.store') }}" method="POST" class="divide-y divide-slate-100" id="requestForm">
                @csrf

                <!-- seccion 1: establecimiento -->
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011 1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>

                        <h3 class="text-[12.5px] font-bold text-slate-700 uppercase tracking-wider">
                            1. Datos del Establecimiento
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                        <div class="lg:col-span-2">
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                Establecimiento <span class="text-rose-500">*</span>
                            </label>

                            <select name="establecimiento" id="establecimiento_select" required onchange="actualizarProductosDisponibles()"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                <option value="" disabled selected>Seleccione establecimiento...</option>
                                <optgroup label="CESFAM (pueden solicitar estupefacientes)">
                                    @foreach ($establecimientos as $est)
                                        @if ($est['tipo'] === 'CESFAM')
                                            <option value="{{ $est['nombre'] }}">{{ $est['nombre'] }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                                <optgroup label="Otros establecimientos (solo medicamentos e insumos)">
                                    @foreach ($establecimientos as $est)
                                        @if ($est['tipo'] === 'Otros')
                                            <option value="{{ $est['nombre'] }}">{{ $est['nombre'] }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            </select>
                            <p id="establecimientoAviso" class="text-[11px] text-slate-400 mt-1 font-medium"></p>
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                Fecha de Solicitud <span class="text-rose-500">*</span>
                            </label>

                            <input type="date" name="fecha_solicitud" value="{{ date('Y-m-d') }}" required
                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg focus:bg-white focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                        </div>

                        <div>
                            <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                N° Solicitud
                            </label>

                            <input type="text" value="#00001" readonly
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

                            <input type="text" name="cargo" placeholder="Ej: Encargado de farmacia"
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
                                2. Productos Solicitados
                            </h3>
                        </div>

                        <button type="button" id="btn_add_product"
                            class="text-xs font-bold text-teal-600 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-md transition-colors border border-teal-200">
                            + Agregar Producto
                        </button>
                    </div>

                    <!-- contenedor dinamico -->
                    <div id="productos_container" class="space-y-4">

                        <div class="producto-item relative border border-slate-200 bg-white p-4 rounded-xl shadow-sm">

                            <button type="button"
                                class="btn-remove-product hidden absolute top-3 right-3 text-slate-400 hover:text-rose-600 transition-colors"
                                title="Eliminar fila">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 01-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Programa <span class="text-rose-500">*</span>
                                    </label>

                                    <select name="programa[]" required
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px]">
                                        <option value="" disabled selected>
                                            Seleccione...
                                        </option>
                                        <option value="Morbilidad General">
                                            Morbilidad General
                                        </option>
                                        <option value="Cardiovascular">
                                            Cardiovascular
                                        </option>
                                        <option value="IRA/ERA">
                                            IRA/ERA
                                        </option>
                                    </select>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Producto <span class="text-rose-500">*</span>
                                    </label>

                                    <select name="producto[]" required
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg focus:ring-2 focus:ring-teal-500/20 block py-2.5 px-3.5 text-[13px] select-producto">

                                        <option value="" disabled selected>
                                            Buscar producto...
                                        </option>

                                        <optgroup label="Medicamentos e Insumos" class="optgroup-normal">
                                            @foreach ($products as $product)
                                                @if (! in_array($product->nombre, $controlados))
                                                    <option value="{{ $product->id }}">
                                                        {{ $product->nombre }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Estupefacientes (solo CESFAM)" class="optgroup-controlado" disabled>
                                            @foreach ($products as $product)
                                                @if (in_array($product->nombre, $controlados))
                                                    <option value="{{ $product->id }}">
                                                        {{ $product->nombre }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>

                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Consumo mensual del mes anterior <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="number" name="consumo_anterior[]" min="0" readonly
                                        placeholder="Se calculará automáticamente"
                                        class="w-full bg-slate-100 border border-slate-200 text-slate-500 rounded-lg py-2.5 px-3.5 text-[13px] font-semibold cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                                        Cantidad en stock actualmente <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="number" name="stock_actual[]" min="1" required
                                        placeholder="Ej: 20"
                                        class="w-full bg-white border border-slate-200 text-slate-800 rounded-lg py-2.5 px-3.5 text-[13px] font-semibold">
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
                            placeholder="Indique alguna observacion, requerimiento especial o informacion adicional..."
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

                    <!-- firma solicita (establecimiento) -->
                    <div class="flex flex-col items-center">

                        <p class="text-[12px] font-semibold text-slate-600 mb-2">
                            Firma Quien Solicita (Establecimiento)
                        </p>

                        <input type="hidden" name="firma_recibe" id="firma_recibe_input">

                        <div
                            class="border-2 border-dashed border-teal-300 rounded-lg bg-white overflow-hidden shadow-sm w-full max-w-[400px]">
                            <canvas id="pad_recibe" width="400" height="150"
                                class="cursor-crosshair touch-none w-full"></canvas>
                        </div>

                        <button type="button" onclick="signatureRecibe.clear()"
                            class="mt-2 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">
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
                        class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-[13px] font-medium hover:bg-slate-100 transition-colors shadow-sm">
                        Limpiar Formulario
                    </button>

                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-teal-600 text-white rounded-lg text-[13px] font-semibold hover:bg-teal-700 flex items-center justify-center gap-2 transition-all shadow-sm">

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

    <!-- libreria para las firmas -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        // instancias de firmas para que sean globales en la vista
        let signatureRecibe = null;

        document.addEventListener('DOMContentLoaded', function() {

            // inicializar firma
            const padRecibeElem = document.getElementById('pad_recibe');

            const padOptions = {
                penColor: 'rgb(15, 23, 42)',
                backgroundColor: 'rgba(255, 255, 255, 1)'
            };

            signatureRecibe = new SignaturePad(padRecibeElem, padOptions);

            // Habilitar/Deshabilitar estupefacientes según el tipo de establecimiento
            window.actualizarProductosDisponibles = function() {
                const seleccion = document.getElementById('establecimiento_select').value;
                const esCesfam = seleccion.trim().toLowerCase().startsWith('cesfam ');

                const aviso = document.getElementById('establecimientoAviso');
                if (aviso) {
                    aviso.textContent = esCesfam
                        ? 'CESFAM: puede solicitar medicamentos, insumos y estupefacientes (estos últimos se despachan por el módulo de Despacho Controlado).'
                        : (seleccion ? 'Solo puede solicitar medicamentos e insumos. Los estupefacientes están restringidos a los CESFAM.' : '');
                    aviso.className = esCesfam
                        ? 'text-[11px] text-teal-600 mt-1 font-semibold'
                        : 'text-[11px] text-slate-400 mt-1 font-medium';
                }

                document.querySelectorAll('.select-producto').forEach(select => {
                    const grupo = select.querySelector('.optgroup-controlado');
                    if (!grupo) return;

                    grupo.disabled = !esCesfam;
                    grupo.style.display = esCesfam ? '' : 'none';

                    if (!esCesfam && grupo.selected) {
                        select.selectedIndex = 0;
                    }
                    if (!esCesfam) {
                        grupo.querySelectorAll('option').forEach(op => op.selected = false);
                    }
                });
            };

            window.actualizarProductosDisponibles();

            // contenedor de productos
            const container = document.getElementById('productos_container');

            // actualizar resumen
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

            // actualizar resumen al cambiar cantidades
            document.querySelectorAll('input[name="cantidad_solicitada[]"]').forEach(input => {
                input.addEventListener('input', actualizarResumen);
            });

            // agregar producto
            document.getElementById('btn_add_product').addEventListener('click', function() {

                const firstItem = container.querySelector('.producto-item');
                const clone = firstItem.cloneNode(true);

                clone.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                clone.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                const btnRemove = clone.querySelector('.btn-remove-product');

                btnRemove.classList.remove('hidden');

                btnRemove.addEventListener('click', function() {
                    clone.remove();
                    actualizarResumen();
                });

                clone.querySelector('input[name="cantidad[]"]')
                    .addEventListener('input', actualizarResumen);

                container.appendChild(clone);

                actualizarResumen();
            });

            // limpiar formulario
            document.getElementById('btn_limpiar').addEventListener('click', function() {

                const items = container.querySelectorAll('.producto-item');

                for (let i = 1; i < items.length; i++) {
                    items[i].remove();
                }

                signatureRecibe.clear();

                actualizarResumen();
            });

            // evitar envio mientras la vista es visual
            document.getElementById('requestForm').addEventListener('submit', function() {

                if (!signatureRecibe.isEmpty()) {
                    document.getElementById('firma_recibe_input').value =
                        signatureRecibe.toDataURL('image/png');
                }
            });

            actualizarResumen();

        });
    </script>
@endsection
