@php
    $variants = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'warning' => 'bg-yellow-400 hover:bg-yellow-500 text-gray-900',
        'info' => 'bg-cyan-500 hover:bg-cyan-600 text-white',
    ];
@endphp
<button {{ $attributes->merge([
    'class' => 'px-4 py-2 rounded-md font-medium transition ' . $variants[$variant]
]) }}>
    {{ $slot }}
</button>
