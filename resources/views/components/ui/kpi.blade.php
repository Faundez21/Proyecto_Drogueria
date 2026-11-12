{{-- Indicador compacto para la tira de indicadores (<div class="das-kpi-strip">).
     tone: ok | warn | crit | info | null.  href: si se indica, el indicador es un enlace. --}}
@props(['label', 'value', 'hint' => null, 'tone' => null, 'href' => null])
@php $clases = 'das-kpi' . ($tone ? ' is-' . $tone : ''); @endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
@else
    <div {{ $attributes->merge(['class' => $clases]) }}>
@endif
    <span class="das-kpi-value">{{ $value }}</span>
    <span class="das-kpi-label">{{ $label }}@if ($hint)<span class="das-kpi-hint">{{ $hint }}</span>@endif</span>
@if ($href)
    </a>
@else
    </div>
@endif
