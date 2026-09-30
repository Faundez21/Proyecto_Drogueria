<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #0d9488;
        }
        .date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #0d9488;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #0f766e;
        }
        td {
            padding: 7px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge-red { color: #dc2626; font-weight: bold;}
        .badge-green { color: #16a34a; font-weight: bold;}
        .badge-orange { color: #ea580c; font-weight: bold;}
    </style>
</head>
<body>

    <div class="header">
        <h2>Reporte Consolidado de Inventario</h2>
        <div class="date">Generado el: {{ date('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto Principal</th>
                <th>Categoría</th>
                <th>Lote</th>
                <th>Vencimiento</th>
                <th>Ubicación (Pasillo - Estante)</th>
                <th class="text-center">Stock</th>
            </tr>
        </thead>
        <tbody>
            @if(count($inventories) == 0)
                <tr>
                    <td colspan="6" class="text-center">No hay registros que coincidan con la búsqueda.</td>
                </tr>
            @else
                @foreach($inventories as $inv)
                    @php
                        // Normalizando datos igual que en la exportación Excel
                        $producto = is_object($inv->producto) ? ($inv->producto->nombre ?? 'S/N') : ($inv->producto ?? 'General');
                        $pasillo = $inv->pasillo_nombre ?? (is_object($inv->aisle ?? null) ? $inv->aisle->nombre ?? ($inv->aisle->name ?? 'S/A') : $inv->pasillo ?? 'S/A');
                        $estante = $inv->estanteria_nombre ?? (is_object($inv->shelf ?? null) ? $inv->shelf->nombre ?? ($inv->shelf->name ?? 'S/E') : $inv->estante ?? ($inv->estanteria ?? 'S/E'));
                        $stock = $inv->stock_total ?? ($inv->cantidad ?? 0);
                        
                        $fechaVenc = $inv->fecha_vencimiento;
                        $textoVenc = 'N/A';
                        $classVenc = '';

                        if ($fechaVenc) {
                            try {
                                $carbonDate = \Carbon\Carbon::parse($fechaVenc);
                                $textoVenc = $carbonDate->format('d/m/Y');
                                if ($carbonDate->isPast()) {
                                    $classVenc = 'badge-red';
                                } elseif (\Carbon\Carbon::now()->diffInDays($carbonDate, false) < 90) {
                                    $classVenc = 'badge-orange';
                                } else {
                                    $classVenc = 'badge-green';
                                }
                            } catch (\Exception $e) {
                                $textoVenc = $fechaVenc;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $producto }}</td>
                        <td>{{ $inv->categoria ?? 'General' }}</td>
                        <td>{{ $inv->lote ?? 'S/L' }}</td>
                        <td class="{{ $classVenc }}">{{ $textoVenc }}</td>
                        <td>{{ $pasillo }} - {{ $estante }}</td>
                        <td class="text-center font-bold">{{ number_format($stock, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>