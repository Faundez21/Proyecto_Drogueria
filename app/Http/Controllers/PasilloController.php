<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasilloController extends Controller
{

    public function index()
{
    // datos temporales

    $pasillos = [
        [
            'id' => 1,
            'nombre' => 'Pasillo A',
            'descripcion' => 'Medicamentos generales',
            'estado' => 'Disponible'
        ],
        [
            'id' => 2,
            'nombre' => 'Pasillo B',
            'descripcion' => 'Medicamentos refrigerados',
            'estado' => 'En uso'
        ],
        [
            'id' => 3,
            'nombre' => 'Pasillo C',
            'descripcion' => 'Medicamentos en cuarentena',
            'estado' => 'Cuarentena'
        ],
    ];

    return view('distribution.maintainers.pasillos.index', compact('pasillos'));
}


    public function create()
    {
        //dirección del menú de crear pasillo

        return view('distribution.maintainers.pasillos.create');
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

    return view('distribution.maintainers.pasillos.show', compact('pasillo'));
}


    public function edit(string $id)
    {
        return view('distribution.maintainers.pasillos.edit');
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
