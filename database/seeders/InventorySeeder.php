<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpia la tabla para evitar registros duplicados al reejecutar el seeder
        DB::table('inventories')->truncate();

        $inventarios = [
            // PARACETAMOL 500MG (Lote 1 - FármacosChile Mayorista)
            [
                'correlativo_interno' => 1001,
                'fecha_recepcion'     => Carbon::now()->subDays(15)->format('Y-m-d'),
                'tipo_documento'      => 'Factura',
                'numero_documento'    => 'F-8849',
                'orden_compra'        => 'OC-1234',
                'origen_mercaderia'   => 'CENABAST',
                'rut_proveedor'       => '79.345.678-1',
                'razon_social'        => 'FarmaChile Mayorista',
                'producto'            => 'PARACETAMOL 500MG COMPRIMIDOS',
                'categoria'           => 'Fármacos',
                'lote'                => 'L-9982A',
                'fecha_vencimiento'   => Carbon::now()->addYear()->format('Y-m-d'),
                'cantidad'            => 500,
                'valor_neto_unitario' => 15.50,
                'estado'              => 'Disponible',
                'pasillo'             => 'Pasillo 1',
                'estante'             => 'A-2',
            ],
            // PARACETAMOL 500MG (Lote 2 - Laboratorios Andinos S.A.)
            [
                'correlativo_interno' => 1002,
                'fecha_recepcion'     => Carbon::now()->subDays(5)->format('Y-m-d'),
                'tipo_documento'      => 'Guía de Despacho',
                'numero_documento'    => 'G-4512',
                'orden_compra'        => 'OC-1250',
                'origen_mercaderia'   => 'COMPRA DIRECTA DAS',
                'rut_proveedor'       => '76.543.210-K',
                'razon_social'        => 'Laboratorios Andinos S.A.',
                'producto'            => 'PARACETAMOL 500MG COMPRIMIDOS',
                'categoria'           => 'Fármacos',
                'lote'                => 'L-9982B',
                'fecha_vencimiento'   => Carbon::now()->addMonths(8)->format('Y-m-d'),
                'cantidad'            => 300,
                'valor_neto_unitario' => 18.00,
                'estado'              => 'Disponible',
                'pasillo'             => 'Pasillo 1',
                'estante'             => 'A-2',
            ],
            // PARACETAMOL 500MG (Lote 3 - FarmaChile Mayorista - Vencido)
            [
                'correlativo_interno' => 1003,
                'fecha_recepcion'     => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'tipo_documento'      => 'Factura',
                'numero_documento'    => 'F-2210',
                'orden_compra'        => 'OC-0982',
                'origen_mercaderia'   => 'Traspaso',
                'rut_proveedor'       => '79.345.678-1',
                'razon_social'        => 'FarmaChile Mayorista',
                'producto'            => 'PARACETAMOL 500MG COMPRIMIDOS',
                'categoria'           => 'Fármacos',
                'lote'                => 'L-7711X',
                'fecha_vencimiento'   => Carbon::now()->subDays(10)->format('Y-m-d'), // Vencido
                'cantidad'            => 50,
                'valor_neto_unitario' => 15.50,
                'estado'              => 'Merma',
                'pasillo'             => 'Pasillo Merma',
                'estante'             => 'M-1',
            ],

            // IBUPROFENO 400MG (Vacunas y Biológicos del Pacífico)
            [
                'correlativo_interno' => 1004,
                'fecha_recepcion'     => Carbon::now()->subDays(2)->format('Y-m-d'),
                'tipo_documento'      => 'Factura',
                'numero_documento'    => 'F-9011',
                'orden_compra'        => 'OC-1300',
                'origen_mercaderia'   => 'SERVICIO DE SALUD TALCAHUANO',
                'rut_proveedor'       => '79.333.444-5',
                'razon_social'        => 'Vacunas y Biológicos del Pacífico',
                'producto'            => 'IBUPROFENO 400MG COMPRIMIDOS',
                'categoria'           => 'Fármacos',
                'lote'                => 'IBU-2026-1',
                'fecha_vencimiento'   => Carbon::now()->addMonths(18)->format('Y-m-d'),
                'cantidad'            => 1200,
                'valor_neto_unitario' => 25.00,
                'estado'              => 'Cuarentena',
                'pasillo'             => 'Pasillo 2',
                'estante'             => 'B-1',
            ],

            // JERINGA 5ML CON AGUJA (Importadora de Agujas y Jeringas)
            [
                'correlativo_interno' => 1005,
                'fecha_recepcion'     => Carbon::now()->subDays(20)->format('Y-m-d'),
                'tipo_documento'      => 'Factura',
                'numero_documento'    => 'F-5541',
                'orden_compra'        => 'OC-1100',
                'origen_mercaderia'   => 'CENABAST',
                'rut_proveedor'       => '79.555.777-6',
                'razon_social'        => 'Importadora de Agujas y Jeringas',
                'producto'            => 'JERINGA 5ML CON AGUJA 21G',
                'categoria'           => 'Insumos Clínicos',
                'lote'                => 'J-2025-09',
                'fecha_vencimiento'   => Carbon::now()->addYears(2)->format('Y-m-d'),
                'cantidad'            => 2500,
                'valor_neto_unitario' => 85.00,
                'estado'              => 'Disponible',
                'pasillo'             => 'Pasillo 4',
                'estante'             => 'C-3',
            ],

            // AMOXICILINA 500MG (Distribuidora Biomédica Spa - Stock bajo)
            [
                'correlativo_interno' => 1006,
                'fecha_recepcion'     => Carbon::now()->subDays(8)->format('Y-m-d'),
                'tipo_documento'      => 'Orden de Compra',
                'numero_documento'    => 'OC-1411',
                'orden_compra'        => 'OC-1411',
                'origen_mercaderia'   => 'Donación',
                'rut_proveedor'       => '77.890.123-5',
                'razon_social'        => 'Distribuidora Biomédica Spa',
                'producto'            => 'AMOXICILINA 500MG CÁPSULAS',
                'categoria'           => 'Fármacos',
                'lote'                => 'AMX-441',
                'fecha_vencimiento'   => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'cantidad'            => 8, // Stock bajo
                'valor_neto_unitario' => 45.00,
                'estado'              => 'Disponible',
                'pasillo'             => 'Pasillo 1',
                'estante'             => 'A-1',
            ],
        ];

        foreach ($inventarios as $data) {
            Inventory::create($data);
        }
    }
}
