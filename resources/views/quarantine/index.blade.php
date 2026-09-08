@extends('layouts.app')
@section('title', 'Control de Cuarentena - Droguería')

@section('content')

    {{-- 1. CABECERA --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Área de Cuarentena</h1>
            <p class="text-sm text-slate-500">Gestión de lotes retenidos, vencidos, averiados o en control de calidad.</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-50 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Exportar Reporte
            </button>
            <button onclick="abrirModalNuevo()" class="flex-1 sm:flex-none items-center justify-center gap-2 bg-amber-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-600 flex transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Ingresar a Cuarentena
            </button>
        </div>
    </div>

    {{-- 2. TARJETAS DE INDICADORES (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Retenidos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total en Cuarentena</p>
                <p class="text-2xl font-bold text-slate-900">1,245 <span class="text-xs font-normal text-slate-400">unidades</span></p>
            </div>
        </div>

        <!-- Control de Calidad -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Control de Calidad</p>
                <p class="text-2xl font-bold text-slate-900">450 <span class="text-xs font-normal text-slate-400">unidades</span></p>
            </div>
        </div>

        <!-- Próximos a Vencer / Vencidos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Vencidos / Averías</p>
                <p class="text-2xl font-bold text-slate-900">620 <span class="text-xs font-normal text-slate-400">unidades</span></p>
            </div>
        </div>

        <!-- Alertas Sanitarias (Recalls) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Alertas (Recall)</p>
                <p class="text-2xl font-bold text-slate-900">175 <span class="text-xs font-normal text-slate-400">unidades</span></p>
            </div>
        </div>
    </div>

    {{-- 3. FILTROS Y TABLA DE CUARENTENA --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Barra de Filtros -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Buscar por Lote, Medicamento o SKU..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
            </div>
            <div>
                <select class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Todos los Motivos</option>
                    <option value="vencimiento">Vencimiento</option>
                    <option value="averia">Avería / Daño</option>
                    <option value="calidad">Control de Calidad</option>
                    <option value="recall">Alerta Sanitaria (Recall)</option>
                </select>
            </div>
            <div>
                <select class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Estado Actual</option>
                    <option value="retenido">Retenido / Pendiente</option>
                    <option value="liberado">Liberado (Devuelto a Stock)</option>
                    <option value="destruccion">Marcado para Destrucción</option>
                </select>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Medicamento / Lote</th>
                        <th class="px-5 py-4 font-semibold">Cantidad</th>
                        <th class="px-5 py-4 font-semibold">Motivo Retención</th>
                        <th class="px-5 py-4 font-semibold">Fecha Ingreso</th>
                        <th class="px-5 py-4 font-semibold">Estado</th>
                        <th class="px-5 py-4 font-semibold text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Fila 1: Control de Calidad -->
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">💊</div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">Amoxicilina 500mg</p>
                                    <p class="text-[11px] text-slate-500">Lote: <span class="font-mono text-slate-700">L-AMX-442</span> • Exp: 05/2026</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs font-semibold text-slate-700">450 Cajas</p>
                            <p class="text-[10px] text-slate-400">Estuche x 50</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Control de Calidad
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-slate-700">01 Sep 2026</p>
                            <p class="text-[10px] text-slate-400">Por: QA Dept.</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                ⏳ En Evaluación
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button onclick="abrirModalResolucion('Amoxicilina 500mg', 'L-AMX-442', 'Control de Calidad')" class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
                                Resolver
                            </button>
                        </td>
                    </tr>

                    <!-- Fila 2: Vencido -->
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center shrink-0">⚠️</div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">Ibuprofeno 400mg</p>
                                    <p class="text-[11px] text-slate-500">Lote: <span class="font-mono text-slate-700">L-IBU-990</span> • Exp: <span class="text-red-500 font-bold">08/2026</span></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs font-semibold text-slate-700">120 Cajas</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Producto Vencido
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-slate-700">01 Sep 2026</p>
                            <p class="text-[10px] text-slate-400">Por: Sistema Aut.</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[11px] font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                🗑️ Pend. Destrucción
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button onclick="abrirModalResolucion('Ibuprofeno 400mg', 'L-IBU-990', 'Vencimiento')" class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
                                Gestionar
                            </button>
                        </td>
                    </tr>

                    <!-- Fila 3: Alerta Sanitaria (Recall) -->
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center shrink-0">📢</div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">Losartán Potásico 50mg</p>
                                    <p class="text-[11px] text-slate-500">Lote: <span class="font-mono text-slate-700">L-LOS-001</span> • Exp: 12/2027</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs font-semibold text-slate-700">175 Cajas</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Recall / Alerta INVIMA
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-slate-700">05 Sep 2026</p>
                            <p class="text-[10px] text-slate-400">Por: Dir. Técnica</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                                🛑 Retenido Fab.
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button onclick="abrirModalResolucion('Losartán Potásico 50mg', 'L-LOS-001', 'Alerta Sanitaria')" class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
                                Gestionar
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        
        <!-- Paginación Simple -->
        <div class="p-4 border-t border-slate-100 flex justify-between items-center bg-white text-sm">
            <span class="text-slate-500">Mostrando <span class="font-medium text-slate-800">1</span> a <span class="font-medium text-slate-800">3</span> de <span class="font-medium text-slate-800">12</span> registros</span>
            <div class="flex gap-1">
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed">Anterior</button>
                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50">Siguiente</button>
            </div>
        </div>
    </div>


    {{-- 4. MODAL DE RESOLUCIÓN DE CUARENTENA (Oculto por defecto) --}}
    <div id="modal-resolucion" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="cerrarModal()"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <!-- Panel del Modal -->
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">Resolución de Cuarentena</h3>
                                
                                <div class="mt-4 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4">
                                    <p class="text-xs text-slate-500 mb-1">Medicamento y Lote afectado:</p>
                                    <p class="text-sm font-bold text-slate-800" id="modal-med-name">--</p>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-slate-600 font-mono" id="modal-med-lote">--</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700" id="modal-med-motivo">--</span>
                                    </div>
                                </div>

                                <form id="formResolucion">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Acción a tomar</label>
                                        <div class="grid grid-cols-1 gap-2">
                                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                                <input type="radio" name="accion_cuarentena" value="liberar" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-600">
                                                <span class="ml-3 block">
                                                    <span class="block text-sm font-medium text-slate-900">✅ Liberar Lote</span>
                                                    <span class="block text-xs text-slate-500">Cumple requisitos, volver al inventario disponible.</span>
                                                </span>
                                            </label>
                                            <label class="flex items-center p-3 border border-red-200 bg-red-50/30 rounded-lg cursor-pointer hover:bg-red-50">
                                                <input type="radio" name="accion_cuarentena" value="destruir" class="w-4 h-4 text-red-600 border-red-300 focus:ring-red-600">
                                                <span class="ml-3 block">
                                                    <span class="block text-sm font-medium text-red-800">🗑️ Enviar a Destrucción</span>
                                                    <span class="block text-xs text-red-500/80">Genera acta de baja por vencimiento o daño severo.</span>
                                                </span>
                                            </label>
                                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                                <input type="radio" name="accion_cuarentena" value="devolver" class="w-4 h-4 text-slate-600 border-slate-300 focus:ring-slate-600">
                                                <span class="ml-3 block">
                                                    <span class="block text-sm font-medium text-slate-900">📦 Devolución a Proveedor</span>
                                                    <span class="block text-xs text-slate-500">Por recall, alerta técnica o acuerdo comercial.</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones Técnicas (Opcional)</label>
                                        <textarea rows="2" placeholder="Justificación de la decisión tomada..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:outline-none"></textarea>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                        <button type="button" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">Guardar Resolución</button>
                        <button type="button" onclick="cerrarModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Lógica Simple para el Modal de Resolución
    const modal = document.getElementById('modal-resolucion');
    
    function abrirModalResolucion(medicamento, lote, motivo) {
        // Llenar datos dinámicos en el modal
        document.getElementById('modal-med-name').textContent = medicamento;
        document.getElementById('modal-med-lote').textContent = `Lote: ${lote}`;
        document.getElementById('modal-med-motivo').textContent = motivo;
        
        // Limpiar selecciones previas
        document.querySelectorAll('input[name="accion_cuarentena"]').forEach(radio => radio.checked = false);
        
        // Mostrar Modal
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    // Cerrar modal con la tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarModal();
        }
    });

    // Función placeholder para el botón de "Ingresar a Cuarentena"
    function abrirModalNuevo() {
        alert("Aquí se abriría un formulario para escanear el QR y enviar un lote a Cuarentena.");
    }
</script>
@endsection