<?php

namespace App\Http\Controllers;

use App\Models\Aisle;
use App\Models\Level;
use App\Models\Position;
use App\Models\Shelf;
use Illuminate\Http\Request;

class AisleController extends Controller
{
    public function index()
    {
        $aisles = Aisle::withCount('shelves')
            ->withSum('shelves', 'capacidad')
            ->orderBy('id')
            ->get();

        $counts = [
            'aisles' => $aisles->count(),
            'shelves' => Shelf::count(),
            'levels' => Level::count(),
            'positions' => Position::count(),
        ];

        return view('distribution.maintainers.aisle.index', compact('aisles', 'counts'));
    }


    public function create()
    {
        //dirección del menú de crear pasillo

    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);
        Aisle::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,

        ]);
        return redirect()->route('aisle.index')->with('success', 'Pasillo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    }


    public function edit(string $id)
    {
        return view('distribution.maintainers.aisle.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aisle = Aisle::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',

        ]);

        $aisle->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,

        ]);

        $aisle->inventories()->update(['pasillo' => $aisle->name]);

        return redirect()
            ->route('aisle.index')
            ->with('success', 'Pasillo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Aisle::destroy($id);

        return redirect()->route('aisle.index')->with('success', 'Pasillo eliminado exitosamente.');
    }
}
