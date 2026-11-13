<?php

use App\Services\InventoryService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// envia a merma todos los lotes vencidos (disponible o cuarentena). uso manual: php artisan inventario:vencidos
Artisan::command('inventario:vencidos', function () {
    $movidos = InventoryService::moverVencidosAMerma(true);
    $this->info("Lotes vencidos enviados a Merma: {$movidos}");
})->purpose('Envía automáticamente a Merma los lotes vencidos');

// en el servidor (con el programador de tareas activo) se ejecuta todos los dias a las 00:05
Schedule::command('inventario:vencidos')->dailyAt('00:05');
