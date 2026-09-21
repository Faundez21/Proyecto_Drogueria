<?php
use Illuminate\Support\Facades\Route;

//  Controladores de Autenticación 
use App\Http\Controllers\Auth\LoginController;

//  Controladores de Módulos y Operaciones 
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\QuarantineController;
use App\Http\Controllers\ProviderController;

//  Controladores de Estructura de Almacén (Mantenedores)  
use App\Http\Controllers\AisleController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\QRController; 

// Rutas Públicas Estas rutas son accesibles sin necesidad de iniciar sesión.


// 1. Redirección principal al login
Route::redirect('/', '/login');

// 2. Rutas de Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

// 3. Ruta de recuperación de contraseña
Route::get('/recuperar-password', function () {
    return "Página de recuperación de contraseña en construcción...";
})->name('password.request');

/*
 Rutas Privadas (Protegidas)
 Todas estas rutas requieren que el usuario esté autenticado (middleware auth).
*/

Route::middleware('auth')->group(function () {

    // 4. Cierre de sesión (Movido dentro del middleware para mayor seguridad)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 5. Vistas Generales y Dashboards
    Route::get('/dashboard', function () { return view('dashboard.index'); })->name('dashboard.index');
    Route::get('/despacho', function () { return view('despacho.index'); })->name('despacho.index');
    Route::get('/trazabilidad', function () { return view('trazabilidad.index'); })->name('trazabilidad.index');
    Route::get('/inventario', function () { return view('inventario.index'); })->name('inventario.index');
    Route::get('/reportes', function () { return view('reportes.index'); })->name('reportes.index');
    Route::get('/users', function () { return view('users.index'); })->name('users.index');
    Route::get('/error', function () { return view('error'); })->name('error');

    // 6. Módulo de Recepción 
    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::post('/guardar', [ReceptionController::class, 'store'])->name('store');
        Route::get('/{reception}/pdf', [ReceptionController::class, 'downloadPdf'])->name('pdf');
    });

    // 7. Módulo de Proveedores 
    Route::prefix('proveedores')->name('providers.')->group(function () {
        Route::get('/', [ProviderController::class, 'index'])->name('index');
        Route::get('/crear', [ProviderController::class, 'create'])->name('create');
        Route::post('/', [ProviderController::class, 'store'])->name('store');
        Route::put('/{provider}', [ProviderController::class, 'update'])->name('update');
        Route::patch('/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])->name('toggle-status');
    });

    // 8. Mantenedores de Estructura Física 
    Route::resource('aisle', AisleController::class);
    Route::resource('shelf', ShelfController::class);
    Route::resource('level', LevelController::class);
    Route::resource('position', PositionController::class);

    // 9. Operaciones Logísticas Especiales
    Route::resource('distribution', DistributionController::class);
    Route::resource('quarantine', QuarantineController::class);

});