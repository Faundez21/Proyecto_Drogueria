@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto p-4 sm:p-6">

    <!-- Miga de pan -->
    <div class="flex items-center gap-2 text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-900">
            Cuarentena
        </a>
    </div>

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">

        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                QR
            </h1>

            <p class="text-gray-500 mt-1">
                Administración de los productos etiquetados
            </p>
        </div>

@endsection
