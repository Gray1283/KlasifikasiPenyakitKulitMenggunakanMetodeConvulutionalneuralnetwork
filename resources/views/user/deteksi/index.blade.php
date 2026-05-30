@extends('layouts.navbar')

@section('title', 'DeteksiAI - Upload Gambar Kulit')
@section('page_title', 'DeteksiAI')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap');
  :root {
    --g600: #146135; --g400: #3eb872; --g100: #d1f0de; --g50: #f0faf4;
    --navy: #0d1f2d; --muted: #5a7080; --cream: #f9f7f2;
  }
  .deteksi-wrap * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

  .deteksi-wrap {
    min-height: calc(100vh - 60px);
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
  }

  .deteksi-inner { width: 100%; max-width: 680px; display: flex; flex-direction: column; gap: 1.2rem; }

  /* ── PAGE TITLE ── */
  .page-heading { text-align: center; margin-bottom: 0.4rem; }
  .page-heading h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 800; color: var(--navy); }
  .page-heading p  { font-size: 0.9rem; color: var(--muted); margin-top: 4px; font-weight: 300; }

  /* ── UPLOAD CARD ── */
  .upload-card {
    background: white;
    border-radius: 20px;
    border: 1px solid rgba(0,0,0,0.07);
    overflow: hidden;
  }

  /* ── DROP ZONE ── */
  .drop-zone {
    margin: 1.8rem 1.8rem 0;
    border: 2px dashed rgba(0,0,0,0.13);
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 280px;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative;
    overflow: hidden;
    padding: 2rem;
  }
  .drop-zone:hover, .drop-zone.drag-over {
    border-color: var(--g400);
    background: var(--g50);
  }
  .drop-zone.has-image { border-style: solid; border-color: var(--g100); padding: 0; }

  /* placeholder */
  .dz-placeholder { display: flex; flex-direction: column; align-items: center; gap: 1rem; pointer-events: none; }
  .dz-icon-wrap {
    width: 72px; height: 72px;
    background: var(--g50);
    border: 1.5px solid var(--g100);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
  }
  .dz-icon-wrap svg { width: 32px; height: 32px; color: var(--g600); }
  .dz-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--navy); }
  .dz-sub   { font-size: 0.82rem; color: var(--muted); }

  /* preview */
  #preview-image {
    display: none;
    width: 100%; height: 100%;
    max-height: 340px;
    object-fit: cover;
    border-radius: 12px;
  }
  #preview-image.visible { display: block; }

  /* file name badge */
  .filename-badge {
    display: none;
    align-items: center;
    gap: 6px;
    background: var(--g50);
    border: 1px solid var(--g100);
    border-radius: 100px;
    padding: 5px 14px;
    font-size: 0.8rem;
    color: var(--g600);
    font-weight: 500;
    margin: 0 1.8rem;
  }
  .filename-badge.visible { display: flex; }
  .filename-badge svg { width: 14px; height: 14px; flex-shrink: 0; }
  .filename-badge .fname { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .btn-remove {
    margin-left: auto;
    background: none; border: none; cursor: pointer;
    color: var(--muted); line-height: 1;
    padding: 0 2px;
    font-size: 1rem;
    transition: color 0.15s;
  }
  .btn-remove:hover { color: #e24b4a; }

  /* ── FOOTER CARD ── */
  .card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.4rem 1.8rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .format-hint { font-size: 0.78rem; color: var(--muted); }
  .format-hint span { font-weight: 500; color: var(--navy); }

  .btn-upload {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--g600); color: white;
    padding: 11px 26px; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 600;
    border: none; cursor: pointer;
    transition: background 0.2s, transform 0.15s, opacity 0.2s;
  }
  .btn-upload:hover:not(:disabled) { background: #0e4726; transform: translateY(-1px); }
  .btn-upload:disabled { opacity: 0.45; cursor: not-allowed; }
  .btn-upload svg { width: 16px; height: 16px; }

  /* loading state */
  .btn-upload.loading .btn-text { display: none; }
  .btn-upload.loading .btn-spinner { display: flex; }
  .btn-spinner { display: none; align-items: center; gap: 8px; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .spinner-ring {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
  }

  /* ── PETUNJUK ── */
  .petunjuk {
    background: white;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 14px;
    padding: 1.2rem 1.5rem;
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }
  .petunjuk-icon {
    width: 34px; height: 34px; flex-shrink: 0;
    background: var(--g50); border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
  }
  .petunjuk-icon svg { width: 17px; height: 17px; color: var(--g600); }
  .petunjuk h3 { font-family: 'Syne', sans-serif; font-size: 0.88rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
  .petunjuk ul { list-style: none; display: flex; flex-direction: column; gap: 4px; }
  .petunjuk li { font-size: 0.82rem; color: var(--muted); display: flex; align-items: center; gap: 7px; }
  .petunjuk li::before { content: ''; width: 5px; height: 5px; background: var(--g400); border-radius: 50%; flex-shrink: 0; }
</style>

<div class="deteksi-wrap">
  <div class="deteksi-inner">

    <div class="page-heading">
      <h1>Deteksi Penyakit Kulit</h1>
      <p>Upload foto area kulit untuk dianalisis oleh model CNN</p>
    </div>

    <div class="upload-card">

      {{-- Drop Zone --}}
      <div id="drop-zone" class="drop-zone" onclick="document.getElementById('fileInput').click()">

        <div id="dz-placeholder" class="dz-placeholder">
          <div class="dz-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="3"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
          </div>
          <div>
            <p class="dz-title">Klik atau seret foto ke sini</p>
            <p class="dz-sub">JPG, PNG, WEBP — maksimal 5 MB</p>
          </div>
        </div>

        <img id="preview-image" alt="Preview gambar kulit">
      </div>

      {{-- Filename badge --}}
      <div id="filename-badge" class="filename-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
        </svg>
        <span class="fname" id="fname-text"></span>
        <button class="btn-remove" onclick="removeImage(event)" title="Hapus">✕</button>
      </div>

      {{-- Card footer --}}
      <div class="card-footer">
        <p class="format-hint">Format: <span>JPG, PNG, WEBP</span> · Maks <span>5 MB</span></p>
        <button id="upload-btn" class="btn-upload" disabled onclick="submitUpload()">
          <span class="btn-text" style="display:flex;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Analisis Sekarang
          </span>
          <span class="btn-spinner">
            <span class="spinner-ring"></span>
            Menganalisis...
          </span>
        </button>
      </div>
    </div>

    {{-- Petunjuk --}}
    <div class="petunjuk">
      <div class="petunjuk-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>
      <div>
        <h3>Petunjuk Upload</h3>
        <ul>
          <li>Gunakan foto yang jelas, terang, dan fokus pada area kulit</li>
          <li>Pastikan area yang bermasalah terlihat dengan baik</li>
          <li>Hindari foto yang buram, gelap, atau terlalu jauh</li>
        </ul>
      </div>
    </div>

  </div>
</div>

<input type="file" id="fileInput" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">

@push('scripts')
<script>
  const dropZone  = document.getElementById('drop-zone');
  const preview   = document.getElementById('preview-image');
  const placeholder = document.getElementById('dz-placeholder');
  const badge     = document.getElementById('filename-badge');
  const fnameText = document.getElementById('fname-text');
  const uploadBtn = document.getElementById('upload-btn');

  function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
      placeholder.style.display = 'none';
      preview.src = e.target.result;
      preview.classList.add('visible');
      dropZone.classList.add('has-image');

      fnameText.textContent = file.name;
      badge.classList.add('visible');

      uploadBtn.disabled = false;
    };
    reader.readAsDataURL(file);
  }

  function removeImage(e) {
    e.stopPropagation();
    document.getElementById('fileInput').value = '';
    preview.src = '';
    preview.classList.remove('visible');
    placeholder.style.display = '';
    dropZone.classList.remove('has-image');
    badge.classList.remove('visible');
    uploadBtn.disabled = true;
  }

  function submitUpload() {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files[0]) return;

    uploadBtn.classList.add('loading');
    uploadBtn.disabled = true;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("deteksi.store") }}';
    form.enctype = 'multipart/form-data';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    // Pindahkan fileInput asli (bukan clone) ke dalam form
    form.appendChild(fileInput);

    document.body.appendChild(form);
    form.submit();
}
  // Drag & drop
  dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
  dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
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