<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index()
    {
        return view('distribution.index');
    }
}
