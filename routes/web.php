<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasilloController;
use App\Http\Controllers\LayoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('pasillos', PasilloController::class);

Route::resource('layout', LayoutController::class);
