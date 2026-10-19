<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Shelf;

class DistributionController extends Controller
{
    public function index()
    {
        $shelves = Shelf::with(['aisle', 'levels.positions'])->get();

        $stock = Inventory::where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->orderBy('producto')
            ->get([
                'producto',
                'lote',
                'estante',
                'nivel',
                'posicion',
                'programa',
                'cantidad',
                'fecha_vencimiento',
            ]);

        return view('distribution.index', compact('shelves', 'stock'));
    }
}
