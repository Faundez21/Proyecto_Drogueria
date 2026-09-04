<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - DAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex justify-between items-center w-full shadow-sm">
        <div class="flex items-center gap-6 w-full max-w-3xl">
            <button class="p-2 bg-slate-50 text-slate-500 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <a href="#" class="text-2xl font-extrabold text-slate-800 tracking-wider">DAS</a>
        </div>
    </nav>

    <div class="max-w-[95%] mx-auto p-6 mt-4">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl text-gray-800">Recepcion</h1>
            </div>

            <div class="flex gap-3">
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                    Reporte
                </button>
                <x-button variant="primary">
                    + Nueva Recepción
                </x-button>
            </div>
        </div>

        <div class="flex flex-col mt-4">
            <div class="border border-neutral-300 rounded-lg bg-white overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[1200px] w-full text-left text-sm font-light text-gray-800 whitespace-nowrap">


                        <thead class="bg-blue-900 text-white">
                            <tr>
                                <th scope="col" class="px-4 py-4">N° Rec. / Fecha</th>
                                <th scope="col" class="px-4 py-4">Producto y Categoría</th>
                                <th scope="col" class="px-4 py-4">Lote y Vencimiento</th>
                                <th scope="col" class="px-4 py-4">Cant. / V. Unitario</th>
                                <th scope="col" class="px-4 py-4">Documento / Origen</th>
                                <th scope="col" class="px-4 py-4">Proveedor</th>
                                <th scope="col" class="px-4 py-4">Total (IVA inc.)</th>
                                <th scope="col" class="px-4 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-neutral-200 bg-white hover:bg-gray-100 transition-colors">

                                <td class="px-4 py-3">
                                    <div class="font-medium">#00105</div>
                                    <div class="text-xs text-gray-500">04-09-2026</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium">PARACETAMOL 500MG</div>
                                    <div class="text-xs text-blue-600 font-semibold">Fármacos - P.M. Artrosis</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>Lote: <span class="font-medium">L-9087</span></div>
                                    <div class="text-xs text-gray-500">Vence: 12/2028</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">1.500 un.</div>
                                    <div class="text-xs text-gray-500">$25 c/u</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">Factura N° 14210</div>
                                    <div class="text-xs text-gray-500">CENABAST</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">PHARMA TRADE S.A</div>
                                    <div class="text-xs text-gray-500">76.123.456-7</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    $44.625
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors mr-2">Editar</button>
                                    <button class="text-red-600 hover:text-red-800 transition-colors">Eliminar</button>
                                </td>
                            </tr>


                            <tr class="border-b border-neutral-200 bg-gray-50 hover:bg-gray-100 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium">#00106</div>
                                    <div class="text-xs text-gray-500">03-09-2026</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">GUANTES CLINICOS TALLA M</div>
                                    <div class="text-xs text-blue-600 font-semibold">Insumo - Insumos Generales</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>Lote: <span class="font-medium">X-4421</span></div>
                                    <div class="text-xs text-gray-500">Vence: 05/2030</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">500 cajas</div>
                                    <div class="text-xs text-gray-500">$1.200 c/u</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">Guía N° 5344</div>
                                    <div class="text-xs text-gray-500">Compra Directa DAS</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">SYNTHON CHILE LIMITADA</div>
                                    <div class="text-xs text-gray-500">77.987.654-3</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    $714.000
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors mr-2">Editar</button>
                                    <button class="text-red-600 hover:text-red-800 transition-colors">Eliminar</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

