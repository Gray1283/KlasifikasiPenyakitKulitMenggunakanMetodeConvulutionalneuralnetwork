@extends('layouts.admin')

@section('pageTitle', 'Dataset — ' . $penyakit->nama_penyakit)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dataset.index') }}"
                class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fa-solid fa-images text-blue-600 mr-2"></i>
                    {{ $penyakit->nama_penyakit }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Label:
                    <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">
                        {{ $penyakit->kode_label ?? '-' }}
                    </span>
                    &bull; {{ $gambar->count() }} gambar
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($gambar->isNotEmpty())
            <form id="formHapusSemua" method="POST"
                action="{{ route('admin.dataset.hapus-semua', $penyakit->id_penyakit) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" onclick="konfirmasiHapusSemua()"
                class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-100 rounded-lg hover:bg-red-100 flex items-center gap-2">
                <i class="fa-solid fa-trash-can"></i>
                Hapus Semua
            </button>
            @endif
            <a href="{{ route('admin.dataset.index') }}"
                class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                <i class="fa-solid fa-file-zipper"></i>
                Upload ZIP
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Grid Gambar --}}
    @if($gambar->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
        <i class="fa-solid fa-image text-5xl mb-3 block opacity-20"></i>
        <p class="font-medium">Belum ada gambar untuk kelas ini.</p>
        <p class="text-sm mt-1">Kembali ke halaman Dataset dan klik "Upload ZIP" untuk menambahkan.</p>
    </div>
    @else

    {{-- Search + info --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Menampilkan <span id="visibleCount">{{ $gambar->count() }}</span> gambar
        </p>
        <input type="text" id="searchGambar" placeholder="Cari nama file..."
            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-300 w-48">
    </div>

    <div id="gridGambar" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($gambar as $img)
        <div class="gambar-card bg-white rounded-xl shadow-sm overflow-hidden group" data-name="{{ $img['name'] }}">
            <div class="aspect-square overflow-hidden bg-gray-100 cursor-pointer"
                onclick="lihatGambar('{{ $img['path'] }}', '{{ $img['name'] }}')">
                <img src="{{ $img['path'] }}" alt="{{ $img['name'] }}"
                    loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform">
            </div>
            <div class="p-2">
                <p class="text-xs text-gray-400 truncate" title="{{ $img['name'] }}">{{ $img['name'] }}</p>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-xs text-gray-300">{{ $img['size'] }}</span>
                    <form id="form-hapus-{{ $loop->index }}" method="POST"
                        action="{{ route('admin.dataset.hapus-gambar', [$penyakit->id_penyakit, $img['name']]) }}"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button"
                        onclick="konfirmasiHapusGambar({{ $loop->index }}, '{{ addslashes($img['name']) }}')"
                        class="text-red-400 hover:text-red-600 text-xs" title="Hapus">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Modal Preview Gambar --}}
<div id="modalPreview" class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center" style="display:none!important;">
    <div class="relative max-w-2xl w-full px-4">
        <button onclick="document.getElementById('modalPreview').style.cssText='display:none!important'"
            class="absolute -top-10 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img id="previewImg" src="" alt="" class="w-full rounded-xl shadow-2xl max-h-[80vh] object-contain">
        <p id="previewNama" class="text-center text-white text-sm mt-3 opacity-70"></p>
    </div>
</div>

{{-- SweetAlert2 (kalau sudah ada di layouts/admin.blade.php, baris ini boleh dihapus biar tidak double-load) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function lihatGambar(path, nama) {
    document.getElementById('previewImg').src = path;
    document.getElementById('previewNama').textContent = nama;
    document.getElementById('modalPreview').style.cssText = 'display:flex!important';
}

// ── Konfirmasi hapus 1 gambar ─────────────────────────────────
function konfirmasiHapusGambar(index, nama) {
    Swal.fire({
        title: 'Hapus gambar ini?',
        text: nama,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-' + index).submit();
        }
    });
}

// ── Konfirmasi hapus SEMUA gambar di kelas ini ─────────────────
function konfirmasiHapusSemua() {
    Swal.fire({
        title: 'Hapus semua gambar?',
        text: 'Seluruh gambar pada kelas "{{ $penyakit->nama_penyakit }}" ({{ $gambar->count() }} gambar) akan dihapus permanen dan tidak bisa dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, hapus semua',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formHapusSemua').submit();
        }
    });
}

// Search gambar
document.getElementById('searchGambar')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    const cards = document.querySelectorAll('.gambar-card');
    let visible = 0;
    cards.forEach(c => {
        const match = c.dataset.name.toLowerCase().includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('visibleCount').textContent = visible;
});
</script>
@endsection