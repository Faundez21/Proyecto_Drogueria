<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasilloController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DistributionController;

// 1. Redirige la página principal exclusivamente al login
Route::redirect('/', '/login');

// 2. Rutas del Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

// 3. Ruta de recuperación
Route::get('/recuperar-password', function () {
    return "Página de recuperación de contraseña en construcción...";
})->name('password.request');

// 4. Ruta del dashboard
Route::get('/', function () {
    return view('welcome');
});



//5. Rutas de mantenedores

Route::resource('pasillos', PasilloController::class);


Route::resource('shelves', ShelfController::class);

Route::resource('levels', LevelController::class);

Route::resource('positions', PositionController::class);

//6.Ruta de distribución

Route::resource('distribution', DistributionController::class);

