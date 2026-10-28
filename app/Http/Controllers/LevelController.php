<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Shelf;
use App\Models\Level;
use App\Models\Position;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            'number' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'shelf_id' => 'required|exists:shelves,id',
        ]);

        Level::create($request->all());


        return redirect()->route('level.index')->with('success', 'Nivel creado exitosamente.');
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
        $level = Level::findOrFail($id);

        $request->validate([
            'number' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'shelf_id' => 'required|exists:shelves,id',
        ]);

        $level->update($request->all());

        return redirect()->route('level.index')->with('success', 'Nivel editado correctamente.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Level::destroy($id);

        return redirect()->route('level.index')->with('success', 'Nivel eliminado correctamente');
    }
}
