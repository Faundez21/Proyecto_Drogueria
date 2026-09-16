<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AisleController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\QuarantineController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\ProviderController; // <-- Importación del nuevo controlador

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

// middelware auth para proteger las rutas del panel
Route::middleware('auth')->group(function () {

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

// --- RUTAS DE PROVEEDORES ---
    Route::get('/proveedores', [ProviderController::class, 'index'])->name('providers.index');
    Route::get('/proveedores/crear', [ProviderController::class, 'create'])->name('providers.create');
    Route::post('/proveedores', [ProviderController::class, 'store'])->name('providers.store');
    Route::put('/proveedores/{provider}', [ProviderController::class, 'update'])->name('providers.update');
    Route::patch('/proveedores/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])->name('providers.toggle-status');
    // Dejo la ruta de crear por si la llegas a necesitar en otra parte, aunque ahora funcione con modales
    Route::get('/providers/crear', function () {
        return view('providers.create');
    })->name('providers.create');
    // -----------------------------------

    Route::get('/inventario', function () {
        return view('inventario.index');
    })->name('inventario.index');

    Route::get('/reportes', function () {
        return view('reportes.index');
    })->name('reportes.index');

    Route::get('/error', function () {
        return view('error');
    });

    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    //6.Ruta de distribución
    Route::resource('distribution', DistributionController::class);
    
    // Ruta para cerrar sesión (ejemplo necesario para tu botón inferior)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    //5. Rutas de mantenedores
    Route::resource('aisle', AisleController::class);

    Route::resource('shelf', ShelfController::class);

    Route::resource('level', LevelController::class);

    Route::resource('position', PositionController::class);

    //6.Ruta de distribución (Estaba repetida en tu código original, la mantengo tal cual)
    Route::resource('distribution', DistributionController::class);

    //7.Ruta de cuarentena
    Route::resource('quarantine', QuarantineController::class);

});