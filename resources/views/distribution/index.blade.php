@extends('layouts.app')

@section('content')
    <!-- Barra Superior: Navegación y Acciones -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Distribución
            </a>
        </div>
    </div>
    <div class="max-w-6xl mx-auto p-4 sm:p-6">

        <!-- Encabezado -->

        <div class="mb-6">

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Distribución de la droguería
            </h1>

            <p class="text-gray-500 mt-1 text-sm sm:text-base">
                Distribución física y organización de la bodega
            </p>

        </div>


        <!-- Mantenedores -->

        <div class="mb-8">

            <h2 class="text-lg sm:text-xl font-bold text-gray-800">
                Mantenedores
            </h2>

            <p class="text-gray-500 mt-1 mb-4 text-sm sm:text-base">
                Acceder a los mantenedores de la distribución física de la droguería
            </p>


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Pasillos -->

                <a href="{{ route('pasillos.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300">

                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Pasillos
                    </h3>

                    <p class="text-gray-500 text-sm mt-1">
                        Administración de pasillos
                    </p>

                </a>


                <!-- Estanterías -->

                <a href="{{ route('shelves.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300">

                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Estanterías
                    </h3>

                    <p class="text-gray-500 text-sm mt-1">
                        Administración de estanterías
                    </p>

                </a>


                <!-- Niveles -->

                <a href="{{ route('levels.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300">

                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Niveles
                    </h3>

                    <p class="text-gray-500 text-sm mt-1">
                        Administración de niveles
                    </p>

                </a>


                <!-- Posiciones -->

                <a href="{{ route('positions.index') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-5 hover:border-blue-300">

                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Posiciones
                    </h3>

                    <p class="text-gray-500 text-sm mt-1">
                        Administración de posiciones
                    </p>

                </a>

            </div>

        </div>


        <!-- Vista del almacenamiento -->

        <div class="w-full bg-white border border-gray-200 rounded-lg p-4 sm:p-6">

            <h2 class="text-lg sm:text-xl font-bold text-gray-800">
                Vista de almacenamiento
            </h2>

            <div class="mt-5 w-full h-64 sm:h-80 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center p-4">

                <p class="text-gray-400 text-center text-sm sm:text-base">
                    Vista del layout 3D
                </p>

            </div>

        </div>

    </div>

@endsection
