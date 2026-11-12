{{-- Encabezado común de página: ruta pequeña, título, descripción y acciones a la derecha.
     Uso: <x-ui.page-header title="Despacho" crumb="Salida" description="...">
              <x-slot:actions> ... botones ... </x-slot:actions>
          </x-ui.page-header> --}}
@props(['title', 'crumb' => null, 'description' => null])
<div {{ $attributes->merge(['class' => 'das-page-header mb-4']) }}>
    <div class="min-w-0">
        @if ($crumb)
            <p class="das-page-crumb">{{ $crumb }}</p>
        @endif
        <h1 class="das-page-title">{{ $title }}</h1>
        @if ($description)
            <p class="das-page-desc">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="das-page-actions">{{ $actions }}</div>
    @endisset
</div>
