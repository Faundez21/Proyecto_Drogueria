<?php

namespace App\Services;

use App\Models\EstablishmentRequest;
use App\Models\Product;

class SolicitudService
{
    /**
     * Registra las cantidades despachadas de una solicitud y recalcula su estado.
     * Lo usan tanto el despacho normal como el despacho controlado.
     */
    public static function registrarDespacho(int $solicitudId, array $productos, array $cantidades): void
    {
        $solicitud = EstablishmentRequest::with('items')->find($solicitudId);

        if (! $solicitud || ! in_array($solicitud->estado, ['Pendiente', 'Parcial'])) {
            return;
        }

        $entregadoPorProducto = [];

        foreach ($productos as $i => $nombre) {
            $clave = mb_strtolower(trim($nombre));
            $entregadoPorProducto[$clave] = ($entregadoPorProducto[$clave] ?? 0) + (int) ($cantidades[$i] ?? 0);
        }

        foreach ($solicitud->items as $item) {
            $nombreItem = $item->product->nombre ?? '';
            $aporte = $entregadoPorProducto[mb_strtolower(trim($nombreItem))] ?? 0;

            if ($aporte <= 0) {
                continue;
            }

            $nuevoDespachado = min($item->cantidad_solicitada, $item->cantidad_despachada + $aporte);
            $item->update(['cantidad_despachada' => $nuevoDespachado]);
        }

        self::actualizarEstado($solicitud->id);
    }

    /**
     * El estado se recalcula sobre TODOS los items de la solicitud,
     * sin importar desde qué módulo de despacho se cubrieron.
     */
    public static function actualizarEstado(int $solicitudId): void
    {
        $solicitud = EstablishmentRequest::with('items')->find($solicitudId);

        if (! $solicitud) {
            return;
        }

        $items = $solicitud->items;

        if ($items->isEmpty()) {
            return;
        }

        $completa = $items->every(fn ($item) => $item->cantidad_despachada >= $item->cantidad_solicitada);
        $algoDespachado = $items->contains(fn ($item) => $item->cantidad_despachada > 0);

        $estado = $completa ? 'Despachado' : ($algoDespachado ? 'Parcial' : 'Pendiente');

        if ($solicitud->estado !== $estado) {
            $solicitud->update(['estado' => $estado]);
        }
    }

    /**
     * Separa los items de una solicitud según el módulo de despacho
     * que corresponde a cada producto (controlados -> despacho controlado).
     */
    public static function separarItems($solicitud): array
    {
        $normales = [];
        $controlados = [];

        foreach ($solicitud->items as $item) {
            $nombre = $item->product->nombre ?? ('Producto #' . $item->product_id);
            $pendiente = max(0, $item->cantidad_solicitada - $item->cantidad_despachada);

            if ($pendiente <= 0) {
                continue;
            }

            if (InventoryService::esControlado($nombre)) {
                $controlados[] = ['item' => $item, 'producto' => $nombre, 'cantidad' => $pendiente];
            } else {
                $normales[] = ['item' => $item, 'producto' => $nombre, 'cantidad' => $pendiente];
            }
        }

        return ['normales' => $normales, 'controlados' => $controlados];
    }
}
