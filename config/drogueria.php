<?php

return [

    'iva' => 0.19,

    'meses_alerta_vencimiento' => 6,

    'controlados' => [
        'Alprazolam 0.5 mg comprimidos',
        'Buprenorfina 35 mcg/h parche',
        'Clonazepam 0.5 mg comprimidos',
        'Clonazepam 2 mg comprimidos',
        'Diazepam 10 mg comprimidos',
        'Diazepam 10 mg/2 ml ampolla',
        'Fenobarbital 100 mg comprimidos',
        'Lorazepam 4 mg/2ml ampolla',
        'Lorazepam 2 mg comprimido sublingual',
        'Midazolam 15 mg/3ml ampolla',
        'Midazolam 5mg/ml ampolla',
        'Metilfenidato Clorhidrato 10 mg comprimidos',
        'Morfina 10mg/ml ampolla',
        'Morfina 20 mg/ml Frasco gotas',
        'Petidina 100 mg/2ml ampolla',
        'Zolpidem 10 mg comprimidos',
        'Recetas Cheque Estupefacientes',
    ],

    /*
    | Principios activos sujetos a control legal (estupefacientes y psicotrópicos).
    | Cualquier producto cuyo nombre contenga uno de estos principios (o una
    | variante muy parecida, por ejemplo con un error de tipeo) se trata como
    | fármaco controlado en todo el sistema, aunque su nombre no sea idéntico
    | a los de la lista 'controlados'.
    */
    'principios_controlados' => [
        'alprazolam',
        'buprenorfina',
        'clonazepam',
        'diazepam',
        'fenobarbital',
        'lorazepam',
        'metilfenidato',
        'midazolam',
        'morfina',
        'petidina',
        'zolpidem',
    ],

    // Frases que identifican a un controlado aunque no sea un principio activo.
    'frases_controlados' => [
        'receta cheque',
        'recetas cheque',
    ],

    'establecimientos' => [
        // CESFAM
        ['nombre' => 'CESFAM Los Cerros', 'tipo' => 'CESFAM', 'direccion' => 'Calle El Galgo, esquina Calle Coliumo, Villa Badaran, Talcahuano'],
        ['nombre' => 'CESFAM Paulina Avendaño Pereda', 'tipo' => 'CESFAM', 'direccion' => 'Calle Carlos Dittborn 4100, Villa San Martín, Talcahuano'],
        ['nombre' => 'CESFAM San Vicente', 'tipo' => 'CESFAM', 'direccion' => 'Calle Brasil 360, San Vicente, Talcahuano'],
        ['nombre' => 'CESFAM Alcalde Leocán Portus Govinden', 'tipo' => 'CESFAM', 'direccion' => 'Calle Manuel Barros Borgoño 2645, Las Salinas, Talcahuano'],

        // CECOSF
        ['nombre' => 'CECOSF Esmeralda', 'tipo' => 'Otros', 'direccion' => 'Calle San Miguel 546, Población Patricio Lynch, Talcahuano'],
        ['nombre' => 'CECOSF 8 de Mayo', 'tipo' => 'Otros', 'direccion' => 'Calle Aysén 7370, Población Diego Portales, Talcahuano'],
        ['nombre' => 'CECOSF Libertad-Gaete', 'tipo' => 'Otros', 'direccion' => 'Calle Isla San Ambrosio 509, Población Libertad, Talcahuano'],
        ['nombre' => 'CECOSF Los Lobos-La Gloria', 'tipo' => 'Otros', 'direccion' => 'Calle Los Lobos 1960, Población Los Lobos, Talcahuano'],
        ['nombre' => 'CECOSF Villa Centinela', 'tipo' => 'Otros', 'direccion' => 'Calle Punta Peñablanca 1301, Población Centinela, Talcahuano'],

        // Otros Establecimientos
        ['nombre' => 'Posta Rural Tumbes', 'tipo' => 'Otros', 'direccion' => 'Maryland s/n, Caleta Tumbes, Talcahuano'],
        ['nombre' => 'SAR San Vicente', 'tipo' => 'Otros', 'direccion' => 'Juan de la Cruz Tapia s/n, Talcahuano'],
        ['nombre' => 'SAR Los Cerros', 'tipo' => 'Otros', 'direccion' => 'Calle Coliumo S/N Nueva los Lobos, Talcahuano'],
        ['nombre' => 'UAPO', 'tipo' => 'Otros', 'direccion' => 'Calle Blanco Encalada 1142, Talcahuano'],
        ['nombre' => 'Dental - CESFAM San Vicente', 'tipo' => 'Otros', 'direccion' => 'Brasil 360, Talcahuano'],
        ['nombre' => 'Laboratorio - CESFAM San Vicente', 'tipo' => 'Otros', 'direccion' => 'Brasil 360, Talcahuano'],
        ['nombre' => 'Módulo Dental JUNAEB', 'tipo' => 'Otros', 'direccion' => 'Calle Blanco Encalada 455, Talcahuano'],
        ['nombre' => 'Móvil: Liceo Polivalente y Liceo Comercial', 'tipo' => 'Otros', 'direccion' => 'Unidad Móvil Salud Escolar, Talcahuano'],
        ['nombre' => 'Móvil: Liceo Industrial y Liceo Asunción', 'tipo' => 'Otros', 'direccion' => 'Unidad Móvil Salud Escolar, Talcahuano'],
        ['nombre' => 'Móvil: Liceo A-21', 'tipo' => 'Otros', 'direccion' => 'Unidad Móvil Salud Escolar, Talcahuano'],
    ],
];
