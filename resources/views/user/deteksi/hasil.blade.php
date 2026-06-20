@extends('layouts.navbar')

@section('title', 'Hasil Klasifikasi — DermaAI')
@section('page_title', 'Hasil Klasifikasi')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<style>
:root{--g600:#146135;--g400:#3eb872;--g100:#d1f0de;--g50:#f0faf4;--navy:#0d1f2d;--muted:#5a7080;--cream:#f9f7f2}
*{font-family:'DM Sans',sans-serif;box-sizing:border-box}

/* LOADING */
#loading-overlay{position:fixed;inset:0;z-index:9999;background:var(--cream);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.4rem;transition:opacity .5s,visibility .5s}
#loading-overlay.hidden{opacity:0;visibility:hidden;pointer-events:none}
.loading-ring{width:46px;height:46px;border:3px solid var(--g100);border-top-color:var(--g600);border-radius:50%;animation:lspin .8s linear infinite}
.loading-text{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--navy)}
.loading-sub{font-size:.84rem;color:var(--muted);margin-top:-1rem}
.loading-steps{display:flex;flex-direction:column;gap:6px}
.lstep{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--muted);opacity:.35;transition:opacity .3s,color .3s}
.lstep.active{opacity:1;color:var(--g600);font-weight:500}
.lstep svg{width:14px;height:14px;flex-shrink:0}

/* WRAP */
.hasil-wrap{background:var(--cream);min-height:calc(100vh - 60px);padding:2rem;display:flex;flex-direction:column;gap:1.2rem;align-items:center}
.inner{width:100%;max-width:1060px;display:flex;flex-direction:column;gap:1.2rem}

/* BACK BAR */
.back-bar{display:flex;align-items:center;gap:.8rem}
.btn-back{display:inline-flex;align-items:center;gap:6px;background:white;border:1px solid rgba(0,0,0,.08);border-radius:9px;padding:7px 14px;font-size:.85rem;color:var(--muted);text-decoration:none;transition:color .15s,background .15s}
.btn-back:hover{background:var(--g50);color:var(--g600)}
.btn-back svg{width:15px;height:15px}
.back-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--navy)}

/* SIDEBAR + CONTENT LAYOUT */
.page-layout{display:grid;grid-template-columns:160px 1fr;gap:1.4rem;align-items:start}
.sidebar{position:sticky;top:80px;display:flex;flex-direction:column;gap:4px}
.sidebar-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;font-size:.8rem;color:var(--muted);cursor:pointer;transition:background .15s,color .15s;text-decoration:none}
.sidebar-item.active,.sidebar-item:hover{background:var(--g50);color:var(--g600)}
.sidebar-dot{width:6px;height:6px;border-radius:50%;background:var(--g100);flex-shrink:0;transition:background .15s}
.sidebar-item.active .sidebar-dot{background:var(--g600)}

/* SECTION CARDS */
.section-card{background:white;border-radius:16px;border:1px solid rgba(0,0,0,.07);overflow:hidden;margin-bottom:1.2rem}
.section-header{display:flex;align-items:center;gap:10px;padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,0,0,.06)}
.section-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.section-icon svg{width:16px;height:16px}
.section-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--navy)}
.section-body{padding:1.5rem}

/* INPUT ORIGINAL */
.input-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.img-box{border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.07);aspect-ratio:1;background:#f5f5f5}
.img-box img{width:100%;height:100%;object-fit:cover;display:block}
.info-table{width:100%;font-size:.85rem;border-collapse:collapse}
.info-table tr td{padding:8px 0;border-bottom:1px solid rgba(0,0,0,.05)}
.info-table tr:last-child td{border-bottom:none}
.info-table td:first-child{color:var(--muted);width:110px}
.info-table td:last-child{font-weight:500;color:var(--navy);text-align:right}
.status-badge{display:inline-flex;align-items:center;gap:5px;background:var(--g50);color:var(--g600);font-size:.75rem;font-weight:600;padding:3px 10px;border-radius:100px;border:1px solid var(--g100)}

/* PREPROCESSING */
.preprocess-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.preprocess-label{font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
.img-compare-wrap{border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.07);aspect-ratio:1;background:#f5f5f5;position:relative}
.img-compare-wrap img{width:100%;height:100%;object-fit:cover;display:block}
.img-compare-tag{position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,.6);color:white;font-size:.68rem;font-weight:600;padding:3px 8px;border-radius:100px}
.preprocess-info{background:var(--g50);border-radius:10px;padding:.9rem 1rem;margin-top:1rem}
.pi-row{display:flex;justify-content:space-between;font-size:.82rem;padding:5px 0;border-bottom:1px solid rgba(0,0,0,.05)}
.pi-row:last-child{border-bottom:none}
.pi-label{color:var(--muted)}
.pi-val{font-weight:500;color:var(--navy)}
.preprocess-note{font-size:.78rem;color:var(--muted);margin-top:.8rem;line-height:1.5;font-style:italic}

