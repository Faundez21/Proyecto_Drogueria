{{-- mensaje de "sin resultados" con una accion opcional --}}
@props(['title' => 'Sin resultados', 'description' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-10 px-4']) }}>
    <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
    <p class="text-[13.5px] font-semibold text-slate-700">{{ $title }}</p>
    @if ($description)<p class="text-[12.5px] text-slate-500 mt-1">{{ $description }}</p>@endif
    @if (! $slot->isEmpty())<div class="mt-3">{{ $slot }}</div>@endif
</div>
