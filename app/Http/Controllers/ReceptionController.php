<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceptionController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('name')->get();
        $products = Product::orderBy('nombre')->get();
        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();
        $nextCorrelativo = (int) Reception::max('correlativo') + 1;
        $controlados = InventoryService::controlados();

        return view('reception.index', compact('providers', 'receptions', 'products', 'nextCorrelativo', 'controlados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_recepcion' => 'required|date',
            'tipo_documento' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255',
            'orden_compra' => 'nullable|string|max:255',
            'origen' => 'required|string|max:255',
            'provider_id' => 'required|exists:providers,id',
            'firma_receptor' => 'nullable|string',
            'action' => 'nullable|string|in:save,save_and_download',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'cantidad_unitaria' => 'required|array|min:1',
            'cantidad_unitaria.*' => 'required|integer|min:1',
            'valor_unitario' => 'nullable|array',
            'valor_unitario.*' => 'nullable|numeric|min:0',
            'lote' => 'nullable|array',
            'lote.*' => 'nullable|string|max:255',
            'fecha_vencimiento' => 'nullable|array',
            'fecha_vencimiento.*' => 'nullable|date',
            'tipo_producto' => 'nullable|array',
            'tipo_producto.*' => 'nullable|string|max:255',
        ]);

        $provider = Provider::findOrFail($validated['provider_id']);

        DB::beginTransaction();

        try {
            $correlativo = (int) Reception::lockForUpdate()->max('correlativo') + 1;

            $reception = Reception::create([
                'correlativo' => $correlativo,
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'orden_compra' => $validated['orden_compra'],
                'origen' => $validated['origen'],
                'provider_id' => $validated['provider_id'],
                'valor_total_recepcion' => 0,
                'firma_receptor' => $validated['firma_receptor'],
            ]);

            $factorIva = 1 + config('drogueria.iva');
            $total_neto_recepcion = 0;

            foreach ($validated['product_id'] as $index => $prodId) {
                $product = Product::find($prodId);
                $nombreProducto = $product?->nombre ?? 'Producto #' . $prodId;

                $cantidad = (int) $validated['cantidad_unitaria'][$index];
                $valorU = (float) ($validated['valor_unitario'][$index] ?? 0);
                $subtotal = $cantidad * $valorU;
                $fechaVence = $validated['fecha_vencimiento'][$index] ?? null;

                $categoria = InventoryService::esControlado($nombreProducto)
                    ? 'Controlado'
                    : ($validated['tipo_producto'][$index] ?? 'General');

                $total_neto_recepcion += $subtotal;

                ReceptionItem::create([
                    'reception_id' => $reception->id,
                    'product_id' => $prodId,
                    'tipo_producto' => $categoria,
                    'lote' => $validated['lote'][$index] ?? null,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $valorU,
                    'valor_total_item' => $subtotal * $factorIva,
                ]);

                $batch = InventoryService::ingresar([
                    'user_id' => Auth::id(),
                    'product_id' => $prodId,
                    'correlativo_interno' => $correlativo,
                    'fecha_recepcion' => $validated['fecha_recepcion'],
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                    'orden_compra' => $validated['orden_compra'],
                    'origen_mercaderia' => $validated['origen'],
                    'rut_proveedor' => $provider->rut,
                    'razon_social' => $provider->name,
                    'producto' => $nombreProducto,
                    'categoria' => $categoria,
                    'estado' => 'Cuarentena',
                    'lote' => $validated['lote'][$index] ?? 'S/L',
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_neto_unitario' => $valorU,
                    'pasillo' => 'P.A',
                    'estante' => 'P.A',
                ]);

                InventoryService::registrarMovimiento($batch, [
                    'user_id' => Auth::id(),
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => $cantidad,
                    'fecha_hora' => now(),
                    'origen_destino' => 'Desde: ' . $provider->name,
                    'documento_respaldo' => $validated['tipo_documento'] . ' ' . $validated['numero_documento'],
                    'estado_lote' => 'Cuarentena',
                ]);
            }

            $reception->update(['valor_total_recepcion' => $total_neto_recepcion * $factorIva]);

            DB::commit();

            if (($validated['action'] ?? '') === 'save_and_download') {
                return redirect()->route('reception.pdf', $reception->id);
            }

            return redirect()
                ->route('reception.index')
                ->with('success', "Recepción guardada y registrada en trazabilidad. N°: {$correlativo}");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al guardar recepción: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Ocurrió un error al guardar la recepción. Intente nuevamente.')->withInput();
        }
    }

    public function downloadPdf(Reception $reception)
    {
        $reception->load(['provider', 'items.product']);
        $pdf = Pdf::loadView('reception.pdf', compact('reception'));

        return $pdf->download("recepcion_{$reception->numero_documento}.pdf");
    }
}
