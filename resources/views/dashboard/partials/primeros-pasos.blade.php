{{-- guia de configuracion inicial, solo admin --}}
@role('Administrador')
    @php
        $pasosConfiguracion = [];
        try {
            $pasosConfiguracion = [
                [
                    'titulo' => 'Registrar proveedores',
                    'texto' => 'Los laboratorios y distribuidores que entregan mercadería.',
                    'hecho' => \App\Models\Provider::exists(),
                    'url' => route('providers.index'),
                    'boton' => 'Ir a Proveedores',
                ],
                [
                    'titulo' => 'Organizar la bodega',
                    'texto' => 'Crear pasillos, estanterías, niveles y posiciones para ubicar cada lote.',
                    'hecho' => \App\Models\Level::exists() && \App\Models\Position::exists(),
                    'url' => route('distribution.index'),
                    'boton' => 'Ir a Distribución',
                ],
                [
                    'titulo' => 'Cargar el inventario',
                    'texto' => 'Subir el stock inicial con «Cargar desde Excel» o registrar una Recepción.',
                    'hecho' => \App\Models\Inventory::exists(),
                    'url' => route('inventory.index'),
                    'boton' => 'Ir a Inventario',
                ],
                [
                    'titulo' => 'Crear usuarios',
                    'texto' => 'Definir roles con sus vistas y crear las cuentas de los funcionarios.',
                    'hecho' => \App\Models\User::count() > 1,
                    'url' => route('users.index'),
                    'boton' => 'Ir a Usuarios',
                ],
            ];
        } catch (\Throwable $e) {
            $pasosConfiguracion = [];
        }

        $pasosHechos = collect($pasosConfiguracion)->where('hecho', true)->count();
        $pasosTotal = count($pasosConfiguracion);
    @endphp

    @if ($pasosTotal > 0 && $pasosHechos < $pasosTotal)
        @php $siguientePaso = collect($pasosConfiguracion)->firstWhere('hecho', false); @endphp
        {{-- una linea con el avance, la lista se despliega al pedirla --}}
        <details id="primeros-pasos" class="group rounded-2xl border border-slate-200 bg-white shadow-xs">
            <summary class="list-none cursor-pointer select-none flex flex-col md:flex-row md:items-center gap-3 px-4 py-3">
                <span class="flex items-center gap-3 min-w-0 flex-1">
                    <span class="text-[13.5px] font-bold text-slate-800 shrink-0">Configuración inicial</span>
                    <span class="h-1.5 w-24 rounded-full bg-slate-100 overflow-hidden shrink-0" role="progressbar"
                        aria-valuenow="{{ $pasosHechos }}" aria-valuemin="0" aria-valuemax="{{ $pasosTotal }}">
                        <span class="block h-full bg-teal-500 rounded-full" style="width: {{ round($pasosHechos / $pasosTotal * 100) }}%"></span>
                    </span>
                    <span class="text-[12.5px] text-slate-500 shrink-0">{{ $pasosHechos }} de {{ $pasosTotal }} listos</span>
                </span>
                <span class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    @if ($siguientePaso)
                        <a href="{{ $siguientePaso['url'] }}" onclick="event.stopPropagation()"
                            class="text-[12.5px] font-bold text-teal-700 hover:underline">Siguiente: {{ $siguientePaso['titulo'] }} →</a>
                    @endif
                    <span class="text-[12.5px] font-semibold text-slate-500 group-open:hidden">Ver los pasos</span>
                    <span class="text-[12.5px] font-semibold text-slate-500 hidden group-open:inline">Ocultar los pasos</span>
                    <button type="button" onclick="event.preventDefault(); event.stopPropagation(); ocultarPrimerosPasos()"
                        class="text-[12px] font-semibold text-slate-400 hover:text-slate-700 cursor-pointer">No mostrar más</button>
                </span>
            </summary>

            <ol class="px-4 pb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($pasosConfiguracion as $i => $paso)
                    <li class="flex flex-col gap-2 rounded-xl border p-3.5 {{ $paso['hecho'] ? 'border-teal-200 bg-teal-50/60' : 'border-slate-200 bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            @if ($paso['hecho'])
                                <span class="w-6 h-6 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0" aria-label="Hecho">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-white border border-slate-300 text-slate-600 text-[11px] font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                            @endif
                            <span class="text-[13px] font-bold text-slate-800">{{ $paso['titulo'] }}</span>
                        </div>
                        <p class="text-[12px] text-slate-600 leading-snug flex-1">{{ $paso['texto'] }}</p>
                        @if ($paso['hecho'])
                            <span class="text-[11.5px] font-semibold text-teal-700">Listo</span>
                        @else
                            <a href="{{ $paso['url'] }}"
                                class="self-start inline-flex items-center gap-1 text-[12px] font-bold text-teal-700 hover:text-teal-900">
                                {{ $paso['boton'] }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </details>

        <style>
            html.dark #primeros-pasos li.bg-slate-50 { background-color: #1E293B; border-color: #334155; }
            html.dark #primeros-pasos li.bg-teal-50\/60 { background-color: rgba(20, 184, 166, .08); border-color: rgba(45, 212, 191, .25); }
            html.dark #primeros-pasos .bg-slate-100 { background-color: #334155; }
            html.dark #primeros-pasos li .bg-white { background-color: #243044; border-color: #475569; color: #CBD5E1; }
            html.dark #primeros-pasos .text-teal-700 { color: #5EEAD4; }
        </style>

        <script>
            (function () {
                try {
                    if (localStorage.getItem('das-primeros-pasos') === 'oculta') {
                        document.getElementById('primeros-pasos')?.remove();
                    }
                } catch (e) {}
            })();

            function ocultarPrimerosPasos() {
                try { localStorage.setItem('das-primeros-pasos', 'oculta'); } catch (e) {}
                document.getElementById('primeros-pasos')?.remove();
            }
        </script>
    @endif
@endrole
