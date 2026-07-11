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

        <button onclick="modalUpload({{ $penyakit->id_penyakit }}, '{{ $penyakit->nama_penyakit }}')"
            class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
            <i class="fa-solid fa-upload"></i>
            Upload Gambar
        </button>
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
        <p class="text-sm mt-1">Klik tombol "Upload Gambar" di kanan atas untuk menambahkan.</p>
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
                    <form method="POST"
                        action="{{ route('admin.dataset.hapus-gambar', [$penyakit->id_penyakit, $img['name']]) }}"
                        onsubmit="return confirm('Hapus gambar ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Modal Upload --}}
<div id="modalUpload" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display:none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">
                    <i class="fa-solid fa-upload text-green-600 mr-2"></i>
                    Upload ke <span id="namaKelas"></span>
                </h3>
                <button onclick="document.getElementById('modalUpload').style.display='none'"
                    class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formUpload" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div id="dropZone"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-500 mb-1">Klik atau drag & drop gambar</p>
                    <p id="fileCount" class="text-xs text-blue-500 font-medium"></p>
                    <input type="file" id="fileInput" name="gambar[]" multiple accept="image/*" class="hidden">
                </div>
                <p class="text-xs text-gray-400">Format: JPG, JPEG, PNG. Maks 2MB per file.</p>
                <div id="progressWrap" class="hidden">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Mengupload...</span>
                        <span id="progressText">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="h-2 rounded-full bg-green-500 transition-all" style="width:0%"></div>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('modalUpload').style.display='none'"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" id="btnUpload"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fa-solid fa-upload mr-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
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

<script>
function modalUpload(id, nama) {
    document.getElementById('namaKelas').textContent = nama;
    document.getElementById('formUpload').action = `/admin/dataset/${id}/upload`;
    document.getElementById('fileCount').textContent = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('progressWrap').classList.add('hidden');
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('modalUpload').style.display = 'block';
}

function lihatGambar(path, nama) {
    document.getElementById('previewImg').src = path;
    document.getElementById('previewNama').textContent = nama;
    document.getElementById('modalPreview').style.cssText = 'display:flex!important';
}

// Drop zone
document.getElementById('dropZone').addEventListener('click', () => document.getElementById('fileInput').click());
document.getElementById('fileInput').addEventListener('change', function () {
    document.getElementById('fileCount').textContent = this.files.length > 0 ? `${this.files.length} file dipilih` : '';
});

const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-blue-400', 'bg-blue-50'); });
dz.addEventListener('dragleave', () => dz.classList.remove('border-blue-400', 'bg-blue-50'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-blue-400', 'bg-blue-50');
    const dt = new DataTransfer();
    [...e.dataTransfer.files].forEach(f => dt.items.add(f));
    document.getElementById('fileInput').files = dt.files;
    document.getElementById('fileCount').textContent = `${dt.files.length} file dipilih`;
});

// Upload + progress
document.getElementById('formUpload').addEventListener('submit', function (e) {
    e.preventDefault();
    const fi = document.getElementById('fileInput');
    if (!fi.files.length) { alert('Pilih gambar dulu!'); return; }

    const fd = new FormData(this);
    [...fi.files].forEach(f => fd.append('gambar[]', f));

    document.getElementById('progressWrap').classList.remove('hidden');
    document.getElementById('btnUpload').disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', this.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('progressText').textContent = pct + '%';
        }
    };
    xhr.onload = () => window.location.reload();
    xhr.onerror = () => {
        alert('Upload gagal.');
        document.getElementById('btnUpload').disabled = false;
    };
    xhr.send(fd);
});

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