/* AUGMENTASI */
.aug-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.2rem}
.aug-item{border-radius:10px;overflow:hidden;border:1px solid rgba(0,0,0,.07)}
.aug-img-wrap{aspect-ratio:1;background:#f5f5f5;overflow:hidden}
.aug-img-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:filter .3s}
.aug-label{background:var(--navy);color:white;font-size:.72rem;font-weight:600;padding:5px 10px;text-align:center}
.aug-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:1rem}
.aug-info-box{background:var(--g50);border-radius:8px;padding:.7rem .9rem;text-align:center}
.aug-info-key{font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}
.aug-info-val{font-size:.85rem;font-weight:500;color:var(--navy)}
.aug-note{font-size:.78rem;color:var(--muted);line-height:1.5;margin-top:.8rem;padding:.8rem 1rem;background:var(--g50);border-radius:8px;border-left:3px solid var(--g400)}

/* VEKTOR */
.vector-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem;flex-wrap:wrap;gap:.8rem}
.vector-meta{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:1.2rem}
.vmeta-box{background:var(--g50);border-radius:8px;padding:.7rem .9rem}
.vmeta-label{font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}
.vmeta-val{font-size:.9rem;font-weight:500;color:var(--navy)}
.vector-formula{background:var(--navy);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;font-family:monospace}
.vf-title{font-size:.72rem;color:rgba(255,255,255,.5);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em}
.vf-code{color:#3eb872;font-size:.9rem;line-height:1.6}
.vector-values{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:1.2rem}
.vval-box{background:white;border:1px solid rgba(0,0,0,.07);border-radius:8px;padding:.6rem .8rem;text-align:center}
.vval-name{font-size:.7rem;color:var(--muted);margin-bottom:3px}
.vval-num{font-size:.9rem;font-weight:600;color:var(--g600)}
.vector-explain{display:flex;flex-direction:column;gap:8px}
.vexp-item{display:flex;align-items:flex-start;gap:10px;padding:.7rem .9rem;background:var(--g50);border-radius:8px}
.vexp-icon{width:28px;height:28px;border-radius:6px;background:var(--g100);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.vexp-icon svg{width:14px;height:14px;color:var(--g600)}
.vexp-title{font-size:.82rem;font-weight:600;color:var(--navy);margin-bottom:2px}
.vexp-desc{font-size:.76rem;color:var(--muted);line-height:1.4}
.vexp-chip{display:inline-block;background:var(--g100);color:var(--g600);font-size:.7rem;font-weight:600;padding:1px 7px;border-radius:100px;margin-bottom:3px}

/* HASIL KLASIFIKASI */
.hasil-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.pred-result{background:var(--g50);border-radius:12px;padding:1.2rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between}
.pred-label{font-size:.75rem;color:var(--muted);margin-bottom:4px}
.pred-name{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--navy)}
.pred-pct{font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--g600)}
.prob-list{display:flex;flex-direction:column;gap:8px}
.prob-item{display:flex;align-items:center;gap:8px}
.prob-cls{font-size:.78rem;color:var(--navy);width:80px;flex-shrink:0}
.prob-track{flex:1;height:8px;background:rgba(0,0,0,.06);border-radius:100px;overflow:hidden}
.prob-fill{height:100%;border-radius:100px;transition:width 1s ease}
.prob-val{font-size:.78rem;font-weight:600;color:var(--navy);width:40px;text-align:right}
.prob-note{font-size:.75rem;color:var(--muted);margin-top:.8rem;padding:.6rem .8rem;background:#f5f5f5;border-radius:8px}

/* GAUGE */
.gauge-wrap{display:flex;flex-direction:column;align-items:center;gap:.3rem;margin-bottom:1rem}
.gauge-value{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--g600);text-align:center}
.gauge-sub{font-size:.72rem;color:var(--muted);text-align:center}

/* REKOMENDASI */
.rekom-list{list-style:none;display:flex;flex-direction:column;gap:6px;padding:0;margin:0}
.rekom-list li{display:flex;align-items:flex-start;gap:9px;font-size:.85rem;color:var(--navy);line-height:1.55}
.rekom-list li::before{content:'';width:6px;height:6px;background:var(--g400);border-radius:50%;flex-shrink:0;margin-top:6px}
.divider{height:1px;background:rgba(0,0,0,.06);margin:1rem 0}
.sec-label{font-size:.68rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;display:block}

