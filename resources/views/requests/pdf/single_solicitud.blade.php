<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud {{ $solicitud->numero_solicitud }} - {{ $solicitud->establecimiento }}</title>
    <style>
        @page { margin: 28px 36px 45px 36px; }
        body { font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        
        /* Cabecera Institucional */
        .top-accent { height: 5px; background-color: #0284c7; width: 100%; margin-bottom: 12px; border-radius: 2px; }
        .header-table { width: 100%; border-bottom: 2px solid #0284c7; padding-bottom: 12px; margin-bottom: 14px; border-collapse: collapse; }
        .logo-container { width: 55%; vertical-align: middle; }
        .logo-icon { width: 32px; height: 32px; vertical-align: middle; margin-right: 8px; }
        .logo-title { font-size: 17px; font-weight: 800; color: #0369a1; margin: 0; display: inline-block; vertical-align: middle; letter-spacing: 0.4px; }
        .logo-subtitle { font-size: 9px; color: #475569; margin: 2px 0 0 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .logo-address { font-size: 8.5px; color: #64748b; margin: 1px 0 0 0; }
        
        .doc-meta { width: 45%; text-align: right; vertical-align: middle; }
        .doc-box { display: inline-block; border: 1.5px solid #0284c7; background-color: #f0f9ff; border-radius: 6px; padding: 8px 14px; text-align: center; }
        .doc-type-label { font-size: 9px; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; }
        .doc-correlativo { font-size: 13px; font-weight: 900; color: #0c4a6e; margin: 0 0 3px 0; font-family: 'Courier New', monospace; }
        .doc-date { font-size: 8.5px; color: #475569; margin: 0; font-weight: 600; }

        /* Secciones y Tablas de Información */
        .section-header { background-color: #f1f5f9; color: #0369a1; font-size: 9.5px; font-weight: 800; text-transform: uppercase; padding: 5px 10px; margin: 12px 0 5px 0; border-left: 4px solid #0284c7; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .info-table th { background-color: #f8fafc; color: #475569; font-size: 8.5px; font-weight: 800; text-transform: uppercase; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; width: 22%; letter-spacing: 0.3px; }
        .info-table td { font-size: 9.5px; color: #0f172a; font-weight: 600; padding: 6px 8px; border: 1px solid #cbd5e1; width: 28%; }

        /* KPI Strip Resumen */
        .kpi-table { width: 100%; border-collapse: collapse; margin: 10px 0 8px 0; }
        .kpi-card { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; width: 33.33%; }
        .kpi-label { font-size: 8px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px; }
        .kpi-value { font-size: 12px; font-weight: 900; color: #0369a1; }

        /* Tabla de Artículos */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .items-table th { background-color: #0369a1; color: #ffffff; font-size: 8px; font-weight: 800; text-transform: uppercase; text-align: center; padding: 6px 4px; border: 1px solid #075985; letter-spacing: 0.2px; }
        .items-table td { font-size: 9px; color: #1e293b; padding: 5px 4px; border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; }
        .items-table td.text-left { text-align: left; }
        .items-table tr:nth-child(even) td { background-color: #f8fafc; }

        .badge { display: inline-block; padding: 2px 4px; font-size: 7.5px; font-weight: 700; border-radius: 3px; background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-amber { display: inline-block; padding: 2px 4px; font-size: 7.5px; font-weight: 700; border-radius: 3px; background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

        .total-row td { background-color: #e0f2fe !important; border-top: 2px solid #0284c7; padding: 7px 8px; }
        .total-label { font-size: 9.5px; font-weight: 900; color: #0c4a6e; text-transform: uppercase; text-align: right; }
        .total-amount { font-size: 11px; font-weight: 900; color: #0369a1; text-align: center; }

        /* Observaciones */
        .obs-box { margin-top: 10px; border: 1px dashed #94a3b8; background-color: #f8fafc; padding: 6px 10px; font-size: 8.5px; color: #475569; border-radius: 4px; }

        /* Firmas: Única firma del solicitante */
        .signatures-wrapper { margin-top: 28px; width: 100%; page-break-inside: avoid; }
        .signatures-table { width: 100%; border-collapse: collapse; }
        .signature-box { text-align: center; vertical-align: bottom; padding: 6px; }
        .signature-image-container { height: 60px; margin-bottom: 3px; }
        .signature-img { max-width: 220px; height: 55px; object-fit: contain; }
        .signature-line { width: 60%; border-top: 1.5px solid #334155; margin: 0 auto 4px auto; }
        .signature-title { font-size: 9.5px; font-weight: 800; color: #0f172a; margin: 0; text-transform: uppercase; }
        .signature-subtitle { font-size: 8.5px; color: #475569; margin: 2px 0 0 0; }
        .signature-meta { font-size: 8px; color: #64748b; margin: 1px 0 0 0; }

        /* Pie de página */
        .footer { position: fixed; bottom: -25px; left: 0; right: 0; height: 22px; text-align: left; font-size: 8px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 4px; }
    </style>
</head>
<body>

    <div class="top-accent"></div>

    @php
        $totalItems = $items->count();
        $totalSolicitado = $items->sum('cantidad_solicitada');
        $totalDespachado = $items->sum('cantidad_despachada');
        $totalPendiente = $items->sum('cantidad_pendiente');
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
                        <p class="logo-subtitle">I. Municipalidad de Talcahuano · Dirección de Administración de Salud</p>
                        <p class="logo-address">Blanco Encalada 450, Talcahuano · Distribución y Logística Sanitaria</p>
                    </div>
                </div>
            </td>
            <td class="doc-meta">
                <div class="doc-box">
                    <p class="doc-type-label">SOLICITUD DE PEDIDO</p>
                    <p class="doc-correlativo">{{ $solicitud->numero_solicitud }}</p>
                    <p class="doc-date">Fecha Solicitud: {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-header">1. Información del Centro Solicitante y Responsable</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Centro / Establecimiento</th>
                <td style="font-weight: 800; color: #0369a1;">{{ $solicitud->establecimiento }}</td>
                <th>Categoría Centro</th>
                <td>{{ $esCesfam ? 'Centro de Salud Familiar (CESFAM)' : 'Establecimiento de Salud (CECOSF / Posta / SAR)' }}</td>
            </tr>
            <tr>
                <th>Responsable Solicitante</th>
                <td>{{ $solicitud->responsable }}</td>
                <th>Cargo / Función</th>
                <td>{{ $solicitud->cargo ?: 'Responsable de Pedido' }}</td>
            </tr>
            <tr>
                <th>Estado de Solicitud</th>
                <td><span class="badge-amber">{{ $solicitud->estado }}</span></td>
                <th>Observaciones</th>
                <td>{{ $solicitud->observaciones ?: 'Sin observaciones registradas' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <span class="kpi-label">Ítems Solicitados</span>
                <span class="kpi-value">{{ number_format($totalItems, 0, ',', '.') }} líneas</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Total Unidades Solicitadas</span>
                <span class="kpi-value">{{ number_format($totalSolicitado, 0, ',', '.') }} uds.</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Unidades Pendientes de Entrega</span>
                <span class="kpi-value" style="color: #b45309;">{{ number_format($totalPendiente, 0, ',', '.') }} uds.</span>
            </td>
        </tr>
    </table>

    <div class="section-header">2. Detalle de Medicamentos e Insumos Solicitados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 34%; text-align: left;">Producto / Insumo</th>
                <th style="width: 16%;">Programa</th>
                <th style="width: 10%;">Consumo Ant.</th>
                <th style="width: 10%;">Stock Declarado</th>
                <th style="width: 8%;">Cant. Sol.</th>
                <th style="width: 8%;">Cant. Desp.</th>
                <th style="width: 10%;">Cant. Pendiente</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $idx => $item)
            <tr>
                <td style="color: #64748b; font-weight: bold;">{{ $idx + 1 }}</td>
                <td class="text-left">
                    <strong style="color: #0f172a;">{{ $item->product->nombre ?? ('Producto #' . $item->product_id) }}</strong>
                </td>
                <td><span class="badge">{{ $item->programa ?: 'General DAS' }}</span></td>
                <td>{{ number_format($item->consumo_anterior ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($item->stock_actual ?? 0, 0, ',', '.') }}</td>
                <td><strong>{{ number_format($item->cantidad_solicitada, 0, ',', '.') }}</strong></td>
                <td style="color: #64748b;">{{ number_format($item->cantidad_despachada, 0, ',', '.') }}</td>
                <td><strong style="color: #b45309;">{{ number_format($item->cantidad_pendiente, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="7" class="total-label">TOTAL GENERAL DE UNIDADES PENDIENTES DE DESPACHO:</td>
                <td class="total-amount">{{ number_format($totalPendiente, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="obs-box">
        <strong>Certificación de Requerimiento:</strong> El responsable solicitante certifica la veracidad de los consumos históricos y del stock físico declarado a la fecha de emisión para el oportuno abastecimiento en Bodega Central DAS.
    </div>

    <!-- firma de quien hace la solicitud -->
    <div class="signatures-wrapper">
        <table class="signatures-table" style="width: 60%; margin: 0 auto;">
            <tr>
                <td class="signature-box">
                    <div class="signature-image-container">
                        @if(!empty($solicitud->firma_solicitante))
                            <img src="{{ $solicitud->firma_solicitante }}" class="signature-img" alt="Firma Solicitante">
                        @endif
                    </div>
                    <div class="signature-line"></div>
                    <p class="signature-title">{{ $solicitud->responsable }}</p>
                    <p class="signature-subtitle">{{ $solicitud->cargo ?: 'Responsable Solicitante' }}</p>
                    <p class="signature-meta">{{ $solicitud->establecimiento }} · Red Comunal DAS Talcahuano</p>
                    <p class="signature-meta" style="color: #94a3b8; font-size: 7.5px;">Fecha Solicitud: {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento oficial generado por el Sistema de Inventario y Abastecimiento DAS Talcahuano · Solicitud {{ $solicitud->numero_solicitud }} · Impreso el {{ date('d/m/Y H:i') }}
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
