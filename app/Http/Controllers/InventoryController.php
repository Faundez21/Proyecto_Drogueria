<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Mostrar el listado del inventario
    public function index(Request $request)
    {
        $query = Inventory::where(function ($q) {
            $q->where('estado', '!=', 'Cuarentena')
                ->orWhereNull('estado');
        });

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                    ->orWhere('lote', 'LIKE', "%{$search}%")
                    ->orWhere('razon_social', 'LIKE', "%{$search}%");
            });
        }

        $inventories = $query->latest()->paginate(10);

        return view('inventario.index', compact('inventories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'correlativo_interno' => 'nullable|integer',
            'fecha_recepcion' => 'required|date',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string',
            'orden_compra' => 'nullable|string',
            'origen_mercaderia' => 'required|string',
            'rut_proveedor' => 'required|string',
            'razon_social' => 'required|string',
            'producto' => 'required|string',
            'categoria' => 'required|string',
            'lote' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad' => 'required|integer|min:1',
            'valor_neto_unitario' => 'required|numeric|min:0',
        ]);

        $validated['estado'] = 'Disponible';

        Inventory::create($validated);

        return redirect()->route('inventario.index')->with('success', 'Producto registrado exitosamente.');
    }

    // Importar carga masiva
    public function import(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        return redirect()->route('inventario.index')->with('success', 'Archivo recibido correctamente.');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'estado' => 'required|in:Disponible,Cuarentena,Merma',
        ]);

        $inventory->update([
            'estado' => $request->estado,
        ]);

        return redirect()->back()
            ->with('success', 'El estado del lote fue actualizado correctamente.');
    }
}
