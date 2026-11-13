<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Services\InventoryService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class InventoriesImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private int $userId;
    private int $registro = 0;
    private int $importados = 0;
    private int $disponibles = 0;
    private int $enCuarentena = 0;
    private int $enMerma = 0;
    private bool $headersValidated = false;
    private bool $forceReimport = false;
    private array $seenRows = [];

    // filas que no se cargaron: [fila => motivo]
    private array $omitidas = [];

    // filas vencidas que se cargaron directamente como merma
    private int $vencidosAMerma = 0;

    // filas cuya ubicacion no existe en el mapa de bodega y quedaron "por asignar" (p.a)
    private array $ubicacionesInvalidas = [];

    private const ESTADOS = [
        'disponible' => 'Disponible',
        'cuarentena' => 'Cuarentena',
        'merma' => 'Merma',
    ];

    private const REQUIRED_COLUMNS = [
        'producto' => 'PRODUCTO',
    ];

    public function __construct(int $userId, bool $forceReimport = false)
    {
        $this->userId = $userId;
        $this->forceReimport = $forceReimport;
    }

    public function model(array $row): ?Inventory
    {
        $this->registro++;

        // 1. validar que el archivo tenga al menos la columna producto
        if (! $this->headersValidated) {
            $missingColumns = [];
            foreach (self::REQUIRED_COLUMNS as $slug => $label) {
                if (! array_key_exists($slug, $row)) {
                    $missingColumns[] = $label;
                }
            }

            if (! empty($missingColumns)) {
                $faltantes = implode(', ', $missingColumns);
                throw new \RuntimeException(
                    "Formato de plantilla incompatible: Falta la columna obligatoria {$faltantes} en el encabezado del archivo. Por favor descargue y utilice la Plantilla Oficial de Carga Masiva."
                );
            }

            $this->headersValidated = true;
        }

        // ignorar filas vacias o de relleno al final del excel donde producto este en blanco
        $producto = trim((string) ($row['producto'] ?? ''));
        if ($producto === '') {
            return null;
        }

        $categoriaRaw = trim((string) ($row['categoria'] ?? ''));
        $categoria = $categoriaRaw !== ''
            ? $categoriaRaw
            : InventoryService::clasificarGrupoProducto($producto);

        // n° de fila tal como se ve en excel (la fila 1 es el encabezado)
        $filaExcel = $this->registro + 1;

        $estadoTexto = trim((string) ($row['estado'] ?? ''));
        $estadoRaw = mb_strtolower($estadoTexto);
        if ($estadoRaw === '') {
            $estadoKey = 'disponible';
        } elseif (isset(self::ESTADOS[$estadoRaw])) {
            $estadoKey = $estadoRaw;
        } elseif (str_contains($estadoRaw, 'cuarentena')) {
            $estadoKey = 'cuarentena';
        } elseif (str_contains($estadoRaw, 'merma') || str_contains($estadoRaw, 'vencid')) {
            $estadoKey = 'merma';
        } elseif (str_contains($estadoRaw, 'disponible')) {
            $estadoKey = 'disponible';
        } else {
            $this->omitidas[$filaExcel] = "estado «{$estadoTexto}» no válido (usa Disponible, Cuarentena o Merma)";

            return null;
        }
        $estadoFinal = self::ESTADOS[$estadoKey];

        $cantidadRaw = $row['cantidad'] ?? null;
        if ($cantidadRaw === null || trim((string) $cantidadRaw) === '') {
            $this->omitidas[$filaExcel] = 'cantidad vacía';

            return null;
        }
        if (! is_numeric($cantidadRaw) && ! preg_match('/^\d{1,3}(\.\d{3})+$/', trim((string) $cantidadRaw))) {
            $this->omitidas[$filaExcel] = "cantidad «{$cantidadRaw}» no es un número";

            return null;
        }
        $cantidad = (int) ($this->entero($cantidadRaw) ?? 0);
        if ($cantidad <= 0) {
            // sin stock: no se crea un lote vacio, pero el producto queda registrado en el catalogo
            InventoryService::productoId($producto);
            $this->omitidas[$filaExcel] = 'cantidad 0 (el producto quedó en el catálogo, sin lote)';

            return null;
        }

        // un lote ya vencido entra directamente como merma (existe fisicamente y debe darse de baja)
        $fechaVencimiento = $this->fecha($row['fecha_vencimiento'] ?? null);
        $motivo = null;
        if ($fechaVencimiento && $estadoFinal !== 'Merma' && Carbon::parse($fechaVencimiento)->startOfDay()->lt(now()->startOfDay())) {
            $estadoFinal = 'Merma';
            $motivo = 'Producto vencido el ' . Carbon::parse($fechaVencimiento)->format('d/m/Y') . '. Cargado directamente a Merma desde la carga masiva.';
            $this->vencidosAMerma++;
        }

        // la ubicacion debe existir en el mapa de bodega; si no, el lote queda "por asignar"
        $pasillo = trim((string) ($row['pasillo'] ?? '')) ?: null;
        $estante = trim((string) ($row['estante'] ?? '')) ?: null;
        $nivel = trim((string) ($row['nivel'] ?? '')) ?: null;
        $posicion = trim((string) ($row['posicion'] ?? '')) ?: null;
        if (($pasillo || $estante) && InventoryService::validarUbicacion($pasillo, $estante, $nivel, $posicion)) {
            $this->ubicacionesInvalidas[] = $filaExcel;
            $pasillo = $estante = $nivel = $posicion = null;
        }

        // campos opcionales con valores por defecto seguros cuando vienen vacios en el excel
        $fechaRecepcion = $this->fecha($row['fecha_recepcion'] ?? null) ?? now()->format('Y-m-d');
        $tipoDocumento = trim((string) ($row['tipo_documento'] ?? '')) ?: 'Carga Masiva';
        $numeroDocumento = trim((string) ($row['numero_documento'] ?? '')) ?: 'S/N';
        $lote = trim((string) ($row['lote'] ?? '')) ?: 'S/L';
        $valorNetoUnitario = $this->decimal($row['valor_neto_unitario'] ?? null) ?? 0;

        $batch = InventoryService::ingresar([
            'user_id' => $this->userId,
            'product_id' => InventoryService::productoId($producto),
            'fecha_recepcion' => $fechaRecepcion,
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'orden_compra' => trim((string) ($row['orden_de_compra'] ?? ($row['orden_compra'] ?? ''))) ?: null,
            'origen_mercaderia' => trim((string) ($row['origen_mercaderia'] ?? '')) ?: null,
            'rut_proveedor' => trim((string) ($row['rut_proveedor'] ?? '')) ?: null,
            'razon_social' => trim((string) ($row['razon_social'] ?? '')) ?: null,
            'producto' => $producto,
            'categoria' => $categoria,
            'estado' => $estadoFinal,
            'motivo' => $motivo,
            'lote' => $lote,
            'fecha_vencimiento' => $fechaVencimiento,
            'cantidad' => $cantidad,
            'valor_neto_unitario' => $valorNetoUnitario,
            'pasillo' => $pasillo,
            'estante' => $estante,
            'nivel' => $nivel,
            'posicion' => $posicion,
            'programa' => trim((string) ($row['programa'] ?? ($row['programa_ministerial'] ?? ($row['programas'] ?? '')))) ?: null,
        ]);

        InventoryService::registrarMovimiento($batch, [
            'user_id' => $this->userId,
            'tipo_movimiento' => 'Recepción',
            'cantidad' => $cantidad,
            'fecha_hora' => now(),
            'origen_destino' => 'Desde: ' . (trim((string) ($row['razon_social'] ?? '')) ?: (trim((string) ($row['origen_mercaderia'] ?? '')) ?: 'Carga Masiva Excel')),
            'documento_respaldo' => "{$tipoDocumento} #{$numeroDocumento}",
            'estado_lote' => $estadoFinal,
        ]);

        $this->importados++;
        if ($estadoFinal === 'Cuarentena') {
            $this->enCuarentena++;
        } elseif ($estadoFinal === 'Merma') {
            $this->enMerma++;
        } else {
            $this->disponibles++;
        }

        return $batch;
    }

    public function importados(): int
    {
        return $this->importados;
    }

    public function disponibles(): int
    {
        return $this->disponibles;
    }

    public function enCuarentena(): int
    {
        return $this->enCuarentena;
    }

    public function enMerma(): int
    {
        return $this->enMerma;
    }

    // @return array<int, string> [fila => motivo]
    public function omitidas(): array
    {
        return $this->omitidas;
    }

    public function vencidosAMerma(): int
    {
        return $this->vencidosAMerma;
    }

    // @return int[] filas con ubicacion inexistente
    public function ubicacionesInvalidas(): array
    {
        return $this->ubicacionesInvalidas;
    }

    private function fecha(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        if (is_object($valor) && $valor instanceof \DateTimeInterface) {
            return $this->validarFechaSql(Carbon::instance($valor)->format('Y-m-d'));
        }

        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        // textos que indican "sin fecha / no aplica"
        $upper = mb_strtoupper($valor);
        if (in_array($upper, ['N/A', 'NA', 'S/V', 'SV', 'S/F', 'SF', 'SIN VENCIMIENTO', 'SIN FECHA', 'NO APLICA', '-', '--', '0'], true)) {
            return null;
        }

        // normalizar espacios alrededor de separadores: '1 .2030' -> '1.2030', '13 / 10 / 2027' -> '13/10/2027'
        $limpio = preg_replace('/\s*([\/\-\.])\s*/', '$1', $valor);
        $limpio = preg_replace('/\s+/', ' ', trim((string) $limpio));

        // 0. fechas escritas como numeros puros sin separadores (ej: '29042028' -> 29/04/2028, '102028' -> 10/2028)
        if (preg_match('/^\d{5,8}$/', $limpio)) {
            $len = strlen($limpio);
            if ($len === 8) {
                // ddmmyyyy (ej: 29042028)
                $d = (int) substr($limpio, 0, 2);
                $mes = (int) substr($limpio, 2, 2);
                $anio = (int) substr($limpio, 4, 4);
                if ($mes > 12 && $d <= 12) {
                    [$d, $mes] = [$mes, $d];
                }
                if ($anio >= 1990 && $anio <= 2100 && checkdate($mes, $d, $anio)) {
                    return sprintf('%04d-%02d-%02d', $anio, $mes, $d);
                }
                // yyyymmdd (ej: 20280429)
                $anioYmd = (int) substr($limpio, 0, 4);
                $mesYmd = (int) substr($limpio, 4, 2);
                $dYmd = (int) substr($limpio, 6, 2);
                if ($anioYmd >= 1990 && $anioYmd <= 2100 && checkdate($mesYmd, $dYmd, $anioYmd)) {
                    return sprintf('%04d-%02d-%02d', $anioYmd, $mesYmd, $dYmd);
                }
            } elseif ($len === 7) {
                // dmmyyyy (ej: 5042028 -> 05/04/2028)
                $d1 = (int) substr($limpio, 0, 1);
                $mes1 = (int) substr($limpio, 1, 2);
                $anio1 = (int) substr($limpio, 3, 4);
                if ($anio1 >= 1990 && $anio1 <= 2100 && checkdate($mes1, $d1, $anio1)) {
                    return sprintf('%04d-%02d-%02d', $anio1, $mes1, $d1);
                }
                // ddmyyyy (ej: 2942028 -> 29/04/2028)
                $d2 = (int) substr($limpio, 0, 2);
                $mes2 = (int) substr($limpio, 2, 1);
                $anio2 = (int) substr($limpio, 3, 4);
                if ($anio2 >= 1990 && $anio2 <= 2100 && checkdate($mes2, $d2, $anio2)) {
                    return sprintf('%04d-%02d-%02d', $anio2, $mes2, $d2);
                }
            } elseif ($len === 6) {
                // mmyyyy (ej: 042028 -> 30/04/2028)
                $mesM = (int) substr($limpio, 0, 2);
                $anioM = (int) substr($limpio, 2, 4);
                if ($mesM >= 1 && $mesM <= 12 && $anioM >= 1990 && $anioM <= 2100) {
                    return Carbon::createFromDate($anioM, $mesM, 1)->endOfMonth()->format('Y-m-d');
                }
                // ddmmyy (ej: 290428 -> 29/04/2028)
                $dShort = (int) substr($limpio, 0, 2);
                $mesShort = (int) substr($limpio, 2, 2);
                $anioShort = 2000 + (int) substr($limpio, 4, 2);
                if (checkdate($mesShort, $dShort, $anioShort)) {
                    return sprintf('%04d-%02d-%02d', $anioShort, $mesShort, $dShort);
                }
            } elseif ($len === 5 && (int) $limpio < 32874) {
                // myyyy menor al rango de serial excel (ej: 12030 -> 01/2030)
                $mes5 = (int) substr($limpio, 0, 1);
                $anio5 = (int) substr($limpio, 1, 4);
                if ($mes5 >= 1 && $mes5 <= 9 && $anio5 >= 1990 && $anio5 <= 2100) {
                    return Carbon::createFromDate($anio5, $mes5, 1)->endOfMonth()->format('Y-m-d');
                }
            }
        }

        // serial numerico de excel valido entre 1990-01-01 (32874) y 2100-12-31 (73415)
        if (is_numeric($valor) && (float) $valor >= 32874 && (float) $valor <= 73415 && ! str_contains($valor, '.')) {
            try {
                return $this->validarFechaSql(ExcelDate::excelToDateTimeObject((float) $valor)->format('Y-m-d'));
            } catch (\Throwable) {
            }
        }

        // 1. formato mes y ano de 4 digitos: m.yyyy, mm/yyyy, mm-yyyy (ej: '1.2030', '01/2030', '12-2028')
        if (preg_match('/^(\d{1,2})[\/\-\.\s](\d{4})$/', $limpio, $m)) {
            $mes = (int) $m[1];
            $anio = (int) $m[2];
            if ($mes >= 1 && $mes <= 12 && $anio >= 1990 && $anio <= 2100) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 2. formato ano de 4 digitos y mes: yyyy.mm, yyyy/mm, yyyy-mm (ej: '2030.01', '2030/1')
        if (preg_match('/^(\d{4})[\/\-\.\s](\d{1,2})$/', $limpio, $m)) {
            $anio = (int) $m[1];
            $mes = (int) $m[2];
            if ($mes >= 1 && $mes <= 12 && $anio >= 1990 && $anio <= 2100) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 3. formato dia, mes y ano completo con separadores /, - o . (con dia o mes de 1 o 2 digitos)
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $limpio, $m)) {
            $d = (int) $m[1];
            $mes = (int) $m[2];
            $anio = (int) $m[3];
            if ($anio < 100) {
                $anio += 2000;
            }
            // si el segundo numero > 12 y el primero <= 12, vino en formato mm/dd/yyyy
            if ($mes > 12 && $d <= 12) {
                [$d, $mes] = [$mes, $d];
            }
            if ($anio >= 1990 && $anio <= 2100 && checkdate($mes, $d, $anio)) {
                return sprintf('%04d-%02d-%02d', $anio, $mes, $d);
            }
        }

        // 4. formato mes y ano corto: mm/yy (ej: '01/30', '12-28')
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{2})$/', $limpio, $m)) {
            $mes = (int) $m[1];
            $anio = 2000 + (int) $m[2];
            if ($mes >= 1 && $mes <= 12) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 5. meses en espanol (ej: 'ene-2030', 'enero 2030', 'dic/28', '15-ene-2028')
        $mesesEs = [
            'enero' => 1, 'ene' => 1,
            'febrero' => 2, 'feb' => 2,
            'marzo' => 3, 'mar' => 3,
            'abril' => 4, 'abr' => 4,
            'mayo' => 5, 'may' => 5,
            'junio' => 6, 'jun' => 6,
            'julio' => 7, 'jul' => 7,
            'agosto' => 8, 'ago' => 8,
            'septiembre' => 9, 'setiembre' => 9, 'sep' => 9, 'set' => 9,
            'octubre' => 10, 'oct' => 10,
            'noviembre' => 11, 'nov' => 11,
            'diciembre' => 12, 'dic' => 12,
        ];
        $lowerLimpio = mb_strtolower($limpio);
        foreach ($mesesEs as $nombreMes => $numMes) {
            if (preg_match('/^(\d{1,2})?[\/\-\.\s]*' . $nombreMes . '[a-z]*[\/\-\.\s]+(\d{2,4})$/u', $lowerLimpio, $m)) {
                $dia = ! empty($m[1]) ? (int) $m[1] : null;
                $anio = (int) $m[2];
                if ($anio < 100) {
                    $anio += 2000;
                }
                if ($anio >= 1990 && $anio <= 2100) {
                    if ($dia !== null && checkdate($numMes, $dia, $anio)) {
                        return sprintf('%04d-%02d-%02d', $anio, $numMes, $dia);
                    }
                    return Carbon::createFromDate($anio, $numMes, 1)->endOfMonth()->format('Y-m-d');
                }
            }
        }

        // 6. formatos estandar de carbon
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y', 'Y/m/d'] as $formato) {
            try {
                $fecha = Carbon::createFromFormat($formato, $limpio);
                if ($fecha !== false) {
                    $validada = $this->validarFechaSql($fecha->format('Y-m-d'));
                    if ($validada !== null) {
                        return $validada;
                    }
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // 7. si fue leido como flotante de excel en rango valido (1990..2100)
        if (is_numeric($limpio) && (float) $limpio >= 32874 && (float) $limpio <= 73415) {
            try {
                return $this->validarFechaSql(ExcelDate::excelToDateTimeObject((float) $limpio)->format('Y-m-d'));
            } catch (\Throwable) {
            }
        }

        try {
            return $this->validarFechaSql(Carbon::parse($limpio)->format('Y-m-d'));
        } catch (\Throwable) {
            // si el texto no representa una fecha reconocible, devolver null para no bloquear toda la carga
            return null;
        }
    }

    private function validarFechaSql(?string $ymd): ?string
    {
        if ($ymd === null || ! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $ymd, $m)) {
            return null;
        }
        $anio = (int) $m[1];
        $mes = (int) $m[2];
        $dia = (int) $m[3];
        if ($anio < 1990 || $anio > 2100 || ! checkdate($mes, $dia, $anio)) {
            return null;
        }

        return $ymd;
    }

    private function entero(mixed $valor): ?int
    {
        if ($valor === null || trim((string) $valor) === '') {
            return 0;
        }

        if (is_numeric($valor)) {
            $num = (int) floor((float) $valor);
            // evitar que codigos concatenados por error en excel (> 50 millones) inflen el inventario
            return ($num >= 0 && $num <= 50000000) ? $num : 0;
        }

        $str = trim((string) $valor);

        // soportar unicamente formato numerico con separador de miles valido (ej: '1.500', '12.000')
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $str)) {
            $num = (int) str_replace('.', '', $str);
            return ($num >= 0 && $num <= 50000000) ? $num : 0;
        }

        // si la celda contiene texto o varios numeros separados (que excel ignora en autosuma), contar como 0
        return 0;
    }

    private function decimal(mixed $valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return 0.0;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        // limpiar simbolos de moneda o separadores comunes si vienen formateados
        if (is_string($valor)) {
            $limpio = str_replace(['$', ' ', '.'], '', $valor);
            $limpio = str_replace(',', '.', $limpio);
            if (is_numeric($limpio)) {
                return (float) $limpio;
            }
        }

        return 0.0;
    }
}
