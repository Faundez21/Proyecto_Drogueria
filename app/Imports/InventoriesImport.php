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

    private const ESTADOS = [
        'disponible' => 'Disponible',
        'cuarentena' => 'Cuarentena',
        'merma' => 'Merma',
    ];

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row): ?Inventory
    {
        $this->registro++;

        $producto = trim((string) ($row['producto'] ?? ''));
        if ($producto === '') {
            throw new \RuntimeException("Registro N° {$this->registro}: la columna PRODUCTO es obligatoria.");
        }

        $categoria = trim((string) ($row['categoria'] ?? '')) ?: 'General';

        $estado = strtolower(trim((string) ($row['estado'] ?? '')));
        if ($estado === '') {
            $estado = 'cuarentena';
        }
        if (! isset(self::ESTADOS[$estado])) {
            throw new \RuntimeException("Registro N° {$this->registro} ({$producto}): el ESTADO '{$estado}' no es válido. Use Disponible, Cuarentena o Merma.");
        }

        $cantidad = $this->entero($row['cantidad'] ?? null);
        if ($cantidad === null || $cantidad < 1) {
            throw new \RuntimeException("Registro N° {$this->registro} ({$producto}): la CANTIDAD debe ser un número entero mayor a 0.");
        }

        $fechaRecepcion = $this->fecha($row['fecha_recepcion'] ?? null);
        if ($fechaRecepcion === null) {
            throw new \RuntimeException("Registro N° {$this->registro} ({$producto}): la FECHA RECEPCION es obligatoria y debe ser una fecha válida.");
        }

        $tipoDocumento = trim((string) ($row['tipo_documento'] ?? ''));
        if ($tipoDocumento === '') {
            throw new \RuntimeException("Registro N° {$this->registro} ({$producto}): la columna TIPO DOCUMENTO es obligatoria.");
        }

        $numeroDocumento = trim((string) ($row['numero_documento'] ?? ''));
        if ($numeroDocumento === '') {
            throw new \RuntimeException("Registro N° {$this->registro} ({$producto}): la columna NUMERO DOCUMENTO es obligatoria.");
        }

        $batch = InventoryService::ingresar([
            'user_id' => $this->userId,
            'product_id' => InventoryService::productoId($producto),
            'fecha_recepcion' => $fechaRecepcion,
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'orden_compra' => trim((string) ($row['orden_de_compra'] ?? '')) ?: null,
            'origen_mercaderia' => trim((string) ($row['origen_mercaderia'] ?? '')) ?: null,
            'rut_proveedor' => trim((string) ($row['rut_proveedor'] ?? '')) ?: null,
            'razon_social' => trim((string) ($row['razon_social'] ?? '')) ?: null,
            'producto' => $producto,
            'categoria' => $categoria,
            'estado' => self::ESTADOS[$estado],
            'lote' => trim((string) ($row['lote'] ?? '')) ?: 'S/L',
            'fecha_vencimiento' => $this->fecha($row['fecha_vencimiento'] ?? null),
            'cantidad' => $cantidad,
            'valor_neto_unitario' => $this->decimal($row['valor_neto_unitario'] ?? null),
            'pasillo' => trim((string) ($row['pasillo'] ?? '')) ?: null,
            'estante' => trim((string) ($row['estante'] ?? '')) ?: null,
            'nivel' => trim((string) ($row['nivel'] ?? '')) ?: null,
            'posicion' => trim((string) ($row['posicion'] ?? '')) ?: null,
            'programa' => trim((string) ($row['programa'] ?? '')) ?: null,
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
            throw new \RuntimeException("Registro N° {$this->registro}: la fecha '{$valor}' no es válida. Use el formato DD/MM/AAAA.");
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

        if (! is_numeric($valor)) {
            throw new \RuntimeException("Registro N° {$this->registro}: el VALOR NETO UNITARIO '{$valor}' no es un número válido.");
        }

        return (float) $valor;
    }
}
