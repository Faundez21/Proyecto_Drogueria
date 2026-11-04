<?php

namespace App\Services;

use App\Models\Aisle;
use App\Models\EstablishmentItem;
use App\Models\Inventory;
use App\Models\Level;
use App\Models\Position;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ReceptionItem;
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

        $fechaVence = (!empty($datos['fecha_vencimiento']) && trim((string)$datos['fecha_vencimiento']) !== '' && $datos['fecha_vencimiento'] !== 'N/A')
            ? $datos['fecha_vencimiento']
            : null;
        $datos['fecha_vencimiento'] = $fechaVence;
        $datos['valor_neto_unitario'] = isset($datos['valor_neto_unitario']) && $datos['valor_neto_unitario'] !== '' && $datos['valor_neto_unitario'] !== null
            ? (float) $datos['valor_neto_unitario']
            : 0;
        $datos['fecha_recepcion'] = !empty($datos['fecha_recepcion']) ? $datos['fecha_recepcion'] : now()->format('Y-m-d');
        $datos['tipo_documento'] = !empty($datos['tipo_documento']) ? $datos['tipo_documento'] : 'Carga Masiva';
        $datos['numero_documento'] = !empty($datos['numero_documento']) ? $datos['numero_documento'] : 'S/N';
        $datos['estado'] = !empty($datos['estado']) ? $datos['estado'] : 'Disponible';
        $datos['categoria'] = !empty($datos['categoria']) ? $datos['categoria'] : self::clasificarGrupoProducto((string) ($datos['producto'] ?? ''));

        $batch = Inventory::where('producto', $datos['producto'])
            ->where('estado', $datos['estado'])
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
            $batch->increment('cantidad', (int) ($datos['cantidad'] ?? 0));
            $batch->fill([
                'categoria' => (!empty($datos['categoria']) && $datos['categoria'] !== 'General') ? $datos['categoria'] : ($batch->categoria ?: ($datos['categoria'] ?? 'General')),
                'valor_neto_unitario' => !empty($datos['valor_neto_unitario']) ? $datos['valor_neto_unitario'] : ($batch->valor_neto_unitario ?? 0),
                'fecha_vencimiento' => $fechaVence ?? $batch->fecha_vencimiento,
                'user_id' => $datos['user_id'] ?? $batch->user_id,
                'provider_id' => $datos['provider_id'] ?? $batch->provider_id,
                'reception_id' => $datos['reception_id'] ?? $batch->reception_id,
                'pasillo' => (!empty($datos['pasillo']) && $datos['pasillo'] !== 'P.A') ? $datos['pasillo'] : ($batch->pasillo ?: ($datos['pasillo'] ?? null)),
                'estante' => (!empty($datos['estante']) && $datos['estante'] !== 'P.A') ? $datos['estante'] : ($batch->estante ?: ($datos['estante'] ?? null)),
                'nivel' => !empty($datos['nivel']) ? $datos['nivel'] : $batch->nivel,
                'posicion' => !empty($datos['posicion']) ? $datos['posicion'] : $batch->posicion,
                'programa' => !empty($datos['programa']) ? $datos['programa'] : $batch->programa,
                'aisle_id' => $datos['aisle_id'] ?? $batch->aisle_id,
                'shelf_id' => $datos['shelf_id'] ?? $batch->shelf_id,
                'level_id' => $datos['level_id'] ?? $batch->level_id,
                'position_id' => $datos['position_id'] ?? $batch->position_id,
            ]);
            $batch->save();

            return $batch;
        }

        if (empty($datos['correlativo_interno'])) {
            $datos['correlativo_interno'] = ((int) Inventory::max('correlativo_interno')) + 1;
        }

        $datos['lote'] = $lote ?? 'S/L';

        return Inventory::create($datos);
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
            $shelf = Shelf::with('aisle')->where('name', $datos['estante'])->first();
            if ($shelf) {
                $datos['shelf_id'] = $shelf->id;
                $datos['aisle_id'] = $datos['aisle_id'] ?? $shelf->aisle_id;
                if ((empty($datos['pasillo']) || $datos['pasillo'] === 'P.A') && $shelf->aisle) {
                    $datos['pasillo'] = $shelf->aisle->name;
                }
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

    /**
     * Sincroniza todos los productos de controlados e inventario (incluyendo carga masiva)
     * hacia el catálogo maestro de Products.
     */
    public static function sincronizarProductosDesdeInventario(): void
    {
        foreach (self::controlados() as $ctrlNombre) {
            $nom = trim((string) $ctrlNombre);
            if ($nom !== '') {
                Product::firstOrCreate(['nombre' => $nom]);
            }
        }

        $productosInv = Inventory::whereNotNull('producto')
            ->where('producto', '!=', '')
            ->distinct()
            ->pluck('producto');

        foreach ($productosInv as $prodNombre) {
            $nom = trim((string) $prodNombre);
            if ($nom !== '') {
                Product::firstOrCreate(['nombre' => $nom]);
            }
        }
    }

    /**
     * Devuelve un mapa con la categoría y programa sugeridos por producto (por ID y por nombre en minúsculas)
     * combinando Inventario (incluyendo Carga Masiva) e Historial de Recepciones / Solicitudes.
     */
    public static function mapaMetadatosProductos(): array
    {
        $categoriasPorNombre = [];
        $categoriasPorId = [];
        $programasPorNombre = [];
        $programasPorId = [];

        $itemsRecHist = ReceptionItem::with('product:id,nombre')
            ->whereNotNull('tipo_producto')
            ->where('tipo_producto', '!=', '')
            ->orderBy('id', 'asc')
            ->get(['product_id', 'tipo_producto']);

        foreach ($itemsRecHist as $itemHist) {
            $catVal = trim((string) $itemHist->tipo_producto);
            if ($catVal === '' || mb_strtolower($catVal) === 'general') {
                continue;
            }
            if ($itemHist->product_id) {
                $categoriasPorId[(int) $itemHist->product_id] = $catVal;
            }
            if ($itemHist->product?->nombre) {
                $categoriasPorNombre[mb_strtolower(trim($itemHist->product->nombre))] = $catVal;
            }
        }

        $itemsSolHist = EstablishmentItem::with('product:id,nombre')
            ->whereNotNull('programa')
            ->where('programa', '!=', '')
            ->orderBy('id', 'asc')
            ->get(['product_id', 'programa']);

        foreach ($itemsSolHist as $itemSol) {
            $progVal = trim((string) $itemSol->programa);
            if ($progVal === '' || mb_strtolower($progVal) === 'general') {
                continue;
            }
            if ($itemSol->product_id) {
                $programasPorId[(int) $itemSol->product_id] = $progVal;
            }
            if ($itemSol->product?->nombre) {
                $programasPorNombre[mb_strtolower(trim($itemSol->product->nombre))] = $progVal;
            }
        }

        $registrosInv = Inventory::select('product_id', 'producto', 'categoria', 'programa')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($registrosInv as $regInv) {
            $keyNombre = mb_strtolower(trim((string) $regInv->producto));
            $prodId = $regInv->product_id ? (int) $regInv->product_id : null;

            $catVal = trim((string) ($regInv->categoria ?? ''));
            if ($catVal !== '') {
                $esGeneral = mb_strtolower($catVal) === 'general';
                if ($keyNombre !== '' && (! $esGeneral || ! isset($categoriasPorNombre[$keyNombre]))) {
                    $categoriasPorNombre[$keyNombre] = $catVal;
                }
                if ($prodId && (! $esGeneral || ! isset($categoriasPorId[$prodId]))) {
                    $categoriasPorId[$prodId] = $catVal;
                }
            }

            $progVal = trim((string) ($regInv->programa ?? ''));
            if ($progVal !== '') {
                if ($keyNombre !== '') {
                    $programasPorNombre[$keyNombre] = $progVal;
                }
                if ($prodId) {
                    $programasPorId[$prodId] = $progVal;
                }
            }
        }

        return [
            'categoriasPorId' => $categoriasPorId,
            'categoriasPorNombre' => $categoriasPorNombre,
            'programasPorId' => $programasPorId,
            'programasPorNombre' => $programasPorNombre,
        ];
    }

    /**
     * Devuelve la lista consolidada y sin duplicados de todos los Programas (base + Inventario + Carga Masiva + Solicitudes).
     */
    public static function programasLista(iterable $extras = []): Collection
    {
        $base = collect([
            'Morbilidad General',
            'Fondo de Farmacia (FOFAR)',
            'Cardiovascular',
            'IRA/ERA',
            'Salud Mental',
            'Cuidados Paliativos',
        ]);

        $enInventario = Inventory::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->pluck('programa');

        $enSolicitudes = EstablishmentItem::whereNotNull('programa')
            ->where('programa', '!=', '')
            ->distinct()
            ->pluck('programa');

        $todos = $base
            ->merge($enInventario)
            ->merge($enSolicitudes)
            ->merge(collect($extras));

        $vistos = [];
        $resultado = [];

        foreach ($todos as $prog) {
            $limpio = trim((string) $prog);
            if ($limpio === '' || mb_strtolower($limpio) === 'sin programa' || mb_strtolower($limpio) === 'n/a') {
                continue;
            }
            $key = mb_strtolower($limpio);
            if (! isset($vistos[$key])) {
                $vistos[$key] = true;
                $resultado[] = $limpio;
            }
        }

        return collect($resultado)->values();
    }

    /**
     * Devuelve la lista consolidada de Categorías (base + Inventario + Carga Masiva + Recepciones).
     */
    public static function categoriasLista(bool $incluirControlados = true): Collection
    {
        $base = collect([
            'Fármacos',
            'Medicamentos',
            'Insumos Clínicos',
            'Insumos',
            'Equipamiento',
        ]);

        if ($incluirControlados) {
            $base->push('Fármacos Controlados');
            $base->push('Estupefacientes');
        }

        $enInventario = Inventory::whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->pluck('categoria');

        $enRecepcion = ReceptionItem::whereNotNull('tipo_producto')
            ->where('tipo_producto', '!=', '')
            ->distinct()
            ->pluck('tipo_producto');

        $todos = $base->merge($enInventario)->merge($enRecepcion);

        $vistos = [];
        $resultado = [];

        foreach ($todos as $cat) {
            $limpio = trim((string) $cat);
            if ($limpio === '' || mb_strtolower($limpio) === 'general') {
                continue;
            }
            if (! $incluirControlados && self::clasificarGrupoProducto('', $limpio) === 'Estupefacientes') {
                continue;
            }
            $key = mb_strtolower($limpio);
            if (! isset($vistos[$key])) {
                $vistos[$key] = true;
                $resultado[] = $limpio;
            }
        }

        return collect($resultado)->values();
    }

    public static function normalizarNivel(?string $nivel): string
    {
        $val = trim((string) $nivel);
        if ($val === '' || strtoupper($val) === 'P.A' || strtoupper($val) === 'N/A') {
            return '';
        }
        $sinPrefijo = trim((string) preg_replace('/^nivel\s*/i', '', $val));

        return $sinPrefijo !== '' ? $sinPrefijo : $val;
    }

    public static function normalizarPosicion(?string $posicion): string
    {
        $val = trim((string) $posicion);
        if ($val === '' || strtoupper($val) === 'P.A' || strtoupper($val) === 'N/A') {
            return '';
        }
        $sinPrefijo = trim((string) preg_replace('/^posici[oó]n\s*/iu', '', $val));

        return $sinPrefijo !== '' ? $sinPrefijo : $val;
    }

    /**
     * Devuelve toda la estructura jerárquica de distribución física (Pasillo -> Estante -> Nivel -> Posición)
     * junto con todas las ubicaciones actualmente ocupadas por lotes activos en bodega.
     */
    public static function datosDistribucionFisica(): array
    {
        $shelves = Shelf::with(['aisle', 'levels.positions'])->orderBy('name')->get();

        $pasillosInv = Inventory::whereNotNull('pasillo')->where('pasillo', '!=', '')->where('pasillo', '!=', 'P.A')->distinct()->pluck('pasillo');
        $pasillosMant = Aisle::orderBy('name')->pluck('name');
        $pasillosLista = $pasillosMant->merge($pasillosInv)->map(fn ($v) => trim((string) $v))->filter()->unique()->sort(SORT_NATURAL)->values();

        $estantesInv = Inventory::whereNotNull('estante')->where('estante', '!=', '')->where('estante', '!=', 'P.A')->distinct()->pluck('estante');
        $estantesMant = Shelf::orderBy('name')->pluck('name');
        $estantesLista = $estantesMant->merge($estantesInv)->map(fn ($v) => trim((string) $v))->filter()->unique()->sort(SORT_NATURAL)->values();

        $nivelesInv = Inventory::whereNotNull('nivel')->where('nivel', '!=', '')->distinct()->pluck('nivel')->map(fn ($v) => self::normalizarNivel((string) $v))->filter();
        $nivelesMant = Level::distinct()->orderBy('number')->pluck('number')->map(fn ($v) => self::normalizarNivel((string) $v))->filter();
        $nivelesLista = $nivelesMant->merge($nivelesInv)->unique()->sort(SORT_NATURAL)->values();

        $posicionesInv = Inventory::whereNotNull('posicion')->where('posicion', '!=', '')->distinct()->pluck('posicion')->map(fn ($v) => self::normalizarPosicion((string) $v))->filter();
        $posicionesMant = Position::distinct()->orderBy('name')->pluck('name')->map(fn ($v) => self::normalizarPosicion((string) $v))->filter();
        $posicionesLista = $posicionesMant->merge($posicionesInv)->unique()->sort(SORT_NATURAL)->values();

        // Lotes activos que ocupan una ubicación física en bodega
        $lotesUbicados = Inventory::where('estado', '!=', 'Merma')
            ->where('cantidad', '>', 0)
            ->whereNotNull('estante')
            ->where('estante', '!=', '')
            ->where('estante', '!=', 'P.A')
            ->orderBy('id', 'desc')
            ->get(['id', 'pasillo', 'estante', 'nivel', 'posicion', 'producto', 'lote', 'cantidad', 'estado']);

        $ubicacionesOcupadas = $lotesUbicados->map(function ($inv) {
            return [
                'id' => (int) $inv->id,
                'pasillo' => trim((string) ($inv->pasillo ?? '')),
                'estante' => trim((string) ($inv->estante ?? '')),
                'nivel' => self::normalizarNivel($inv->nivel),
                'posicion' => self::normalizarPosicion($inv->posicion),
                'producto' => (string) $inv->producto,
                'lote' => (string) ($inv->lote ?: 'S/L'),
                'cantidad' => (int) $inv->cantidad,
                'estado' => (string) $inv->estado,
            ];
        })->values();

        // Construir jerarquía completa por estante (Mantenedor + ubicaciones históricas de Inventario)
        $mapaEstantes = [];

        foreach ($shelves as $s) {
            $estName = trim((string) $s->name);
            if ($estName === '') {
                continue;
            }
            $keyEst = mb_strtolower($estName);
            $nivelesArr = [];

            foreach ($s->levels->sortBy('number') as $lvl) {
                $nivStr = self::normalizarNivel((string) $lvl->number);
                if ($nivStr === '') {
                    continue;
                }
                $posArr = $lvl->positions
                    ->pluck('name')
                    ->map(fn ($p) => self::normalizarPosicion((string) $p))
                    ->filter()
                    ->unique()
                    ->sort(SORT_NATURAL)
                    ->values()
                    ->all();

                $nivelesArr[$nivStr] = $posArr;
            }

            $mapaEstantes[$keyEst] = [
                'estante' => $estName,
                'pasillo' => trim((string) ($s->aisle?->name ?? '')),
                'capacidad' => (int) ($s->capacidad ?? 0),
                'niveles_map' => $nivelesArr,
            ];
        }

        // Integrar también estantes/niveles/posiciones presentes en Inventario para que ningún estante quede huérfano
        $ubicacionesInvRaw = Inventory::whereNotNull('estante')
            ->where('estante', '!=', '')
            ->where('estante', '!=', 'P.A')
            ->select('pasillo', 'estante', 'nivel', 'posicion')
            ->distinct()
            ->get();

        foreach ($ubicacionesInvRaw as $uRow) {
            $estName = trim((string) $uRow->estante);
            if ($estName === '') {
                continue;
            }
            $keyEst = mb_strtolower($estName);
            $pasName = trim((string) ($uRow->pasillo ?? ''));
            if ($pasName === 'P.A') {
                $pasName = '';
            }
            $nivStr = self::normalizarNivel($uRow->nivel);
            $posStr = self::normalizarPosicion($uRow->posicion);

            if (! isset($mapaEstantes[$keyEst])) {
                $mapaEstantes[$keyEst] = [
                    'estante' => $estName,
                    'pasillo' => $pasName,
                    'capacidad' => 0,
                    'niveles_map' => [],
                ];
            } elseif ($mapaEstantes[$keyEst]['pasillo'] === '' && $pasName !== '') {
                $mapaEstantes[$keyEst]['pasillo'] = $pasName;
            }

            if ($nivStr !== '') {
                if (! isset($mapaEstantes[$keyEst]['niveles_map'][$nivStr])) {
                    $mapaEstantes[$keyEst]['niveles_map'][$nivStr] = [];
                }
                if ($posStr !== '' && ! in_array($posStr, $mapaEstantes[$keyEst]['niveles_map'][$nivStr], true)) {
                    $mapaEstantes[$keyEst]['niveles_map'][$nivStr][] = $posStr;
                    sort($mapaEstantes[$keyEst]['niveles_map'][$nivStr], SORT_NATURAL);
                }
            }
        }

        $distribucionJerarquia = collect(array_values($mapaEstantes))->map(function ($item) {
            $nivelesMap = $item['niveles_map'];
            uksort($nivelesMap, 'strnatcmp');
            $nivelesList = [];
            foreach ($nivelesMap as $nivKey => $posList) {
                $nivelesList[] = [
                    'nivel' => (string) $nivKey,
                    'posiciones' => array_values($posList),
                ];
            }

            return [
                'estante' => $item['estante'],
                'pasillo' => $item['pasillo'],
                'capacidad' => $item['capacidad'],
                'niveles' => $nivelesList,
            ];
        })->values();

        return [
            'pasillosLista' => $pasillosLista,
            'estantesLista' => $estantesLista,
            'nivelesLista' => $nivelesLista,
            'posicionesLista' => $posicionesLista,
            'distribucionJerarquia' => $distribucionJerarquia,
            'ubicacionesOcupadas' => $ubicacionesOcupadas,
        ];
    }

    /**
     * Verifica si una ubicación física (Estante + Nivel + Posición) ya está ocupada por otro lote activo en bodega.
     * Si el nivel no tiene posiciones definidas o no se envía posición, verifica ocupación a nivel de (Estante + Nivel).
     */
    public static function verificarUbicacionOcupada(
        ?string $estante,
        ?string $nivel,
        ?string $posicion,
        ?int $ignorarId = null,
        ?string $mismoProducto = null,
        ?string $mismoLote = null
    ): ?Inventory {
        $est = trim((string) $estante);
        $niv = self::normalizarNivel($nivel);
        $pos = self::normalizarPosicion($posicion);

        if ($est === '' || strtoupper($est) === 'P.A' || $niv === '') {
            return null;
        }

        $candidatos = Inventory::where('estado', '!=', 'Merma')
            ->where('cantidad', '>', 0)
            ->whereRaw('LOWER(TRIM(estante)) = ?', [mb_strtolower($est)])
            ->when($ignorarId !== null, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->get();

        $loteNorm = self::normalizarLote($mismoLote);

        foreach ($candidatos as $cand) {
            if ($mismoProducto !== null && mb_strtolower(trim($cand->producto)) === mb_strtolower(trim($mismoProducto))) {
                $candLote = self::normalizarLote($cand->lote);
                if ($candLote === $loteNorm) {
                    // Es el mismo producto y lote (acumulación de stock en el mismo lote), no hay conflicto
                    continue;
                }
            }

            $candNiv = self::normalizarNivel($cand->nivel);
            $candPos = self::normalizarPosicion($cand->posicion);

            if (mb_strtolower($candNiv) !== mb_strtolower($niv)) {
                continue;
            }

            // Si ambos tienen posición o se especificó posición, comparamos posición
            if ($pos !== '') {
                if ($candPos !== '' && mb_strtolower($candPos) === mb_strtolower($pos)) {
                    return $cand;
                }
                // Si el lote existente ocupaba todo el nivel sin especificar posición
                if ($candPos === '') {
                    return $cand;
                }
            } else {
                // Si se intenta asignar un nivel sin posición y el nivel ya tiene un ocupante sin posición
                if ($candPos === '') {
                    return $cand;
                }
            }
        }

        return null;
    }
}
