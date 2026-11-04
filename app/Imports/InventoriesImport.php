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

        // 1. Validar que el archivo tenga al menos la columna PRODUCTO
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

        // Ignorar filas vacías o de relleno al final del Excel donde PRODUCTO esté en blanco
        $producto = trim((string) ($row['producto'] ?? ''));
        if ($producto === '') {
            return null;
        }

        $categoriaRaw = trim((string) ($row['categoria'] ?? ''));
        $categoria = $categoriaRaw !== ''
            ? $categoriaRaw
            : InventoryService::clasificarGrupoProducto($producto);

        $estadoRaw = mb_strtolower(trim((string) ($row['estado'] ?? '')));
        if ($estadoRaw === '') {
            $estadoKey = 'disponible';
        } elseif (isset(self::ESTADOS[$estadoRaw])) {
            $estadoKey = $estadoRaw;
        } elseif (str_contains($estadoRaw, 'cuarentena')) {
            $estadoKey = 'cuarentena';
        } elseif (str_contains($estadoRaw, 'merma') || str_contains($estadoRaw, 'vencid')) {
            $estadoKey = 'merma';
        } else {
            $estadoKey = 'disponible';
        }
        $estadoFinal = self::ESTADOS[$estadoKey];

        $cantidadRaw = $row['cantidad'] ?? null;
        if ($cantidadRaw === null || trim((string) $cantidadRaw) === '') {
            $cantidad = 1;
        } else {
            $cantidad = $this->entero($cantidadRaw);
            if ($cantidad === null || $cantidad < 0) {
                throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): La CANTIDAD debe ser un número válido.");
            }
            if ($cantidad === 0) {
                $cantidad = 1;
            }
        }

        // Campos opcionales con valores por defecto seguros cuando vienen vacíos en el Excel
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
            'lote' => $lote,
            'fecha_vencimiento' => $this->fecha($row['fecha_vencimiento'] ?? null),
            'cantidad' => $cantidad,
            'valor_neto_unitario' => $valorNetoUnitario,
            'pasillo' => trim((string) ($row['pasillo'] ?? '')) ?: null,
            'estante' => trim((string) ($row['estante'] ?? '')) ?: null,
            'nivel' => trim((string) ($row['nivel'] ?? '')) ?: null,
            'posicion' => trim((string) ($row['posicion'] ?? '')) ?: null,
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

        // Textos que indican "sin fecha / no aplica"
        $upper = mb_strtoupper($valor);
        if (in_array($upper, ['N/A', 'NA', 'S/V', 'SV', 'S/F', 'SF', 'SIN VENCIMIENTO', 'SIN FECHA', 'NO APLICA', '-', '--', '0'], true)) {
            return null;
        }

        // Normalizar espacios alrededor de separadores: '1 .2030' -> '1.2030', '13 / 10 / 2027' -> '13/10/2027'
        $limpio = preg_replace('/\s*([\/\-\.])\s*/', '$1', $valor);
        $limpio = preg_replace('/\s+/', ' ', trim((string) $limpio));

        // 0. Fechas escritas como números puros sin separadores (ej: '29042028' -> 29/04/2028, '102028' -> 10/2028)
        if (preg_match('/^\d{5,8}$/', $limpio)) {
            $len = strlen($limpio);
            if ($len === 8) {
                // DDMMYYYY (ej: 29042028)
                $d = (int) substr($limpio, 0, 2);
                $mes = (int) substr($limpio, 2, 2);
                $anio = (int) substr($limpio, 4, 4);
                if ($mes > 12 && $d <= 12) {
                    [$d, $mes] = [$mes, $d];
                }
                if ($anio >= 1990 && $anio <= 2100 && checkdate($mes, $d, $anio)) {
                    return sprintf('%04d-%02d-%02d', $anio, $mes, $d);
                }
                // YYYYMMDD (ej: 20280429)
                $anioYmd = (int) substr($limpio, 0, 4);
                $mesYmd = (int) substr($limpio, 4, 2);
                $dYmd = (int) substr($limpio, 6, 2);
                if ($anioYmd >= 1990 && $anioYmd <= 2100 && checkdate($mesYmd, $dYmd, $anioYmd)) {
                    return sprintf('%04d-%02d-%02d', $anioYmd, $mesYmd, $dYmd);
                }
            } elseif ($len === 7) {
                // DMMYYYY (ej: 5042028 -> 05/04/2028)
                $d1 = (int) substr($limpio, 0, 1);
                $mes1 = (int) substr($limpio, 1, 2);
                $anio1 = (int) substr($limpio, 3, 4);
                if ($anio1 >= 1990 && $anio1 <= 2100 && checkdate($mes1, $d1, $anio1)) {
                    return sprintf('%04d-%02d-%02d', $anio1, $mes1, $d1);
                }
                // DDMYYYY (ej: 2942028 -> 29/04/2028)
                $d2 = (int) substr($limpio, 0, 2);
                $mes2 = (int) substr($limpio, 2, 1);
                $anio2 = (int) substr($limpio, 3, 4);
                if ($anio2 >= 1990 && $anio2 <= 2100 && checkdate($mes2, $d2, $anio2)) {
                    return sprintf('%04d-%02d-%02d', $anio2, $mes2, $d2);
                }
            } elseif ($len === 6) {
                // MMYYYY (ej: 042028 -> 30/04/2028)
                $mesM = (int) substr($limpio, 0, 2);
                $anioM = (int) substr($limpio, 2, 4);
                if ($mesM >= 1 && $mesM <= 12 && $anioM >= 1990 && $anioM <= 2100) {
                    return Carbon::createFromDate($anioM, $mesM, 1)->endOfMonth()->format('Y-m-d');
                }
                // DDMMYY (ej: 290428 -> 29/04/2028)
                $dShort = (int) substr($limpio, 0, 2);
                $mesShort = (int) substr($limpio, 2, 2);
                $anioShort = 2000 + (int) substr($limpio, 4, 2);
                if (checkdate($mesShort, $dShort, $anioShort)) {
                    return sprintf('%04d-%02d-%02d', $anioShort, $mesShort, $dShort);
                }
            } elseif ($len === 5 && (int) $limpio < 32874) {
                // MYYYY menor al rango de serial Excel (ej: 12030 -> 01/2030)
                $mes5 = (int) substr($limpio, 0, 1);
                $anio5 = (int) substr($limpio, 1, 4);
                if ($mes5 >= 1 && $mes5 <= 9 && $anio5 >= 1990 && $anio5 <= 2100) {
                    return Carbon::createFromDate($anio5, $mes5, 1)->endOfMonth()->format('Y-m-d');
                }
            }
        }

        // Serial numérico de Excel válido entre 1990-01-01 (32874) y 2100-12-31 (73415)
        if (is_numeric($valor) && (float) $valor >= 32874 && (float) $valor <= 73415 && ! str_contains($valor, '.')) {
            try {
                return $this->validarFechaSql(ExcelDate::excelToDateTimeObject((float) $valor)->format('Y-m-d'));
            } catch (\Throwable) {
            }
        }

        // 1. Formato Mes y Año de 4 dígitos: M.YYYY, MM/YYYY, MM-YYYY (ej: '1.2030', '01/2030', '12-2028')
        if (preg_match('/^(\d{1,2})[\/\-\.\s](\d{4})$/', $limpio, $m)) {
            $mes = (int) $m[1];
            $anio = (int) $m[2];
            if ($mes >= 1 && $mes <= 12 && $anio >= 1990 && $anio <= 2100) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 2. Formato Año de 4 dígitos y Mes: YYYY.MM, YYYY/MM, YYYY-MM (ej: '2030.01', '2030/1')
        if (preg_match('/^(\d{4})[\/\-\.\s](\d{1,2})$/', $limpio, $m)) {
            $anio = (int) $m[1];
            $mes = (int) $m[2];
            if ($mes >= 1 && $mes <= 12 && $anio >= 1990 && $anio <= 2100) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 3. Formato Día, Mes y Año completo con separadores /, - o . (con día o mes de 1 o 2 dígitos)
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $limpio, $m)) {
            $d = (int) $m[1];
            $mes = (int) $m[2];
            $anio = (int) $m[3];
            if ($anio < 100) {
                $anio += 2000;
            }
            // Si el segundo número > 12 y el primero <= 12, vino en formato MM/DD/YYYY
            if ($mes > 12 && $d <= 12) {
                [$d, $mes] = [$mes, $d];
            }
            if ($anio >= 1990 && $anio <= 2100 && checkdate($mes, $d, $anio)) {
                return sprintf('%04d-%02d-%02d', $anio, $mes, $d);
            }
        }

        // 4. Formato Mes y Año corto: MM/YY (ej: '01/30', '12-28')
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{2})$/', $limpio, $m)) {
            $mes = (int) $m[1];
            $anio = 2000 + (int) $m[2];
            if ($mes >= 1 && $mes <= 12) {
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->format('Y-m-d');
            }
        }

        // 5. Meses en español (ej: 'ene-2030', 'enero 2030', 'dic/28', '15-ene-2028')
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

        // 6. Formatos estándar de Carbon
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

        // 7. Si fue leído como flotante de Excel en rango válido (1990..2100)
        if (is_numeric($limpio) && (float) $limpio >= 32874 && (float) $limpio <= 73415) {
            try {
                return $this->validarFechaSql(ExcelDate::excelToDateTimeObject((float) $limpio)->format('Y-m-d'));
            } catch (\Throwable) {
            }
        }

        try {
            return $this->validarFechaSql(Carbon::parse($limpio)->format('Y-m-d'));
        } catch (\Throwable) {
            // Si el texto no representa una fecha reconocible, devolver null para no bloquear toda la carga
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
            return null;
        }

        if (is_numeric($valor)) {
            return (int) floor((float) $valor);
        }

        // Soportar separadores de miles o unidades (ej: '1.500', '500 uds')
        $limpio = preg_replace('/[^\d\-]/', '', str_replace('.', '', (string) $valor));
        if ($limpio !== '' && is_numeric($limpio)) {
            return (int) $limpio;
        }

        return null;
    }

    private function decimal(mixed $valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return 0.0;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        // Limpiar símbolos de moneda o separadores comunes si vienen formateados
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
