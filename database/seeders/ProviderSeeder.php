<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    // proveedores habituales de la drogueria
    public function run(): void
    {
        $proveedores = [
            [
                'rut' => '76.669.630-9',
                'name' => 'OPKO CHILE S.A.',
                'business_line' => 'Venta al por mayor de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'contactochile@opko.com',
                'phone' => null,
                'address' => 'Agustinas 640, piso 10, Santiago',
            ],
            [
                'rut' => '76.237.266-5',
                'name' => 'LABORATORIOS ANDROMACO S.A.',
                'business_line' => 'Fabricación de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'contacto@andromaco.cl',
                'phone' => '+56 2 2594 8000',
                'address' => 'Av. Quilín 5273, Peñalolén, Santiago',
            ],
            [
                'rut' => '91.546.000-3',
                'name' => 'LABORATORIO SANDERSON SPA',
                'business_line' => 'Fabricación de productos farmacéuticos, sustancias químicas medicinales y sueros',
                'category' => 'Fármacos y medicamentos',
                'email' => 'cotizaciones@laboratoriosanderson.cl',
                'phone' => '+56 2 2462 7000',
                'address' => 'Carlos Fernández Concha 244, San Joaquín, Santiago',
            ],
            [
                'rut' => '76.830.090-9',
                'name' => 'FARMACEUTICA CARIBEAN SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'contacto@caribeanpharma.cl',
                'phone' => '+56 2 2621 9290',
                'address' => 'Av. El Salto 2301, Santiago',
            ],
            [
                'rut' => '76.095.228-1',
                'name' => 'COMERCIALIZADORA E INVERSIONES GHALENO LIMITADA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'com.ghaleno@gmail.com',
                'phone' => null,
                'address' => null,
            ],
            [
                'rut' => '76.079.782-0',
                'name' => 'WINPHARM SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'asanchez@winpharm.cl',
                'phone' => null,
                'address' => 'Pudahuel, Santiago',
            ],
            [
                'rut' => '76.416.055-K',
                'name' => 'UNIFARMA SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos y medicinales',
                'category' => 'Fármacos y medicamentos',
                'email' => 'info@unifarma.cl',
                'phone' => null,
                'address' => 'Vitacura, Santiago',
            ],
            [
                'rut' => '88.597.500-3',
                'name' => 'LABORATORIO BIOSANO S.A.',
                'business_line' => 'Fabricación de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'lab.biosano@biosano.cl',
                'phone' => '+56 2 2390 1300',
                'address' => 'Av. Aeropuerto 9941, Cerrillos, Santiago',
            ],
            [
                'rut' => '76.458.051-6',
                'name' => 'NEOETHICALS CHILE SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'chilefinance@caplinpoint.com',
                'phone' => null,
                'address' => 'Las Condes, Santiago',
            ],
            [
                'rut' => '59.077.290-9',
                'name' => 'NIPRO MEDICAL CORPORATION (AGENCIA EN CHILE)',
                'business_line' => 'Venta al por mayor de insumos médicos',
                'category' => 'Insumos clínicos',
                'email' => 'marcelore@nipromed.com',
                'phone' => '+56 2 2498 7800',
                'address' => 'Los Gobelinos 2512, Renca, Santiago',
            ],
            [
                'rut' => '76.956.140-4',
                'name' => 'ETHON PHARMACEUTICALS COMERCIALIZADORA IMP EXP Y DIST SPA',
                'business_line' => 'Importación, exportación y distribución de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'ventas@ethon.cl',
                'phone' => '+56 2 2887 8250',
                'address' => 'Sgto. José Bernardo Cuevas 021, Rancagua',
            ],
            [
                'rut' => '76.055.804-4',
                'name' => 'INVERSIONES PHARMAVISAN SOCIEDAD ANONIMA',
                'business_line' => 'Venta al por menor de productos farmacéuticos y medicinales',
                'category' => 'Fármacos y medicamentos',
                'email' => 'ventas@pharmavisan.cl',
                'phone' => '+56 2 2303 9401',
                'address' => 'Los Jardines 261, Ñuñoa, Santiago',
            ],
            [
                'rut' => '77.596.940-7',
                'name' => 'LABORATORIO CHILE S.A.',
                'business_line' => 'Fabricación de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'proveedoreselectronicos@laboratoriochile.cl',
                'phone' => '+56 2 2365 5000',
                'address' => 'Av. Marathon 1315, Ñuñoa, Santiago',
            ],
            [
                'rut' => '80.447.400-5',
                'name' => 'MUNNICH PHARMA MEDICAL SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos y medicinales',
                'category' => 'Fármacos y medicamentos',
                'email' => 'contacto.munnich@munnich.cl',
                'phone' => '800 530 250',
                'address' => 'Av. El Salto 4875, of. 601, Huechuraba, Santiago',
            ],
            [
                'rut' => '77.615.297-8',
                'name' => 'ALEMBIC PHARMACEUTICALS SPA',
                'business_line' => 'Venta al por mayor de productos farmacéuticos y medicinales',
                'category' => 'Fármacos y medicamentos',
                'email' => 'cobranzachile@alembic.co.in',
                'phone' => null,
                'address' => 'Rosario Norte 100, of. 1602, Las Condes, Santiago',
            ],
            [
                'rut' => '96.026.000-7',
                'name' => 'CHEMOPHARMA S.A.',
                'business_line' => 'Fabricación de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'chemopharma@chemopharma.cl',
                'phone' => '+56 2 2444 6600',
                'address' => 'Av. Américo Vespucio 01260, Quilicura, Santiago',
            ],
            [
                'rut' => '96.859.930-5',
                'name' => 'LIBRA CHILE S.A.',
                'business_line' => 'Importación y comercialización de productos farmacéuticos',
                'category' => 'Fármacos y medicamentos',
                'email' => 'info@lablibra.com',
                'phone' => null,
                'address' => null,
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Provider::updateOrCreate(
                ['rut' => $proveedor['rut']],
                $proveedor + ['status' => 'activo']
            );
        }
    }
}
