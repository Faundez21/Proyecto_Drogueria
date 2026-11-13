<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Shelf;
use App\Models\Level;
use App\Models\Position;

class LevelController extends Controller
{
    // display a listing of the resource
    public function index()
    {
        $levels = Level::with('shelf.aisle')
            ->withCount('positions')
            ->orderBy('shelf_id')
            ->orderBy('number')
            ->get();

        $shelves = Shelf::with('aisle')->orderBy('name')->get();

        $counts = [
            'aisles' => Aisle::count(),
            'shelves' => $shelves->count(),
            'levels' => $levels->count(),
            'positions' => Position::count(),
        ];

        return view('distribution.maintainers.level.index', compact('levels', 'shelves', 'counts'));
    }

    // show the form for creating a new resource
    public function create()
    {
        // seccion
    }

    // store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'shelf_id' => 'required|exists:shelves,id',
        ]);

        if (Level::where('shelf_id', $request->shelf_id)->where('number', (int) $request->number)->exists()) {
            return redirect()->back()->withInput()->with('error', "Esa estantería ya tiene un Nivel {$request->number}.");
        }

        Level::create($request->all());


        return redirect()->route('level.index')->with('success', 'Nivel creado exitosamente.');
    }

    // display the specified resource
    public function show(string $id)
    {
        // seccion
    }

    // show the form for editing the specified resource
    public function edit(string $id)
    {
        // seccion
    }

    // update the specified resource in storage
    public function update(Request $request, string $id)
    {
        $level = Level::findOrFail($id);

        $request->validate([
            'number' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'shelf_id' => 'required|exists:shelves,id',
        ]);

        if (Level::where('id', '!=', $level->id)->where('shelf_id', $request->shelf_id)->where('number', (int) $request->number)->exists()) {
            return redirect()->back()->withInput()->with('error', "Esa estantería ya tiene un Nivel {$request->number}.");
        }

        $level->update($request->all());

        return redirect()->route('level.index')->with('success', 'Nivel editado correctamente.');


    }

    // remove the specified resource from storage
    public function destroy(string $id)
    {
        $level = Level::with('shelf')->findOrFail($id);

        $lotes = InventoryService::lotesEnUbicacion('nivel', $level);
        if ($lotes > 0) {
            return redirect()->route('level.index')->with('error', "No se puede eliminar el Nivel {$level->number} de la estantería «" . ($level->shelf->name ?? '') . "» porque todavía tiene {$lotes} lote(s) con stock. Primero reubica esos productos.");
        }

        $level->delete();

        return redirect()->route('level.index')->with('success', 'Nivel eliminado correctamente');
    }
}
