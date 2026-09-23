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
        $providers = [
            [
                'name' => 'Laboratorios Andinos S.A.',
                'rut' => '76.543.210-K',
                'email' => 'contacto@labandinos.cl',
                'phone' => '+56 9 1234 5678',
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo'
            ],
            [
                'name' => 'Equipamiento Médico Central',
                'rut' => '78.444.333-2',
                'email' => 'hola@equipcentral.cl',
                'phone' => '+56 2 2345 6789',
                'category' => 'Equipamiento mayor',
                'status' => 'inactivo'
            ],
            [
                'name' => 'Distribuidora Biomédica Spa',
                'rut' => '77.890.123-5',
                'email' => 'ventas@biomedicaspa.cl',
                'phone' => '+56 9 8765 4321',
                'category' => 'Insumos médicos genéricos',
                'status' => 'activo'
            ],
            [
                'name' => 'Tecnología Hospitalaria Limitada',
                'rut' => '76.123.456-7',
                'email' => 'soporte@techospitalaria.cl',
                'phone' => '+56 2 2987 6543',
                'category' => 'Equipamiento mayor',
                'status' => 'activo'
            ],
            [
                'name' => 'FarmaChile Mayorista',
                'rut' => '79.345.678-1',
                'email' => 'comercial@farmachile.cl',
                'phone' => '+56 9 5555 4444',
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo'
            ],
            [
                'name' => 'Insumos Quirúrgicos del Sur',
                'rut' => '76.987.654-3',
                'email' => 'contacto@insur.cl',
                'phone' => '+56 63 221 4455',
                'category' => 'Material quirúrgico',
                'status' => 'activo'
            ],
            [
                'name' => 'Gases Medicinales Oxígeno Vital',
                'rut' => '78.111.222-K',
                'email' => 'pedidos@oxigenovital.cl',
                'phone' => '+56 2 2444 5555',
                'category' => 'Gases clínicos',
                'status' => 'activo'
            ],
            [
                'name' => 'Soluciones Digitales Radiológicas',
                'rut' => '77.222.333-4',
                'email' => 'info@sdrmedical.cl',
                'phone' => '+56 9 9988 7766',
                'category' => 'Imagenología y radiología',
                'status' => 'inactivo'
            ],
            [
                'name' => 'Laboratorio Clínico San José S.A.',
                'rut' => '76.444.555-6',
                'email' => 'convenios@labsanjose.cl',
                'phone' => '+56 2 2333 4444',
                'category' => 'Reactivos y laboratorio',
                'status' => 'activo'
            ],
            [
                'name' => 'Importadora Dental Pacíficos',
                'rut' => '79.555.666-7',
                'email' => 'ventas@dentalpacifico.cl',
                'phone' => '+56 9 8888 1122',
                'category' => 'Insumos dentales',
                'status' => 'activo'
            ],
            [
                'name' => 'Prótesis y Ortopedia Santiago',
                'rut' => '78.666.777-8',
                'email' => 'contacto@ortopediasantiago.cl',
                'phone' => '+56 2 2777 8888',
                'category' => 'Ortopedia y prótesis',
                'status' => 'activo'
            ],
            [
                'name' => 'Textiles Clínicos e Uniformes Ltda.',
                'rut' => '77.777.888-9',
                'email' => 'textiles@clinicos.cl',
                'phone' => '+56 9 7777 6655',
                'category' => 'Ropa médica y uniformes',
                'status' => 'activo'
            ],
            [
                'name' => 'Esterilización y Esterilidad Total',
                'rut' => '76.888.999-0',
                'email' => 'servicios@estetotal.cl',
                'phone' => '+56 2 2666 5544',
                'category' => 'Servicios de esterilización',
                'status' => 'activo'
            ],
            [
                'name' => 'Nutrición y Dietética Clínicas S.A.',
                'rut' => '79.999.000-1',
                'email' => 'nutricion@nutriclinic.cl',
                'phone' => '+56 9 6666 3322',
                'category' => 'Suplementos y nutrición',
                'status' => 'inactivo'
            ],
            [
                'name' => 'Mobiliario Clínico del Norte',
                'rut' => '78.000.111-2',
                'email' => 'norte@mobilclinico.cl',
                'phone' => '+56 55 222 3344',
                'category' => 'Mobiliario clínico',
                'status' => 'activo'
            ],
            [
                'name' => 'Desechables Médicos Clínex',
                'rut' => '77.111.222-3',
                'email' => 'ventas@clinexmed.cl',
                'phone' => '+56 9 4444 5566',
                'category' => 'Insumos médicos genéricos',
                'status' => 'activo'
            ],
            [
                'name' => 'Instrumental Quirúrgico Premium',
                'rut' => '76.222.333-4',
                'email' => 'premium@instrumental.cl',
                'phone' => '+56 2 2111 2233',
                'category' => 'Material quirúrgico',
                'status' => 'activo'
            ],
            [
                'name' => 'Vacunas y Biológicos del Pacífico',
                'rut' => '79.333.444-5',
                'email' => 'vacunas@biopacifico.cl',
                'phone' => '+56 9 3333 4455',
                'category' => 'Fármacos y medicamentos',
                'status' => 'activo'
            ],
            [
                'name' => 'Mantenimiento de Equipos MedTech',
                'rut' => '78.444.555-6',
                'email' => 'soporte@medtechchile.cl',
                'phone' => '+56 2 2999 8877',
                'category' => 'Mantenimiento preventivo',
                'status' => 'activo'
            ],
            [
                'name' => 'Sistemas Informáticos de Salud',
                'rut' => '77.555.666-7',
                'email' => 'contacto@saluddigital.cl',
                'phone' => '+56 9 2222 9988',
                'category' => 'Software médico y TI',
                'status' => 'activo'
            ],
            [
                'name' => 'Ambulancias y Rescate Urgente',
                'rut' => '76.666.777-8',
                'email' => 'logistica@rescateurgente.cl',
                'phone' => '+56 2 2888 7766',
                'category' => 'Vehículos de emergencia',
                'status' => 'inactivo'
            ],
            [
                'name' => 'Desinfectantes y Antisépticos BioSafe',
                'rut' => '79.777.888-9',
                'email' => 'pedidos@biosafe.cl',
                'phone' => '+56 9 1111 2233',
                'category' => 'Insumos médicos genéricos',
                'status' => 'activo'
            ],
            [
                'name' => 'Monitores y Sensores Médicos Ltda.',
                'rut' => '78.888.999-0',
                'email' => 'ventas@monitoresmedicos.cl',
                'phone' => '+56 2 2777 6655',
                'category' => 'Equipamiento mayor',
                'status' => 'activo'
            ],
            [
                'name' => 'Laboratorio Oftalmológico Visión',
                'rut' => '77.999.000-K',
                'email' => 'contacto@labvision.cl',
                'phone' => '+56 9 7766 5544',
                'category' => 'Insumos oftalmológicos',
                'status' => 'activo'
            ],
            [
                'name' => 'Seguridad y Protección Radiológica',
                'rut' => '76.000.222-1',
                'email' => 'proteccion@radioprotect.cl',
                'phone' => '+56 2 2555 4433',
                'category' => 'Imagenología y radiología',
                'status' => 'activo'
            ],
            [
                'name' => 'Reactivos y Químicos del Centro',
                'rut' => '79.111.333-2',
                'email' => 'ventas@quimicocentro.cl',
                'phone' => '+56 9 4433 2211',
                'category' => 'Reactivos y laboratorio',
                'status' => 'activo'
            ],
            [
                'name' => 'Equipamiento de Rescate y Trauma',
                'rut' => '78.222.444-3',
                'email' => 'trauma@rescatetrauma.cl',
                'phone' => '+56 2 2333 1122',
                'category' => 'Equipamiento mayor',
                'status' => 'activo'
            ],
            [
                'name' => 'CardioTecnología Chile S.A.',
                'rut' => '77.333.555-4',
                'email' => 'info@cardiotec.cl',
                'phone' => '+56 9 5544 3322',
                'category' => 'Equipamiento mayor',
                'status' => 'inactivo'
            ],
            [
                'name' => 'Insumos de Pediatría PediMed',
                'rut' => '76.444.666-5',
                'email' => 'ventas@pedimed.cl',
                'phone' => '+56 2 2666 7788',
                'category' => 'Insumos médicos genéricos',
                'status' => 'activo'
            ],
            [
                'name' => 'Importadora de Agujas y Jeringas',
                'rut' => '79.555.777-6',
                'email' => 'contacto@jeringaschile.cl',
                'phone' => '+56 9 2211 4433',
                'category' => 'Insumos médicos genéricos',
                'status' => 'activo'
            ]
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