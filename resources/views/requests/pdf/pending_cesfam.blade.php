<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes Pendientes - CESFAM</title>
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
        .doc-correlativo { font-size: 12px; font-weight: 900; color: #0c4a6e; margin: 0 0 3px 0; font-family: 'Courier New', monospace; }
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

        /* Firmas */
        .signatures-wrapper { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .signatures-table { width: 100%; border-collapse: collapse; }
        .signature-box { width: 48%; text-align: center; vertical-align: bottom; padding: 6px; }
        .signature-image-container { height: 45px; margin-bottom: 3px; }
        .signature-line { width: 85%; border-top: 1.5px solid #334155; margin: 0 auto 4px auto; }
        .signature-title { font-size: 9px; font-weight: 800; color: #0f172a; margin: 0; text-transform: uppercase; }
        .signature-subtitle { font-size: 8px; color: #64748b; margin: 2px 0 0 0; }

        /* Pie de página */
        .footer { position: fixed; bottom: -25px; left: 0; right: 0; height: 22px; text-align: left; font-size: 8px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 4px; }
    </style>
</head>
<body>

    <div class="top-accent"></div>

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
                    <p class="doc-type-label">SOLICITUDES PENDIENTES</p>
                    <p class="doc-correlativo">CENTROS DE SALUD FAMILIAR (CESFAM)</p>
                    <p class="doc-date">Emisión: {{ \Carbon\Carbon::parse($fechaEmision)->format('d/m/Y H:i') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-header">1. Parámetros de Selección y Resumen Operativo</div>
    <table class="info-table">
        <tbody>
            <tr>
                <th>Categoría / Ámbito</th>
                <td style="font-weight: 800; color: #0369a1;">Centros de Salud Familiar (CESFAM)</td>
                <th>Estado Solicitudes</th>
                <td><span class="badge-amber">Pendientes de Despacho (FEFO / Controlados)</span></td>
            </tr>
            <tr>
                <th>CESFAM</th>
                <td style="font-weight: 700;">{{ !empty($centroFiltro) ? $centroFiltro : 'Todos los CESFAM' }}</td>
                <th>Rango de Fechas</th>
                <td>
                    @if(!empty($fechaDesde) && !empty($fechaHasta))
                        Del {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    @elseif(!empty($fechaDesde))
                        Desde el {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}
                    @elseif(!empty($fechaHasta))
                        Hasta el {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    @else
                        Todas las Fechas
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <span class="kpi-label">Solicitudes Pendientes</span>
                <span class="kpi-value">{{ number_format($totalSolicitudes, 0, ',', '.') }} solicitudes</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Líneas / Ítems Pendientes</span>
                <span class="kpi-value">{{ number_format($totalLineas, 0, ',', '.') }} ítems</span>
            </td>
            <td class="kpi-card">
                <span class="kpi-label">Total Unidades Pendientes</span>
                <span class="kpi-value">{{ number_format($totalUnidades, 0, ',', '.') }} uds.</span>
            </td>
        </tr>
    </table>

    <div class="section-header">2. Detalle de Medicamentos, Insumos y Controlados Pendientes</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 10%;">N° Solicitud</th>
                <th style="width: 9%;">Fecha</th>
                <th style="width: 20%; text-align: left;">CESFAM Destino</th>
                <th style="width: 26%; text-align: left;">Producto / Insumo</th>
                <th style="width: 13%;">Programa</th>
                <th style="width: 6%;">Sol.</th>
                <th style="width: 6%;">Desp.</th>
                <th style="width: 6%;">Pend.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itemsPendientes as $idx => $item)
            @php
                $sol = $item->solicitud;
            @endphp
            <tr>
                <td style="color: #64748b; font-weight: bold;">{{ $idx + 1 }}</td>
                <td style="font-family: 'Courier New', monospace; font-weight: 800; color: #0c4a6e;">{{ $sol->numero_solicitud ?? ('#' . $item->establishment_request_id) }}</td>
                <td>{{ $sol && $sol->fecha_solicitud ? \Carbon\Carbon::parse($sol->fecha_solicitud)->format('d/m/Y') : 'N/A' }}</td>
                <td class="text-left"><strong style="color: #0369a1;">{{ $sol->establecimiento ?? 'N/A' }}</strong></td>
                <td class="text-left">
                    <strong style="color: #0f172a;">{{ $item->product->nombre ?? 'Producto #' . $item->product_id }}</strong>
                </td>
                <td><span class="badge">{{ $item->programa ?: 'General DAS' }}</span></td>
                <td>{{ number_format($item->cantidad_solicitada, 0, ',', '.') }}</td>
                <td style="color: #64748b;">{{ number_format($item->cantidad_despachada, 0, ',', '.') }}</td>
                <td><strong style="color: #b45309;">{{ number_format($item->cantidad_pendiente, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding: 16px; color: #64748b; font-style: italic;">
                    No existen solicitudes pendientes con los filtros aplicados.
                </td>
            </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="8" class="total-label">TOTAL GENERAL DE UNIDADES PENDIENTES:</td>
                <td class="total-amount">{{ number_format($totalUnidades, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="obs-box">
        <strong>Constancia de Control y Planificación:</strong> El presente informe consolida los requerimientos sanitarios pendientes de despacho para los Centros de Salud Familiar (CESFAM) de la comuna de Talcahuano, incluyendo fármacos de programas ministeriales, insumos clínicos y medicamentos controlados sujetos a fiscalización. La firma individual y validada de cada solicitante se encuentra debidamente registrada en su respectiva Solicitud de Pedido oficial.
    </div>

    <div class="footer">
        Documento oficial generado por el Sistema de Inventario y Abastecimiento DAS Talcahuano · Impreso el {{ date('d/m/Y H:i') }}
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
