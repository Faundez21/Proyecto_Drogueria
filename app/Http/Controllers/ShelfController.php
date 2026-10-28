<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Level;
use App\Models\Position;
use App\Models\Shelf;

class ShelfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shelves = Shelf::with('aisle')
            ->withCount('levels')
            ->orderBy('id')
            ->get();

        // traer todos los pasillos para el select del formulario
        $aisles = Aisle::orderBy('name')->get();

        $counts = [
            'aisles' => $aisles->count(),
            'shelves' => $shelves->count(),
            'levels' => Level::count(),
            'positions' => Position::count(),
        ];

        return view('distribution.maintainers.shelf.index', compact('shelves', 'aisles', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'aisle_id' => 'required|exists:aisles,id',
            'capacidad' => 'nullable|integer|min:0|max:1000000',
        ]);

        $datos = $request->only(['name', 'description', 'aisle_id', 'capacidad']);
        $datos['capacidad'] = (int) ($datos['capacidad'] ?? 0);

        Shelf::create($datos);

        return redirect()->route('shelf.index')->with('success', 'Estantería creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shelf = Shelf::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'aisle_id' => 'required|exists:aisles,id',
            'capacidad' => 'nullable|integer|min:0|max:1000000',
        ]);

        $datos = $request->only(['name', 'description', 'aisle_id', 'capacidad']);
        $datos['capacidad'] = (int) ($datos['capacidad'] ?? 0);

        $shelf->update($datos);

        $shelf->inventories()->update([
            'estante' => $shelf->name,
            'aisle_id' => $shelf->aisle_id,
        ]);

        return redirect()->route('shelf.index')->with('success', 'Estantería actualizada exitosamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Shelf::destroy($id);

        return redirect()->route('shelf.index')->with('success', 'Estantería eliminada correctamente');
    }
    public function updatePosition(Request $request, $id)
    {
        // Validamos que vengan coordenadas
        $request->validate([
            'pos_x' => 'required|numeric',
            'pos_z' => 'required|numeric',
        ]);

        // Buscamos la estantería y le actualizamos sus coordenadas
        $shelf = Shelf::findOrFail($id);
        $shelf->update([
            'pos_x' => $request->pos_x,
            'pos_z' => $request->pos_z
        ]);

        return response()->json(['success' => true, 'message' => 'Posición actualizada correctamente']);
    }
}
