<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelf;

class DistributionController extends Controller
{
    public function index()
    {
        // Traemos todas las estanterías con su pasillo asignado
        $shelves = Shelf::with('aisle')->get();

        return view('distribution.index', compact('shelves'));
    }
}
