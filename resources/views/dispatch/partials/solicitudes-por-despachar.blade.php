{{-- paso 1 del despacho: solicitudes pendientes con boton para cargarlas --}}
@php
    $controlados = $controlados ?? false;
    // clases completas para que tailwind las compile
    $c = $controlados ? [
        'paso' => 'bg-rose-600', 'link' => 'text-rose-700 hover:text-rose-900',
        'mia' => 'border-rose-300 bg-rose-50/40', 'badge' => 'bg-rose-600',
        'btn' => 'bg-rose-600 hover:bg-rose-700', 'hover' => 'hover:border-rose-400 hover:text-rose-700',
        'texto' => 'text-rose-700', 'ring' => 'ring-rose-400',
    ] : [
        'paso' => 'bg-teal-600', 'link' => 'text-teal-700 hover:text-teal-900',
        'mia' => 'border-teal-300 bg-teal-50/40', 'badge' => 'bg-teal-600',
        'btn' => 'bg-teal-600 hover:bg-teal-700', 'hover' => 'hover:border-teal-400 hover:text-teal-700',
        'texto' => 'text-teal-700', 'ring' => 'ring-teal-400',
    ];
    $miId = (int) auth()->id();

    $activas = collect($solicitudesPanel)
        ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
        ->map(function ($sol) use ($controlados, $miId) {
            $items = $sol->items->filter(function ($it) use ($controlados) {
                $esCtrl = \App\Services\InventoryService::esControlado($it->product->nombre ?? '');
                return $controlados ? $esCtrl : ! $esCtrl;
            });
            $sol->panel_pendiente = (int) $items->sum(fn ($it) => max(0, (int) $it->cantidad_solicitada - (int) $it->cantidad_despachada));
            $sol->panel_productos = $items->count();
            $sol->panel_es_mia = (int) ($sol->assigned_user_id ?? 0) === $miId;
            return $sol;
        })
        ->filter(fn ($sol) => $sol->panel_pendiente > 0)
        // primero las asignadas a mi, luego las mas antiguas
        ->sortBy(fn ($sol) => [$sol->panel_es_mia ? 0 : 1, (string) $sol->fecha_solicitud, $sol->id])
        ->values();

    $maxVisibles = 6;
    $visibles = $activas->take($maxVisibles);
    $restantes = $activas->count() - $visibles->count();
    $estadoClases = [
        'Pendiente' => 'bg-amber-100 text-amber-800',
        'Asignada' => 'bg-indigo-100 text-indigo-700',
        'Parcial' => 'bg-sky-100 text-sky-700',
    ];
@endphp

<section class="solicitudes-panel mb-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full {{ $c['paso'] }} text-white text-[14px] font-bold flex items-center justify-center shrink-0">1</span>
            <div>
                <h2 class="text-[16px] font-bold text-slate-800">Elige qué vas a despachar</h2>
                <p class="text-[12.5px] text-slate-500">
                    @if ($activas->count() > 0)
                        {{ $activas->count() }} {{ $activas->count() === 1 ? 'solicitud espera' : 'solicitudes esperan' }} ser despachada{{ $activas->count() === 1 ? '' : 's' }}.
                    @else
                        No hay solicitudes pendientes.
                    @endif
                </p>
            </div>
        </div>
        <button type="button" onclick="{{ $fnVerTodas }}"
            class="self-start sm:self-auto text-[12.5px] font-semibold {{ $c['link'] }} underline-offset-2 hover:underline cursor-pointer">
            Ver todas y asignar trabajadores
        </button>
    </div>

    <div class="p-4 sm:p-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($visibles as $sol)
            <article class="rounded-xl border {{ $sol->panel_es_mia ? $c['mia'] : 'border-slate-200 bg-white' }} p-4 flex flex-col gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11.5px] font-mono font-bold text-slate-500">{{ $sol->numero_solicitud }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $estadoClases[$sol->estado] ?? 'bg-slate-100 text-slate-700' }}">{{ $sol->estado }}</span>
                        @if ($sol->panel_es_mia)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $c['badge'] }} text-white">Asignada a ti</span>
                        @endif
                    </div>
                    <p class="text-[14px] font-bold text-slate-800 mt-1 leading-snug">{{ $sol->establecimiento }}</p>
                    <p class="text-[12px] text-slate-500 mt-0.5">
                        {{ $sol->panel_productos }} {{ $sol->panel_productos === 1 ? 'producto' : 'productos' }}
                        · faltan {{ number_format($sol->panel_pendiente, 0, ',', '.') }} uds.
                        · {{ \Carbon\Carbon::parse($sol->fecha_solicitud)->format('d/m/Y') }}
                    </p>
                </div>
                <button type="button" onclick="elegirSolicitudParaDespacho({{ $sol->id }})"
                    class="mt-auto w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-bold text-white {{ $c['btn'] }} shadow-sm transition-colors cursor-pointer">
                    Despachar esta solicitud
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </article>
        @endforeach

        @if ($restantes > 0)
            <button type="button" onclick="{{ $fnVerTodas }}"
                class="rounded-xl border border-dashed border-slate-300 p-4 flex flex-col items-center justify-center gap-1 text-slate-600 {{ $c['hover'] }} transition-colors cursor-pointer min-h-[140px]">
                <span class="text-[22px] font-bold">+{{ $restantes }}</span>
                <span class="text-[12.5px] font-semibold">Ver las demás solicitudes</span>
            </button>
        @endif

        @if ($permitirSinSolicitud ?? false)
            <button type="button" onclick="iniciarDespachoSinSolicitud()"
                class="rounded-xl border border-dashed border-slate-300 p-4 flex flex-col items-start justify-center gap-1 text-left {{ $c['hover'] }} hover:bg-slate-50 transition-colors cursor-pointer min-h-[140px]">
                <span class="text-[14px] font-bold text-slate-800">Despachar sin solicitud</span>
                <span class="text-[12px] text-slate-500 leading-snug">Traspaso interno a un establecimiento de la red, o salida externa (préstamo, donación, devolución).</span>
                <span class="mt-1 text-[12.5px] font-bold {{ $c['texto'] }}">Llenar formulario a mano →</span>
            </button>
        @elseif ($activas->isEmpty())
            <div class="sm:col-span-2 xl:col-span-3 rounded-xl bg-slate-50 border border-slate-200 p-5 text-center">
                <p class="text-[13.5px] font-semibold text-slate-700">No hay solicitudes de CESFAM con controlados por despachar.</p>
                <p class="text-[12.5px] text-slate-500 mt-1">Aparecerán aquí cuando un CESFAM las cree en «Solicitudes CESFAM».</p>
            </div>
        @endif
    </div>
</section>

<script>
    // carga la solicitud y lleva al formulario
    function elegirSolicitudParaDespacho(id) {
        if (typeof {{ $fnUsar }} !== 'function') return;
        {{ $fnUsar }}(id);
        irAlFormularioDespacho();
    }

    function iniciarDespachoSinSolicitud() {
        if (typeof desvincularSolicitudActiva === 'function') desvincularSolicitudActiva();
        irAlFormularioDespacho();
        setTimeout(() => document.getElementById('clasificacion_select')?.focus({ preventScroll: true }), 400);
    }

    function irAlFormularioDespacho() {
        const destino = document.getElementById('{{ $idFormulario }}');
        if (!destino) return;
        setTimeout(() => {
            destino.scrollIntoView({ behavior: 'smooth', block: 'start' });
            destino.classList.add('ring-2', '{{ $c['ring'] }}');
            setTimeout(() => destino.classList.remove('ring-2', '{{ $c['ring'] }}'), 1600);
        }, 200);
    }
</script>
