<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasilloController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('pasillos', PasilloController::class);
