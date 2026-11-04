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
    private bool $headersValidated = false;
    private array $seenRows = [];

    private const ESTADOS = [
        'disponible' => 'Disponible',
        'cuarentena' => 'Cuarentena',
        'merma' => 'Merma',
    ];

    private const REQUIRED_COLUMNS = [
        'producto' => 'PRODUCTO',
        'cantidad' => 'CANTIDAD',
        'fecha_recepcion' => 'FECHA RECEPCION',
        'tipo_documento' => 'TIPO DOCUMENTO',
        'numero_documento' => 'NUMERO DOCUMENTO',
    ];

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row): ?Inventory
    {
        $this->registro++;

        // 1. Validar que el archivo tenga las columnas de la plantilla oficial
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
                    "Formato de plantilla incompatible: Faltan las siguientes columnas obligatorias en el encabezado del archivo: {$faltantes}. Por favor descargue y utilice la Plantilla Oficial de Carga Masiva."
                );
            }

            $this->headersValidated = true;
        }

        $producto = trim((string) ($row['producto'] ?? ''));
        if ($producto === '') {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . ": La columna PRODUCTO es obligatoria y no puede estar vacía.");
        }

        $categoria = trim((string) ($row['categoria'] ?? '')) ?: 'General';

        $estado = strtolower(trim((string) ($row['estado'] ?? '')));
        if ($estado === '') {
            $estado = 'disponible';
        }
        if (! isset(self::ESTADOS[$estado])) {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): El ESTADO '{$estado}' no es válido. Use Disponible, Cuarentena o Merma.");
        }

        $cantidad = $this->entero($row['cantidad'] ?? null);
        if ($cantidad === null || $cantidad < 1) {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): La CANTIDAD debe ser un número entero mayor a 0.");
        }

        $fechaRecepcion = $this->fecha($row['fecha_recepcion'] ?? null);
        if ($fechaRecepcion === null) {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): La FECHA RECEPCION es obligatoria y debe tener un formato válido (DD/MM/AAAA).");
        }

        $tipoDocumento = trim((string) ($row['tipo_documento'] ?? ''));
        if ($tipoDocumento === '') {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): La columna TIPO DOCUMENTO es obligatoria.");
        }

        $numeroDocumento = trim((string) ($row['numero_documento'] ?? ''));
        if ($numeroDocumento === '') {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . " ({$producto}): La columna NUMERO DOCUMENTO es obligatoria.");
        }

        $lote = trim((string) ($row['lote'] ?? '')) ?: 'S/L';

        // 2. Detectar filas duplicadas dentro del mismo archivo Excel
        $rowSignature = mb_strtolower("{$tipoDocumento}|{$numeroDocumento}|{$producto}|{$lote}|{$fechaRecepcion}|{$cantidad}");
        if (isset($this->seenRows[$rowSignature])) {
            $filaOriginal = $this->seenRows[$rowSignature];
            throw new \RuntimeException(
                "Registro duplicado dentro del archivo (Fila N° " . ($this->registro + 1) . "): El producto «{$producto}» (Lote: {$lote}, Doc: {$tipoDocumento} #{$numeroDocumento}) ya aparece en la Fila N° {$filaOriginal} de este mismo archivo."
            );
        }
        $this->seenRows[$rowSignature] = $this->registro + 1;

        // 3. Detectar si este mismo documento + lote + producto ya fue cargado previamente en la base de datos
        $yaExisteDocumentoLote = Inventory::where('tipo_documento', $tipoDocumento)
            ->where('numero_documento', $numeroDocumento)
            ->where('producto', $producto)
            ->where('lote', $lote)
            ->exists();

        if ($yaExisteDocumentoLote) {
            throw new \RuntimeException(
                "Carga duplicada detectada en Fila N° " . ($this->registro + 1) . ": El producto «{$producto}» (Lote: {$lote}) asociado al documento {$tipoDocumento} N° {$numeroDocumento} ya se encuentra registrado en el sistema."
            );
        }

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
            'estado' => self::ESTADOS[$estado],
            'lote' => $lote,
            'fecha_vencimiento' => $this->fecha($row['fecha_vencimiento'] ?? null),
            'cantidad' => $cantidad,
            'valor_neto_unitario' => $this->decimal($row['valor_neto_unitario'] ?? null),
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
            'estado_lote' => self::ESTADOS[$estado],
        ]);

        $this->importados++;

        return $batch;
    }

    public function importados(): int
    {
        return $this->importados;
    }

    private function fecha(mixed $valor): ?string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            return ExcelDate::excelToDateTimeObject((float) $valor)->format('Y-m-d');
        }

        if (is_object($valor) && $valor instanceof \DateTimeInterface) {
            return Carbon::instance($valor)->format('Y-m-d');
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y'] as $formato) {
            try {
                if (! Carbon::hasFormat($valor, $formato)) {
                    continue;
                }
                $fecha = Carbon::createFromFormat($formato, $valor);
                if ($fecha !== false) {
                    return $fecha->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Throwable) {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . ": La fecha '{$valor}' no es válida. Use el formato DD/MM/AAAA.");
        }
    }

    private function entero(mixed $valor): ?int
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        if (! is_numeric($valor)) {
            return null;
        }

        return (int) floor((float) $valor);
    }

    private function decimal(mixed $valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        // Limpiar símbolos de moneda o separadores comunes si vienen formateados
        if (is_string($valor)) {
            $limpio = str_replace(['$', ' ', '.'], '', $valor);
            $limpio = str_replace(',', '.', $limpio);
            if (is_numeric($limpio)) {
                return (float) $limpio;
            }
        }

        if (! is_numeric($valor)) {
            throw new \RuntimeException("Fila N° " . ($this->registro + 1) . ": El VALOR NETO UNITARIO '{$valor}' no es un número válido.");
        }

        return (float) $valor;
    }
}
