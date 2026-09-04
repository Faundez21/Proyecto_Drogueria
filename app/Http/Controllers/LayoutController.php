<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        //dirección del menú del layout
        return view('layout.index');
    }
}
