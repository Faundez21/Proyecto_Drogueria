{{-- etiqueta de estado con color e icono fijos para cada significado. uso: <x-ui.estado :valor="$lote->estado" /> --}}
@props(['valor' => ''])
@php
    $v = mb_strtolower(trim((string) $valor));
    $tono = match (true) {
        str_contains($v, 'controlad') => 'ctrl',
        in_array($v, ['disponible', 'recibido', 'despachado', 'completado', 'activo', 'liberado', 'liberada']) => 'ok',
        str_contains($v, 'cuarentena') || str_contains($v, 'por vencer') || str_contains($v, 'parcial') => 'warn',
        str_contains($v, 'vencid') || str_contains($v, 'merma') || str_contains($v, 'rechaz') || str_contains($v, 'inactiv') || str_contains($v, 'anulad') => 'crit',
        str_contains($v, 'pendiente') || str_contains($v, 'asignad') || str_contains($v, 'tránsito') || str_contains($v, 'transito') => 'info',
        default => 'neutral',
    };
    $iconos = [
        'ok' => 'M5 13l4 4L19 7',
        'warn' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'crit' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        'info' => 'M13 7l5 5m0 0l-5 5m5-5H6',
        'ctrl' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'neutral' => 'M12 12h.01',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'das-estado das-estado-' . $tono]) }}>
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="{{ $iconos[$tono] }}"></path></svg>
    {{ $slot->isEmpty() ? $valor : $slot }}
</span>
