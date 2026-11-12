<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;

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

        if (Aisle::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ya existe un pasillo llamado «" . trim($request->name) . "». Use otro nombre.");
        }

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

        if (Aisle::where('id', '!=', $aisle->id)->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ya existe otro pasillo llamado «" . trim($request->name) . "». Use otro nombre.");
        }

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
        $aisle = Aisle::findOrFail($id);

        $lotes = InventoryService::lotesEnUbicacion('pasillo', $aisle);
        if ($lotes > 0) {
            return redirect()->route('aisle.index')->with('error', "No se puede eliminar el pasillo «{$aisle->name}» porque todavía tiene {$lotes} lote(s) con stock guardados en sus estantes. Primero reubica esos productos.");
        }

        $aisle->delete();

        return redirect()->route('aisle.index')->with('success', 'Pasillo eliminado exitosamente.');
    }
}