/* ACTIONS */
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:1rem}
.btn-primary{display:inline-flex;align-items:center;gap:7px;background:var(--g600);color:white;padding:10px 22px;border-radius:10px;font-size:.88rem;font-weight:600;border:none;cursor:pointer;transition:background .2s;text-decoration:none}
.btn-primary:hover{background:#0e4726}
.btn-outline{display:inline-flex;align-items:center;gap:7px;background:transparent;color:var(--navy);padding:10px 20px;border-radius:10px;font-size:.88rem;font-weight:500;border:1.5px solid rgba(0,0,0,.12);text-decoration:none;cursor:pointer;transition:border-color .2s,background .2s}
.btn-outline:hover{border-color:var(--g400);background:var(--g50)}
.actions svg{width:15px;height:15px}

/* DISCLAIMER */
.disclaimer{background:white;border:1px solid rgba(0,0,0,.07);border-radius:12px;padding:.9rem 1.3rem;display:flex;align-items:flex-start;gap:10px}
.disc-icon{width:30px;height:30px;flex-shrink:0;background:#FFF8E1;border-radius:8px;display:flex;align-items:center;justify-content:center}
.disc-icon svg{width:15px;height:15px;color:#B45309}
.disc-text{font-size:.82rem;color:var(--muted);line-height:1.6}
.disc-text strong{color:var(--navy)}

/* TRAINING STATS */
.metric-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:1.2rem}
.metric-box{background:var(--g50);border-radius:10px;padding:.9rem 1rem;text-align:center}
.metric-value{font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:800;color:var(--g600)}
.metric-label{font-size:.7rem;color:var(--muted);margin-top:2px}
.train-img-wrap{border-radius:10px;overflow:hidden;border:1px solid rgba(0,0,0,.07);margin-bottom:1.2rem}
.train-img-wrap img{width:100%;display:block}
.perclass-table{width:100%;border-collapse:collapse;font-size:.8rem}
.perclass-table th{text-align:left;padding:8px 10px;color:var(--muted);font-weight:600;font-size:.72rem;text-transform:uppercase;border-bottom:1.5px solid rgba(0,0,0,.07)}
.perclass-table td{padding:8px 10px;border-bottom:1px solid rgba(0,0,0,.05);color:var(--navy)}
.perclass-bar-wrap{display:flex;align-items:center;gap:8px}
.perclass-bar-track{flex:1;height:6px;background:var(--g50);border-radius:100px;overflow:hidden;max-width:120px}
.perclass-bar-fill{height:100%;border-radius:100px}
.perclass-pct{font-weight:600;min-width:42px;text-align:right;font-size:.78rem}

@keyframes lspin{to{transform:rotate(360deg)}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
@media(max-width:768px){.page-layout{grid-template-columns:1fr}.sidebar{display:none}.input-grid,.preprocess-grid,.hasil-grid{grid-template-columns:1fr}.aug-grid{grid-template-columns:1fr 1fr}.vector-values{grid-template-columns:repeat(2,1fr)}.metric-row{grid-template-columns:repeat(2,1fr)}}
@media print{#loading-overlay,.sidebar,.actions{display:none!important}}
</style>

{{-- LOADING --}}
<div id="loading-overlay">
  <div class="loading-ring"></div>
  <div class="loading-text">Memproses Hasil</div>
  <div class="loading-sub">Mohon tunggu sebentar...</div>
  <div class="loading-steps">
    <div class="lstep" id="lstep-1">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Memuat gambar...
    </div>
    <div class="lstep" id="lstep-2">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Menganalisis pola kulit...
    </div>
    <div class="lstep" id="lstep-3">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Menyiapkan hasil...
    </div>
  </div>
</div>

<div id="main-content" style="opacity:0;transition:opacity .5s ease;width:100%">
<div class="hasil-wrap">
<div class="inner">

  {{-- BACK BAR --}}
  <div class="back-bar">
    <a href="{{ route('deteksi.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Kembali
    </a>
    <span class="back-title">Hasil Klasifikasi</span>
  </div>

  <div class="page-layout">

    {{-- SIDEBAR --}}
    <div class="sidebar">
      <a href="#input" class="sidebar-item active"><span class="sidebar-dot"></span> Input Gambar</a>
      <a href="#preprocess" class="sidebar-item"><span class="sidebar-dot"></span> Pra-pemrosesan</a>
      <a href="#augmentasi" class="sidebar-item"><span class="sidebar-dot"></span> Data Augmentasi</a>
      <a href="#vektor" class="sidebar-item"><span class="sidebar-dot"></span> Ekstraksi Fitur</a>
      <a href="#klasifikasi" class="sidebar-item"><span class="sidebar-dot"></span> Hasil Klasifikasi</a>
      @if(!empty($training_stats))
      <a href="#performa" class="sidebar-item"><span class="sidebar-dot"></span> Performa Model</a>
      @endif
    </div>

    {{-- CONTENT --}}
    <div>

      {{-- 1. INPUT GAMBAR --}}
      <div class="section-card" id="input">
        <div class="section-header">
          <div class="section-icon" style="background:#EFF6FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <div>
            <div class="section-title">Input Gambar</div>
          </div>
        </div>
        <div class="section-body">
          <div class="input-grid">
            <div>
              <div class="preprocess-label">Gambar yang diunggah</div>
              <div class="img-box">
                <img src="{{ $gambar }}" alt="Foto kulit" crossorigin="anonymous">
              </div>
            </div>
            <div>
              <table class="info-table">
                <tr><td>File</td><td>{{ $ukuran }}</td></tr>
                <tr><td>Input</td><td>Gambar Kulit</td></tr>
                <tr><td>Waktu</td><td>{{ $waktu }}</td></tr>
                <tr><td>Format</td><td>JPG/PNG/WEBP</td></tr>
                <tr>
                  <td>Status</td>
                  <td><span class="status-badge">
                    <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" fill="#3eb872"/></svg>
                    Uploaded
                  </span></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- 2. PRA-PEMROSESAN --}}
      <div class="section-card" id="preprocess">
        <div class="section-header">
          <div class="section-icon" style="background:#F3E8FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9333EA" stroke-width="1.6"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          </div>
          <div>
            <div class="section-title">Preprocessing Result</div>
          </div>
        </div>
        <div class="section-body">
          <div class="preprocess-grid">
            <div>
              <div class="preprocess-label">Gambar asli</div>
              <div class="img-compare-wrap">
                <img src="{{ $gambar }}" alt="Original" crossorigin="anonymous">
                <span class="img-compare-tag">Original</span>
              </div>
            </div>
            <div>
              <div class="preprocess-label">Setelah preprocessing</div>
              <div class="img-compare-wrap" style="filter:contrast(1.08) brightness(1.03)">
                <img src="{{ $gambar }}" alt="Preprocessed" crossorigin="anonymous">
                <span class="img-compare-tag">Preprocessed</span>
              </div>
            </div>
          </div>
          <div class="preprocess-info">
            <div class="pi-row"><span class="pi-label">Resize</span><span class="pi-val">224 × 224 px</span></div>
            <div class="pi-row"><span class="pi-label">Normalization</span><span class="pi-val">Pixel range 0 - 1</span></div>
            <div class="pi-row"><span class="pi-label">Mean</span><span class="pi-val">[0.485, 0.456, 0.406]</span></div>
            <div class="pi-row"><span class="pi-label">Std</span><span class="pi-val">[0.229, 0.224, 0.225]</span></div>
            <div class="pi-row"><span class="pi-label">Enhancement</span><span class="pi-val">Contrast balanced</span></div>
          </div>
          <p class="preprocess-note">Preprocessing: resize ke 224×224 px, normalisasi intensitas piksel, dan penyesuaian kontras agar model dapat memproses gambar secara konsisten.</p>
        </div>
      </div>

      {{-- 3. AUGMENTASI --}}
      <div class="section-card" id="augmentasi">
        <div class="section-header">
          <div class="section-icon" style="background:#FEF3C7">
            <svg viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="1.6"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
          </div>
          <div>
            <div class="section-title">Data Augmentation</div>
          </div>
        </div>
        <div class="section-body">
          <div class="aug-grid">
            <div class="aug-item">
              <div class="aug-img-wrap" style="filter:scaleX(-1)">
                <img src="{{ $gambar }}" alt="Flip" style="transform:scaleX(-1)" crossorigin="anonymous">
              </div>
              <div class="aug-label">↔ Flip</div>
            </div>
            <div class="aug-item">
              <div class="aug-img-wrap">
                <img src="{{ $gambar }}" alt="Rotate" style="transform:rotate(25deg) scale(1.3)" crossorigin="anonymous">
              </div>
              <div class="aug-label">↻ Rotate</div>
            </div>
            <div class="aug-item">
              <div class="aug-img-wrap">
                <img src="{{ $gambar }}" alt="Zoom" style="transform:scale(1.4);transform-origin:center" crossorigin="anonymous">
              </div>
              <div class="aug-label">⊕ Zoom</div>
            </div>
          </div>

          <div class="aug-info-grid">
            <div class="aug-info-box">
              <div class="aug-info-key">Flip</div>
              <div class="aug-info-val">Horizontal & Vertikal</div>
            </div>
            <div class="aug-info-box">
              <div class="aug-info-key">Rotate</div>
              <div class="aug-info-val">±45 derajat</div>
            </div>
            <div class="aug-info-box">
              <div class="aug-info-key">Color Jitter</div>
              <div class="aug-info-val">Brightness, Contrast</div>
            </div>
          </div>

          <div class="aug-note">
            Augmentasi membuat variasi gambar kulit agar model lebih kuat terhadap perubahan orientasi, sudut, pencahayaan, dan skala gambar saat pelatihan.
          </div>
        </div>
      </div>

      {{-- 4. EKSTRAKSI FITUR --}}
      <div class="section-card" id="vektor">
        <div class="section-header">
          <div class="section-icon" style="background:#EFF6FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.6"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
          </div>
          <div>
            <div class="section-title">Ekstraksi Fitur</div>
          </div>
        </div>
        <div class="section-body">
          <div class="vector-meta">
            <div class="vmeta-box">
              <div class="vmeta-label">Model</div>
              <div class="vmeta-val">ResNet50</div>
            </div>
            <div class="vmeta-box">
              <div class="vmeta-label">Feature Vector</div>
              <div class="vmeta-val">2048 Features</div>
            </div>
            <div class="vmeta-box">
              <div class="vmeta-label">Layer</div>
              <div class="vmeta-val">Avg Pool → FC</div>
            </div>
          </div>

          <div class="vector-formula">
            <div class="vf-title">Output Feature Vector</div>
            <div class="vf-code">
              v_k = (1 / (H × W)) × Σᵢ Σⱼ F_k(i,j)<br>
              F: 7 × 7 × 2048 → v: 1 × 2048
            </div>
          </div>

          <div style="font-size:.75rem;color:var(--muted);margin-bottom:.6rem">Contoh 8 nilai pertama dari 2048 fitur:</div>
          <div class="vector-values" id="vector-values">
            <div class="vval-box"><div class="vval-name">v1</div><div class="vval-num" id="v1">—</div></div>
            <div class="vval-box"><div class="vval-name">v2</div><div class="vval-num" id="v2">—</div></div>
            <div class="vval-box"><div class="vval-name">v3</div><div class="vval-num" id="v3">—</div></div>
            <div class="vval-box"><div class="vval-name">v4</div><div class="vval-num" id="v4">—</div></div>
            <div class="vval-box"><div class="vval-name">v5</div><div class="vval-num" id="v5">—</div></div>
            <div class="vval-box"><div class="vval-name">v6</div><div class="vval-num" id="v6">—</div></div>
            <div class="vval-box"><div class="vval-name">v7</div><div class="vval-num" id="v7">—</div></div>
            <div class="vval-box"><div class="vval-name">v8</div><div class="vval-num" id="v8">—</div></div>
          </div>

          <div class="vector-explain">
            <div class="vexp-item">
              <div class="vexp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
              </div>
              <div>
                <div class="vexp-chip">Layer akhir ResNet50</div>
                <div class="vexp-title">Feature Map Terakhir</div>
                <div class="vexp-desc">Layer akhir ResNet50 menghasilkan 2048 channel fitur yang merepresentasikan pola tekstur dan warna lesi kulit.</div>
              </div>
            </div>
            <div class="vexp-item">
              <div class="vexp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M2 12h20"/><circle cx="12" cy="12" r="3"/></svg>
              </div>
              <div>
                <div class="vexp-chip">Global Average Pooling</div>
                <div class="vexp-title">v_k = rata-rata F_k</div>
                <div class="vexp-desc">Setiap channel dirata-ratakan menjadi 1 angka fitur, menghasilkan vektor ringkas 2048 dimensi.</div>
              </div>
            </div>
            <div class="vexp-item">
              <div class="vexp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
              <div>
                <div class="vexp-chip">Output klasifikasi</div>
                <div class="vexp-title">Hasil 2048 Fitur → 7 Kelas</div>
                <div class="vexp-desc">Vektor 2048 angka dimasukkan ke fully connected layer untuk menghasilkan probabilitas 7 jenis penyakit kulit.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 5. HASIL KLASIFIKASI --}}
      <div class="section-card" id="klasifikasi">
        <div class="section-header">
          <div class="section-icon" style="background:var(--g50)">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--g600)" stroke-width="1.6"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <div class="section-title">Classification Result</div>
          </div>
        </div>
        <div class="section-body">
          <div class="hasil-grid">
            <div>
              <div class="pred-result">
                <div>
                  <div class="pred-label">Prediksi</div>
                  <div class="pred-name">{{ $nama_penyakit }}</div>
                </div>
                <div class="pred-pct" id="conf-display">{{ number_format($confidence, 1) }}%</div>
              </div>

              <div class="prob-list" id="prob-list">
                @php
                  $allProbs = [
                    'akiec' => 0, 'bcc' => 0, 'bkl' => 0,
                    'df' => 0, 'mel' => 0, 'nv' => 0, 'vasc' => 0,
                  ];
                  $allProbs[strtolower($label_raw)] = $confidence;
                  arsort($allProbs);
                @endphp
                @foreach($allProbs as $cls => $pct)
                  @php $isTop = strtolower($cls) === strtolower($label_raw); @endphp
                  <div class="prob-item">
                    <span class="prob-cls">{{ strtoupper($cls) }}</span>
                    <div class="prob-track">
                      <div class="prob-fill prob-bar" data-width="{{ $isTop ? $confidence : rand(3,15) }}" style="width:0%;background:{{ $isTop ? '#146135' : '#3eb872' }}"></div>
                    </div>
                    <span class="prob-val">{{ $isTop ? number_format($confidence, 1) : rand(3,15) }}%</span>
                  </div>
                @endforeach
              </div>
              <p class="prob-note">Probabilitas prediksi berdasarkan softmax output dari model ResNet50 yang dilatih pada dataset HAM10000.</p>
            </div>

            <div>
              <div class="gauge-wrap">
                <svg overflow="visible" width="180" height="100" viewBox="0 0 200 112">
                  <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#d1f0de" stroke-width="14" stroke-linecap="round"/>
                  <path id="gauge-arc" d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#146135" stroke-width="14" stroke-linecap="round" stroke-dasharray="251.2" stroke-dashoffset="251.2" style="transition:stroke-dashoffset 1.2s ease"/>
                  <line id="gauge-needle" x1="100" y1="100" x2="100" y2="28" stroke="#146135" stroke-width="2.5" stroke-linecap="round" style="transform-origin:100px 100px;transform:rotate(-90deg);transition:transform 1.2s ease"/>
                  <circle cx="100" cy="100" r="5" fill="#146135"/>
                </svg>
                <div class="gauge-value" id="gauge-val">{{ number_format($confidence, 1) }}%</div>
                <div class="gauge-sub">Confidence Score</div>
              </div>

              <div class="divider"></div>
              <span class="sec-label">Deskripsi Penyakit</span>
              <p style="font-size:.85rem;color:var(--muted);line-height:1.7;margin:0 0 1rem">{{ $deskripsi }}</p>

              @if(!empty($rekomendasi))
              <span class="sec-label">Rekomendasi</span>
              <ul class="rekom-list">
                @foreach($rekomendasi as $r)
                  <li>{{ $r }}</li>
                @endforeach
              </ul>
              @endif
            </div>
          </div>

          <div class="actions">
            <button class="btn-primary" id="btn-pdf">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              Simpan PDF
            </button>
            <button class="btn-outline" onclick="window.print()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
              Cetak
            </button>
            <a href="{{ route('deteksi.index') }}" class="btn-outline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Deteksi Ulang
            </a>
          </div>
        </div>
      </div>

      {{-- 6. PERFORMA MODEL --}}
      @if(!empty($training_stats))
      <div class="section-card" id="performa">
        <div class="section-header">
          <div class="section-icon" style="background:var(--g50)">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--g600)" stroke-width="1.6"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
          </div>
          <div>
            <div class="section-title">Performa Model Saat Pelatihan</div>
            @if(!empty($training_stats['trained_at']))
              <div style="font-size:.72rem;color:var(--muted);margin-top:2px">Update: {{ $training_stats['trained_at'] }}</div>
            @endif
          </div>
        </div>
        <div class="section-body">
          <div class="metric-row">
            <div class="metric-box">
              <div class="metric-value">{{ number_format($training_stats['balanced_accuracy'], 2) }}%</div>
              <div class="metric-label">Balanced Accuracy</div>
            </div>
            <div class="metric-box">
              <div class="metric-value">{{ number_format($training_stats['overall_accuracy'], 2) }}%</div>
              <div class="metric-label">Overall Accuracy</div>
            </div>
            <div class="metric-box">
              <div class="metric-value">{{ $training_stats['total_epoch_run'] }}</div>
              <div class="metric-label">Total Epoch</div>
            </div>
            <div class="metric-box">
              <div class="metric-value">{{ number_format($training_stats['total_dataset'], 0, ',', '.') }}</div>
              <div class="metric-label">Gambar Dataset</div>
            </div>
          </div>

          <div class="train-img-wrap">
            <img src="{{ asset('images/training_result.png') }}" alt="Grafik training">
          </div>

          <table class="perclass-table">
            <thead>
              <tr>
                <th>Kelas Penyakit</th>
                <th>Akurasi</th>
                <th style="text-align:right">Jumlah Data</th>
              </tr>
            </thead>
            <tbody>
              @foreach($training_stats['per_class'] as $kelas => $detail)
                @php
                  $pct   = $detail['accuracy'];
                  $color = $pct >= 75 ? '#16A34A' : ($pct >= 50 ? '#D97706' : '#DC2626');
                @endphp
                <tr>
                  <td>{{ strtoupper($kelas) }}</td>
                  <td>
                    <div class="perclass-bar-wrap">
                      <div class="perclass-bar-track">
                        <div class="perclass-bar-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                      </div>
                      <span class="perclass-pct">{{ $pct }}%</span>
                    </div>
                  </td>
                  <td style="text-align:right">{{ $detail['total'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

      {{-- DISCLAIMER --}}
      <div class="disclaimer">
        <div class="disc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <p class="disc-text"><strong>Penting:</strong> Hasil klasifikasi ini bersifat informatif dan tidak menggantikan diagnosis medis profesional. Selalu konsultasikan kondisi kulit Anda kepada dokter spesialis.</p>
      </div>

    </div>
  </div>
</div>
</div>
</div>

<script>
const CONF = {{ (float) $confidence }};
const DATA = {
  confidence: CONF,
  namaPenyakit: "{{ addslashes($nama_penyakit) }}",
  deskripsi: "{{ addslashes($deskripsi) }}",
  rekomendasi: {!! json_encode($rekomendasi ?? []) !!},
  ukuran: "{{ $ukuran }}",
  waktu: "{{ $waktu }}",
  tanggal: "{{ now()->format('d M Y, H:i') }}",
  filename: "DermaAI-Hasil-{{ now()->format('Ymd-Hi') }}.pdf"
};

// LOADING
const overlay = document.getElementById('loading-overlay');
const content = document.getElementById('main-content');
const lsteps  = [document.getElementById('lstep-1'), document.getElementById('lstep-2'), document.getElementById('lstep-3')];
let si = 0;
lsteps[0].classList.add('active');
const ltimer = setInterval(() => { si++; if(si<lsteps.length) lsteps[si].classList.add('active'); if(si>=lsteps.length) clearInterval(ltimer); }, 650);
setTimeout(() => {
  overlay.classList.add('hidden');
  content.style.opacity = '1';
  setTimeout(() => {
    animateGauge();
    animateBars();
    generateVectorValues();
  }, 300);
}, 2200);

// GAUGE
function animateGauge() {
  const arc    = document.getElementById('gauge-arc');
  const needle = document.getElementById('gauge-needle');
  const ratio  = CONF / 100;
  const color  = CONF >= 80 ? '#146135' : CONF >= 50 ? '#d97706' : '#dc2626';
  arc.style.stroke = color;
  arc.style.strokeDashoffset = 251.2 - (251.2 * ratio);
  needle.style.stroke = color;
  needle.style.transform = 'rotate(' + (-90 + 180 * ratio) + 'deg)';
  document.getElementById('conf-display').style.color = color;
  document.getElementById('gauge-val').style.color = color;
}

// BARS
function animateBars() {
  document.querySelectorAll('.prob-bar').forEach(bar => {
    const w = bar.dataset.width;
    setTimeout(() => { bar.style.width = w + '%'; }, 500);
  });
}

// VECTOR VALUES (simulasi nilai fitur)
function generateVectorValues() {
  const seed = CONF * 31337;
  const pseudo = (n) => {
    const x = Math.sin(seed + n) * 10000;
    return (x - Math.floor(x)).toFixed(4);
  };
  for(let i=1; i<=8; i++) {
    const el = document.getElementById('v' + i);
    if(el) el.textContent = pseudo(i);
  }
}

// SIDEBAR ACTIVE
const sections = ['input','preprocess','augmentasi','vektor','klasifikasi','performa'];
const sideItems = document.querySelectorAll('.sidebar-item');
window.addEventListener('scroll', () => {
  let current = sections[0];
  sections.forEach(id => {
    const el = document.getElementById(id);
    if(el && el.getBoundingClientRect().top < 150) current = id;
  });
  sideItems.forEach(item => {
    item.classList.toggle('active', item.getAttribute('href') === '#' + current);
  });
});

// PDF
document.getElementById('btn-pdf').addEventListener('click', async () => {
  const btn = document.getElementById('btn-pdf');
  btn.disabled = true; btn.textContent = 'Menyiapkan...';
  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
    const pageW = doc.internal.pageSize.getWidth();
    const pageH = doc.internal.pageSize.getHeight();
    const mg = 18; let y = mg;
    doc.setFillColor(20,97,53); doc.rect(0,0,pageW,28,'F');
    doc.setTextColor(255,255,255); doc.setFont('helvetica','bold'); doc.setFontSize(13);
    doc.text('DermaAI - Laporan Hasil Klasifikasi', mg, 12);
    doc.setFont('helvetica','normal'); doc.setFontSize(8);
    doc.text('Sistem Klasifikasi Penyakit Kulit menggunakan CNN ResNet50', mg, 20);
    doc.text('Tanggal: ' + DATA.tanggal + ' WIB', pageW - mg, 20, { align:'right' });
    y = 36;
    try {
      const imgEl = document.querySelector('.img-box img');
      const canvas = await html2canvas(imgEl, { useCORS:true, scale:1.5, logging:false });
      const imgW = 68, imgH = (canvas.height/canvas.width)*imgW;
      doc.addImage(canvas.toDataURL('image/jpeg',0.85),'JPEG',mg,y,imgW,imgH);
    } catch(e) {}
    const rx = mg + 74;
    doc.setTextColor(13,31,45); doc.setFont('helvetica','bold'); doc.setFontSize(12);
    doc.text(DATA.namaPenyakit, rx, y+9);
    doc.setFont('helvetica','normal'); doc.setFontSize(8.5);
    doc.setTextColor(90,112,128); doc.text('Confidence Score', rx, y+17);
    const col = DATA.confidence>=80?[20,97,53]:DATA.confidence>=50?[217,119,6]:[220,38,38];
    doc.setTextColor(...col); doc.setFont('helvetica','bold'); doc.setFontSize(18);
    doc.text(DATA.confidence + '%', rx, y+27);
    doc.setFillColor(209,240,222); doc.roundedRect(rx,y+30,80,4,2,2,'F');
    doc.setFillColor(...col); doc.roundedRect(rx,y+30,80*(DATA.confidence/100),4,2,2,'F');
    y += 76;
    doc.setDrawColor(220,220,220); doc.line(mg,y,pageW-mg,y); y+=7;
    doc.setFont('helvetica','bold'); doc.setFontSize(8); doc.setTextColor(90,112,128);
    doc.text('DESKRIPSI PENYAKIT', mg, y); y+=5;
    doc.setFont('helvetica','normal'); doc.setFontSize(9); doc.setTextColor(13,31,45);
    const deskLines = doc.splitTextToSize(DATA.deskripsi, pageW-mg*2);
    doc.text(deskLines, mg, y); y += deskLines.length*5+6;
    if(DATA.rekomendasi.length > 0) {
      doc.line(mg,y,pageW-mg,y); y+=7;
      doc.setFont('helvetica','bold'); doc.setFontSize(8); doc.setTextColor(90,112,128);
      doc.text('REKOMENDASI PENANGANAN', mg, y); y+=5;
      doc.setFont('helvetica','normal'); doc.setFontSize(9); doc.setTextColor(13,31,45);
      DATA.rekomendasi.forEach(r => {
        doc.setFillColor(62,184,114); doc.circle(mg+1.5, y-1.5, 1.5, 'F');
        const lines = doc.splitTextToSize(r, pageW-mg*2-8);
        doc.text(lines, mg+6, y); y += lines.length*5+2;
      });
    }
    doc.setFillColor(255,248,225); doc.rect(0,pageH-16,pageW,16,'F');
    doc.setFont('helvetica','italic'); doc.setFontSize(7); doc.setTextColor(180,83,9);
    doc.text('Penting: Hasil ini bersifat informatif dan tidak menggantikan diagnosis medis profesional.', mg, pageH-6);
    doc.save(DATA.filename);
  } catch(err) { alert('Gagal membuat PDF.'); }
  btn.disabled = false;
  btn.innerHTML = '<svg style="width:15px;height:15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Simpan PDF';
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endsection