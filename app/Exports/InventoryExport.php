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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle, WithCustomStartCell, WithDrawings
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

    public function title(): string
    {
        return 'Inventario Droguería DAS';
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function drawings(): BaseDrawing|array
    {
        $logoPath = public_path('images/favicon4.png');
        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo Droguería DAS');
        $drawing->setDescription('Logo Institucional Droguería Comunal DAS');
        $drawing->setPath($logoPath);
        $drawing->setHeight(42);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(8);
        $drawing->setOffsetY(4);

        return [$drawing];
    }

    public function headings(): array
    {
        return [
            'PRODUCTO / DESCRIPCIÓN',
            'CATEGORÍA',
            'N° DE LOTE',
            'FECHA VENCIMIENTO',
            'PASILLO',
            'ESTANTE',
            'PROGRAMA',
            'STOCK FÍSICO',
            'VALOR NETO UNIT. ($)',
            'VALORIZACIÓN TOTAL ($)',
            'ESTADO BODEGA'
        ];
    }

    public function map($inventory): array
    {
        $producto = is_object($inventory->producto) ? ($inventory->producto->nombre ?? 'S/N') : ($inventory->producto ?? 'General');
        $pasillo = $inventory->pasillo_nombre ?? (is_object($inventory->aisle ?? null) ? $inventory->aisle->nombre ?? ($inventory->aisle->name ?? 'S/A') : $inventory->pasillo ?? 'S/A');
        $estante = $inventory->estanteria_nombre ?? (is_object($inventory->shelf ?? null) ? $inventory->shelf->nombre ?? ($inventory->shelf->name ?? 'S/E') : $inventory->estante ?? ($inventory->estanteria ?? 'S/E'));
        $stock = (int) ($inventory->stock_total ?? ($inventory->cantidad ?? 0));
        $estado = $inventory->estado ?? 'Disponible';
        $valorUnitario = (float) ($inventory->valor_neto_unitario ?? 0);
        $valorTotal = $stock * $valorUnitario;

        $fechaVencimiento = $inventory->fecha_vencimiento;
        if ($fechaVencimiento) {
            try {
                $fechaVencimiento = \Carbon\Carbon::parse($fechaVencimiento)->format('d/m/Y');
            } catch (\Exception $e) {
            }
        } else {
            $fechaVencimiento = 'N/A';
        }

        return [
            self::textoSeguro($producto),
            self::textoSeguro($inventory->categoria ?? 'General'),
            self::textoSeguro($inventory->lote ?? 'S/L'),
            $fechaVencimiento,
            self::textoSeguro($pasillo),
            self::textoSeguro($estante),
            self::textoSeguro($inventory->programa ?? 'General DAS'),
            $stock,
            (int) round($valorUnitario, 0),
            (int) round($valorTotal, 0),
            $estado
        ];
    }

    // un texto que empieza con = + - @ excel lo toma como formula: se le antepone un apostrofo
    private static function textoSeguro($valor)
    {
        $texto = (string) $valor;

        return preg_match('/^[=+\-@\t\r]/', $texto) ? "'" . $texto : $texto;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            4 => [
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F766E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // encabezado en las filas 1 a 3; la celda a1 queda libre para el logo
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'DROGUERÍA COMUNAL DAS - I. MUNICIPALIDAD DE TALCAHUANO');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF0F766E']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'indent' => 6,
                    ],
                ]);

                $sheet->mergeCells('A2:K2');
                $sheet->setCellValue('A2', 'REPORTE MAESTRO CONSOLIDADO DE EXISTENCIAS Y VALORIZACIÓN DE INVENTARIO · Emitido: ' . date('d/m/Y H:i') . ' hrs');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'indent' => 6,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(26);

                if ($highestRow >= 5) {
                    $sheet->getStyle('A4:K' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFCBD5E1'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // filas alternadas en gris para que se lean mejor
                    for ($r = 5; $r <= $highestRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(20);
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:K{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF8FAFC');
                        }
                    }

                    $sheet->getStyle("C5:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H5:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I5:J{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("K5:K{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("H5:H{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("I5:J{$highestRow}")->getNumberFormat()->setFormatCode('$#,##0');

                    // fila de totales al final
                    $totalRow = $highestRow + 1;
                    $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
                    $sheet->setCellValue("A{$totalRow}", 'TOTAL GENERAL CONSOLIDADO DEL REPORTE:');
                    $sheet->setCellValue("H{$totalRow}", "=SUM(H5:H{$highestRow})");
                    $sheet->setCellValue("I{$totalRow}", '');
                    $sheet->setCellValue("J{$totalRow}", "=SUM(J5:J{$highestRow})");
                    $sheet->setCellValue("K{$totalRow}", '');

                    $sheet->getRowDimension($totalRow)->setRowHeight(24);
                    $sheet->getStyle("A{$totalRow}:K{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF134E4A']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCFBF1']],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF0D9488'],
                            ],
                            'top' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => 'FF0D9488'],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("J{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("J{$totalRow}")->getNumberFormat()->setFormatCode('$#,##0');
                }
            },
        ];
    }
}
