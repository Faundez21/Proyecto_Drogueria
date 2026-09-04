<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - DAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex justify-between items-center w-full shadow-sm">
        <div class="flex items-center gap-6 w-full max-w-3xl">
            <button class="p-2 bg-slate-50 text-slate-500 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <a href="#" class="text-2xl font-extrabold text-slate-800 tracking-wider">DAS</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 mt-4">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl text-gray-800">Reportes</h1>
                <p class="text-gray-500 mt-1">Historial de importaciones y registros recientes</p>
            </div>

            <x-button variant="success" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Exportar Reporte
            </x-button>
        </div>

        <div
            class="bg-white p-4 rounded-lg shadow-sm border border-neutral-300 mb-6 flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label for="buscar_id" class="block text-sm font-medium text-gray-700 mb-1">ID Importación</label>
                <input type="text" id="buscar_id" placeholder="Ej. #IMP-1042"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-colors">
            </div>

            <div class="w-full md:w-1/4">
                <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                <input type="date" id="fecha_desde"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-colors">
            </div>

            <div class="w-full md:w-1/4">
                <label for="modulo" class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                <select id="modulo"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 transition-colors bg-white">
                    <option value="">Todos</option>
                    <option value="recepcion">Recepción</option>
                    <option value="despacho">Despacho</option>
                </select>
            </div>

            <div>
                <button
                    class="bg-blue-900 text-white px-5 py-2 rounded-md hover:bg-blue-800 transition-colors font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtrar
                </button>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="border border-neutral-300 rounded-lg bg-white overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm font-light text-gray-800 whitespace-nowrap">

                        <thead class="bg-blue-900 text-white font-medium">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID Importación</th>
                                <th scope="col" class="px-6 py-4">Fecha y Hora</th>
                                <th scope="col" class="px-6 py-4">Módulo / Origen</th>
                                <th scope="col" class="px-6 py-4">Registros</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="border-b border-neutral-200 bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium">#IMP-1042</td>
                                <td class="px-6 py-4 text-gray-600">04-09-2026 14:30</td>
                                <td class="px-6 py-4">
                                    <span class="font-medium">Recepción</span> - Fármacos
                                </td>
                                <td class="px-6 py-4 text-gray-600">150 filas</td>
                            </tr>

                            <tr class="border-b border-neutral-200 bg-gray-50 hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-4 font-medium">#IMP-1041</td>
                                <td class="px-6 py-4 text-gray-600">03-09-2026 09:15</td>
                                <td class="px-6 py-4">
                                    <span class="font-medium">Recepción</span> - Insumos
                                </td>
                                <td class="px-6 py-4 text-gray-600">45 filas</td>
                            </tr>


                            <tr class="bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium">#IMP-1040</td>
                                <td class="px-6 py-4 text-gray-600">01-09-2026 11:20</td>
                                <td class="px-6 py-4">
                                    <span class="font-medium">Despacho</span> - Equipamiento
                                </td>
                                <td class="px-6 py-4 text-gray-600">12 filas</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
