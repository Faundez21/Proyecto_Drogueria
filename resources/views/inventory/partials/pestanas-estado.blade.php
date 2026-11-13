{{-- pestanas disponibles, cuarentena y mermas segun permisos --}}
@php
    $usuarioPestanas = auth()->user();
    $verInventario = $usuarioPestanas->can('ver.inventario');
    $verCuarentena = $usuarioPestanas->can('ver.cuarentena');
    $verControlados = $usuarioPestanas->hasRole('Administrador') || $usuarioPestanas->can('ver.despacho_controlado');

    $contarEstado = function (string $estado, bool $soloConStock) use ($verControlados) {
        return \App\Models\Inventory::where('estado', $estado)
            ->when($soloConStock, fn ($q) => $q->where('cantidad', '>', 0))
            ->when(! $verControlados, fn ($q) => $q->whereNotIn('categoria', ['Fármacos Controlados', 'Estupefacientes']))
            ->count();
    };
    $totalCuarentena = $verCuarentena ? $contarEstado('Cuarentena', true) : 0;
    $totalMermas = $verInventario ? $contarEstado('Merma', false) : 0;
    $cantidadPestanas = ($verInventario ? 2 : 0) + ($verCuarentena ? 1 : 0);

    $claseActiva = 'bg-white text-slate-900 shadow-sm';
    $claseInactiva = 'text-slate-600 hover:text-slate-900 hover:bg-white/60';
@endphp

@if ($cantidadPestanas > 1)
    <nav class="pestanas-estado flex w-full sm:w-auto sm:inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200 overflow-x-auto" aria-label="Estado de los lotes">
        @if ($verInventario)
            <a href="{{ route('inventory.index') }}" @if ($activa === 'disponibles') aria-current="page" @endif
                class="flex-1 sm:flex-none whitespace-nowrap text-center px-4 py-2 rounded-lg text-[13px] font-bold transition {{ $activa === 'disponibles' ? $claseActiva : $claseInactiva }}">
                Disponibles
            </a>
        @endif
        @if ($verCuarentena)
            <a href="{{ route('quarantine.index') }}" @if ($activa === 'cuarentena') aria-current="page" @endif
                class="flex-1 sm:flex-none whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-bold transition {{ $activa === 'cuarentena' ? $claseActiva : $claseInactiva }}">
                Cuarentena
                <span class="px-1.5 py-0.5 rounded-md text-[11px] font-bold {{ $totalCuarentena > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-600' }}">{{ $totalCuarentena }}</span>
            </a>
        @endif
        @if ($verInventario)
            {{-- mermas se muestra en inventario (?ver=mermas) --}}
            <a href="{{ route('inventory.index', ['ver' => 'mermas']) }}" @if ($activa === 'mermas') aria-current="page" @endif
                class="flex-1 sm:flex-none whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-bold transition {{ $activa === 'mermas' ? $claseActiva : $claseInactiva }}">
                Mermas
                <span class="px-1.5 py-0.5 rounded-md text-[11px] font-bold {{ $totalMermas > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">{{ $totalMermas }}</span>
            </a>
        @endif
    </nav>
@endif
