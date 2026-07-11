@extends('layouts.navbar')

@section('title', 'DeteksiAI - Upload Gambar Kulit')
@section('page_title', 'DeteksiAI')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  :root {
    --green: #16a34a;
    --green-dark: #15803d;
    --green-light: #dcfce7;
    --green-mid: #bbf7d0;
    --navy: #0f172a;
    --slate: #475569;
    --muted: #94a3b8;
    --surface: #f8fafc;
    --white: #ffffff;
    --border: rgba(0,0,0,.08);
    --border-hover: rgba(0,0,0,.15);
    --radius-sm: 10px;
    --radius-md: 14px;
    --radius-lg: 20px;
    --radius-xl: 24px;
  }

  .d-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

  .d-wrap {
    min-height: calc(100vh - 60px);
    background: var(--surface);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 3rem 1.25rem 4rem;
  }

  .d-inner {
    width: 100%;
    max-width: 620px;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  /* ── Header ── */
  .d-header { text-align: center; padding-bottom: .25rem; }

  .d-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--green-light);
    color: var(--green-dark);
    border-radius: 100px;
    padding: 5px 14px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .3px;
    margin-bottom: 12px;
  }

  .d-badge svg { width: 13px; height: 13px; }

  .d-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--navy);
    letter-spacing: -.5px;
    margin-bottom: 6px;
  }

  .d-subtitle {
    font-size: 14px;
    color: var(--slate);
    font-weight: 400;
    line-height: 1.5;
  }

  /* ── Error ── */
  .d-error {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    padding: .85rem 1.1rem;
    font-size: 13.5px;
    color: #b91c1c;
  }

  .d-error svg { width: 16px; height: 16px; flex-shrink: 0; }

  .d-error--skin {
    align-items: flex-start;
    gap: 12px;
    padding: 1rem 1.25rem;
  }

  .d-error__icon {
    width: 36px;
    height: 36px;
    background: #fee2e2;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .d-error__icon svg { width: 18px; height: 18px; color: #b91c1c; }

  .d-error__body { flex: 1; }

  .d-error__title {
    font-size: 14px;
    font-weight: 700;
    color: #7f1d1d;
    margin-bottom: 2px;
  }

  .d-error__desc {
    font-size: 12.5px;
    color: #b91c1c;
    font-weight: 400;
  }

  .d-error__retry {
    flex-shrink: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #b91c1c;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 7px 16px;
    cursor: pointer;
    transition: opacity .15s;
    white-space: nowrap;
  }

  .d-error__retry:hover { opacity: .75; }

  /* ── Card ── */
  .d-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
  }

  /* ── Drop Zone ── */
  .d-dropzone {
    margin: 1.5rem;
    border: 2px dashed var(--border-hover);
    border-radius: var(--radius-lg);
    min-height: 270px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
    overflow: hidden;
    padding: 2.5rem 1.5rem;
    gap: 1.1rem;
  }

  .d-dropzone:hover,
  .d-dropzone.drag-over {
    border-color: var(--green);
    background: #f0fdf4;
  }

  .d-dropzone.has-image {
    border-style: solid;
    border-color: var(--green-mid);
    padding: 0;
    min-height: 260px;
  }

  .dz-icon-wrap {
    width: 64px;
    height: 64px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .dz-icon-wrap svg { width: 28px; height: 28px; color: var(--slate); }

  .dz-copy { text-align: center; }

  .dz-copy-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 4px;
  }

  .dz-copy-sub {
    font-size: 13px;
    color: var(--muted);
  }

  .dz-browse {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--green-dark);
    background: var(--green-light);
    border: none;
    border-radius: var(--radius-sm);
    padding: 8px 18px;
    cursor: pointer;
    transition: opacity .15s;
  }

  .dz-browse:hover { opacity: .8; }
  .dz-browse svg { width: 14px; height: 14px; }

  .dz-formats {
    font-size: 11.5px;
    color: var(--muted);
    letter-spacing: .2px;
  }

  #d-preview {
    display: none;
    width: 100%;
    height: 270px;
    object-fit: cover;
  }

  #d-preview.visible { display: block; }

  /* ── File Badge ── */
  .d-filebadge {
    display: none;
    align-items: center;
    gap: 10px;
    margin: 0 1.5rem .75rem;
    padding: .7rem 1rem;
    background: #f0fdf4;
    border: 1px solid var(--green-mid);
    border-radius: var(--radius-sm);
  }

  .d-filebadge.visible { display: flex; }

  .fb-icon {
    width: 30px;
    height: 30px;
    background: var(--green-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .fb-icon svg { width: 15px; height: 15px; color: var(--green-dark); }

  .fb-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--navy);
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .fb-size {
    font-size: 12px;
    color: var(--slate);
    flex-shrink: 0;
  }

  .fb-remove {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    padding: 4px 6px;
    border-radius: 6px;
    font-size: 15px;
    display: flex;
    align-items: center;
    transition: color .15s, background .15s;
  }

  .fb-remove:hover { color: #dc2626; background: #fee2e2; }

  /* ── Footer ── */
  .d-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border);
    gap: 1rem;
    flex-wrap: wrap;
  }

  .d-fmt {
    font-size: 12px;
    color: var(--muted);
  }

  .d-fmt strong { font-weight: 600; color: var(--slate); }

  .d-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--navy);
    color: var(--white);
    padding: 11px 24px;
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: background .2s, transform .15s, opacity .2s;
    white-space: nowrap;
  }

  .d-btn:hover:not(:disabled) { background: #1e293b; transform: translateY(-1px); }
  .d-btn:disabled { opacity: .35; cursor: not-allowed; transform: none; }
  .d-btn svg { width: 15px; height: 15px; }

  .d-btn .btn-loading { display: none; align-items: center; gap: 8px; }
  .d-btn .btn-default { display: flex; align-items: center; gap: 8px; }
  .d-btn.loading .btn-default { display: none; }
  .d-btn.loading .btn-loading { display: flex; }

  @keyframes d-spin { to { transform: rotate(360deg); } }

  .d-spinner {
    width: 15px;
    height: 15px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: white;
    border-radius: 50%;
    animation: d-spin .65s linear infinite;
  }

  /* ── Tips ── */
  .d-tips {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
  }

  .d-tip {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.1rem 1rem;
  }

  .tip-dot {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
  }

  .tip-dot svg { width: 14px; height: 14px; }
  .tip-dot.green { background: var(--green-light); color: var(--green-dark); }
  .tip-dot.blue { background: #dbeafe; color: #1d4ed8; }
  .tip-dot.amber { background: #fef3c7; color: #b45309; }

  .tip-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 4px;
  }

  .tip-desc {
    font-size: 11.5px;
    color: var(--muted);
    line-height: 1.55;
  }

  /* ── Responsive ── */
  @media (max-width: 480px) {
    .d-tips { grid-template-columns: 1fr; }
    .d-title { font-size: 22px; }
    .d-dropzone { min-height: 220px; }
  }
</style>

<div class="d-wrap">
  <div class="d-inner">

    {{-- Header --}}
    <div class="d-header">
      <div class="d-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2a5 5 0 0 1 5 5c0 2.76-5 8-5 8S7 9.76 7 7a5 5 0 0 1 5-5z"/>
          <circle cx="12" cy="7" r="2"/>
        </svg>
        AI-Powered CNN
      </div>
      <h1 class="d-title">Deteksi penyakit kulit</h1>
      <p class="d-subtitle">Upload foto area kulit untuk dianalisis oleh model CNN secara otomatis</p>
    </div>

    {{-- Validation / ML error --}}
    @if($errors->any())
    @php
      $rawError   = $errors->first();
      $isSkinErr  = str_contains($rawError, 'tidak terdeteksi sebagai gambar kulit')
                 || str_contains($rawError, 'is_skin');
    @endphp
    @if($isSkinErr)
    <div class="d-error d-error--skin">
      <div class="d-error__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>
      <div class="d-error__body">
        <p class="d-error__title">Gambar tidak terdeteksi sebagai kulit</p>
        <p class="d-error__desc">Pastikan foto menampilkan area kulit dengan jelas, lalu coba unggah ulang.</p>
      </div>
      <button type="button" class="d-error__retry" onclick="document.getElementById('d-file').click()">
        Coba ulang
      </button>
    </div>
    @else
    <div class="d-error">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      {{ $rawError }}
    </div>
    @endif
    @endif

    {{-- Form --}}
    <form id="d-form" method="POST" action="{{ route('deteksi.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="d-card">

        {{-- Drop Zone --}}
        <div id="d-dropzone" class="d-dropzone" onclick="document.getElementById('d-file').click()" role="button" tabindex="0" aria-label="Klik atau seret foto ke sini">

          <div class="dz-icon-wrap" id="dz-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
          </div>

          <div class="dz-copy" id="dz-copy">
            <p class="dz-copy-title">Seret & lepas foto di sini</p>
            <p class="dz-copy-sub">atau</p>
          </div>

          <button type="button" class="dz-browse" id="dz-browse" onclick="event.stopPropagation(); document.getElementById('d-file').click()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
            Pilih dari perangkat
          </button>

          <p class="dz-formats" id="dz-fmt">JPG &middot; PNG &middot; WEBP &middot; maks 5 MB</p>

          <img id="d-preview" alt="Preview gambar kulit yang akan dianalisis">
        </div>

        {{-- Hidden input --}}
        <input type="file" id="d-file" name="image" accept="image/*" class="hidden" onchange="dHandleFile(this.files[0])">

        {{-- File info badge --}}
        <div id="d-filebadge" class="d-filebadge">
          <div class="fb-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <span class="fb-name" id="fb-name">—</span>
          <span class="fb-size" id="fb-size"></span>
          <button type="button" class="fb-remove" onclick="dRemoveFile()" title="Hapus file">✕</button>
        </div>

        {{-- Footer --}}
        <div class="d-footer">
          <p class="d-fmt">Format: <strong>JPG, PNG, WEBP</strong> &middot; Maks <strong>5 MB</strong></p>
          <button type="button" id="d-btn" class="d-btn" disabled onclick="dSubmit()">
            <span class="btn-default">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              Analisis sekarang
            </span>
            <span class="btn-loading">
              <span class="d-spinner"></span>
              Menganalisis...
            </span>
          </button>
        </div>

      </div>
    </form>

    {{-- Tips --}}
    <div class="d-tips">
      <div class="d-tip">
        <div class="tip-dot green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
          </svg>
        </div>
        <p class="tip-title">Pencahayaan baik</p>
        <p class="tip-desc">Pastikan area kulit terlihat jelas dalam kondisi cahaya yang cukup</p>
      </div>
      <div class="d-tip">
        <div class="tip-dot blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>
        <p class="tip-title">Fokus dan tajam</p>
        <p class="tip-desc">Hindari foto buram — dekatkan kamera ke area yang bermasalah</p>
      </div>
      <div class="d-tip">
        <div class="tip-dot amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
        <p class="tip-title">Area terlihat jelas</p>
        <p class="tip-desc">Pastikan seluruh area kulit yang ingin dideteksi masuk dalam frame</p>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
(function () {
  const dropzone   = document.getElementById('d-dropzone');
  const preview    = document.getElementById('d-preview');
  const dzIcon     = document.getElementById('dz-icon');
  const dzCopy     = document.getElementById('dz-copy');
  const dzBrowse   = document.getElementById('dz-browse');
  const dzFmt      = document.getElementById('dz-fmt');
  const filebadge  = document.getElementById('d-filebadge');
  const fbName     = document.getElementById('fb-name');
  const fbSize     = document.getElementById('fb-size');
  const btn        = document.getElementById('d-btn');

  function fmtBytes(b) {
    return b < 1024 * 1024
      ? (b / 1024).toFixed(0) + ' KB'
      : (b / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function showPlaceholder(show) {
    const v = show ? '' : 'none';
    dzIcon.style.display   = v;
    dzCopy.style.display   = v;
    dzBrowse.style.display = v;
    dzFmt.style.display    = v;
  }

  window.dHandleFile = function (file) {
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      alert('File harus berupa gambar (JPG, PNG, atau WEBP).');
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file melebihi batas 5 MB. Silakan pilih file yang lebih kecil.');
      return;
    }
    const reader = new FileReader();
    reader.onload = function (e) {
      showPlaceholder(false);
      preview.src = e.target.result;
      preview.classList.add('visible');
      dropzone.classList.add('has-image');
      fbName.textContent = file.name;
      fbSize.textContent = fmtBytes(file.size);
      filebadge.classList.add('visible');
      btn.disabled = false;
    };
    reader.readAsDataURL(file);
  };

  window.dRemoveFile = function () {
    document.getElementById('d-file').value = '';
    preview.src = '';
    preview.classList.remove('visible');
    showPlaceholder(true);
    dropzone.classList.remove('has-image');
    filebadge.classList.remove('visible');
    btn.disabled = true;
  };

  window.dSubmit = function () {
    if (!document.getElementById('d-file').files[0]) return;
    btn.classList.add('loading');
    btn.disabled = true;
    document.getElementById('d-form').submit();
  };

  // Drag & drop
  dropzone.addEventListener('dragover', function (e) {
    e.preventDefault();
    dropzone.classList.add('drag-over');
  });
  dropzone.addEventListener('dragleave', function () {
    dropzone.classList.remove('drag-over');
  });
  dropzone.addEventListener('drop', function (e) {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
      const dt = new DataTransfer();
      dt.items.add(file);
      document.getElementById('d-file').files = dt.files;
      window.dHandleFile(file);
    }
  });

  // Keyboard accessibility
  dropzone.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      document.getElementById('d-file').click();
    }
  });
})();
</script>
@endpush

@endsection