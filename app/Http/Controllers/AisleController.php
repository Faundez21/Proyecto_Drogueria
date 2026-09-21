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

        return view('distribution.maintainers.aisle.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        Aisle::create($request->all());
        return redirect()->route('aisle.index')->with('success', 'Pasillo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $aisles = [
            [
                'id' => 1,
                'nombre' => 'Pasillo A',
                'descripcion' => 'Medicamentos generales'
            ],
            [
                'id' => 2,
                'nombre' => 'Pasillo B',
                'descripcion' => 'Medicamentos refrigerados'
            ],
            [
                'id' => 3,
                'nombre' => 'Pasillo C',
                'descripcion' => 'Medicamentos en cuarentena'
            ],
        ];

        $aisle = collect($aisles)->firstWhere('id', $id);

        return view('distribution.maintainers.aisle.show', compact('aisle'));
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
        //
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
