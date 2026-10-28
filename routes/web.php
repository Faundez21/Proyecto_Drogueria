<?php

// controladores de autenticacion
use App\Http\Controllers\AisleController;
// controladores de modulos y operaciones
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ControlledDispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\EstablishmentRequestController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\QRController;
//  Controladores de Estructura de Almacén (Mantenedores)
use App\Http\Controllers\QuarantineController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\TraceabilityController;
// controladores de usuarios
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// rutas publicas. estas rutas son accesibles sin necesidad de iniciar sesion.

// 1. redireccion principal al login
Route::redirect('/', '/login');

// 2. rutas de login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('throttle:10,1');

// Enviar solicitud desde el Login (con throttle anti-abuso)
Route::post('/password-reset-request', [PasswordResetRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('password.request.store');

/*
 rutas privadas (protegidas)
 todas estas rutas requieren que el usuario este autenticado (middleware auth).
*/

Route::middleware('auth')->group(function () {

    // cierre de sesion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // rutas para la barra de busqueda global y notificaciones
    Route::get('/api/global-search', [GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/api/notifications', [GlobalSearchController::class, 'notifications'])->name('global.notifications');

    // ===== GESTIÓN DE USUARIOS Y ROLES (acceso por permiso ver.usuarios; Administrador entra siempre vía Gate::before) =====
    Route::middleware('permission:ver.usuarios')->group(function () {

        // asignar contraseña por el administrador
        Route::post('/users/reset-password/{email}', [PasswordResetRequestController::class, 'resetPassword'])
            ->name('users.reset_password');

        // usuarios y roles
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/roles', [UserController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [UserController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [UserController::class, 'destroyRole'])->name('roles.destroy');
    });

    // ===== SOLO ADMINISTRADOR =====
    Route::middleware('role:Administrador')->group(function () {
        // asignar solicitudes de establecimientos/cesfam a trabajadores desde Despacho
        Route::patch('/requests/{establishmentRequest}/assign', [EstablishmentRequestController::class, 'assign'])
            ->name('requests.assign');
    });

    // modulo de despacho de controlados (acceso por permiso ver.despacho_controlado; Administrador entra siempre vía Gate::before)
    Route::middleware('permission:ver.despacho_controlado')->group(function () {
        Route::get('/controlled_dispatch', [ControlledDispatchController::class, 'index'])->name('controlled_dispatch.index');
        Route::post('/controlled_dispatch', [ControlledDispatchController::class, 'store'])->name('controlled_dispatch.store');
        Route::get('/controlled_dispatch/{id}/pdf', [ControlledDispatchController::class, 'pdf'])->name('controlled_dispatch.pdf');
    });

    // ===== ACCESO POR PERMISO DE VISTA (definido en roles) =====
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:ver.dashboard')->name('dashboard.index');

    Route::get('/dispatch', [DispatchController::class, 'index'])
        ->middleware('permission:ver.despacho')->name('dispatch.index');
    Route::post('/dispatch', [DispatchController::class, 'store'])
        ->middleware('permission:ver.despacho')->name('dispatch.store');
    Route::get('/dispatch/{id}/pdf', [DispatchController::class, 'pdf'])
        ->middleware('permission:ver.despacho')->name('dispatch.pdf');
    Route::get('/dispatch/solicitudes', [DispatchController::class, 'solicitudes'])
        ->middleware('permission:ver.despacho')->name('dispatch.solicitudes');

    Route::get('/traceability', [TraceabilityController::class, 'index'])
        ->middleware('permission:ver.trazabilidad')->name('traceability.index');
    Route::get('/traceability/timeline', [TraceabilityController::class, 'getTimeline'])
        ->middleware('permission:ver.trazabilidad')->name('traceability.timeline');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:ver.reportes')->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->middleware('permission:ver.reportes')->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->middleware('permission:ver.reportes')->name('reports.pdf');

    Route::get('/inventory', [InventoryController::class, 'index'])
        ->middleware('permission:ver.inventario')->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])
        ->middleware('permission:ver.inventario')->name('inventory.store');
    Route::post('/inventory/import', [InventoryController::class, 'import'])
        ->middleware('permission:ver.inventario')->name('inventory.import');
    Route::get('/inventory/template', [InventoryController::class, 'template'])
        ->middleware('permission:ver.inventario')->name('inventory.template');
    Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])
        ->middleware('permission:ver.inventario|ver.cuarentena')->name('inventory.update');
    Route::get('/requests', [EstablishmentRequestController::class, 'index'])
        ->middleware(['auth', 'permission:ver.solicitudes'])
        ->name('requests.index');
    Route::get('/requests/cesfam', [EstablishmentRequestController::class, 'cesfam'])
        ->middleware(['auth', 'permission:ver.solicitudes_cesfam'])
        ->name('requests.cesfam');
    Route::post('/requests', [EstablishmentRequestController::class, 'store'])
        ->middleware(['auth', 'permission:ver.solicitudes|ver.solicitudes_cesfam'])
        ->name('requests.store');
    // modulo de recepcion
    Route::prefix('reception')->name('reception.')->middleware('permission:ver.recepcion')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::post('/guardar', [ReceptionController::class, 'store'])->name('store');
        Route::get('/{reception}/pdf', [ReceptionController::class, 'downloadPdf'])->name('pdf');
    });

    // modulo de proveedores
    Route::prefix('suppliers')->name('providers.')->middleware('permission:ver.proveedores')->group(function () {
        Route::get('/', [ProviderController::class, 'index'])->name('index');
        Route::post('/', [ProviderController::class, 'store'])->name('store');
        Route::put('/{provider}', [ProviderController::class, 'update'])->name('update');
        Route::patch('/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])->name('toggle-status');
    });

    // rutas de mantenedores
    Route::middleware('permission:ver.distribucion')->group(function () {
        Route::resource('aisle', AisleController::class);
        Route::resource('shelf', ShelfController::class);
        Route::resource('level', LevelController::class);
        Route::resource('position', PositionController::class);
        Route::post('/shelf/{id}/update-position', [ShelfController::class, 'updatePosition'])
            ->name('shelf.updatePosition');
    });

    // ruta de distribucion
    Route::get('/distribution', [DistributionController::class, 'index'])
        ->middleware('permission:ver.distribucion')->name('distribution.index');

    // ruta de cuarentena
    Route::get('/quarantine', [QuarantineController::class, 'index'])
        ->middleware('permission:ver.cuarentena')->name('quarantine.index');

    // codigos QR de etiquetas de inventario
    Route::get('/qr/{inventory}/image', [QRController::class, 'image'])->name('qr.image');
});
