<!-- resources/views/component/sidebar.blade.php -->
<div id="sidebar" class="bg-[#146135] text-white w-64 transition-all duration-300">
    <!-- Hamburger Menu -->
    <div class="p-4">
        <button onclick="toggleSidebar()" class="p-2 hover:bg-[#0f4a27] rounded-lg transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Menu Items -->
    <nav class="mt-8">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-4 px-6 py-4 sidebar-hover transition-colors {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
            <i class="fas fa-home text-xl"></i>
            <span class="text-lg menu-text">Dashboard</span>
        </a>

        <a href="{{ route('deteksi.index') }}" 
           class="flex items-center gap-4 px-6 py-4 sidebar-hover transition-colors {{ request()->routeIs('deteksi.*') ? 'active-menu' : '' }}">
            <i class="fas fa-file-medical text-xl"></i>
            <span class="text-lg menu-text">DeteksiAI</span>
        </a>

        <a href="{{ route('riwayat_kesehatan.index') }}" 
           class="flex items-center gap-4 px-6 py-4 sidebar-hover transition-colors {{ request()->routeIs('riwayat.*') ? 'active-menu' : '' }}">
            <i class="fas fa-chart-line text-xl"></i>
            <span class="text-lg menu-text">Riwayat Kesehatan</span>
        </a>

        <a href="" 
           class="flex items-center gap-4 px-6 py-4 sidebar-hover transition-colors {{ request()->routeIs('pengaturan') ? 'active-menu' : '' }}">
            <i class="fas fa-cog text-xl"></i>
            <span class="text-lg menu-text">Pengaturan</span>
        </a>
    </nav>
</div>