<?php

namespace App\Services;

use App\Models\Aisle;
use App\Models\Inventory;
use App\Models\Level;
use App\Models\Position;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Shelf;
use Illuminate\Support\Collection;

class InventoryService
{
    public static function esControlado(string $producto): bool
    {
        $nombreNorm = mb_strtolower(trim($producto));
        foreach (config('drogueria.controlados', []) as $ctrl) {
            if (mb_strtolower(trim($ctrl)) === $nombreNorm) {
                return true;
            }
        }

        return false;
    }

    public static function controlados(): array
    {
        return config('drogueria.controlados');
    }

    /**
     * Clasifica un producto en uno de los 3 grandes grupos:
     * 'Estupefacientes', 'Insumos' o 'Medicamentos'.
     */
    public static function clasificarGrupoProducto(string $producto, ?string $categoria = null): string
    {
        if (self::esControlado($producto)) {
            return 'Estupefacientes';
        }

        $catLower = mb_strtolower(trim((string) $categoria));
        if ($catLower !== '') {
            if (str_contains($catLower, 'controlado') || str_contains($catLower, 'estupefaciente') || str_contains($catLower, 'psicotr')) {
                return 'Estupefacientes';
            }
            if (
                str_contains($catLower, 'insumo') ||
                str_contains($catLower, 'equipamiento') ||
                str_starts_with($catLower, 'i-') ||
                str_starts_with($catLower, 'e-')
            ) {
                return 'Insumos';
            }
            if (
                str_contains($catLower, 'medicamento') ||
                str_contains($catLower, 'fármaco') ||
                str_contains($catLower, 'farmaco') ||
                str_starts_with($catLower, 'f-')
            ) {
                return 'Medicamentos';
            }
        }

        $nombreLower = mb_strtolower(trim($producto));
        $keywordsInsumos = [
            'jeringa', 'gasa', 'aguja', 'guante', 'mascarilla', 'apósito', 'aposito',
            'venda', 'sonda', 'catéter', 'cateter', 'bajada', 'algodón', 'algodon',
            'tela ', 'bisturí', 'bisturi', 'sutura', 'desfibrilador', 'termómetro',
            'termometro', 'oxímetro', 'oximetro', 'tensiómetro', 'tensiometro',
            'cánula', 'canula', 'espéculo', 'especulo', 'branula', 'bránula',
            'alcohol pad', 'torula', 'tórula', 'llave 3 pasos', 'papel camilla',
        ];

        foreach ($keywordsInsumos as $kw) {
            if (str_contains($nombreLower, $kw)) {
                return 'Insumos';
            }
        }

        return 'Medicamentos';
    }

    public static function esCesfam(string $establecimiento): bool
    {
        foreach (config('drogueria.establecimientos') as $est) {
            if (mb_strtolower(trim($est['nombre'])) === mb_strtolower(trim($establecimiento))) {
                return $est['tipo'] === 'CESFAM';
            }
        }

        return str_starts_with(mb_strtolower(trim($establecimiento)), 'cesfam ');
    }

    public static function establecimientos(): array
    {
        return config('drogueria.establecimientos');
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
        $datos = self::resolverRelaciones($datos);
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
                'user_id' => $datos['user_id'] ?? $batch->user_id,
                'provider_id' => $datos['provider_id'] ?? $batch->provider_id,
                'reception_id' => $datos['reception_id'] ?? $batch->reception_id,
                'aisle_id' => $datos['aisle_id'] ?? $batch->aisle_id,
                'shelf_id' => $datos['shelf_id'] ?? $batch->shelf_id,
                'level_id' => $datos['level_id'] ?? $batch->level_id,
                'position_id' => $datos['position_id'] ?? $batch->position_id,
            ]);
            $batch->save();

            return $batch;
        }

        return Inventory::create($datos + ['lote' => $lote]);
    }

    /**
     * Resuelve las llaves foráneas de producto, proveedor y ubicación física a partir de los textos.
     */
    public static function resolverRelaciones(array $datos): array
    {
        if (empty($datos['product_id']) && ! empty($datos['producto'])) {
            $datos['product_id'] = self::productoId($datos['producto']);
        }

        if (empty($datos['provider_id'])) {
            if (! empty($datos['rut_proveedor'])) {
                $datos['provider_id'] = Provider::where('rut', $datos['rut_proveedor'])->value('id');
            }
            if (empty($datos['provider_id']) && ! empty($datos['razon_social'])) {
                $datos['provider_id'] = Provider::where('name', $datos['razon_social'])->value('id');
            }
        }

        if (! empty($datos['estante']) && empty($datos['shelf_id'])) {
            $shelf = Shelf::where('name', $datos['estante'])->first();
            if ($shelf) {
                $datos['shelf_id'] = $shelf->id;
                $datos['aisle_id'] = $datos['aisle_id'] ?? $shelf->aisle_id;
            }
        }

        if (empty($datos['aisle_id']) && ! empty($datos['pasillo'])) {
            $datos['aisle_id'] = Aisle::where('name', $datos['pasillo'])->value('id');
        }

        if (! empty($datos['shelf_id']) && ! empty($datos['nivel']) && empty($datos['level_id'])) {
            $levelNum = (int) preg_replace('/\D+/', '', (string) $datos['nivel']);
            if ($levelNum > 0) {
                $datos['level_id'] = Level::where('shelf_id', $datos['shelf_id'])
                    ->where('number', $levelNum)
                    ->value('id');
            }
        }

        if (! empty($datos['level_id']) && ! empty($datos['posicion']) && empty($datos['position_id'])) {
            $datos['position_id'] = Position::where('level_id', $datos['level_id'])
                ->where('name', $datos['posicion'])
                ->value('id');
        }

        return $datos;
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
