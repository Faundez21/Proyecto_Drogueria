<?php

// controladores de autenticacion
use App\Http\Controllers\AisleController;
// controladores de modulos y operaciones
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ControlledDispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\QuarantineController;
// Controladores de Estructura de Almacén (Mantenedores)
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TraceabilityController;
// controladores de usuarios
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// rutas publicas. estas rutas son accesibles sin necesidad de iniciar sesion.

// 1. redireccion principal al login
Route::redirect('/', '/login');

// 2. rutas de login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

// 3. ruta de recuperacion de contraseña
Route::get('/recuperar-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

/*
 rutas privadas (protegidas)
 todas estas rutas requieren que el usuario este autenticado (middleware auth).
*/

Route::middleware('auth')->group(function () {

    // 4. cierre de sesion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- Rutas exclusivas para el rol Administrador ---
    Route::middleware(['auth', 'role:Administrador'])->group(function () {

        // 5. Vistas generales y dashboards
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Rutas para la barra de búsqueda global y notificaciones
        Route::get('/api/global-search', [GlobalSearchController::class, 'search'])->name('global.search');
        Route::get('/api/notifications', [GlobalSearchController::class, 'notifications'])->name('global.notifications');

        // modulo de despacho de controlados
        Route::get('/despacho_controlado', [ControlledDispatchController::class, 'index'])->name('controlled_dispatch.index');
        Route::post('/despacho_controlado', [ControlledDispatchController::class, 'store'])->name('controlled_dispatch.store');
        Route::get('/despacho_controlado/{id}/pdf', [ControlledDispatchController::class, 'pdf'])->name('controlled_dispatch.pdf');

        // 13. solicitudes de establecimientos
        Route::resource('requests', SolicitudController::class);
    });

    // modulo de despacho general
    Route::get('/despacho', [DispatchController::class, 'index'])->name('despacho.index');
    Route::post('/despacho', [DispatchController::class, 'store'])->name('despacho.store');
    Route::get('/despacho/{id}/pdf', [DispatchController::class, 'pdf'])->name('despacho.pdf');

    // modulo de trazabilidad
    Route::get('/traceability', [TraceabilityController::class, 'index'])->name('traceability.index');
    Route::get('/traceability/timeline', [TraceabilityController::class, 'getTimeline']);

    // modulo de reportes
    Route::get('/reportes', [ReportController::class, 'index'])->name('reportes.index');

    Route::get('/error', function () {
        return view('error');
    })->name('error');

    // usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // inventario
    Route::get('/inventario', [InventoryController::class, 'index'])->name('inventario.index');
    Route::post('/inventario', [InventoryController::class, 'store'])->name('inventario.store');
    Route::post('/inventario/subir-excel', [InventoryController::class, 'import'])->name('inventario.import');
    Route::put('/inventario/{inventory}', [InventoryController::class, 'update'])->name('inventario.update');

    // 6. modulo de recepcion
    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::post('/guardar', [ReceptionController::class, 'store'])->name('store');
        Route::get('/{reception}/pdf', [ReceptionController::class, 'downloadPdf'])->name('pdf');
    });

    // 7. modulo de proveedores
    Route::prefix('proveedores')->name('providers.')->group(function () {
        Route::get('/', [ProviderController::class, 'index'])->name('index');
        Route::get('/crear', [ProviderController::class, 'create'])->name('create');
        Route::post('/', [ProviderController::class, 'store'])->name('store');
        Route::put('/{provider}', [ProviderController::class, 'update'])->name('update');
        Route::patch('/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])->name('toggle-status');
    });

    // 8. rutas de mantenedores
    Route::resource('aisle', AisleController::class);
    Route::resource('shelf', ShelfController::class);
    Route::resource('level', LevelController::class);
    Route::resource('position', PositionController::class);

    // 9. ruta de distribucion
    Route::resource('distribution', DistributionController::class);

    // 10. ruta de cuarentena
    Route::resource('quarantine', QuarantineController::class);

    // 11. ruta de qr
    Route::resource('qr', QRController::class);

    // 12. ruta para mover posiciones 3d
    Route::post('/shelf/{id}/update-position', [ShelfController::class, 'updatePosition'])->name('shelf.updatePosition');

});
