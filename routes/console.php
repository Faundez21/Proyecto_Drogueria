<?php

use App\Services\InventoryService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Envía a Merma todos los lotes vencidos (Disponible o Cuarentena).
// Uso manual: php artisan inventario:vencidos
Artisan::command('inventario:vencidos', function () {
    $movidos = InventoryService::moverVencidosAMerma(true);
    $this->info("Lotes vencidos enviados a Merma: {$movidos}");
})->purpose('Envía automáticamente a Merma los lotes vencidos');

// En el servidor (con el programador de tareas activo) se ejecuta todos los días a las 00:05.
// Mientras tanto, la plataforma también lo hace sola cada 5 minutos cuando alguien la usa.
Schedule::command('inventario:vencidos')->dailyAt('00:05');
