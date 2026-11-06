<?php

namespace App\Http\Controllers;

use App\Models\Aisle;
use App\Models\Inventory;
use App\Models\Level;
use App\Models\Position;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Models\Shelf;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ReceptionController extends Controller
{
    public function index()
    {
        foreach (InventoryService::controladosOficiales() as $ctrlNombre) {
            Product::firstOrCreate(['nombre' => $ctrlNombre]);
        }

        // Sincronizar productos que hayan ingresado por Carga Masiva o Inventario Directo
        $productosEnInventario = Inventory::whereNotNull('producto')
            ->where('producto', '!=', '')
            ->distinct()
            ->pluck('producto');

        foreach ($productosEnInventario as $prodInvNombre) {
            Product::firstOrCreate(['nombre' => trim($prodInvNombre)]);
        }

        $providers = Provider::where('status', 'activo')->orderBy('name')->get();
        $products = Product::orderBy('nombre')->get();

        // Mapear categoría y programa desde Inventario (incluyendo Carga Masiva) e Historial de Recepciones
        $categoriasPorNombre = [];
        $categoriasPorId = [];
        $programasPorNombre = [];
        $programasPorId = [];

        $itemsRecHist = ReceptionItem::with('product:id,nombre')
            ->whereNotNull('tipo_producto')
            ->where('tipo_producto', '!=', '')
            ->orderBy('id', 'asc')
            ->get(['product_id', 'tipo_producto']);

        foreach ($itemsRecHist as $itemHist) {
            $catVal = trim((string) $itemHist->tipo_producto);
            if ($catVal === '' || mb_strtolower($catVal) === 'general') {
                continue;
            }
            if ($itemHist->product_id) {
                $categoriasPorId[(int) $itemHist->product_id] = $catVal;
            }
            if ($itemHist->product?->nombre) {
                $categoriasPorNombre[mb_strtolower(trim($itemHist->product->nombre))] = $catVal;
            }
        }

        $registrosInv = Inventory::select('product_id', 'producto', 'categoria', 'programa')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($registrosInv as $regInv) {
            $keyNombre = mb_strtolower(trim((string) $regInv->producto));
            $prodId = $regInv->product_id ? (int) $regInv->product_id : null;

            $catVal = trim((string) ($regInv->categoria ?? ''));
            if ($catVal !== '') {
                $esGeneral = mb_strtolower($catVal) === 'general';
                if ($keyNombre !== '' && (! $esGeneral || ! isset($categoriasPorNombre[$keyNombre]))) {
                    $categoriasPorNombre[$keyNombre] = $catVal;
                }
                if ($prodId && (! $esGeneral || ! isset($categoriasPorId[$prodId]))) {
                    $categoriasPorId[$prodId] = $catVal;
                }
            }

            $progVal = trim((string) ($regInv->programa ?? ''));
            if ($progVal !== '') {
                if ($keyNombre !== '') {
                    $programasPorNombre[$keyNombre] = $progVal;
                }
                if ($prodId) {
                    $programasPorId[$prodId] = $progVal;
                }
            }
        }

        foreach ($products as $prod) {
            $keyProd = mb_strtolower(trim($prod->nombre));
            $catHist = $categoriasPorId[(int) $prod->id] ?? ($categoriasPorNombre[$keyProd] ?? null);
            $progHist = $programasPorId[(int) $prod->id] ?? ($programasPorNombre[$keyProd] ?? '');

            $prod->grupo_recepcion = InventoryService::clasificarGrupoProducto($prod->nombre, $catHist);
            $prod->programa_sugerido = $progHist;
            $prod->categoria_sugerida = ($catHist && mb_strtolower($catHist) !== 'general')
                ? $catHist
                : ($prod->grupo_recepcion === 'Estupefacientes' ? 'Estupefacientes' : ($prod->grupo_recepcion === 'Insumos' ? 'Insumos' : 'Medicamentos'));
        }

        // Categorías base + todas las categorías existentes en Inventario y Carga Masiva agrupadas por clasificación
        $categoriasAgrupadas = [
            'Medicamentos' => [
                'Medicamentos' => 'MEDICAMENTOS GENERALES',
                'Fármacos' => 'FÁRMACOS',
                'F-FOFAR' => 'FOFAR (FARMACIA)',
                'F-PALIATIVOS' => 'CUIDADOS PALIATIVOS',
                'F-PARO' => 'CARRO DE PARO',
            ],
            'Insumos' => [
                'Insumos' => 'INSUMOS CLÍNICOS GENERALES',
                'Insumos Clínicos' => 'INSUMOS CLÍNICOS',
                'Equipamiento' => 'EQUIPAMIENTO',
                'I-CIRUGIA' => 'CIRUGÍA MENOR (INSUMOS)',
                'I-URGENCIA' => 'URGENCIA CIRUGÍA (INSUMOS)',
                'E-COMUNAL' => 'EQUIPAMIENTO COMUNAL',
            ],
            'Estupefacientes' => [
                'Estupefacientes' => 'ESTUPEFACIENTES / CONTROLADOS',
                'Fármacos Controlados' => 'FÁRMACOS CONTROLADOS',
            ],
        ];

        $categoriasEnBD = Inventory::whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->pluck('categoria')
            ->merge(
                ReceptionItem::whereNotNull('tipo_producto')
                    ->where('tipo_producto', '!=', '')
                    ->distinct()
                    ->pluck('tipo_producto')
            )
            ->map(fn ($c) => trim((string) $c))
            ->filter(fn ($c) => $c !== '' && mb_strtolower($c) !== 'general')
            ->unique();

        foreach ($categoriasEnBD as $catBD) {
            $yaExiste = false;
            foreach ($categoriasAgrupadas as $grp => $lista) {
                foreach ($lista as $valKey => $labelText) {
                    if (mb_strtolower($valKey) === mb_strtolower($catBD)) {
                        $yaExiste = true;
                        break 2;
                    }
                }
            }
            if (! $yaExiste) {
                $grupoDestino = InventoryService::clasificarGrupoProducto('', $catBD);
                $categoriasAgrupadas[$grupoDestino][$catBD] = mb_strtoupper($catBD);
            }
        }

        // Programas base + todos los programas existentes en Inventario y Carga Masiva
        $programasBase = collect([
            'Morbilidad General',
            'Fondo de Farmacia (FOFAR)',
            'Cardiovascular',
            'IRA/ERA',
            'Salud Mental',
        ]);
        $programasEnBD = Inventory::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->pluck('programa')
            ->map(fn ($p) => trim((string) $p))
            ->filter();
        $programasLista = $programasBase->merge($programasEnBD)->unique()->values();

        $productosAgrupados = [
            'Medicamentos' => $products->where('grupo_recepcion', 'Medicamentos')->values(),
            'Insumos' => $products->where('grupo_recepcion', 'Insumos')->values(),
            'Estupefacientes' => $products->where('grupo_recepcion', 'Estupefacientes')->values(),
        ];

        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();

        // Asociar el programa registrado en inventario a cada ítem de recepción para el historial
        $invPorRecepcion = Inventory::whereNotNull('reception_id')
            ->whereNotNull('programa')
            ->where('programa', '!=', '')
            ->get(['reception_id', 'product_id', 'lote', 'programa']);

        foreach ($receptions as $rec) {
            foreach ($rec->items as $rItem) {
                $match = $invPorRecepcion->first(function ($inv) use ($rec, $rItem) {
                    return (int) $inv->reception_id === (int) $rec->id
                        && (int) $inv->product_id === (int) $rItem->product_id;
                });
                $nomProd = mb_strtolower(trim($rItem->product?->nombre ?? ''));
                $rItem->setAttribute('programa', $match?->programa ?? ($programasPorId[(int) $rItem->product_id] ?? ($programasPorNombre[$nomProd] ?? null)));
            }
        }
        $nextCorrelativo = (int) Reception::max('correlativo') + 1;
        $controlados = InventoryService::controlados();

        // Extraer campos de distribución física y ubicaciones ocupadas
        $datosDist = InventoryService::datosDistribucionFisica();
        $pasillosLista = $datosDist['pasillosLista'];
        $estantesLista = $datosDist['estantesLista'];
        $nivelesLista = $datosDist['nivelesLista'];
        $posicionesLista = $datosDist['posicionesLista'];
        $distribucionJerarquia = $datosDist['distribucionJerarquia'];
        $ubicacionesOcupadas = $datosDist['ubicacionesOcupadas'];

        return view('reception.index', compact(
            'providers',
            'receptions',
            'products',
            'productosAgrupados',
            'categoriasAgrupadas',
            'programasLista',
            'nextCorrelativo',
            'controlados',
            'pasillosLista',
            'estantesLista',
            'nivelesLista',
            'posicionesLista',
            'distribucionJerarquia',
            'ubicacionesOcupadas'
        ));
    }

    public function store(Request $request)
    {
        // Normalizar campos opcionales para evitar cadenas vacías que fallen en validación date o inserción SQL
        if ($request->has('fecha_vencimiento') && is_array($request->fecha_vencimiento)) {
            $fechas = array_map(function ($val) {
                if (is_string($val)) {
                    $val = trim($val);
                    return $val === '' ? null : $val;
                }
                return $val ?: null;
            }, $request->fecha_vencimiento);
            $request->merge(['fecha_vencimiento' => $fechas]);
        }

        if ($request->has('orden_compra') && empty(trim((string)$request->orden_compra))) {
            $request->merge(['orden_compra' => null]);
        }

        $validated = $request->validate([
            'fecha_recepcion' => ['required', 'date'],
            'tipo_documento' => ['required', 'string', 'max:255'],
            'numero_documento' => ['required', 'string', 'max:255'],
            'orden_compra' => ['nullable', 'string', 'max:255'],
            'origen' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', Rule::exists('providers', 'id')->where('status', 'activo')],
            'firma_receptor' => ['nullable', 'string'],
            'action' => ['nullable', 'string', 'in:save,save_only,save_and_download'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'integer', 'exists:products,id'],
            'cantidad_unitaria' => ['required', 'array', 'min:1'],
            'cantidad_unitaria.*' => ['required', 'integer', 'min:1'],
            'valor_unitario' => ['nullable', 'array'],
            'valor_unitario.*' => ['nullable', 'numeric', 'min:0'],
            'lote' => ['nullable', 'array'],
            'lote.*' => ['nullable', 'string', 'max:255'],
            'fecha_vencimiento' => ['required', 'array', 'min:1'],
            'fecha_vencimiento.*' => ['required', 'date'],
            'tipo_producto' => ['nullable', 'array'],
            'tipo_producto.*' => ['nullable', 'string', 'max:500'],
            'programa' => ['nullable', 'array'],
            'programa.*' => ['nullable', 'string', 'max:500'],
            'pasillo' => ['nullable', 'array'],
            'pasillo.*' => ['nullable', 'string', 'max:50'],
            'estante' => ['nullable', 'array'],
            'estante.*' => ['nullable', 'string', 'max:50'],
            'nivel' => ['nullable', 'array'],
            'nivel.*' => ['nullable', 'string', 'max:100'],
            'posicion' => ['nullable', 'array'],
            'posicion.*' => ['nullable', 'string', 'max:100'],
        ], [
            'fecha_vencimiento.required' => 'Debes ingresar la fecha de vencimiento para cada producto.',
            'fecha_vencimiento.*.required' => 'La fecha de vencimiento es obligatoria para cada producto recepcionado.',
            'fecha_vencimiento.*.date' => 'La fecha de vencimiento ingresada no es válida.',
        ]);

        $provider = Provider::findOrFail($validated['provider_id']);
        $nombreProveedor = $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor';

        // Validar que ninguna ubicación física seleccionada esté ocupada en bodega ni duplicada entre productos distintos del acta
        $ubicacionesEnActa = [];
        foreach ($validated['product_id'] as $index => $prodId) {
            $estCheck = ! empty($validated['estante'][$index]) ? trim((string) $validated['estante'][$index]) : '';
            $nivCheck = ! empty($validated['nivel'][$index]) ? InventoryService::normalizarNivel($validated['nivel'][$index]) : '';
            $posCheck = ! empty($validated['posicion'][$index]) ? InventoryService::normalizarPosicion($validated['posicion'][$index]) : '';

            if ($estCheck === '' || strtoupper($estCheck) === 'P.A' || $nivCheck === '') {
                continue;
            }

            $productCheck = Product::find($prodId);
            $nomProdCheck = $productCheck?->nombre ?? ('Producto #' . $prodId);
            $loteCheck = ! empty($validated['lote'][$index]) ? trim((string) $validated['lote'][$index]) : 'S/L';

            $ocupanteBD = InventoryService::verificarUbicacionOcupada(
                $estCheck,
                $nivCheck,
                $posCheck,
                null,
                $nomProdCheck,
                $loteCheck
            );

            if ($ocupanteBD) {
                $detallePos = $posCheck !== '' ? ", Posición {$posCheck}" : '';
                $msgError = "La ubicación seleccionada para «{$nomProdCheck}» (Estante {$estCheck}, Nivel {$nivCheck}{$detallePos}) ya está ocupada en bodega por «{$ocupanteBD->producto}» (Lote: " . ($ocupanteBD->lote ?: 'S/L') . ").";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgError], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgError);
            }

            $slotKey = mb_strtolower($estCheck) . '|' . mb_strtolower($nivCheck) . '|' . mb_strtolower($posCheck);
            $prodLoteKey = mb_strtolower(trim($nomProdCheck)) . '|' . mb_strtolower((string) InventoryService::normalizarLote($loteCheck));
            if ($posCheck !== '' && isset($ubicacionesEnActa[$slotKey]) && $ubicacionesEnActa[$slotKey]['prod_lote'] !== $prodLoteKey) {
                $otro = $ubicacionesEnActa[$slotKey]['nombre'];
                $msgError = "No puedes asignar la misma ubicación (Estante {$estCheck}, Nivel {$nivCheck}, Posición {$posCheck}) a dos lotes distintos en la misma recepción («{$otro}» y «{$nomProdCheck}»).";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $msgError], 422);
                }

                return redirect()->back()->withInput()->with('error', $msgError);
            }
            $ubicacionesEnActa[$slotKey] = ['prod_lote' => $prodLoteKey, 'nombre' => $nomProdCheck];
        }

        DB::beginTransaction();

        try {
            $correlativo = (int) Reception::lockForUpdate()->max('correlativo') + 1;

            $reception = Reception::create([
                'correlativo' => $correlativo,
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'orden_compra' => $validated['orden_compra'] ?? null,
                'origen' => $validated['origen'],
                'provider_id' => $validated['provider_id'],
                'valor_total_recepcion' => 0,
                'firma_receptor' => $validated['firma_receptor'] ?? null,
            ]);

            $ivaConfig = (float) config('drogueria.iva', 0.19);
            $factorIva = 1 + ($ivaConfig > 1 ? ($ivaConfig / 100) : $ivaConfig);
            $total_neto_recepcion = 0;
            $itemsResumen = [];

            foreach ($validated['product_id'] as $index => $prodId) {
                $product = Product::find($prodId);
                $nombreProducto = $product?->nombre ?? 'Producto #' . $prodId;

                $cantidad = (int) $validated['cantidad_unitaria'][$index];
                $valorU = (float) ($validated['valor_unitario'][$index] ?? 0);
                $subtotal = $cantidad * $valorU;
                $subtotalIva = round($subtotal * $factorIva);
                $rawVence = $validated['fecha_vencimiento'][$index] ?? null;
                $fechaVence = (!empty($rawVence) && trim((string)$rawVence) !== '') ? trim((string)$rawVence) : null;
                $loteItem = !empty($validated['lote'][$index]) ? trim($validated['lote'][$index]) : 'S/L';

                $tipoSolicitado = $validated['tipo_producto'][$index] ?? null;
                $grupoProducto = InventoryService::clasificarGrupoProducto($nombreProducto, $tipoSolicitado);

                $categoriaRecepcion = $grupoProducto === 'Estupefacientes'
                    ? 'Estupefacientes'
                    : ($tipoSolicitado ?: $grupoProducto);

                $categoriaInventario = $grupoProducto === 'Estupefacientes'
                    ? 'Fármacos Controlados'
                    : ($tipoSolicitado ?: ($grupoProducto === 'Insumos' ? 'Insumos Clínicos' : 'Fármacos'));

                $total_neto_recepcion += $subtotal;

                ReceptionItem::create([
                    'reception_id' => $reception->id,
                    'product_id' => $prodId,
                    'tipo_producto' => $categoriaRecepcion,
                    'lote' => $loteItem,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $valorU,
                    'valor_total_item' => $subtotalIva,
                ]);

                $batch = InventoryService::ingresar([
                    'user_id' => Auth::id(),
                    'product_id' => $prodId,
                    'provider_id' => $provider->id,
                    'reception_id' => $reception->id,
                    'correlativo_interno' => $correlativo,
                    'fecha_recepcion' => $validated['fecha_recepcion'],
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                    'orden_compra' => $validated['orden_compra'] ?? null,
                    'origen_mercaderia' => $validated['origen'],
                    'rut_proveedor' => $provider->rut ?? null,
                    'razon_social' => $nombreProveedor,
                    'producto' => $nombreProducto,
                    'categoria' => $categoriaInventario,
                    'estado' => 'Disponible',
                    'lote' => $loteItem,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_neto_unitario' => $valorU,
                    'programa' => $validated['programa'][$index] ?? null,
                    'pasillo' => !empty($validated['pasillo'][$index]) ? trim($validated['pasillo'][$index]) : 'P.A',
                    'estante' => !empty($validated['estante'][$index]) ? trim($validated['estante'][$index]) : 'P.A',
                    'nivel' => !empty($validated['nivel'][$index]) ? trim($validated['nivel'][$index]) : null,
                    'posicion' => !empty($validated['posicion'][$index]) ? trim($validated['posicion'][$index]) : null,
                ]);

                InventoryService::registrarMovimiento($batch, [
                    'user_id' => Auth::id(),
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => $cantidad,
                    'fecha_hora' => now(),
                    'origen_destino' => 'Desde: ' . $nombreProveedor,
                    'documento_respaldo' => $validated['tipo_documento'] . ' ' . $validated['numero_documento'],
                    'estado_lote' => 'Disponible',
                ]);

                $pasVal = !empty($validated['pasillo'][$index]) ? trim($validated['pasillo'][$index]) : '';
                $estVal = !empty($validated['estante'][$index]) ? trim($validated['estante'][$index]) : '';
                $nivVal = !empty($validated['nivel'][$index]) ? trim($validated['nivel'][$index]) : '';

                $ubicacionTexto = 'P.A';
                if ($estVal && $estVal !== 'P.A') {
                    $ubicacionTexto = ($pasVal && $pasVal !== 'P.A') ? "{$pasVal} / {$estVal}" : $estVal;
                    if ($nivVal) $ubicacionTexto .= " (N: {$nivVal})";
                } elseif ($pasVal && $pasVal !== 'P.A') {
                    $ubicacionTexto = $pasVal;
                }

                $itemsResumen[] = [
                    'producto' => $nombreProducto,
                    'lote' => $loteItem,
                    'vencimiento' => !empty($fechaVence) ? date('d/m/Y', strtotime($fechaVence)) : 'N/A',
                    'cantidad' => (int) $cantidad,
                    'tipo' => $categoriaInventario,
                    'programa' => !empty($validated['programa'][$index]) ? trim($validated['programa'][$index]) : ($batch->programa ?: 'Sin programa'),
                    'subtotal_iva' => $subtotalIva,
                    'ubicacion' => $ubicacionTexto,
                ];
            }

            $valorTotalConIva = round($total_neto_recepcion * $factorIva);
            $reception->update(['valor_total_recepcion' => $valorTotalConIva]);

            DB::commit();

            $receptionDetail = [
                'correlativo' => $correlativo,
                'fecha_recepcion' => date('d/m/Y', strtotime($validated['fecha_recepcion'])),
                'origen' => $validated['origen'],
                'orden_compra' => !empty($validated['orden_compra']) ? $validated['orden_compra'] : 'N/A',
                'proveedor' => $nombreProveedor,
                'rut_proveedor' => $provider->rut ?? 'N/A',
                'documento' => $validated['tipo_documento'] . ' N° ' . $validated['numero_documento'],
                'total_items' => count($itemsResumen),
                'total_unidades' => array_sum(array_column($itemsResumen, 'cantidad')),
                'valor_total' => $valorTotalConIva,
                'items' => $itemsResumen,
                'pdf_url' => route('reception.pdf', $reception->id),
                'auto_download' => ($validated['action'] ?? '') === 'save_and_download',
            ];

            if ($request->expectsJson() or $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => "Recepción guardada y registrada en trazabilidad. N°: {$correlativo}",
                    'detail' => $receptionDetail,
                ]);
            }

            return redirect()
                ->route('reception.index')
                ->with('success', "Recepción guardada y registrada en trazabilidad. N°: {$correlativo}")
                ->with('reception_success_detail', $receptionDetail);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al guardar recepción: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() or $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ocurrió un error al guardar la recepción: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Ocurrió un error al guardar la recepción. Intente nuevamente.')
                ->withInput();
        }
    }

    public function downloadPdf(Reception $reception)
    {
        $reception->load(['provider', 'items.product']);
        $pdf = Pdf::loadView('reception.pdf', compact('reception'));

        return $pdf->download("recepcion_{$reception->numero_documento}.pdf");
    }
}