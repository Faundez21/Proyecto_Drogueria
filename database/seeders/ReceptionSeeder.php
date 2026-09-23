<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Models\Provider;
use App\Models\Product;
use Carbon\Carbon;

class ReceptionSeeder extends Seeder
{
    public function run()
    {
        // Rescatamos el primer proveedor y producto para la prueba
        $provider = Provider::first();
        $product1 = Product::find(1);
        $product2 = Product::find(2);

        // Si no existen, abortamos el seeder para no causar errores de llave foránea
        if (!$provider || !$product1) {
            return;
        }

        // 1. Crear la Cabecera de la Recepción
        $reception = Reception::create([
            'correlativo' => 1,
            'fecha_recepcion' => Carbon::now()->format('Y-m-d'),
            'tipo_documento' => 'Factura',
            'numero_documento' => '32456',
            'orden_compra' => 'OC-98765',
            'origen' => 'CENABAST',
            'provider_id' => $provider->id,
            'valor_total_recepcion' => 0, // Se actualizará al final
            'firma_proveedor' => null,
            'firma_receptor' => null,
        ]);

        // 2. Crear Item 1
        $cant1 = 300;
        $val1 = 350;
        $subtotal1 = $cant1 * $val1;
        
        ReceptionItem::create([
            'reception_id' => $reception->id,
            'product_id' => $product1->id,
            'tipo_producto' => 'F-FOFAR',
            'lote' => 'L-10293',
            'fecha_vencimiento' => Carbon::now()->addYears(2)->format('Y-m-d'),
            'cantidad' => $cant1,
            'valor_unitario' => $val1,
            'valor_total_item' => $subtotal1 * 1.19,
        ]);

        // 3. Crear Item 2
        $cant2 = 50;
        $val2 = 120;
        $subtotal2 = $cant2 * $val2;

        ReceptionItem::create([
            'reception_id' => $reception->id,
            'product_id' => $product2->id,
            'tipo_producto' => 'I-CIRUGIA',
            'lote' => 'L-99887',
            'fecha_vencimiento' => Carbon::now()->addYear()->format('Y-m-d'),
            'cantidad' => $cant2,
            'valor_unitario' => $val2,
            'valor_total_item' => $subtotal2 * 1.19,
        ]);

        // 4. Actualizar el Gran Total de la Recepción
        $gran_total_neto = $subtotal1 + $subtotal2;
        $reception->update([
            'valor_total_recepcion' => $gran_total_neto * 1.19
        ]);
    }
}