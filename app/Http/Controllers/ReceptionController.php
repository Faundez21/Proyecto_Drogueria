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
        foreach (InventoryService::controlados() as $ctrlNombre) {
            Product::firstOrCreate(['nombre' => $ctrlNombre]);
        }

        $providers = Provider::where('status', 'activo')->orderBy('name')->get();
        $products = Product::orderBy('nombre')->get();

        $categoriasInv = Inventory::select('producto', 'categoria')
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->get()
            ->pluck('categoria', 'producto');

        foreach ($products as $prod) {
            $catHist = $categoriasInv[$prod->nombre] ?? null;
            $prod->grupo_recepcion = InventoryService::clasificarGrupoProducto($prod->nombre, $catHist);
        }

        $productosAgrupados = [
            'Medicamentos' => $products->where('grupo_recepcion', 'Medicamentos')->values(),
            'Insumos' => $products->where('grupo_recepcion', 'Insumos')->values(),
            'Estupefacientes' => $products->where('grupo_recepcion', 'Estupefacientes')->values(),
        ];

        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();
        $nextCorrelativo = (int) Reception::max('correlativo') + 1;
        $controlados = InventoryService::controlados();

        // Extraer campos de distribución física
        $shelves = Shelf::with(['aisle', 'levels.positions'])->orderBy('name')->get();

        $pasillosInv = Inventory::whereNotNull('pasillo')->where('pasillo', '!=', '')->where('pasillo', '!=', 'P.A')->distinct()->pluck('pasillo');
        $pasillosMant = Aisle::orderBy('name')->pluck('name');
        $pasillosLista = $pasillosMant->merge($pasillosInv)->unique()->sort(SORT_NATURAL)->values();

        $estantesInv = Inventory::whereNotNull('estante')->where('estante', '!=', '')->where('estante', '!=', 'P.A')->distinct()->pluck('estante');
        $estantesMant = Shelf::orderBy('name')->pluck('name');
        $estantesLista = $estantesMant->merge($estantesInv)->unique()->sort(SORT_NATURAL)->values();

        $nivelesInv = Inventory::whereNotNull('nivel')->where('nivel', '!=', '')->distinct()->pluck('nivel')->map(fn ($v) => (string) $v);
        $nivelesMant = Level::distinct()->orderBy('number')->pluck('number')->map(fn ($v) => (string) $v);
        $nivelesLista = $nivelesMant->merge($nivelesInv)->unique()->sort(SORT_NATURAL)->values();

        $posicionesInv = Inventory::whereNotNull('posicion')->where('posicion', '!=', '')->distinct()->pluck('posicion');
        $posicionesMant = Position::distinct()->orderBy('name')->pluck('name');
        $posicionesLista = $posicionesMant->merge($posicionesInv)->unique()->sort(SORT_NATURAL)->values();

        $distribucionJerarquia = $shelves->map(function ($s) {
            return [
                'estante' => $s->name,
                'pasillo' => $s->aisle?->name ?? '',
                'niveles' => $s->levels->map(function ($lvl) {
                    return [
                        'nivel' => (string) $lvl->number,
                        'posiciones' => $lvl->positions->pluck('name')->values()->all(),
                    ];
                })->values()->all(),
            ];
        })->values();

        return view('reception.index', compact(
            'providers',
            'receptions',
            'products',
            'productosAgrupados',
            'nextCorrelativo',
            'controlados',
            'pasillosLista',
            'estantesLista',
            'nivelesLista',
            'posicionesLista',
            'distribucionJerarquia'
        ));
    }

    public function store(Request $request)
    {
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
            'fecha_vencimiento' => ['nullable', 'array'],
            'fecha_vencimiento.*' => ['nullable', 'date'],
            'tipo_producto' => ['nullable', 'array'],
            'tipo_producto.*' => ['nullable', 'string', 'max:255'],
            'programa' => ['nullable', 'array'],
            'programa.*' => ['nullable', 'string', 'max:255'],
            'pasillo' => ['nullable', 'array'],
            'pasillo.*' => ['nullable', 'string', 'max:50'],
            'estante' => ['nullable', 'array'],
            'estante.*' => ['nullable', 'string', 'max:50'],
            'nivel' => ['nullable', 'array'],
            'nivel.*' => ['nullable', 'string', 'max:100'],
            'posicion' => ['nullable', 'array'],
            'posicion.*' => ['nullable', 'string', 'max:100'],
        ]);

        $provider = Provider::findOrFail($validated['provider_id']);
        $nombreProveedor = $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor';

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
                $fechaVence = $validated['fecha_vencimiento'][$index] ?? null;
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