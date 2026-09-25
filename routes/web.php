<?php

//  Controladores de Autenticación
use App\Http\Controllers\Auth\LoginController;

//  Controladores de Módulos y Operaciones
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\QuarantineController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\TraceabilityController;
use App\Http\Controllers\GlobalSearchController;

//  Controladores de Estructura de Almacén (Mantenedores)
use App\Http\Controllers\AisleController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;

//  Controladores de Usuarios
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

// Rutas Públicas Estas rutas son accesibles sin necesidad de iniciar sesión.

// 1. Redirección principal al login
Route::redirect('/', '/login');

// 2. Rutas de Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

// 3. Ruta de recuperación de contraseña
Route::get('/recuperar-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

/*
 Rutas Privadas (Protegidas)
 Todas estas rutas requieren que el usuario esté autenticado (middleware auth).
*/

Route::middleware('auth')->group(function () {

    // 4. Cierre de sesión (Movido dentro del middleware para mayor seguridad)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 5. Vistas Generales y Dashboards

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // Rutas para la Barra de Búsqueda Global y Notificaciones
    Route::get('/api/global-search', [GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/api/notifications', [GlobalSearchController::class, 'notifications'])->name('global.notifications');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/despacho', [DispatchController::class, 'index'])->name('despacho.index');
    Route::post('/despacho', [DispatchController::class, 'store'])->name('despacho.store');
    Route::get('/despacho/{id}/pdf', [DispatchController::class, 'pdf'])->name('despacho.pdf');

    Route::get('/traceability', [TraceabilityController::class, 'index'])->name('traceability.index');
    Route::get('/traceability/timeline', [TraceabilityController::class, 'getTimeline']);



    Route::get('/reportes', function () {
        return view('reportes.index');
    })->name('reportes.index');

    Route::get('/error', function () {
        return view('error');
    })->name('error');

    // Usuarios
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');


    // Inventario
    Route::get('/inventario', [InventoryController::class, 'index'])
        ->name('inventario.index');

    Route::post('/inventario/guardar', [InventoryController::class, 'store'])
        ->name('inventario.store');

    Route::post('/inventario/subir-excel', [InventoryController::class, 'import'])
        ->name('inventario.import');
        
    Route::put('/inventario/{inventory}', [InventoryController::class, 'update'])
        ->name('inventario.update');


    // 6. Módulo de Recepción
    Route::prefix('reception')->name('reception.')->group(function () {

        Route::get('/', [ReceptionController::class, 'index'])
            ->name('index');

        Route::post('/guardar', [ReceptionController::class, 'store'])
            ->name('store');

        Route::get('/{reception}/pdf', [ReceptionController::class, 'downloadPdf'])
            ->name('pdf');
    });


    // 7. Módulo de Proveedores
    Route::prefix('proveedores')->name('providers.')->group(function () {

        Route::get('/', [ProviderController::class, 'index'])
            ->name('index');

        Route::get('/crear', [ProviderController::class, 'create'])
            ->name('create');

        Route::post('/', [ProviderController::class, 'store'])
            ->name('store');

        Route::put('/{provider}', [ProviderController::class, 'update'])
            ->name('update');

        Route::patch('/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])
            ->name('toggle-status');
    });


    // 5. Rutas de mantenedores

    Route::resource('aisle', AisleController::class);

    Route::resource('shelf', ShelfController::class);

    Route::resource('level', LevelController::class);

    Route::resource('position', PositionController::class);


    // 6. Ruta de distribución

    Route::resource('distribution', DistributionController::class);


    // 7. Ruta de cuarentena

    Route::resource('quarantine', QuarantineController::class);


    // 8. Ruta de QR

    Route::resource('qr', QRController::class);


    // 9. ruta para mover posiciones 3d

    Route::post(
        '/shelf/{id}/update-position',
        [ShelfController::class, 'updatePosition']
    )->name('shelf.updatePosition');

});
