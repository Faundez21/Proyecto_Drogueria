<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function image(Inventory $inventory)
    {
        $contenido = sprintf(
            'LOTE:%s|PROD:%s|VENCE:%s|STOCK:%s',
            $inventory->lote ?? 'S/L',
            $inventory->producto,
            $inventory->fecha_vencimiento ? $inventory->fecha_vencimiento->format('d/m/Y') : 'N/A',
            $inventory->cantidad
        );

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($contenido);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
