<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

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
    return view('dashboard');
});
Route::get('/recepcion', function () {
    return view('recepcion.index');
});