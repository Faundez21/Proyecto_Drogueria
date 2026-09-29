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

        //esta es lista de productos para el filtro
        $productosLista = Inventory::where(function ($q) {
            $q->where('estado', '!=', 'Cuarentena')
                ->orWhereNull('estado');
        })
            ->pluck('producto')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        // ---------------------------

        $inventories = $query->latest()->paginate(10)->withQueryString();
        return view('inventario.index', compact('inventories', 'productosLista'));
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
            'minimo' => 'nullable|integer|min:0',
            'valor_neto_unitario' => 'required|numeric|min:0',
        ]);

        $validated['estado'] = $request->input('estado', 'Disponible');
        $validated['minimo'] = $request->input('minimo', 0);

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
        $inventory->motivo = $request->input('motivo');
        $inventory->save();

        return redirect()->back()
            ->with('success', 'El estado del lote fue actualizado correctamente.');
    }
}
