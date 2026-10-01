<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\EstablishmentRequest;
use Illuminate\Http\Request;

class EstablishmentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('nombre')->get();

        return view('requests.index', compact('products'));
    }
    public function show(EstablishmentRequest $establishmentRequest)
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'establecimiento' => 'required|string',
            'fecha_solicitud' => 'required|date',
            'responsable' => 'required|string',
            'cargo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firma_recibe' => 'nullable|string',

            'producto' => 'required|array',
            'producto.*' => 'required|integer|exists:products,id',

            'consumo_anterior' => 'nullable|array',
            'consumo_anterior.*' => 'nullable|integer|min:0',

            'stock_actual' => 'required|array',
            'stock_actual.*' => 'required|integer|min:0',

            'cantidad_solicitada' => 'required|array',
            'cantidad_solicitada.*' => 'required|integer|min:1',
        ]);

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
            ->with('success', 'Solicitud registrada exitosamente.');
    }
    public function edit(EstablishmentRequest $establishmentRequest)
    {
        //
    }

    public function update(Request $request, EstablishmentRequest $establishmentRequest)
    {
        //
    }

    public function destroy(EstablishmentRequest $establishmentRequest)
    {
        //
    }
}
