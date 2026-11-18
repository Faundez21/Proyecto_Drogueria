<?php

namespace App\Http\Middleware;

use App\Services\InventoryService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

// envia automaticamente a merma los lotes vencidos
class MoverVencidosAMerma
{
    private const INTERVALO_MINUTOS = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            try {
                if (Cache::add('inventario:revision-vencidos', true, now()->addMinutes(self::INTERVALO_MINUTOS))) {
                    InventoryService::moverVencidosAMerma();
                }
            } catch (\Throwable $e) {
                Log::warning('No se pudo revisar los lotes vencidos: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
