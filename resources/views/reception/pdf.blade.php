<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante Recepción #{{ $reception->correlativo ?? 'N/A' }}</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #334155; line-height: 1.4; }
        .header-table { width: 100%; border-bottom: 2px solid #0d9488; padding-bottom: 10px; margin-bottom: 15px; }
        .logo-container { width: 55%; vertical-align: middle; }
        .logo-icon { width: 24px; height: 24px; vertical-align: middle; margin-right: 6px; }
        .logo-title { font-size: 18px; font-weight: 800; color: #0f766e; margin: 0; display: inline-block; vertical-align: middle; }
        .logo-subtitle { font-size: 9.5px; color: #64748b; margin: 2px 0 0 30px; text-transform: uppercase; font-weight: 600; }
        .doc-meta { width: 45%; text-align: right; vertical-align: middle; }
        .doc-title-main { font-size: 13px; font-weight: bold; color: #1e293b; margin: 0 0 3px 0; text-transform: uppercase; }
        .doc-badge { font-size: 11px; font-weight: bold; color: #0f766e; background: #f0fdfa; padding: 2px 6px; border: 1px solid #ccfbf1; display: inline-block; margin-bottom: 2px; }
        .doc-date { font-size: 9px; color: #64748b; margin: 0; }
        .section-header { background-color: #f8fafc; color: #0f766e; font-size: 10px; font-weight: bold; text-transform: uppercase; padding: 5px 8px; margin: 12px 0 4px 0; border-left: 3px solid #0d9488; border: 1px solid #e2e8f0; letter-spacing: 0.3px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table th { background-color: #f1f5f9; color: #475569; font-size: 9px; text-transform: uppercase; text-align: left; padding: 5px 8px; border: 1px solid #cbd5e1; width: 22%; }
        .info-table td { font-size: 11px; color: #1e293b; font-weight: 500; padding: 5px 8px; border: 1px solid #cbd5e1; width: 28%; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .items-table th { background-color: #0f766e; color: #ffffff; font-size: 9px; text-transform: uppercase; text-align: center; padding: 7px 8px; border: 1px solid #0d9488; }
        .items-table td { font-size: 11px; color: #334155; padding: 7px 8px; border: 1px solid #cbd5e1; text-align: center; }
        .items-table td.text-left { text-align: left; }
        .items-table td.text-right { text-align: right; }
        .items-table tr:nth-child(even) { background-color: #f8fafc; }
        .total-row { background-color: #e6f6f5 !important; }
        .total-label { font-size: 10px; font-weight: bold; color: #0f766e; text-transform: uppercase; }
        .total-amount { font-size: 12px; font-weight: bold; color: #0f766e; }
        .signatures-wrapper { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .signatures-table { width: 100%; border-collapse: collapse; }
        .signature-box { width: 45%; text-align: center; vertical-align: bottom; padding: 5px; }
        .signature-image-container { height: 55px; margin-bottom: 2px; }
        .signature-img { max-width: 150px; height: 50px; object-fit: contain; }
        .signature-line { width: 90%; border-top: 1px solid #64748b; margin: 0 auto 4px auto; }
        .signature-title { font-size: 10px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase; }
        .signature-subtitle { font-size: 9px; color: #64748b; margin: 2px 0 0 0; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; height: 20px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <div>
                    <img src="{{ public_path('images/favicon.svg') }}" class="logo-icon" alt="Logo">
                    <h2 class="logo-title">DROGUERÍA DAS</h2>
                </div>
                <p class="logo-subtitle">Departamento de Salud - Abastecimiento Médico</p>
            </td>
            <td class="doc-meta">
                <h1 class="doc-title-main">Comprobante de Recepción</h1>
                <div class="doc-badge">Correlativo N°: #{{ $reception->correlativo ?? 'N/A' }}</div>
                <p class="doc-date">Fecha de Emisión: {{ date('d-m-Y H:i') }}</p>
            </td>
        </tr>
    </table>

    <div class="section-header">1. Detalles del Documento y Origen</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Tipo y Número</th>
                <td>{{ $reception->tipo_documento }} <strong>#{{ $reception->numero_documento }}</strong></td>
                <th>Fecha de Recepción</th>
                <td>{{ \Carbon\Carbon::parse($reception->fecha_recepcion)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Origen de Mercadería</th>
                <td>{{ $reception->origen }}</td>
                <th>Orden de Compra</th>
                <td>{{ $reception->orden_compra ?: 'Sin Orden' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">2. Información del Proveedor</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Razón Social</th>
                <td style="font-weight: bold;" colspan="3">
                    {{ $reception->provider->name ?? $reception->provider->nombre ?? $reception->provider->razon_social ?? 'Proveedor' }}
                </td>
            </tr>
            <tr>
                <th>RUT Comercial</th>
                <td style="font-family: monospace; font-size: 11px;" colspan="3">
                    {{ $reception->provider->rut ?? $reception->provider->nit ?? 'N/A' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">3. Desglose de Artículos Recepcionados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 32%; text-align: left;">Descripción del Producto</th>
                <th style="width: 14%;">Categoría</th>
                <th style="width: 14%;">Programa</th>
                <th style="width: 14%;">Lote / Vence</th>
                <th style="width: 8%;">Cant.</th>
                <th style="width: 18%; text-align: right;">Total Item (+IVA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reception->items as $item)
            <tr>
                <td class="text-left"><strong style="font-size: 11px;">{{ $item->product->nombre ?? $item->producto ?? 'N/A' }}</strong></td>
                <td><span style="background-color: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 9px; border: 1px solid #e2e8f0;">{{ $item->tipo_producto ?? $item->categoria }}</span></td>
                <td><span style="background-color: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 9px; border: 1px solid #e2e8f0;">{{ \App\Models\Inventory::where('product_id', $item->product_id) ->where('lote', $item->lote) ->where('correlativo_interno', $reception->correlativo) ->value('programa') ?: 'Sin Programa' }}</span></td>
                <td><strong>{{ $item->lote ?: 'S/L' }}</strong><br><span style="font-size: 8px; color: #64748b;">{{ $item->fecha_vencimiento ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</span></td>
                <td><strong style="font-size: 12px;">{{ $item->cantidad }}</strong></td>
                <td class="text-right">${{ number_format($item->valor_total_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="5" class="text-right total-label" style="border-right: none;">GRAN TOTAL RECEPCIÓN (CON IVA 19%):</td>
                <td class="text-right total-amount">${{ number_format($reception->valor_total_recepcion, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signatures-wrapper">
        <table class="signatures-table">
            <tr>
                <!-- Columnas vacías para centrar la firma usando el 100% de la tabla -->
                <td style="width: 27.5%;"></td>

                <td class="signature-box" style="width: 45%;">
                    <div class="signature-image-container">
                        @if(!empty($reception->firma_receptor))
                            <img src="{{ $reception->firma_receptor }}" class="signature-img">
                        @endif
                    </div>
                    <div class="signature-line"></div>
                    <p class="signature-title">Recibido Conforme (DAS)</p>
                    <p class="signature-subtitle">Firma y Timbre Encargado Droguería</p>
                </td>

                <td style="width: 27.5%;"></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento oficial generado por el Sistema de Control y Abastecimiento de Droguería DAS. Válido como comprobante interno.
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 22;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>
