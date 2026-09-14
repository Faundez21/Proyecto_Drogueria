<?php

namespace App\Http\Controllers;
use App\Models\Aisle;
use Illuminate\Http\Request;

class AisleController extends Controller
{

    public function index()
    {
        // datos temporales
        $aisles = Aisle::all();

        return view('distribution.maintainers.aisle.index', compact('aisles'));
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
        ]);
        Aisle::create([
            'name' => $request->name,
            'description' => $request->description,
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
        ]);

        $aisle->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

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
