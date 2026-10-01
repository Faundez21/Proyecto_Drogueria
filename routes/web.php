<?php

// controladores de autenticacion
use App\Http\Controllers\AisleController;
use App\Http\Controllers\Auth\LoginController;
// controladores de modulos y operaciones
use App\Http\Controllers\ControlledDispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProviderController; // nuevo controlador
use App\Http\Controllers\QuarantineController;
use App\Http\Controllers\ReceptionController;
//  Controladores de Estructura de Almacén (Mantenedores)
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

/*
 rutas privadas (protegidas)
 todas estas rutas requieren que el usuario este autenticado (middleware auth).
*/

Route::middleware('auth')->group(function () {

    // 4. cierre de sesion (movido dentro del middleware para mayor seguridad)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 5. vistas generales y dashboards

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // rutas para la barra de busqueda global y notificaciones
    Route::get('/api/global-search', [GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/api/notifications', [GlobalSearchController::class, 'notifications'])->name('global.notifications');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Asignar contraseña por el Administrador

    Route::post('/users/reset-password/{email}', [PasswordResetRequestController::class, 'resetPassword'])
        ->name('users.reset_password');

    // modulo de despacho general
    Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
    Route::post('/dispatch', [DispatchController::class, 'store'])->name('dispatch.store');
    Route::get('/dispatch/{id}/pdf', [DispatchController::class, 'pdf'])->name('dispatch.pdf');

    // modulo de despacho de controlados
    Route::get('/controlled_dispatch', [ControlledDispatchController::class, 'index'])->name('controlled_dispatch.index');
    Route::post('/controlled_dispatch', [ControlledDispatchController::class, 'store'])->name('controlled_dispatch.store');
    Route::get('/controlled_dispatch/{id}/pdf', [ControlledDispatchController::class, 'pdf'])->name('controlled_dispatch.pdf');

    // modulo de trazabilidad
    Route::get('/traceability', [TraceabilityController::class, 'index'])->name('traceability.index');
    Route::get('/traceability/timeline', [TraceabilityController::class, 'getTimeline']);

    // modulo de reportes
    Route::get('/reportes', [ReportController::class, 'index'])->name('reportes.index');

    // usuarios
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    // inventario
    Route::get('/inventario', [InventoryController::class, 'index'])->name('inventario.index');
    Route::post('/inventario', [InventoryController::class, 'store'])->name('inventario.store');
    Route::post('/inventario/subir-excel', [InventoryController::class, 'import'])->name('inventario.import');
    Route::put('/inventario/{inventory}', [InventoryController::class, 'update'])->name('inventario.update');

    // 6. modulo de recepcion
    Route::prefix('reception')->name('reception.')->group(function () {

        Route::get('/', [ReceptionController::class, 'index'])
            ->name('index');

        Route::post('/guardar', [ReceptionController::class, 'store'])
            ->name('store');

        Route::get('/{reception}/pdf', [ReceptionController::class, 'downloadPdf'])
            ->name('pdf');
    });

    // 7. modulo de proveedores
    Route::prefix('suppliers')->name('providers.')->group(function () {

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

    Route::post(
        '/shelf/{id}/update-position',
        [ShelfController::class, 'updatePosition']
    )->name('shelf.updatePosition');

    // 13. solicitudes de establecimientos
    Route::resource('requests', SolicitudController::class);

});
// Enviar solicitud desde el Login
Route::post('/password-reset-request', [PasswordResetRequestController::class, 'store'])
    ->name('password.request.store');
