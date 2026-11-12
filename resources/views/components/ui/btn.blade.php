{{-- Botón con las 4 variantes del sistema: primary | secondary | danger | ghost.
     Con href se dibuja como enlace. Los demás atributos (id, onclick, type...) se conservan. --}}
@props(['variant' => 'secondary', 'href' => null])
@php $clases = 'das-btn das-btn-' . $variant; @endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $clases]) }}>{{ $slot }}</button>
@endif
