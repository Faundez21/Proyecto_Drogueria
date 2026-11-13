{{-- boton con 4 variantes: primary, secondary, danger y ghost; con href se dibuja como enlace --}}
@props(['variant' => 'secondary', 'href' => null])
@php $clases = 'das-btn das-btn-' . $variant; @endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $clases]) }}>{{ $slot }}</button>
@endif
