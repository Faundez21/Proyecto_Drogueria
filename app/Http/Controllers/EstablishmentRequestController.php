<?php

namespace App\Http\Controllers;

use App\Models\EstablishmentItem;
use App\Models\EstablishmentRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\SolicitudService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstablishmentRequestController extends Controller
{
    public function index()
    {
        $controlados = InventoryService::controlados();

        // Solo Medicamentos e Insumos (sin estupefacientes)
        $products = Product::whereNotIn('nombre', $controlados)
            ->orderBy('nombre')
            ->get();

        // Solo establecimientos que NO son CESFAM
        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') !== 'CESFAM'
        ));

        $nombresEstablecimientos = array_column($establecimientos, 'nombre');
        $nombresHistoricos = [
            'CECOSF Centinela',
            'CECOSF CENTINELA',
            'CECOSF Villa Centinela',
            'CECOSF VILLA CENTINELA',
            'CECOSF Los Lobos - La Gloria',
            'CECOSF LOS LOBOS - LA GLORIA',
            'CECOSF Los Lobos-La Gloria',
            'CECOSF LOS LOBOS-LA GLORIA',
            'Posta Tumbe',
            'POSTA TUMBE',
            'Posta Rural Tumbes',
            'POSTA RURAL TUMBES',
            'CECOSF Libertad Gaete',
            'CECOSF LIBERTAD GAETE',
            'CECOSF Libertad-Gaete',
            'CECOSF LIBERTAD-GAETE',
        ];
        $nombresEstablecimientosQuery = array_values(array_unique(array_merge($nombresEstablecimientos, $nombresHistoricos)));

        // Inventario propio de los Establecimientos registrado en las solicitudes (independiente del Inventario de Droguería)
        $inventarioSolicitudes = EstablishmentItem::with(['request', 'product'])
            ->whereHas('request', fn ($q) => $q->whereIn('establecimiento', $nombresEstablecimientosQuery))
            ->whereHas('product', fn ($q) => $q->whereNotIn('nombre', $controlados))
            ->orderByDesc('id')
            ->get();

        $productosLista = $inventarioSolicitudes
            ->map(fn ($item) => $item->product->nombre ?? null)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $programasLista = $inventarioSolicitudes
            ->pluck('programa')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $consumoMapa = SolicitudService::mapaConsumoYStock($nombresEstablecimientosQuery);

        return view('requests.index', compact(
            'products',
            'controlados',
            'establecimientos',
            'inventarioSolicitudes',
            'productosLista',
            'programasLista',
            'consumoMapa'
        ));
    }

    public function cesfam()
    {
        $controlados = InventoryService::controlados();

        // Medicamentos, insumos y estupefacientes
        $products = Product::orderBy('nombre')->get();

        // Solo establecimientos tipo CESFAM
        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') === 'CESFAM'
        ));

        $nombresCesfam = array_column($establecimientos, 'nombre');
        $nombresCesfamHistoricos = [
            'CESFAM Los Cerros',
            'CESFAM LOS CERROS',
            'CESFAM Paulina Avendaño',
            'CESFAM PAULINA AVENDAÑO',
            'CESFAM Paulina Avendaño Pereda',
            'CESFAM PAULINA AVENDAÑO PEREDA',
            'CESFAM San Vicente',
            'CESFAM SAN VICENTE',
            'CESFAM Leocan Portus',
            'CESFAM LEOCAN PORTUS',
            'CESFAM Alcalde Leocán Portus Govinden',
            'CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN',
        ];
        $nombresCesfamQuery = array_values(array_unique(array_merge($nombresCesfam, $nombresCesfamHistoricos)));

        // Inventario propio de los CESFAM registrado en las solicitudes (independiente del Inventario de Droguería)
        $inventarioSolicitudes = EstablishmentItem::with(['request', 'product'])
            ->whereHas('request', fn ($q) => $q->whereIn('establecimiento', $nombresCesfamQuery))
            ->orderByDesc('id')
            ->get();

        $productosLista = $inventarioSolicitudes
            ->map(fn ($item) => $item->product->nombre ?? null)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $programasLista = $inventarioSolicitudes
            ->pluck('programa')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $consumoMapa = SolicitudService::mapaConsumoYStock($nombresCesfamQuery);

        return view('requests.cesfam', compact(
            'products',
            'controlados',
            'establecimientos',
            'inventarioSolicitudes',
            'productosLista',
            'programasLista',
            'consumoMapa'
        ));
    }

    public function store(Request $request)
    {
        $nombresEstablecimientos = array_column(InventoryService::establecimientos(), 'nombre');

        $validated = $request->validate([
            'tipo_vista' => 'nullable|string|in:establecimientos,cesfam',
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
        foreach ($validated['producto'] as $productId) {
            $nombre = Product::find($productId)->nombre ?? '';

            if (InventoryService::esControlado($nombre) && (! $esCesfam || ($validated['tipo_vista'] ?? '') === 'establecimientos')) {
                return back()
                    ->with('error', "El establecimiento '{$validated['establecimiento']}' solo puede solicitar medicamentos e insumos no estupefacientes.")
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
            $consumoVal = $validated['consumo_anterior'][$index] ?? null;
            if ($consumoVal === null || $consumoVal === '') {
                $consumoVal = SolicitudService::calcularConsumoAnterior($validated['establecimiento'], (int) $productId);
            }

            $establishmentRequest->items()->create([
                'product_id' => $productId,
                'programa' => $validated['programa'][$index] ?? null,
                'consumo_anterior' => (int) $consumoVal,
                'stock_actual' => (int) $validated['stock_actual'][$index],
                'cantidad_solicitada' => (int) $validated['cantidad_solicitada'][$index],
                'cantidad_despachada' => 0,
            ]);
        }

        $rutaRetorno = ($validated['tipo_vista'] ?? '') === 'cesfam' || $esCesfam
            ? 'requests.cesfam'
            : 'requests.index';

        return redirect()
            ->route($rutaRetorno)
            ->with('success', "Solicitud {$establishmentRequest->numero_solicitud} registrada exitosamente para {$validated['establecimiento']}.");
    }

    /**
     * Permite al Administrador asignar o desasignar una solicitud a un trabajador desde Despacho,
     * recalculando automáticamente el estado de la solicitud (Pendiente <-> Asignada / Parcial / Despachado).
     */
    public function assign(Request $request, EstablishmentRequest $establishmentRequest)
    {
        $validated = $request->validate([
            'assigned_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $userId = $validated['assigned_user_id'] ?? null;

        $establishmentRequest->update([
            'assigned_user_id' => $userId,
            'assigned_at' => $userId ? now() : null,
        ]);

        $nuevoEstado = SolicitudService::actualizarEstado($establishmentRequest->id);
        $establishmentRequest->load('assignedUser');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $establishmentRequest->id,
                'numero_solicitud' => $establishmentRequest->numero_solicitud,
                'estado' => $nuevoEstado,
                'assigned_user_id' => $establishmentRequest->assigned_user_id,
                'assigned_user_name' => $establishmentRequest->assignedUser
                    ? trim($establishmentRequest->assignedUser->name . ' ' . ($establishmentRequest->assignedUser->last_name ?? ''))
                    : null,
                'assigned_at' => $establishmentRequest->assigned_at?->format('d/m/Y H:i'),
                'message' => $userId
                    ? "Solicitud {$establishmentRequest->numero_solicitud} asignada a " . trim($establishmentRequest->assignedUser->name . ' ' . ($establishmentRequest->assignedUser->last_name ?? '')) . '.'
                    : "Solicitud {$establishmentRequest->numero_solicitud} quedó sin asignar.",
            ]);
        }

        return back()->with(
            'success',
            $userId
                ? "Solicitud {$establishmentRequest->numero_solicitud} asignada correctamente."
                : "Asignación removida de la solicitud {$establishmentRequest->numero_solicitud}."
        );
    }
}
