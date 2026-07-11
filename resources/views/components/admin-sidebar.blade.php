<div id="sidebar" class="flex flex-col h-screen shrink-0" style="background:#146135; width:220px;">

    {{-- Brand --}}
    <div class="px-5 py-4 border-b border-white/15">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                <i class="fas fa-brain text-white text-xs"></i>
            </div>
            <div>
                <h1 class="text-sm font-semibold text-white leading-tight">Admin Panel CNN</h1>
                <p class="text-[10px] text-white/60">Penyakit Kulit</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-0.5">

        @php
            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => 'fa-gauge', 'label' => 'Dashboard'],
                ['route' => 'admin.users', 'icon' => 'fa-users', 'label' => 'Manajemen User'],
                ['route' => 'admin.riwayat_kesehatan.index', 'icon' => 'fa-clipboard-list', 'label' => 'Riwayat Kesehatan'],
            ];

            // Urutan disesuaikan dengan alur kerja ML yang sebenarnya:
            // 1. Siapkan data (Dataset) -> 2. Perbanyak variasi data (Augmentasi)
            // -> 3. Latih model (Training) -> 4. Kelola model aktif (Model) -> 5. Ukur performa (Evaluasi)
            $navAI = [
                ['route' => 'admin.dataset.index', 'icon' => 'fa-database', 'label' => 'Dataset'],
                ['route' => 'admin.augmentasi.index', 'icon' => 'fa-wand-magic-sparkles', 'label' => 'Augmentasi'],
                ['route' => 'admin.training.index', 'icon' => 'fa-microchip', 'label' => 'Training'],
                ['route' => 'admin.model.index', 'icon' => 'fa-brain', 'label' => 'Model'],
                ['route' => 'admin.evaluasi.index', 'icon' => 'fa-chart-pie', 'label' => 'Evaluasi'],
            ];
        @endphp

        {{-- Grup Umum --}}
        <p class="text-[10px] font-semibold text-white/50 uppercase tracking-widest px-3 pt-1 pb-2">Umum</p>
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm border-l-[3px]
                      {{ request()->routeIs($item['route'])
                          ? 'bg-white/20 font-semibold text-white border-white'
                          : 'text-white/75 hover:text-white hover:bg-white/10 border-transparent' }}">
                <i class="fas {{ $item['icon'] }} w-4 text-center text-sm"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        {{-- Grup AI --}}
        <p class="text-[10px] font-semibold text-white/50 uppercase tracking-widest px-3 pt-4 pb-2">AI &amp; Model</p>
        @foreach ($navAI as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm border-l-[3px]
                      {{ request()->routeIs($item['route'] . '*')
                          ? 'bg-white/20 font-semibold text-white border-white'
                          : 'text-white/75 hover:text-white hover:bg-white/10 border-transparent' }}">
                <i class="fas {{ $item['icon'] }} w-4 text-center text-sm"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

    </nav>

    {{-- User + Logout --}}
    <div class="px-2.5 py-3 border-t border-white/15 space-y-1">
        <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10">
            <div class="w-7 h-7 rounded-full bg-white/25 flex items-center justify-center text-xs font-bold text-white shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden flex-1">
                <p class="text-sm font-medium text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-white/60 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

</div>