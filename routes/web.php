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

// 4. Rutas del Panel (Nombres asignados)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/recepcion', function () {
    return view('recepcion.index');
})->name('recepcion.index');

Route::get('/despacho', function () {
    return view('despacho.index');
})->name('despacho.index');

Route::get('/trazabilidad', function () {
    return view('trazabilidad.index');
})->name('trazabilidad.index');

Route::get('/proveedores', function () {
    return view('proveedores.index');
})->name('proveedores.index');

Route::get('/proveedores/crear', function () {
    return view('proveedores.create');
})->name('proveedores.create');

// Ruta para cerrar sesión (ejemplo necesario para tu botón inferior)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
