<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Proveedores reales del rubro farmacéutico chileno, con su RUT público verificado
        // (dígito verificador correcto). Los correos son de prueba (@example.com) y no
        // corresponden a contactos reales de las empresas.
        $providers = [
            [
                'name' => 'Central de Abastecimiento del Sistema Nacional de Servicios de Salud (CENABAST)',
                'rut' => '61.608.700-2',
                'business_line' => 'Distribución de medicamentos e insumos para la red pública',
                'email' => 'compras.cenabast@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo',
            ],
            [
                'name' => 'Socofar S.A.',
                'rut' => '91.575.000-1',
                'business_line' => 'Distribución mayorista de productos farmacéuticos',
                'email' => 'compras.socofar@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo',
            ],
            [
                'name' => 'Laboratorios Recalcine S.A.',
                'rut' => '91.637.000-8',
                'business_line' => 'Laboratorio farmacéutico',
                'email' => 'compras.recalcine@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo',
            ],
            [
                'name' => 'Laboratorio Saval S.A.',
                'rut' => '91.650.000-9',
                'business_line' => 'Laboratorio farmacéutico',
                'email' => 'compras.saval@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo',
            ],
            [
                'name' => 'Roche Chile Limitada',
                'rut' => '82.999.400-3',
                'business_line' => 'Laboratorio farmacéutico',
                'email' => 'compras.roche@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo',
            ],
            [
                'name' => 'B. Braun Medical SpA',
                'rut' => '96.756.540-7',
                'business_line' => 'Insumos médico-quirúrgicos',
                'email' => 'compras.bbraun@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Insumos clínicos',
                'status' => 'activo',
            ],
            [
                'name' => 'Fresenius Kabi Chile Ltda.',
                'rut' => '77.478.120-K',
                'business_line' => 'Soluciones de infusión y nutrición clínica',
                'email' => 'compras.freseniuskabi@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Dispositivos médicos',
                'status' => 'activo',
            ],
            [
                'name' => 'Laboratorios Andrómaco S.A.',
                'rut' => '76.237.266-5',
                'business_line' => 'Laboratorio farmacéutico',
                'email' => 'compras.andromaco@example.com',
                'phone' => null,
                'address' => null,
                'category' => 'Fármacos y medicamentos',
                'status' => 'inactivo',
            ],
        ];

        // Recorremos el array y lo guardamos en la base de datos
        foreach ($providers as $provider) {
            Provider::updateOrCreate(
                ['rut' => $provider['rut']], // Busca por RUT para no duplicar
                $provider // Si no existe, lo crea con todos estos datos; si existe, lo actualiza
            );
        }
    }
}