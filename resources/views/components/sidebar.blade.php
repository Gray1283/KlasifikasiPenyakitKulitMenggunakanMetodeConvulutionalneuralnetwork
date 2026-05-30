{{-- resources/views/component/sidebar.blade.php --}}
<div id="sidebar" class="flex flex-col bg-[#146135] text-white w-56 min-h-screen transition-all duration-300">

    {{-- Top: brand + hamburger --}}
    <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
        <span class="menu-text font-bold tracking-wide text-sm">Klasifikasi Penyakit Kulit</span>
        <button onclick="toggleSidebar()" class="p-1.5 hover:bg-white/10 rounded-lg transition-colors">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 flex flex-col gap-0.5 px-2.5 py-3">
        @php
            $links = [
                ['route' => 'dashboard',               'icon' => 'fa-house',         'label' => 'Dashboard'],
                ['route' => 'deteksi.index',            'icon' => 'fa-file-medical',  'label' => 'DeteksiAI'],
                ['route' => 'riwayat_kesehatan.index',  'icon' => 'fa-chart-line',    'label' => 'Riwayat Kesehatan'],
            ];
        @endphp

        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                      {{ request()->routeIs(explode('.', $link['route'])[0].'*') ? 'bg-white/20 font-semibold text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                <i class="fas {{ $link['icon'] }} w-5 text-center"></i>
                <span class="menu-text">{{ $link['label'] }}</span>
            </a>
        @endforeach

        <div class="my-2 border-t border-white/10"></div>

        <a href="{{ route('pengaturan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                  {{ request()->routeIs('pengaturan') ? 'bg-white/20 font-semibold text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
            <i class="fas fa-cog w-5 text-center"></i>
            <span class="menu-text">Pengaturan</span>
        </a>
    </nav>

    {{-- User info --}}
    <div class="px-2.5 py-3 border-t border-white/10">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/10">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="menu-text overflow-hidden">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-white/50 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

</div>