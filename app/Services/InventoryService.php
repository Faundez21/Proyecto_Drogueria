<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Collection;

class InventoryService
{
    public static function esControlado(string $producto): bool
    {
        return in_array($producto, config('drogueria.controlados'), true);
    }

    public static function controlados(): array
    {
        return config('drogueria.controlados');
    }

    /**
     * Descuenta stock del inventario.
     * Si se indica un lote concreto consume solo ese lote; si no,
     * aplica FEFO (primero vence, primero sale) repartiendo entre lotes.
     *
     * @return array<int, array{inventory: Inventory, cantidad: int}>
     */
    public static function consumir(string $producto, int $cantidad, ?string $lote = null, array $estadosPermitidos = ['Disponible']): array
    {
        $lote = self::normalizarLote($lote);

        $lotes = Inventory::where('producto', $producto)
            ->whereIn('estado', $estadosPermitidos)
            ->where('cantidad', '>', 0)
            ->when($lote !== null, fn ($q) => $q->where('lote', $lote))
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC, id ASC')
            ->lockForUpdate()
            ->get();

        $disponible = (int) $lotes->sum('cantidad');

        if ($disponible < $cantidad) {
            $detalle = $lote !== null
                ? "el lote '{$lote}'"
                : 'ningún lote';

            throw new \RuntimeException(
                "Stock insuficiente de '{$producto}' en estado " . implode('/', $estadosPermitidos) .
                ": solicitado {$cantidad}, disponible {$disponible} en {$detalle}."
            );
        }

        $pendiente = $cantidad;
        $asignaciones = [];

        foreach ($lotes as $batch) {
            if ($pendiente <= 0) {
                break;
            }

            $tomar = min($pendiente, (int) $batch->cantidad);
            $batch->decrement('cantidad', $tomar);
            $batch->cantidad -= $tomar;

            $asignaciones[] = ['inventory' => $batch, 'cantidad' => $tomar];
            $pendiente -= $tomar;
        }

        return $asignaciones;
    }

    /**
     * Registra (crea o acumula) el lote recibido en inventario.
     */
    public static function ingresar(array $datos): Inventory
    {
        $lote = self::normalizarLote($datos['lote'] ?? null);

        $batch = Inventory::where('producto', $datos['producto'])
            ->where('estado', '!=', 'Merma')
            ->where(function ($q) use ($lote) {
                if ($lote === null) {
                    $q->whereNull('lote')->orWhere('lote', 'S/L')->orWhere('lote', '');
                } else {
                    $q->where('lote', $lote);
                }
            })
            ->lockForUpdate()
            ->first();

        if ($batch) {
            $batch->increment('cantidad', $datos['cantidad']);
            $batch->fill([
                'valor_neto_unitario' => $datos['valor_neto_unitario'] ?? $batch->valor_neto_unitario,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? $batch->fecha_vencimiento,
                'user_id' => $datos['user_id'] ?? null,
            ]);
            $batch->save();

            return $batch;
        }

        return Inventory::create($datos + ['lote' => $lote]);
    }

    public static function productoId(?string $nombre): ?int
    {
        if (! $nombre) {
            return null;
        }

        return Product::where('nombre', $nombre)->value('id')
            ?? Product::create(['nombre' => $nombre])->id;
    }

    public static function normalizarLote(?string $lote): ?string
    {
        $lote = trim((string) $lote);

        if ($lote === '' || strtoupper($lote) === 'S/L') {
            return null;
        }

        return $lote;
    }

    /**
     * Registra un movimiento de trazabilidad enlazado al lote y usuario.
     */
    public static function registrarMovimiento(Inventory $inventory, array $datos): void
    {
        $inventory->movements()->create($datos + [
            'producto' => $datos['producto'] ?? $inventory->producto,
            'lote' => $datos['lote'] ?? ($inventory->lote ?? 'S/L'),
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? $inventory->fecha_vencimiento,
            'estado_lote' => $datos['estado_lote'] ?? $inventory->estado,
            'user_id' => $datos['user_id'] ?? null,
        ]);
    }
}
