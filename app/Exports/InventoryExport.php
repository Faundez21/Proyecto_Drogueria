<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected EloquentBuilder $query;

    public function __construct(EloquentBuilder $query)
    {
        $this->query = $query;
    }

    public function query(): QueryBuilder|EloquentBuilder|Relation
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Categoría',
            'Lote',
            'Fecha Vencimiento',
            'Pasillo',
            'Estante',
            'Programa',
            'Stock Físico',
            'Valor Neto Unitario',
            'Valor Total',
            'Estado'
        ];
    }

    public function map($inventory): array
    {
        $producto = is_object($inventory->producto) ? ($inventory->producto->nombre ?? 'S/N') : ($inventory->producto ?? 'General');
        $pasillo = $inventory->pasillo_nombre ?? (is_object($inventory->aisle ?? null) ? $inventory->aisle->nombre ?? ($inventory->aisle->name ?? 'S/A') : $inventory->pasillo ?? 'S/A');
        $estante = $inventory->estanteria_nombre ?? (is_object($inventory->shelf ?? null) ? $inventory->shelf->nombre ?? ($inventory->shelf->name ?? 'S/E') : $inventory->estante ?? ($inventory->estanteria ?? 'S/E'));
        $stock = $inventory->stock_total ?? ($inventory->cantidad ?? 0);
        $estado = $inventory->estado ?? 'Activo';
        $valorUnitario = (float) ($inventory->valor_neto_unitario ?? 0);
        $valorTotal = ((float) $stock) * $valorUnitario;

        $fechaVencimiento = $inventory->fecha_vencimiento;
        if($fechaVencimiento) {
            try {
                $fechaVencimiento = \Carbon\Carbon::parse($fechaVencimiento)->format('d/m/Y');
            } catch (\Exception $e) {
                // Mantiene el valor original si falla
            }
        } else {
            $fechaVencimiento = 'N/A';
        }

        return [
            $producto,
            $inventory->categoria ?? 'General',
            $inventory->lote ?? 'S/L',
            $fechaVencimiento,
            $pasillo,
            $estante,
            $inventory->programa ?? 'S/P',
            $stock,
            $valorUnitario,
            $valorTotal,
            $estado
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0D9488']]],
        ];
    }
}