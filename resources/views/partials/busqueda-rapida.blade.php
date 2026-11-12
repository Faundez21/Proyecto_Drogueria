{{-- buscador visible que usa el campo search del formulario de filtros --}}
<form class="busqueda-rapida relative w-full sm:w-72" role="search"
    onsubmit="event.preventDefault(); enviarBusquedaRapida(this, '{{ $formulario }}');">
    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none"
        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <input type="search" value="{{ request('search') }}" placeholder="{{ $placeholder ?? 'Buscar...' }}"
        aria-label="{{ $placeholder ?? 'Buscar' }}" autocomplete="off"
        class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-[13px] text-slate-800 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
</form>

@once
    <script>
        function enviarBusquedaRapida(form, idFormulario) {
            const filtros = document.getElementById(idFormulario);
            const campo = filtros ? filtros.querySelector('[name="search"]') : null;
            if (!filtros || !campo) return;
            campo.value = form.querySelector('input').value.trim();
            filtros.submit();
        }
    </script>
@endonce
