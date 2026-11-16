<?php

namespace App\Services;

use App\Models\EstablishmentStock;
use App\Models\EstablishmentStockLot;
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
        // si declara menos de lo que suman sus lotes, se asume que uso primero los que vencian antes
        self::ajustarLotesAlSaldo($stock);
        self::registrar($stock, 'Stock declarado', $diferencia, 'Declarado en solicitud', ['establishment_request_id' => $requestId]);
    }

    // suma lo que recibe el establecimiento; si viene con lote, queda registrado en sus lotes
    public static function ingresar(string $establecimiento, ?int $productId, string $producto, int $cantidad, string $referencia, ?int $dispatchId = null, ?string $lote = null, $vencimiento = null): void
    {
        if ($cantidad <= 0) {
            return;
        }
        $stock = self::saldo($establecimiento, $productId, $producto);
        $stock->increment('cantidad', $cantidad);
        $stock->refresh();

        $lote = self::loteConocido($lote);
        $venc = $vencimiento ? date('Y-m-d', strtotime((string) $vencimiento)) : null;
        if ($lote !== null) {
            $fila = EstablishmentStockLot::where('establishment_stock_id', $stock->id)
                ->whereRaw('LOWER(lote) = ?', [mb_strtolower($lote)])
                ->where(fn ($q) => $venc ? $q->whereDate('fecha_vencimiento', $venc) : $q->whereNull('fecha_vencimiento'))
                ->lockForUpdate()
                ->first();
            $fila
                ? $fila->increment('cantidad', $cantidad)
                : EstablishmentStockLot::create(['establishment_stock_id' => $stock->id, 'lote' => $lote, 'fecha_vencimiento' => $venc, 'cantidad' => $cantidad]);
        }

        self::registrar($stock, 'Recibido', $cantidad, $referencia, ['dispatch_id' => $dispatchId, 'lote' => $lote ?? 'S/L', 'fecha_vencimiento' => $venc]);
    }

    // descuenta lo que sale del establecimiento por orden de vencimiento (fefo); falla si no alcanza.
    // devuelve de que lotes salio: [['lote' => ..., 'fecha_vencimiento' => 'Y-m-d'|null, 'cantidad' => n], ...]
    public static function retirar(string $establecimiento, ?int $productId, string $producto, int $cantidad, string $referencia, ?int $dispatchId = null, ?string $lotePreferido = null): array
    {
        $stock = self::saldo($establecimiento, $productId, $producto, false);
        $est = self::nombreCanonico($establecimiento);
        $saldoTotal = (int) ($stock?->cantidad ?? 0);

        $lotes = $stock ? self::lotesOrdenados($stock, true) : collect();
        $vencidas = (int) $lotes->filter(fn ($l) => self::estaVencido($l))->sum('cantidad');
        $disponible = $saldoTotal - $vencidas;

        if (! $stock || $disponible < $cantidad) {
            $detalleVencidas = $vencidas > 0 ? " (además tiene {$vencidas} uds. vencidas, que no se pueden traspasar)" : '';
            throw new \RuntimeException("«{$est}» solo tiene {$disponible} uds. disponibles de «{$producto}»{$detalleVencidas}; no se pueden despachar {$cantidad}.");
        }

        // primero el lote indicado, luego por vencimiento; los vencidos no salen
        $preferido = self::loteConocido($lotePreferido);
        $esPreferido = fn ($l) => $preferido !== null && mb_strtolower($l->lote) === mb_strtolower($preferido);
        $vigentes = $lotes->reject(fn ($l) => self::estaVencido($l))->values();
        $vigentes = $vigentes->filter($esPreferido)->merge($vigentes->reject($esPreferido))->values();

        $sinLote = $saldoTotal - (int) $lotes->sum('cantidad');
        $asignaciones = [];
        $restante = $cantidad;
        foreach ($vigentes as $lote) {
            if ($restante <= 0) {
                break;
            }
            $toma = min($restante, (int) $lote->cantidad);
            if ($toma <= 0) {
                continue;
            }
            $lote->decrement('cantidad', $toma);
            $asignaciones[] = [
                'lote' => $lote->lote,
                'fecha_vencimiento' => $lote->fecha_vencimiento?->format('Y-m-d'),
                'cantidad' => $toma,
            ];
            $restante -= $toma;
        }
        // lo que falta sale de la parte del saldo sin lote conocido (por ejemplo, stock declarado en una solicitud)
        if ($restante > 0 && $sinLote > 0) {
            $asignaciones[] = ['lote' => 'S/L', 'fecha_vencimiento' => null, 'cantidad' => min($restante, $sinLote)];
            $restante -= min($restante, $sinLote);
        }
        if ($restante > 0) {
            throw new \RuntimeException("«{$est}» no tiene suficientes unidades vigentes de «{$producto}» para despachar {$cantidad}.");
        }
        EstablishmentStockLot::where('establishment_stock_id', $stock->id)->where('cantidad', '<=', 0)->delete();

        $stock->decrement('cantidad', $cantidad);
        $stock->refresh();
        foreach ($asignaciones as $a) {
            self::registrar($stock, 'Despacho interno', -$a['cantidad'], $referencia, ['dispatch_id' => $dispatchId, 'lote' => $a['lote'], 'fecha_vencimiento' => $a['fecha_vencimiento']]);
        }

        return $asignaciones;
    }

    private static function loteConocido(?string $lote): ?string
    {
        $lote = trim((string) $lote);

        return in_array(mb_strtoupper($lote), ['', 'S/L', 'N/A', 'AUTO...'], true) ? null : $lote;
    }

    private static function estaVencido(EstablishmentStockLot $lote): bool
    {
        return $lote->fecha_vencimiento !== null && $lote->fecha_vencimiento->lt(today());
    }

    // los lotes sin vencimiento van al final
    private static function claveVencimiento(EstablishmentStockLot $lote): string
    {
        return $lote->fecha_vencimiento?->format('Y-m-d') ?? '9999-12-31';
    }

    private static function lotesOrdenados(EstablishmentStock $stock, bool $bloquear = false)
    {
        $consulta = EstablishmentStockLot::where('establishment_stock_id', $stock->id)->where('cantidad', '>', 0);
        if ($bloquear) {
            $consulta->lockForUpdate();
        }

        return $consulta->get()->sortBy([
            fn ($a, $b) => self::claveVencimiento($a) <=> self::claveVencimiento($b),
            fn ($a, $b) => $a->id <=> $b->id,
        ])->values();
    }

    // los lotes nunca suman mas que el saldo: el exceso se descuenta de los que vencen antes
    private static function ajustarLotesAlSaldo(EstablishmentStock $stock): void
    {
        $lotes = self::lotesOrdenados($stock, true);
        $exceso = (int) $lotes->sum('cantidad') - max(0, (int) $stock->cantidad);
        foreach ($lotes as $lote) {
            if ($exceso <= 0) {
                break;
            }
            $quita = min($exceso, (int) $lote->cantidad);
            $lote->decrement('cantidad', $quita);
            $exceso -= $quita;
        }
        EstablishmentStockLot::where('establishment_stock_id', $stock->id)->where('cantidad', '<=', 0)->delete();
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

    // saldos con stock por establecimiento, para el formulario de despacho interno.
    // cantidad = unidades que se pueden traspasar (sin las vencidas); lotes = lotes vigentes por orden de vencimiento
    public static function disponiblePorEstablecimiento(): array
    {
        $resultado = [];
        EstablishmentStock::with(['lotes' => fn ($q) => $q->where('cantidad', '>', 0)])
            ->where('cantidad', '>', 0)
            ->orderBy('producto')
            ->get()
            // los controlados no salen por despacho interno
            ->reject(fn ($s) => InventoryService::esControlado($s->producto))
            ->each(function ($s) use (&$resultado) {
                $lotes = $s->lotes->sortBy([
                    fn ($a, $b) => self::claveVencimiento($a) <=> self::claveVencimiento($b),
                    fn ($a, $b) => $a->id <=> $b->id,
                ]);
                $vencidas = (int) $lotes->filter(fn ($l) => self::estaVencido($l))->sum('cantidad');
                $vigentes = $lotes->reject(fn ($l) => self::estaVencido($l))->values();
                $disponible = (int) $s->cantidad - $vencidas;
                if ($disponible <= 0) {
                    return;
                }
                $sinLote = (int) $s->cantidad - (int) $lotes->sum('cantidad');
                $resultado[$s->establecimiento][] = [
                    'product_id' => $s->product_id,
                    'producto' => $s->producto,
                    'cantidad' => $disponible,
                    'sin_lote' => max(0, $sinLote),
                    'lotes' => $vigentes->map(fn ($l) => [
                        'lote' => $l->lote,
                        'fecha_vencimiento' => $l->fecha_vencimiento?->format('Y-m-d'),
                        'cantidad' => (int) $l->cantidad,
                    ])->all(),
                ];
            });

        return $resultado;
    }
}
