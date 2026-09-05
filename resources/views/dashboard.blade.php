<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Panel Inferior: Sistema QR y Lotes en Cuarentena -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Panel Sistema QR -->
        <div class="bg-[#0f172a] p-6 rounded-2xl shadow-md text-white flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                <h2 class="text-sm font-bold text-slate-300 mb-4">Sistema QR</h2>
                <!-- Simulador de código QR -->
                <div class="w-28 h-28 bg-white p-2 rounded-lg mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=LOT-12345" alt="QR Code" class="w-full h-full object-cover rounded-md opacity-90">
                </div>
                <button class="w-full py-1.5 bg-blue-600 hover:bg-blue-700 rounded-md text-xs font-medium transition-colors">
                    Imprimir etiqueta
                </button>
            </div>
            <div class="flex-1">
                <div class="border-b border-slate-700 pb-3 mb-3">
                    <h3 class="font-bold text-lg text-white">Paracetamol 500mg</h3>
                    <p class="text-xs text-slate-400">Lote: LOT-12345</p>
                </div>
                <div class="grid grid-cols-2 gap-y-3 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs">Vencimiento</p>
                        <p class="font-medium">30/06/2026</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Ubicación</p>
                        <p class="font-medium">B-02-01</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Stock actual</p>
                        <p class="font-medium text-blue-400">350 uds</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs">Estado</p>
                        <p class="font-medium text-emerald-400">Disponible</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lotes en Cuarentena -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-bold text-slate-800">Lotes en Cuarentena</h2>
                <a href="#" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="text-xs uppercase text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="pb-2 font-medium">Producto</th>
                            <th class="pb-2 font-medium">Lote</th>
                            <th class="pb-2 font-medium">Motivo</th>
                            <th class="pb-2 font-medium">Días</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Cefalexina 500mg</td>
                            <td class="py-3 text-xs">LOT-3333</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] rounded-full">Doc. pendiente</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">2</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Losartán 50mg</td>
                            <td class="py-3 text-xs">LOT-4444</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[10px] rounded-full">Calidad</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">5</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-800">Azitromicina</td>
                            <td class="py-3 text-xs">LOT-5555</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] rounded-full">Análisis</span>
                            </td>
                            <td class="py-3 text-slate-800 font-medium">4</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
