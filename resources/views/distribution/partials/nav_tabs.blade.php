@php
    $activeTab = $activeTab ?? 'distribution';
    $c = $counts ?? [
        'aisles' => 0,
        'shelves' => 0,
        'levels' => 0,
        'positions' => 0,
    ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
    <!-- Mapa 3D Distribución -->
    <a href="{{ route('distribution.index') }}"
        class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 shadow-sm {{ $activeTab === 'distribution' ? 'bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-teal-500/30' : 'bg-white border-slate-200/80 text-slate-700 hover:border-teal-300 hover:shadow-md' }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 {{ $activeTab === 'distribution' ? 'bg-teal-500/20 text-teal-300 border border-teal-400/30' : 'bg-teal-50 text-teal-600 border border-teal-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block text-[10px] font-mono-das uppercase tracking-widest font-semibold {{ $activeTab === 'distribution' ? 'text-teal-300' : 'text-slate-400' }}">Visor 2D / 3D</span>
                <h3 class="text-[14px] font-bold truncate {{ $activeTab === 'distribution' ? 'text-white' : 'text-slate-800 group-hover:text-teal-700' }}">Mapa de Bodega</h3>
            </div>
        </div>
        <span class="w-2 h-2 rounded-full shrink-0 {{ $activeTab === 'distribution' ? 'bg-teal-400 pulse-dot' : 'bg-slate-200 group-hover:bg-teal-400' }}"></span>
    </a>

    <!-- 1. Pasillos -->
    <a href="{{ route('aisle.index') }}"
        class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 shadow-sm {{ $activeTab === 'aisle' ? 'bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-teal-500/30' : 'bg-white border-slate-200/80 text-slate-700 hover:border-teal-300 hover:shadow-md' }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 {{ $activeTab === 'aisle' ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block text-[10px] font-mono-das uppercase tracking-widest font-semibold {{ $activeTab === 'aisle' ? 'text-amber-300' : 'text-slate-400' }}">Nivel 01 · Zonas</span>
                <h3 class="text-[14px] font-bold truncate {{ $activeTab === 'aisle' ? 'text-white' : 'text-slate-800 group-hover:text-teal-700' }}">Pasillos</h3>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg font-mono-das text-[12px] font-bold shrink-0 {{ $activeTab === 'aisle' ? 'bg-white/10 text-white border border-white/15' : 'bg-slate-100 text-slate-700 border border-slate-200/80' }}">
            {{ $c['aisles'] ?? 0 }}
        </span>
    </a>

    <!-- 2. Estanterías -->
    <a href="{{ route('shelf.index') }}"
        class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 shadow-sm {{ $activeTab === 'shelf' ? 'bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-teal-500/30' : 'bg-white border-slate-200/80 text-slate-700 hover:border-teal-300 hover:shadow-md' }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 {{ $activeTab === 'shelf' ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16M6 4v16M18 4v16"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block text-[10px] font-mono-das uppercase tracking-widest font-semibold {{ $activeTab === 'shelf' ? 'text-blue-300' : 'text-slate-400' }}">Nivel 02 · Racks</span>
                <h3 class="text-[14px] font-bold truncate {{ $activeTab === 'shelf' ? 'text-white' : 'text-slate-800 group-hover:text-teal-700' }}">Estanterías</h3>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg font-mono-das text-[12px] font-bold shrink-0 {{ $activeTab === 'shelf' ? 'bg-white/10 text-white border border-white/15' : 'bg-slate-100 text-slate-700 border border-slate-200/80' }}">
            {{ $c['shelves'] ?? 0 }}
        </span>
    </a>

    <!-- 3. Niveles -->
    <a href="{{ route('level.index') }}"
        class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 shadow-sm {{ $activeTab === 'level' ? 'bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-teal-500/30' : 'bg-white border-slate-200/80 text-slate-700 hover:border-teal-300 hover:shadow-md' }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 {{ $activeTab === 'level' ? 'bg-purple-500/20 text-purple-300 border border-purple-400/30' : 'bg-purple-50 text-purple-600 border border-purple-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block text-[10px] font-mono-das uppercase tracking-widest font-semibold {{ $activeTab === 'level' ? 'text-purple-300' : 'text-slate-400' }}">Nivel 03 · Repisas</span>
                <h3 class="text-[14px] font-bold truncate {{ $activeTab === 'level' ? 'text-white' : 'text-slate-800 group-hover:text-teal-700' }}">Niveles</h3>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg font-mono-das text-[12px] font-bold shrink-0 {{ $activeTab === 'level' ? 'bg-white/10 text-white border border-white/15' : 'bg-slate-100 text-slate-700 border border-slate-200/80' }}">
            {{ $c['levels'] ?? 0 }}
        </span>
    </a>

    <!-- 4. Posiciones -->
    <a href="{{ route('position.index') }}"
        class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 shadow-sm {{ $activeTab === 'position' ? 'bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-teal-500/30' : 'bg-white border-slate-200/80 text-slate-700 hover:border-teal-300 hover:shadow-md' }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 {{ $activeTab === 'position' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block text-[10px] font-mono-das uppercase tracking-widest font-semibold {{ $activeTab === 'position' ? 'text-emerald-300' : 'text-slate-400' }}">Nivel 04 · Casillas</span>
                <h3 class="text-[14px] font-bold truncate {{ $activeTab === 'position' ? 'text-white' : 'text-slate-800 group-hover:text-teal-700' }}">Posiciones</h3>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg font-mono-das text-[12px] font-bold shrink-0 {{ $activeTab === 'position' ? 'bg-white/10 text-white border border-white/15' : 'bg-slate-100 text-slate-700 border border-slate-200/80' }}">
            {{ $c['positions'] ?? 0 }}
        </span>
    </a>
</div>
