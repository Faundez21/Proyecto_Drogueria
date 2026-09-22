<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Level;
class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::with('level')->get();
        $levels = Level::all();

        return view(
            'distribution.maintainers.position.index',
            compact('positions', 'levels')
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
        Position::destroy($id);

        return redirect()
            ->route('position.index')
            ->with('success', 'Posición eliminada exitosamente.');
    }
}
