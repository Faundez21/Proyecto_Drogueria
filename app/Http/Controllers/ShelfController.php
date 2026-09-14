<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Shelf;
class ShelfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shelves = Shelf::with('aisle')->get();

        // traer todos los pasillos para el select del formulario
        $aisles = Aisle::all();

        return view('distribution.maintainers.shelf.index', compact('shelves', 'aisles'));

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
        ]);

        Shelf::create($request->all());

        return redirect()->route('shelf.index') ->with('success', 'Estantería creada exitosamente.');
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
        ]);

        $shelf->update($request->all());

        return redirect()->route('shelf.index') ->with('success', 'Estantería actualizada exitosamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Shelf::destroy($id);

        return redirect()->route('shelf.index') ->with('success','Estantería eliminada correctamente');
    }
}
