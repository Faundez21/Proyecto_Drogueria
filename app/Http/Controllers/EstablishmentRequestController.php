<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\EstablishmentRequest;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstablishmentRequestController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('nombre')->get();
        $controlados = InventoryService::controlados();
        $establecimientos = InventoryService::establecimientos();

        return view('requests.index', compact('products', 'controlados', 'establecimientos'));
    }

    public function store(Request $request)
    {
        $nombresEstablecimientos = array_column(InventoryService::establecimientos(), 'nombre');

        $validated = $request->validate([
            'establecimiento' => ['required', 'string', Rule::in($nombresEstablecimientos)],
            'fecha_solicitud' => 'required|date',
            'responsable' => 'required|string',
            'cargo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firma_recibe' => 'required|string',

            'producto' => 'required|array',
            'producto.*' => 'required|integer|exists:products,id',

            'programa' => 'nullable|array',
            'programa.*' => 'nullable|string',

            'consumo_anterior' => 'nullable|array',
            'consumo_anterior.*' => 'nullable|integer|min:0',

            'stock_actual' => 'required|array',
            'stock_actual.*' => 'required|integer|min:0',

            'cantidad_solicitada' => 'required|array',
            'cantidad_solicitada.*' => 'required|integer|min:1',
        ], [
            'firma_recibe.required' => 'Debes firmar la solicitud antes de enviarla.',
        ]);

        $esCesfam = InventoryService::esCesfam($validated['establecimiento']);

        // Solo los CESFAM pueden solicitar estupefacientes
        foreach ($validated['producto'] as $index => $productId) {
            $nombre = Product::find($productId)->nombre ?? '';
            $nombreProducto = $validated['producto'][$index];

            if (InventoryService::esControlado($nombre) && !$esCesfam) {
                return back()
                    ->with('error', "El establecimiento '{$validated['establecimiento']}' no puede solicitar estupefacientes. Solo los CESFAM tienen acceso.")
                    ->withInput();
            }
        }

        $establishmentRequest = EstablishmentRequest::create([
            'establecimiento' => $validated['establecimiento'],
            'fecha_solicitud' => $validated['fecha_solicitud'],
            'numero_solicitud' => '#00000',
            'responsable' => $validated['responsable'],
            'cargo' => $validated['cargo'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,
            'firma_solicitante' => $validated['firma_recibe'] ?? null,
            'estado' => 'Pendiente',
        ]);

        $establishmentRequest->update([
            'numero_solicitud' => '#' . str_pad($establishmentRequest->id, 5, '0', STR_PAD_LEFT),
        ]);

        foreach ($validated['producto'] as $index => $productId) {
            $establishmentRequest->items()->create([
                'product_id' => $productId,
                'consumo_anterior' => $validated['consumo_anterior'][$index] ?? null,
                'stock_actual' => $validated['stock_actual'][$index],
                'cantidad_solicitada' => $validated['cantidad_solicitada'][$index],
                'cantidad_despachada' => 0,
            ]);
        }

        return redirect()
            ->route('requests.index')
            ->with('success', "Solicitud registrada para {$validated['establecimiento']}. Los estupefacientes (si los hay) se despachan por el módulo de Despacho Controlado.");
    }
}
