<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <title>Guia de Despacho {{ $despacho->correlativo }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .header td { border: none; }
        .title { font-size: 18px; font-weight: bold; text-align: right; color: #0f172a; }
        .box { border: 1px solid #ccc; padding: 10px; border-radius: 5px; margin-bottom: 15px; line-height: 1.6; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.items th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; }

        /* estilos para las firmas */
        table.signatures { width: 100%; margin-top: 60px; text-align: center; border: none; }
        table.signatures td { border: none; vertical-align: bottom; }
        .signature-line { width: 60%; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px; margin-top: 5px; font-size: 11px; }

        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 10px; color: #666; }
    </style>
</head>

<body>

    <!-- cabecera del documento -->
    <div class="header">
        <table>
            <tr>
                <td width="60%">
                    <strong>I. MUNICIPALIDAD DE TALCAHUANO</strong><br>
                    Drogueria Comunal Central<br>
                    Blanco Encalada 450, Talcahuano
                </td>
                <td width="40%" class="title">
                    GUIA DE DESPACHO {{ strtoupper($despacho->clasificacion ?? 'INTERNA') }}<br>
                    N° {{ $despacho->correlativo }}
                </td>
            </tr>
        </table>
    </div>

    <!-- caja de informacion de destino y logistica -->
    <div class="box">
        <strong>CLASIFICACION:</strong> {{ ucfirst($despacho->clasificacion ?? 'Interno') }}<br>
        <strong>DESTINO:</strong> {{ $despacho->establecimiento_destino }}<br>
        <strong>DIRECCION:</strong> {{ $despacho->direccion_destino ?? 'S/D' }}<br>
        <strong>FECHA EMISION:</strong> {{ \Carbon\Carbon::parse($despacho->fecha_despacho)->format('d/m/Y') }}<br>
        <strong>CONDICIONES:</strong> {{ $despacho->condiciones_transporte ?? 'Transporte Normal' }}
    </div>

    <!-- tabla de productos extraidos -->
    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Lote</th>
                <th>Vencimiento</th>
                <th>Programa</th>
                <th style="text-align: center;">Cantidad Extraida</th>
            </tr>
        </thead>
        <tbody>
            @foreach($despacho->items as $item)
            <tr>
                <td>{{ $item->producto }}</td>
                <td>{{ $item->lote }}</td>
                <td>{{ $item->fecha_vencimiento ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $item->programa }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($item->cantidad, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- seccion de firmas agregadas recientemente -->
    <table class="signatures">
        <tr>
            <!-- firma de quien despacha -->
            <td style="width: 50%;">
                @if(isset($despacho->firma_despacha) && $despacho->firma_despacha)
                    <img src="{{ $despacho->firma_despacha }}" style="max-height: 80px;" alt="Firma Despacha">
                @else
                    <div style="height: 80px;"></div>
                @endif
                <div class="signature-line">
                    <strong>Firma Quien Despacha</strong><br>
                    Bodega Central / DAS
                </div>
            </td>

            <!-- firma de quien recibe -->
            <td style="width: 50%;">
                @if(isset($despacho->firma_recibe) && $despacho->firma_recibe)
                    <img src="{{ $despacho->firma_recibe }}" style="max-height: 80px;" alt="Firma Recibe">
                @else
                    <div style="height: 80px;"></div>
                @endif
                <div class="signature-line">
                    <strong>Firma Quien Recibe</strong><br>
                    Establecimiento Destino
                </div>
            </td>
        </tr>
    </table>

    <!-- pie de pagina -->
    <div class="footer">
        Documento generado automaticamente por Sistema de Inventario DAS - {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
