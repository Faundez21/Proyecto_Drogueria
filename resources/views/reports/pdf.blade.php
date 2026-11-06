<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Consolidado de Inventario - Droguería DAS</title>
    <style>
        @page { margin: 26px 32px 42px 32px; }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.35;
        }
        .top-accent { height: 5px; background-color: #0d9488; width: 100%; margin-bottom: 10px; border-radius: 2px; }
        .header-table { width: 100%; border-bottom: 2px solid #0d9488; padding-bottom: 10px; margin-bottom: 12px; border-collapse: collapse; }
        .logo-container { width: 58%; vertical-align: middle; }
        .logo-icon { width: 30px; height: 30px; vertical-align: middle; margin-right: 8px; }
        .logo-title { font-size: 16px; font-weight: 800; color: #0f766e; margin: 0; display: inline-block; vertical-align: middle; }
        .logo-subtitle { font-size: 8.5px; color: #475569; margin: 2px 0 0 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; }
        .doc-meta { width: 42%; text-align: right; vertical-align: middle; }
        .doc-box { display: inline-block; border: 1.5px solid #0d9488; background-color: #f0fdfa; border-radius: 5px; padding: 6px 12px; text-align: right; }
        .doc-title { font-size: 10.5px; font-weight: 900; color: #134e4a; text-transform: uppercase; margin: 0 0 2px 0; }
        .doc-date { font-size: 8.5px; color: #475569; margin: 0; font-weight: 600; }

        /* KPI Strip */
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpi-card { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; width: 25%; }
        .kpi-label { font-size: 7.5px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px; letter-spacing: 0.3px; }
        .kpi-value { font-size: 11.5px; font-weight: 900; color: #0f766e; }

        /* Tabla principal */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.items-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: 800;
            font-size: 8px;
            text-transform: uppercase;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #115e59;
            letter-spacing: 0.3px;
        }
        table.items-table td {
            padding: 5.5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
            vertical-align: middle;
        }
        table.items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: 700; }
        
        .badge-cat { display: inline-block; padding: 1.5px 5px; font-size: 7.5px; font-weight: 700; border-radius: 3px; background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-prog { display: inline-block; padding: 1.5px 5px; font-size: 7.5px; font-weight: 700; border-radius: 3px; background-color: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .badge-prog-empty { display: inline-block; padding: 1.5px 5px; font-size: 7.5px; font-weight: 600; border-radius: 3px; background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
        .badge-red { color: #b91c1c; background-color: #fef2f2; border: 1px solid #fecaca; padding: 1.5px 5px; border-radius: 3px; font-weight: 800; font-size: 8px; display: inline-block; }
        .badge-orange { color: #c2410c; background-color: #fff7ed; border: 1px solid #fed7aa; padding: 1.5px 5px; border-radius: 3px; font-weight: 800; font-size: 8px; display: inline-block; }
        .badge-green { color: #15803d; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 1.5px 5px; border-radius: 3px; font-weight: 800; font-size: 8px; display: inline-block; }

        .total-row td { background-color: #ccfbf1 !important; border-top: 2px solid #0d9488; padding: 7px 6px; font-weight: 900; color: #134e4a; font-size: 10px; }
        .footer { position: fixed; bottom: -25px; left: 0; right: 0; height: 20px; text-align: left; font-size: 8px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 4px; }
    </style>
</head>
<body>

    <div class="top-accent"></div>

    @php
        $totalRegistros = count($inventories);
        $totalUnidadesReporte = 0;
        $sumaTotalPdf = 0;
        $lotesPorVencer = 0;

        foreach ($inventories as $itemTmp) {
            $stTmp = (float) ($itemTmp->stock_total ?? ($itemTmp->cantidad ?? 0));
            $vuTmp = (float) ($itemTmp->valor_neto_unitario ?? 0);
            $totalUnidadesReporte += $stTmp;
            $sumaTotalPdf += ($stTmp * $vuTmp);
            if (!empty($itemTmp->fecha_vencimiento)) {
                try {
                    if (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($itemTmp->fecha_vencimiento), false) < 90) {
                        $lotesPorVencer++;
                    }
                } catch (\Exception $e) {}
            }
        }
    @endphp

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <div>
                    @php
                        $logoPath = public_path('images/favicon4.png');
                        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
                    @endphp
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" class="logo-icon" alt="Logo">
                    @endif
                    <div style="display: inline-block; vertical-align: middle;">
                        <h2 class="logo-title">DROGUERÍA COMUNAL DAS</h2>
                        <p class="logo-subtitle">I. Municipalidad de Talcahuano · Reporte Maestro de Existencias y Valorización</p>
                    </div>
                </div>
            </td>
            <td class="doc-meta">
                <div class="doc-box">
                    <p class="doc-title">Reporte Consolidado de Inventario</p>
                    <p class="doc-date">Emitido el: {{ date('d/m/Y H:i') }} hrs</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <span class="kpi-label">Total Lotes / Registros</span>
                <span class="kpi-value">{{ number_format($totalRegistros, 0, ',', '.') }}</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Existencia Física Total</span>
                <span class="kpi-value">{{ number_format($totalUnidadesReporte, 0, ',', '.') }} uds.</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Lotes Críticos (&lt; 90 días)</span>
                <span class="kpi-value" style="color: {{ $lotesPorVencer > 0 ? '#dc2626' : '#0f766e' }};">{{ number_format($lotesPorVencer, 0, ',', '.') }}</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Valorización Total Bodega</span>
                <span class="kpi-value">${{ number_format($sumaTotalPdf, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    @if(!empty($isTruncated))
        <div style="background-color: #fffbeb; border: 1px solid #fef3c7; color: #b45309; padding: 6px 10px; border-radius: 4px; font-size: 8px; margin-bottom: 10px; font-weight: 700;">
            Aviso de volumen: Mostrando los primeros {{ count($inventories) }} registros de un total de {{ number_format($totalItems, 0, ',', '.') }}. Para obtener el consolidado completo de inventario sin restricciones de paginación, descargue la versión Excel.
        </div>
    @endif

    {{-- La tabla se divide en bloques de 100 filas: así el PDF usa la mitad de memoria y tiempo --}}
    @if($totalRegistros == 0)
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 3.5%;" class="text-center">#</th>
                <th style="width: 22%;">Producto Principal</th>
                <th style="width: 11.5%;">Categoría</th>
                <th style="width: 12%;">Programa</th>
                <th style="width: 9.5%;">N° Lote</th>
                <th style="width: 9.5%;" class="text-center">Vencimiento</th>
                <th style="width: 11.5%;">Ubicación Física</th>
                <th style="width: 6%;" class="text-center">Stock</th>
                <th style="width: 6.5%;" class="text-right">V. Unit.</th>
                <th style="width: 8%;" class="text-right">V. Total</th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td colspan="10" class="text-center" style="padding: 18px; color: #64748b;">No hay registros de inventario que coincidan con los filtros seleccionados.</td>
                </tr>
        </tbody>
    </table>
    @else
        @php $bloques = collect($inventories)->chunk(100); @endphp
        @foreach($bloques as $numBloque => $bloque)
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 3.5%;" class="text-center">#</th>
                <th style="width: 22%;">Producto Principal</th>
                <th style="width: 11.5%;">Categoría</th>
                <th style="width: 12%;">Programa</th>
                <th style="width: 9.5%;">N° Lote</th>
                <th style="width: 9.5%;" class="text-center">Vencimiento</th>
                <th style="width: 11.5%;">Ubicación Física</th>
                <th style="width: 6%;" class="text-center">Stock</th>
                <th style="width: 6.5%;" class="text-right">V. Unit.</th>
                <th style="width: 8%;" class="text-right">V. Total</th>
            </tr>
        </thead>
        <tbody>
                @foreach($bloque as $idx => $inv)
                    @php
                        $producto = is_object($inv->producto) ? ($inv->producto->nombre ?? 'S/N') : ($inv->producto ?? 'General');
                        $pasillo = $inv->pasillo_nombre ?? (is_object($inv->aisle ?? null) ? $inv->aisle->nombre ?? ($inv->aisle->name ?? 'S/A') : $inv->pasillo ?? 'S/A');
                        $estante = $inv->estanteria_nombre ?? (is_object($inv->shelf ?? null) ? $inv->shelf->nombre ?? ($inv->shelf->name ?? 'S/E') : $inv->estante ?? ($inv->estanteria ?? 'S/E'));
                        $nivel = $inv->nivel ?? null;
                        $ubicacionTexto = trim($pasillo . ' - ' . $estante . ($nivel ? ' (N' . $nivel . ')' : ''));
                        $stock = (float) ($inv->stock_total ?? ($inv->cantidad ?? 0));
                        $valorUnitario = (float) ($inv->valor_neto_unitario ?? 0);
                        $valorTotal = $stock * $valorUnitario;
                        $programaTxt = !empty($inv->programa) ? trim((string) $inv->programa) : '';

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
                        <td class="text-center" style="color: #64748b; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td class="font-bold" style="color: #0f172a;">{{ $producto }}</td>
                        <td><span class="badge-cat">{{ $inv->categoria ?? 'General' }}</span></td>
                        <td>
                            @if($programaTxt !== '')
                                <span class="badge-prog">{{ $programaTxt }}</span>
                            @else
                                <span class="badge-prog-empty">Sin programa</span>
                            @endif
                        </td>
                        <td style="font-family: 'Courier New', monospace; font-weight: 700;">{{ $inv->lote ?? 'S/L' }}</td>
                        <td class="text-center">
                            @if($classVenc)
                                <span class="{{ $classVenc }}">{{ $textoVenc }}</span>
                            @else
                                {{ $textoVenc }}
                            @endif
                        </td>
                        <td style="font-size: 8.5px; color: #334155;">{{ $ubicacionTexto }}</td>
                        <td class="text-center font-bold">{{ number_format($stock, 0, ',', '.') }}</td>
                        <td class="text-right">${{ number_format($valorUnitario, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #0f766e;">${{ number_format($valorTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if($loop->last)
                <tr class="total-row">
                    <td colspan="7" class="text-right">TOTALES CONSOLIDADOS DEL REPORTE:</td>
                    <td class="text-center">{{ number_format($totalUnidadesReporte, 0, ',', '.') }}</td>
                    <td class="text-right">VALOR TOTAL:</td>
                    <td class="text-right" style="color: #0f766e;">${{ number_format($sumaTotalPdf, 0, ',', '.') }}</td>
                </tr>
                @endif
        </tbody>
    </table>
        @endforeach
    @endif

    <div class="footer">
        Reporte oficial generado por el Sistema de Control y Abastecimiento de Droguería DAS Talcahuano · Confidencial de Uso Interno.
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = $pdf->get_width() - $width - 32;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.39, 0.45, 0.54));
        }
    </script>
</body>
</html>
