@extends('layouts.navbar')

@section('title', 'DeteksiAI - Upload Gambar Kulit')
@section('page_title', 'Upload Gambar Kulit')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto">

        <!-- Upload Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">

            <!-- Upload Area -->
            <div class="p-8">
                <div
                    id="drop-zone"
                    class="border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center py-16 px-6 cursor-pointer hover:border-[#146135] hover:bg-green-50 transition-all"
                    onclick="document.getElementById('fileInput').click()"
                >
                    <!-- Image Preview or Placeholder -->
                    <div id="preview-container" class="flex flex-col items-center gap-4">
                        <!-- Placeholder icon -->
                        <div id="placeholder-icon" class="flex flex-col items-center gap-3">
                            <div class="relative">
                                <!-- Img... text with sparkle icon -->
                                <div class="flex items-center gap-1 text-purple-400 text-sm font-medium mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l1.5 4.5L18 8l-4.5 1.5L12 14l-1.5-4.5L6 8l4.5-1.5z"/>
                                    </svg>
                                    <span>Img...</span>
                                </div>
                                <!-- Image placeholder frame -->
                                <div class="w-20 h-16 border-2 border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Preview image (hidden by default) -->
                        <img
                            id="preview-image"
                            class="hidden max-h-64 max-w-full rounded-xl object-contain"
                            alt="Preview gambar kulit"
                        />
                    </div>

                    <p id="drop-text" class="text-gray-400 text-sm mt-4">Klik atau seret gambar ke sini</p>
                </div>

                <!-- Hidden file input -->
                <input
                    type="file"
                    id="fileInput"
                    name="image"
                    accept="image/*"
                    class="hidden"
                    onchange="previewImage(event)"
                />
            </div>

            <!-- Upload Button -->
            <div class="px-8 pb-8 flex justify-center">
                <button
                    type="button"
                    id="upload-btn"
                    onclick="submitUpload()"
                    class="flex items-center gap-3 bg-[#3B5BDB] hover:bg-[#2f4cbf] text-white px-10 py-3 rounded-lg font-semibold text-base transition-colors shadow"
                >
                    <span>Upload Gambar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Info / Petunjuk Section -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-700 mb-1">Petunjuk Upload</p>
                    <ul class="text-sm text-blue-600 space-y-1 list-disc list-inside">
                        <li>Gunakan gambar kulit yang jelas dan terang</li>
                        <li>Format yang didukung: JPG, PNG, WEBP</li>
                        <li>Ukuran maksimal file: 5 MB</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const placeholder = document.getElementById('placeholder-icon');
            const preview = document.getElementById('preview-image');
            const dropText = document.getElementById('drop-text');

            placeholder.classList.add('hidden');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            dropText.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    function submitUpload() {
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.files[0]) {
            alert('Silakan pilih gambar terlebih dahulu.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("deteksi.store") }}';
        form.enctype = 'multipart/form-data';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const clonedFile = fileInput.cloneNode(true);
        form.appendChild(clonedFile);

        document.body.appendChild(form);
        form.submit();
    }

    // Drag & drop support
    const dropZone = document.getElementById('drop-zone');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-[#146135]', 'bg-green-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-[#146135]', 'bg-green-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-[#146135]', 'bg-green-50');

        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('fileInput').files = dt.files;
            previewImage({ target: document.getElementById('fileInput') });
        }
    });
</script>
@endpush

@endsection