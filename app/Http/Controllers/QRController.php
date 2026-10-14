<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use SimpleSoftwareIO\QrCode\Generator;

class QRController extends Controller
{
    public function image(Inventory $inventory)
    {
        $contenido = sprintf(
            'LOTE:%s|PROD:%s|VENCE:%s|STOCK:%s',
            $inventory->lote ?? 'S/L',
            $inventory->producto,
            $inventory->fecha_vencimiento ? \Carbon\Carbon::parse($inventory->fecha_vencimiento)->format('d/m/Y') : 'N/A',
            $inventory->cantidad
        );

        $svg = (new Generator())
            ->format('svg')
            ->size(300)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($contenido);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
