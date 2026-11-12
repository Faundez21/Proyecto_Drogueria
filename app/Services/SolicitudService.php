<?php

namespace App\Services;

use App\Models\EstablishmentItem;
use App\Models\EstablishmentRequest;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SolicitudService
{
    /**
     * Registra las cantidades despachadas de una solicitud y recalcula su estado.
     * Lo usan tanto el despacho normal como el despacho controlado.
     */
    public static function registrarDespacho(int $solicitudId, array $productos, array $cantidades): void
    {
        $solicitud = EstablishmentRequest::with('items.product')->find($solicitudId);

        if (! $solicitud || ! in_array($solicitud->estado, ['Pendiente', 'Asignada', 'Parcial'])) {
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
     * Revisa que un despacho vinculado a una solicitud no entregue más unidades
     * de las que aún faltan por despachar en ella. Suma las filas del mismo producto
     * (un producto puede venir repartido en varios lotes).
     * Devuelve el mensaje de error, o null si todo cuadra.
     * Los productos que no están en la solicitud no se revisan aquí.
     */
    public static function excesoSobrePendiente(EstablishmentRequest $solicitud, array $productos, array $cantidades): ?string
    {
        $solicitud->loadMissing('items.product');

        $aDespachar = [];
        $nombres = [];
        foreach ($productos as $i => $nombre) {
            $clave = mb_strtolower(trim($nombre));
            $aDespachar[$clave] = ($aDespachar[$clave] ?? 0) + (int) ($cantidades[$i] ?? 0);
            $nombres[$clave] = trim($nombre);
        }

        $excesos = [];
        foreach ($solicitud->items as $item) {
            $clave = mb_strtolower(trim($item->product->nombre ?? ''));
            if (! isset($aDespachar[$clave])) {
                continue;
            }

            $pendiente = max(0, (int) $item->cantidad_solicitada - (int) $item->cantidad_despachada);
            if ($aDespachar[$clave] > $pendiente) {
                $excesos[] = "«{$nombres[$clave]}»: faltan {$pendiente} y se intentan despachar {$aDespachar[$clave]}";
            }
        }

        if (empty($excesos)) {
            return null;
        }

        $numero = $solicitud->numero_solicitud ?: ('N° ' . $solicitud->id);

        return "No puede despachar más de lo que falta en la solicitud {$numero}. " . implode('; ', $excesos) . '.';
    }

    /**
     * El estado se recalcula sobre TODOS los items de la solicitud,
     * sin importar desde qué módulo de despacho se cubrieron.
     * Estados posibles:
     * - 'Pendiente': sin despachos y sin trabajador asignado
     * - 'Asignada': sin despachos pero con trabajador asignado para despacharla
     * - 'Parcial': algunos productos/unidades despachados pero faltan otros
     * - 'Despachado': todos los productos solicitados fueron despachados
     */
    public static function actualizarEstado(int $solicitudId): string
    {
        $solicitud = EstablishmentRequest::with('items')->find($solicitudId);

        if (! $solicitud) {
            return 'Pendiente';
        }

        $items = $solicitud->items;

        if ($items->isEmpty()) {
            $estado = $solicitud->assigned_user_id ? 'Asignada' : 'Pendiente';
            if ($solicitud->estado !== $estado) {
                $solicitud->update(['estado' => $estado]);
            }

            return $estado;
        }

        $completa = $items->every(fn ($item) => $item->cantidad_despachada >= $item->cantidad_solicitada);
        $algoDespachado = $items->contains(fn ($item) => $item->cantidad_despachada > 0);

        if ($completa) {
            $estado = 'Despachado';
        } elseif ($algoDespachado) {
            $estado = 'Parcial';
        } else {
            $estado = $solicitud->assigned_user_id ? 'Asignada' : 'Pendiente';
        }

        if ($solicitud->estado !== $estado) {
            $solicitud->update(['estado' => $estado]);
        }

        return $estado;
    }

    /**
     * Calcula el mapa de consumo mensual anterior y último stock declarado por establecimiento y producto.
     * Combina los despachos reales (normales y controlados) realizados hacia el establecimiento
     * en el mes anterior / últimos 30 días, y como respaldo las cantidades despachadas en solicitudes previas.
     */
    public static function mapaConsumoYStock(array $nombresEstablecimientos = []): array
    {
        $inicioMesAnterior = Carbon::now()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $finMesAnterior = Carbon::now()->subMonthNoOverflow()->endOfMonth()->toDateString();
        $hace30Dias = Carbon::now()->subDays(30)->toDateString();

        $productsByName = Product::pluck('id', 'nombre')
            ->mapWithKeys(fn ($id, $nombre) => [mb_strtolower(trim($nombre)) => $id])
            ->all();

        $mapa = [];

        // 1. Despachos normales hacia los establecimientos (mes calendario anterior o últimos 30 días)
        $despachosNormales = DB::table('dispatches')
            ->join('dispatch_items', 'dispatches.id', '=', 'dispatch_items.dispatch_id')
            ->when(! empty($nombresEstablecimientos), fn ($q) => $q->whereIn('dispatches.establecimiento_destino', $nombresEstablecimientos))
            ->select(
                'dispatches.establecimiento_destino as establecimiento',
                'dispatch_items.product_id',
                'dispatch_items.producto',
                'dispatches.fecha_despacho',
                'dispatch_items.cantidad'
            )
            ->get();

        // 2. Despachos controlados hacia los establecimientos
        $despachosControlados = DB::table('controlled_dispatches')
            ->join('controlled_dispatch_items', 'controlled_dispatches.id', '=', 'controlled_dispatch_items.controlled_dispatch_id')
            ->when(! empty($nombresEstablecimientos), fn ($q) => $q->whereIn('controlled_dispatches.establecimiento_destino', $nombresEstablecimientos))
            ->select(
                'controlled_dispatches.establecimiento_destino as establecimiento',
                'controlled_dispatch_items.product_id',
                'controlled_dispatch_items.producto',
                'controlled_dispatches.fecha_despacho',
                'controlled_dispatch_items.cantidad'
            )
            ->get();

        foreach ($despachosNormales->concat($despachosControlados) as $row) {
            $est = trim((string) $row->establecimiento);
            $prodId = $row->product_id ?: ($productsByName[mb_strtolower(trim((string) $row->producto))] ?? null);

            if (! $est || ! $prodId) {
                continue;
            }

            if (! isset($mapa[$est][$prodId])) {
                $mapa[$est][$prodId] = [
                    'consumo_mes_anterior' => 0,
                    'consumo_ultimos_30d' => 0,
                    'consumo_historico_reciente' => 0,
                    'ultimo_stock_declarado' => null,
                ];
            }

            $fecha = substr((string) $row->fecha_despacho, 0, 10);
            $cant = (int) $row->cantidad;

            if ($fecha >= $inicioMesAnterior && $fecha <= $finMesAnterior) {
                $mapa[$est][$prodId]['consumo_mes_anterior'] += $cant;
            }
            if ($fecha >= $hace30Dias) {
                $mapa[$est][$prodId]['consumo_ultimos_30d'] += $cant;
            }
            $mapa[$est][$prodId]['consumo_historico_reciente'] += $cant;
        }

        // 3. Historial de solicitudes previas (para obtener último stock declarado y respaldo de consumo)
        $itemsPrevios = EstablishmentItem::with('request')
            ->whereHas('request', function ($q) use ($nombresEstablecimientos) {
                if (! empty($nombresEstablecimientos)) {
                    $q->whereIn('establecimiento', $nombresEstablecimientos);
                }
            })
            ->orderByDesc('id')
            ->get();

        foreach ($itemsPrevios as $item) {
            $est = trim((string) ($item->request->establecimiento ?? ''));
            $prodId = (int) $item->product_id;

            if (! $est || ! $prodId) {
                continue;
            }

            if (! isset($mapa[$est][$prodId])) {
                $mapa[$est][$prodId] = [
                    'consumo_mes_anterior' => 0,
                    'consumo_ultimos_30d' => 0,
                    'consumo_historico_reciente' => 0,
                    'ultimo_stock_declarado' => null,
                ];
            }

            if ($mapa[$est][$prodId]['ultimo_stock_declarado'] === null && $item->stock_actual !== null) {
                $mapa[$est][$prodId]['ultimo_stock_declarado'] = (int) $item->stock_actual;
            }

            if ($mapa[$est][$prodId]['consumo_historico_reciente'] === 0) {
                $base = (int) ($item->cantidad_despachada > 0 ? $item->cantidad_despachada : ($item->consumo_anterior ?? $item->cantidad_solicitada));
                $mapa[$est][$prodId]['consumo_historico_reciente'] = $base;
            }
        }

        // Consolidar valor final de consumo_anterior por (establecimiento, product_id)
        $resultado = [];
        foreach ($mapa as $est => $productos) {
            foreach ($productos as $prodId => $datos) {
                $consumo = $datos['consumo_mes_anterior'] > 0
                    ? $datos['consumo_mes_anterior']
                    : ($datos['consumo_ultimos_30d'] > 0
                        ? $datos['consumo_ultimos_30d']
                        : $datos['consumo_historico_reciente']);

                $resultado[$est][$prodId] = [
                    'consumo_anterior' => (int) $consumo,
                    'ultimo_stock' => $datos['ultimo_stock_declarado'],
                ];
            }
        }

        return $resultado;
    }

    /**
     * Calcula el consumo mensual anterior para un establecimiento y producto específico.
     */
    public static function calcularConsumoAnterior(string $establecimiento, int $productId): int
    {
        $mapa = self::mapaConsumoYStock([$establecimiento]);

        return (int) ($mapa[trim($establecimiento)][$productId]['consumo_anterior'] ?? 0);
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
