<?php

namespace App\Services;

use App\Models\EstablishmentStock;
use App\Models\EstablishmentStockMovement;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;

// stock real de cada establecimiento de la red
class EstablishmentStockService
{
    // nombre del establecimiento tal como esta en la configuracion
    public static function nombreCanonico(?string $establecimiento): string
    {
        $registrado = InventoryService::establecimientoRegistrado($establecimiento);

        return $registrado['nombre'] ?? trim((string) $establecimiento);
    }

    // saldo del producto en el establecimiento, bloqueado para actualizarlo
    private static function saldo(string $establecimiento, ?int $productId, string $producto, bool $crear = true): ?EstablishmentStock
    {
        $est = self::nombreCanonico($establecimiento);
        $producto = trim(preg_replace('/\s+/', ' ', $producto));

        $stock = EstablishmentStock::whereRaw('LOWER(establecimiento) = ?', [mb_strtolower($est)])
            ->where(function ($q) use ($productId, $producto) {
                $productId
                    ? $q->where('product_id', $productId)
                    : $q->whereRaw('LOWER(producto) = ?', [mb_strtolower($producto)]);
            })
            ->lockForUpdate()
            ->first();

        if (! $stock && $crear) {
            try {
                $stock = EstablishmentStock::create([
                    'establecimiento' => $est,
                    'product_id' => $productId,
                    'producto' => $producto,
                    'cantidad' => 0,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // otra operacion lo creo al mismo tiempo: se usa ese
                $stock = EstablishmentStock::whereRaw('LOWER(establecimiento) = ?', [mb_strtolower($est)])
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->firstOrFail();
            }
        }

        return $stock;
    }

    private static function registrar(EstablishmentStock $stock, string $tipo, int $cantidad, string $referencia, array $extra = []): void
    {
        EstablishmentStockMovement::create(array_merge([
            'establishment_stock_id' => $stock->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'saldo' => (int) $stock->cantidad,
            'referencia' => mb_substr($referencia, 0, 500),
        ], $extra));
    }

    // el stock que declara el establecimiento en su solicitud pasa a ser su saldo
    public static function declarar(string $establecimiento, ?int $productId, string $producto, int $cantidad, ?int $requestId = null): void
    {
        $stock = self::saldo($establecimiento, $productId, $producto);
        $diferencia = $cantidad - (int) $stock->cantidad;
        $stock->update(['cantidad' => $cantidad]);
        self::registrar($stock, 'Stock declarado', $diferencia, 'Declarado en solicitud', ['establishment_request_id' => $requestId]);
    }

    // suma lo que recibe el establecimiento
    public static function ingresar(string $establecimiento, ?int $productId, string $producto, int $cantidad, string $referencia, ?int $dispatchId = null): void
    {
        if ($cantidad <= 0) {
            return;
        }
        $stock = self::saldo($establecimiento, $productId, $producto);
        $stock->increment('cantidad', $cantidad);
        $stock->refresh();
        self::registrar($stock, 'Recibido', $cantidad, $referencia, ['dispatch_id' => $dispatchId]);
    }

    // descuenta lo que sale del establecimiento; falla si no alcanza
    public static function retirar(string $establecimiento, ?int $productId, string $producto, int $cantidad, string $referencia, ?int $dispatchId = null): void
    {
        $stock = self::saldo($establecimiento, $productId, $producto, false);
        $disponible = (int) ($stock?->cantidad ?? 0);
        if (! $stock || $disponible < $cantidad) {
            $est = self::nombreCanonico($establecimiento);
            throw new \RuntimeException("«{$est}» solo tiene {$disponible} uds. de «{$producto}»; no se pueden despachar {$cantidad}.");
        }
        $stock->decrement('cantidad', $cantidad);
        $stock->refresh();
        self::registrar($stock, 'Despacho interno', -$cantidad, $referencia, ['dispatch_id' => $dispatchId]);
    }

    // saldo actual de cada producto en cada establecimiento (clave en minusculas)
    public static function saldosPorProducto(): array
    {
        $resultado = [];
        EstablishmentStock::whereNotNull('product_id')
            ->get(['establecimiento', 'product_id', 'cantidad'])
            ->each(function ($s) use (&$resultado) {
                $resultado[$s->product_id][mb_strtolower($s->establecimiento)] = (int) $s->cantidad;
            });

        return $resultado;
    }

    // saldos con stock por establecimiento, para el formulario de despacho interno
    public static function disponiblePorEstablecimiento(): array
    {
        $resultado = [];
        EstablishmentStock::where('cantidad', '>', 0)
            ->orderBy('producto')
            ->get(['establecimiento', 'product_id', 'producto', 'cantidad'])
            // los controlados no salen por despacho interno
            ->reject(fn ($s) => InventoryService::esControlado($s->producto))
            ->each(function ($s) use (&$resultado) {
                $resultado[$s->establecimiento][] = [
                    'product_id' => $s->product_id,
                    'producto' => $s->producto,
                    'cantidad' => (int) $s->cantidad,
                ];
            });

        return $resultado;
    }
}
