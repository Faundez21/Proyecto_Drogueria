<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante Recepción #{{ $reception->correlativo ?? 'N/A' }}</title>
    <style>
        @page { margin: 28px 36px 45px 36px; }
        body { font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; font-size: 10.5px; color: #1e293b; line-height: 1.4; }
        
        /* Cabecera Institucional */
        .top-accent { height: 5px; background-color: #0d9488; width: 100%; margin-bottom: 12px; border-radius: 2px; }
        .header-table { width: 100%; border-bottom: 2px solid #0d9488; padding-bottom: 12px; margin-bottom: 14px; border-collapse: collapse; }
        .logo-container { width: 55%; vertical-align: middle; }
        .logo-icon { width: 32px; height: 32px; vertical-align: middle; margin-right: 8px; }
        .logo-title { font-size: 17px; font-weight: 800; color: #0f766e; margin: 0; display: inline-block; vertical-align: middle; letter-spacing: 0.4px; }
        .logo-subtitle { font-size: 9px; color: #475569; margin: 2px 0 0 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .logo-address { font-size: 8.5px; color: #64748b; margin: 1px 0 0 0; }
        
        .doc-meta { width: 45%; text-align: right; vertical-align: middle; }
        .doc-box { display: inline-block; border: 1.5px solid #0d9488; background-color: #f0fdfa; border-radius: 6px; padding: 8px 14px; text-align: center; }
        .doc-type-label { font-size: 9px; font-weight: 800; color: #0f766e; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; }
        .doc-correlativo { font-size: 14px; font-weight: 900; color: #134e4a; margin: 0 0 3px 0; font-family: 'Courier New', monospace; }
        .doc-date { font-size: 8.5px; color: #475569; margin: 0; font-weight: 600; }

        /* Secciones y Tablas de Información */
        .section-header { background-color: #f1f5f9; color: #0f766e; font-size: 9.5px; font-weight: 800; text-transform: uppercase; padding: 5px 10px; margin: 12px 0 5px 0; border-left: 4px solid #0d9488; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .info-table th { background-color: #f8fafc; color: #475569; font-size: 8.5px; font-weight: 800; text-transform: uppercase; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; width: 20%; letter-spacing: 0.3px; }
        .info-table td { font-size: 10px; color: #0f172a; font-weight: 600; padding: 6px 8px; border: 1px solid #cbd5e1; width: 30%; }

        /* KPI Strip Resumen */
        .kpi-table { width: 100%; border-collapse: collapse; margin: 10px 0 8px 0; }
        .kpi-card { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; width: 33.33%; }
        .kpi-label { font-size: 8px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px; }
        .kpi-value { font-size: 12px; font-weight: 900; color: #0f766e; }

        /* Tabla de Artículos */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .items-table th { background-color: #0f766e; color: #ffffff; font-size: 8.5px; font-weight: 800; text-transform: uppercase; text-align: center; padding: 7px 6px; border: 1px solid #115e59; letter-spacing: 0.3px; }
        .items-table td { font-size: 9.5px; color: #1e293b; padding: 6px 6px; border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; }
        .items-table td.text-left { text-align: left; }
        .items-table td.text-right { text-align: right; }
        .items-table tr:nth-child(even) td { background-color: #f8fafc; }
        
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: 700; border-radius: 3px; background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-teal { background-color: #f0fdfa; color: #0f766e; border-color: #99f6e4; }

        /* Totales */
        .subtotal-row td { background-color: #f8fafc !important; font-size: 9.5px; font-weight: 700; color: #475569; padding: 5px 8px; }
        .total-row td { background-color: #ccfbf1 !important; border-top: 2px solid #0d9488; padding: 7px 8px; }
        .total-label { font-size: 10px; font-weight: 900; color: #134e4a; text-transform: uppercase; }
        .total-amount { font-size: 12px; font-weight: 900; color: #0f766e; }

        /* Firmas */
        .signatures-wrapper { margin-top: 32px; width: 100%; page-break-inside: avoid; }
        .signatures-table { width: 100%; border-collapse: collapse; }
        .signature-box { width: 45%; text-align: center; vertical-align: bottom; padding: 6px; }
        .signature-image-container { height: 55px; margin-bottom: 3px; }
        .signature-img { max-width: 150px; height: 50px; object-fit: contain; }
        .signature-line { width: 85%; border-top: 1.5px solid #334155; margin: 0 auto 4px auto; }
        .signature-title { font-size: 9.5px; font-weight: 800; color: #0f172a; margin: 0; text-transform: uppercase; }
        .signature-subtitle { font-size: 8.5px; color: #64748b; margin: 2px 0 0 0; }

        /* Pie de página */
        .footer { position: fixed; bottom: -25px; left: 0; right: 0; height: 22px; text-align: left; font-size: 8px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 4px; }
    </style>
</head>
<body>

    <div class="top-accent"></div>

    @php
        $totalUnidades = $reception->items->sum('cantidad');
        $totalLineas = $reception->items->count();
        $netoCalculado = $reception->valor_total_recepcion ? round($reception->valor_total_recepcion / 1.19) : 0;
        $ivaCalculado = $reception->valor_total_recepcion ? ($reception->valor_total_recepcion - $netoCalculado) : 0;
    @endphp

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <div>
                    @if(file_exists(public_path('images/favicon4.png')))
                        <img src="{{ public_path('images/favicon4.png') }}" class="logo-icon" alt="Logo">
                    @endif
                    <div style="display: inline-block; vertical-align: middle;">
                        <h2 class="logo-title">DROGUERÍA COMUNAL DAS</h2>
                        <p class="logo-subtitle">I. Municipalidad de Talcahuano · Dirección de Administración de Salud</p>
                        <p class="logo-address">Blanco Encalada 450, Talcahuano · Bodega Central de Abastecimiento</p>
                    </div>
                </div>
            </td>
            <td class="doc-meta">
                <div class="doc-box">
                    <p class="doc-type-label">Acta de Recepción Conforme</p>
                    <p class="doc-correlativo">N° {{ $reception->correlativo ?? 'N/A' }}</p>
                    <p class="doc-date">Emisión: {{ date('d/m/Y H:i') }} hrs</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-header">1. Antecedentes del Documento Tributario y Origen</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Documento Respaldo</th>
                <td>{{ $reception->tipo_documento }} <strong style="color: #0f766e;">#{{ $reception->numero_documento }}</strong></td>
                <th>Fecha de Recepción</th>
                <td>{{ \Carbon\Carbon::parse($reception->fecha_recepcion)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Origen Mercadería</th>
                <td>{{ $reception->origen ?: 'Compra Directa / CENABAST' }}</td>
                <th>Orden de Compra</th>
                <td style="font-family: 'Courier New', monospace;">{{ $reception->orden_compra ?: 'Sin Orden Asociada' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">2. Identificación del Proveedor</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Razón Social</th>
                <td style="font-weight: 800;">
                    {{ $reception->provider->name ?? $reception->provider->nombre ?? $reception->provider->razon_social ?? 'Proveedor No Especificado' }}
                </td>
                <th>RUT Comercial</th>
                <td style="font-family: 'Courier New', monospace; font-size: 10.5px;">
                    {{ $reception->provider->rut ?? $reception->provider->nit ?? 'N/A' }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <span class="kpi-label">Lotes / Líneas Ingresadas</span>
                <span class="kpi-value">{{ number_format($totalLineas, 0, ',', '.') }} ítems</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Total Unidades Recepcionadas</span>
                <span class="kpi-value">{{ number_format($totalUnidades, 0, ',', '.') }} uds.</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Monto Total Documento (+IVA)</span>
                <span class="kpi-value">${{ number_format($reception->valor_total_recepcion ?? 0, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <div class="section-header">3. Detalle Técnico de Productos Recepcionados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 31%; text-align: left;">Descripción del Producto</th>
                <th style="width: 13%;">Categoría</th>
                <th style="width: 13%;">Programa</th>
                <th style="width: 14%;">Lote / Vence</th>
                <th style="width: 9%;">Cantidad</th>
                <th style="width: 15%; text-align: right;">Total Ítem (+IVA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reception->items as $idx => $item)
            @php
                $prog = \App\Models\Inventory::where('product_id', $item->product_id)
                    ->where('lote', $item->lote)
                    ->where('correlativo_interno', $reception->correlativo)
                    ->value('programa') ?: 'General DAS';
            @endphp
            <tr>
                <td style="color: #64748b; font-weight: bold;">{{ $idx + 1 }}</td>
                <td class="text-left">
                    <strong style="font-size: 10px; color: #0f172a;">{{ $item->product->nombre ?? $item->producto ?? 'N/A' }}</strong>
                </td>
                <td><span class="badge">{{ $item->tipo_producto ?? $item->categoria ?? 'Insumos' }}</span></td>
                <td><span class="badge badge-teal">{{ $prog }}</span></td>
                <td>
                    <strong style="font-family: 'Courier New', monospace; font-size: 9.5px;">{{ $item->lote ?: 'S/L' }}</strong><br>
                    <span style="font-size: 8px; color: #64748b;">Vence: {{ $item->fecha_vencimiento ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</span>
                </td>
                <td><strong style="font-size: 10.5px; color: #0f172a;">{{ number_format($item->cantidad, 0, ',', '.') }}</strong></td>
                <td class="text-right" style="font-weight: 700;">${{ number_format($item->valor_total_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="subtotal-row">
                <td colspan="5" class="text-right">TOTAL UNIDADES RECEPCIONADAS:</td>
                <td style="text-align: center; color: #0f766e;">{{ number_format($totalUnidades, 0, ',', '.') }}</td>
                <td class="text-right">Neto Est.: ${{ number_format($netoCalculado, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right total-label">GRAN TOTAL RECEPCIÓN (CON IVA 19% INCLUIDO):</td>
                <td class="text-right total-amount">${{ number_format($reception->valor_total_recepcion, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signatures-wrapper">
        <table class="signatures-table">
            <tr>
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
        Documento oficial emitido por el Sistema de Gestión Farmacéutica y Logística - Droguería Comunal DAS Talcahuano.
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = $pdf->get_width() - $width - 36;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.39, 0.45, 0.54));
        }
    </script>
</body>
</html>
