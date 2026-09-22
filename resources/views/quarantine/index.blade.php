@extends('layouts.app')

@section('content')
    <div class="w-full">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl text-gray-800 font-semibold">Cuarentena</h1>
                <p class="text-gray-500 mt-1">
                    Lista de lotes en cuarentena
                </p>
            </div>

        </div>
        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Total de lotes</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">44</h4>
                    </div>
                    <div class="p-3 bg-yellow-400 text-white rounded-xl shadow-md shadow-blue-200">
                        <svg  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Unidades totales -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Unidades totales</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">550</h4>
                    </div>
                    <div class="p-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Nuevos -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Alertas sanitarias</p>
                        <h4 class="text-3xl font-extrabold text-slate-800">4</h4>
                    </div>
                    <div class="p-3 bg-purple-600 text-white rounded-xl shadow-md shadow-purple-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!--Tabla cuarentena-->

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="overflow-x-auto w-full">

                <table class="min-w-[1100px] w-full text-left text-sm font-light text-gray-800 whitespace-nowrap">

                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-4">ID</th>
                            <th scope="col" class="px-4 py-4">Producto</th>
                            <th scope="col" class="px-4 py-4">Lote</th>
                            <th scope="col" class="px-4 py-4">Motivo</th>
                            <th scope="col" class="px-4 py-4">Cantidad</th>
                            <th scope="col" class="px-4 py-4">Fecha de Ingreso</th>
                            <th scope="col" class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class="border-b border-neutral-200 bg-orange-50/40 hover:bg-orange-50 transition-colors">

                            <td class="px-4 py-3 font-medium text-orange-800">
                                1042
                            </td>

                            <td class="px-4 py-3 font-bold text-orange-900">
                                ERITROMICINA 500 MG CM REC.
                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                LOT-2026-0145
                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                Alerta sanitaria
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-300">
                                    15 Unidades
                                </span>
                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                16/09/2026
                            </td>


                            <td class="px-4 py-3 text-center flex justify-center gap-1">

                                <!--botón ver-->

                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>

                                    </svg>

                                </button>


                            </td>

                        </tr>


                        <tr class="border-b border-neutral-200 bg-orange-50/40 hover:bg-orange-50 transition-colors">

                            <td class="px-4 py-3 font-medium text-orange-800">
                                1088
                            </td>

                            <td class="px-4 py-3 font-bold text-orange-900">
                                IBUPROFENO 400MG
                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                LOT-2026-0281
                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                Producto observado
                            </td>

                            <td class="px-4 py-3">

                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-300">
                                    320 Unidades
                                </span>

                            </td>

                            <td class="px-4 py-3 text-orange-800">
                                15/09/2026
                            </td>



                            <td class="px-4 py-3 text-center flex justify-center gap-1">

                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>

                                    </svg>

                                </button>



                            </td>

                        </tr>


                        <tr class="border-b border-neutral-200 bg-white hover:bg-gray-50 transition-colors">

                            <td class="px-4 py-3 font-medium text-gray-600">
                                1124
                            </td>

                            <td class="px-4 py-3 font-bold text-slate-800">
                                AMOXICILINA 500MG
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                LOT-2026-0348
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                Producto dañado
                            </td>

                            <td class="px-4 py-3">

                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300">
                                    24 Unidades
                                </span>

                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                14/09/2026
                            </td>



                            <td class="px-4 py-3 text-center flex justify-center gap-1">

                                <!--botón ver-->

                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>

                                    </svg>

                                </button>



                        <tr class="border-b border-neutral-200 bg-red-50 hover:bg-red-100 transition-colors">

                            <td class="px-4 py-3 font-medium text-red-800">
                                1197
                            </td>

                            <td class="px-4 py-3 font-bold text-red-900">
                                DICLOFENACO 50MG
                            </td>

                            <td class="px-4 py-3 text-red-800">
                                LOT-2026-0412
                            </td>

                            <td class="px-4 py-3 text-red-800">
                                Alerta sanitaria
                            </td>

                            <td class="px-4 py-3">

                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">

                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>

                                    </svg>

                                    48 Unidades

                                </span>

                            </td>

                            <td class="px-4 py-3 text-red-800">
                                13/09/2026
                            </td>



                            <td class="px-4 py-3 text-center flex justify-center gap-1">

                                <button
                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                    title="Ver Detalles">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>

                                    </svg>

                                </button>



                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <div
                class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-b-2xl">

                <span class="text-sm text-slate-500">
                    Mostrando del
                    <span class="font-medium text-slate-900">1</span>
                    al
                    <span class="font-medium text-slate-900">10</span>
                    de
                    <span class="font-medium text-slate-900">32</span>
                    productos en cuarentena
                </span>

                <div class="flex items-center gap-1">

                    <button
                        class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed bg-slate-50">
                        Anterior
                    </button>

                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 font-bold border border-orange-100">
                        1
                    </button>

                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                        2
                    </button>

                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                        3
                    </button>

                    <span class="px-1 text-slate-400">
                        ...
                    </span>

                    <button
                        class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Siguiente
                    </button>

                </div>

            </div>

        </div>
    @endsection
