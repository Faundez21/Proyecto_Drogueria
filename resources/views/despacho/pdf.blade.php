<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Despacho {{ $despacho->correlativo }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .title { font-size: 18px; font-weight: bold; text-align: right; color: #0f172a; }
        .box { border: 1px solid #ccc; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.items th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; }
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td width="60%">
                    <strong>I. MUNICIPALIDAD DE TALCAHUANO</strong><br>
                    Droguería Comunal Central<br>
                    Blanco Encalada 450, Talcahuano
                </td>
                <td width="40%" class="title">
                    GUÍA DE DESPACHO INTERNA<br>
                    N° {{ $despacho->correlativo }}
                </td>
            </tr>
        </table>
    </div>

    <div class="box">
        <strong>DESTINO:</strong> {{ $despacho->establecimiento_destino }}<br>
        <strong>DIRECCIÓN:</strong> {{ $despacho->direccion_destino ?? 'S/D' }}<br>
        <strong>FECHA EMISIÓN:</strong> {{ \Carbon\Carbon::parse($despacho->fecha_despacho)->format('d/m/Y') }}<br>
        <strong>CONDICIONES:</strong> {{ $despacho->condiciones_transporte ?? 'Transporte Normal' }}
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Lote</th>
                <th>Vencimiento</th>
                <th>Programa</th>
                <th style="text-align: center;">Cantidad Extraída</th>
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

    <div class="footer">
        Documento generado automáticamente por Sistema de Inventario DAS - {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>