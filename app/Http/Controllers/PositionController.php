<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Position;
use App\Models\Level;
use App\Models\Shelf;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::with('level.shelf.aisle')
            ->orderBy('level_id')
            ->orderBy('name')
            ->get();
        $levels = Level::with('shelf.aisle')
            ->orderBy('shelf_id')
            ->orderBy('number')
            ->get();

        $counts = [
            'aisles' => Aisle::count(),
            'shelves' => Shelf::count(),
            'levels' => $levels->count(),
            'positions' => $positions->count(),
        ];

        return view(
            'distribution.maintainers.position.index',
            compact('positions', 'levels', 'counts')
        );
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
            'level_id' => 'required|exists:levels,id',
            'description' => 'nullable|string|max:255',
        ]);

        if (Position::where('level_id', $request->level_id)->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ese nivel ya tiene una posición llamada «" . trim($request->name) . "».");
        }

        Position::create($request->all());

        return redirect()->route('position.index')->with('success', 'Posición creada exitosamente.');
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
        $position = Position::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'description' => 'nullable|string|max:255',
        ]);

        if (Position::where('id', '!=', $position->id)->where('level_id', $request->level_id)->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ese nivel ya tiene una posición llamada «" . trim($request->name) . "».");
        }

        $position->update($request->all());


        return redirect()
            ->route('position.index')
            ->with('success', 'Posición actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = Position::with('level.shelf')->findOrFail($id);

        $lotes = InventoryService::lotesEnUbicacion('posicion', $position);
        if ($lotes > 0) {
            return redirect()->route('position.index')->with('error', "No se puede eliminar la posición «{$position->name}» porque todavía tiene {$lotes} lote(s) con stock. Primero reubica esos productos.");
        }

        $position->delete();

        return redirect()
            ->route('position.index')
            ->with('success', 'Posición eliminada exitosamente.');
    }
}
