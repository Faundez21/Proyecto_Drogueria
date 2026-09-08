<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasilloController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DistributionController;


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
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
    return view('dashboard.index');
})->name('dashboard.index');

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

Route::get('/inventario', function () {
        return view('inventario.index');
    })->name('inventario.index');

    Route::get('/reportes', function () {
        return view('reportes.index');
    })->name('reportes.index');

Route::get('/error', function () {
    return view('error');
});



//5. Rutas de mantenedores

Route::resource('pasillos', PasilloController::class);


Route::resource('shelves', ShelfController::class);

Route::resource('levels', LevelController::class);

Route::resource('positions', PositionController::class);

//6.Ruta de distribución

Route::resource('distribution', DistributionController::class);
// Ruta para cerrar sesión (ejemplo necesario para tu botón inferior)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
