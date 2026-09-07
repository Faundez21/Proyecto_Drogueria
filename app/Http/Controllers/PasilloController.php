<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasilloController extends Controller
{

    public function index()
    {
        //dirección del menú del mantenedor pasillo

        return view('pasillos.index');
    }


    public function create()
    {
        //dirección del menú de crear pasillo

        return view('pasillos.create');
    }


    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $pasillos = [
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

    $pasillo = collect($pasillos)->firstWhere('id', $id);

    return view('pasillos.show', compact('pasillo'));
}


    public function edit(string $id)
    {
        return view('pasillos.edit');
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
        //
    }
}
