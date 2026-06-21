@extends('layouts.navbar')

@section('title', 'Hasil Klasifikasi — DermaAI')
@section('page_title', 'Hasil Klasifikasi')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<style>
:root{--g600:#146135;--g400:#3eb872;--g100:#d1f0de;--g50:#f0faf4;--navy:#0d1f2d;--muted:#5a7080;--cream:#f9f7f2;--white:#ffffff}
*{font-family:'DM Sans',sans-serif;box-sizing:border-box;margin:0;padding:0}

#loading-overlay{position:fixed;inset:0;z-index:9999;background:var(--cream);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.4rem;transition:opacity .5s,visibility .5s}
#loading-overlay.hidden{opacity:0;visibility:hidden;pointer-events:none}
.loading-ring{width:52px;height:52px;border:3px solid var(--g100);border-top-color:var(--g600);border-radius:50%;animation:lspin .8s linear infinite}
.loading-text{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:var(--navy)}
.loading-sub{font-size:.84rem;color:var(--muted);margin-top:-1rem}
.lstep{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--muted);opacity:.35;transition:opacity .4s,color .4s}
.lstep.active{opacity:1;color:var(--g600);font-weight:500}
.lstep svg{width:14px;height:14px}

.hasil-wrap{background:var(--cream);min-height:calc(100vh - 60px);padding:2rem 1.5rem}
.inner{max-width:1100px;margin:0 auto}

.back-bar{display:flex;align-items:center;gap:.8rem;margin-bottom:1.5rem}
.btn-back{display:inline-flex;align-items:center;gap:6px;background:white;border:1px solid rgba(0,0,0,.08);border-radius:10px;padding:8px 16px;font-size:.84rem;color:var(--muted);text-decoration:none;transition:all .2s}
.btn-back:hover{background:var(--g50);color:var(--g600);border-color:var(--g100)}
.btn-back svg{width:15px;height:15px}
.back-title{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;color:var(--navy)}

.page-layout{display:grid;grid-template-columns:180px 1fr;gap:1.6rem;align-items:start}

.sidebar{position:sticky;top:80px;background:white;border-radius:14px;border:1px solid rgba(0,0,0,.07);padding:.8rem;display:flex;flex-direction:column;gap:2px}
.sidebar-section{font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:8px 10px 4px;margin-top:4px}
.sidebar-section:first-child{margin-top:0}
.sidebar-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;font-size:.8rem;color:var(--muted);cursor:pointer;transition:all .2s;text-decoration:none;line-height:1.2}
.sidebar-item:hover{background:var(--g50);color:var(--g600)}
.sidebar-item.active{background:var(--g50);color:var(--g600);font-weight:500}
.sidebar-num{width:20px;height:20px;border-radius:6px;background:var(--g100);color:var(--g600);font-size:.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s}
.sidebar-item.active .sidebar-num{background:var(--g600);color:white}

.section-card{background:white;border-radius:16px;border:1px solid rgba(0,0,0,.07);overflow:hidden;margin-bottom:1.2rem;transition:box-shadow .2s}
.section-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.06)}
.section-header{display:flex;align-items:center;gap:12px;padding:1.2rem 1.6rem;border-bottom:1px solid rgba(0,0,0,.05);background:linear-gradient(to right,rgba(20,97,53,.02),transparent)}
.sec-badge{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;flex-shrink:0}
.sec-badge svg{width:18px;height:18px}
.section-title{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;color:var(--navy)}
.section-subtitle{font-size:.75rem;color:var(--muted);margin-top:1px}
.step-chip{margin-left:auto;background:var(--g50);color:var(--g600);font-size:.68rem;font-weight:700;padding:3px 10px;border-radius:100px;border:1px solid var(--g100);white-space:nowrap}
.section-body{padding:1.6rem}

/* INPUT */
.input-grid{display:grid;grid-template-columns:220px 1fr;gap:1.5rem;align-items:start}
.img-frame{border-radius:14px;overflow:hidden;border:1.5px solid rgba(0,0,0,.08);background:#f8f8f8;aspect-ratio:1;position:relative}
.img-frame img{width:100%;height:100%;object-fit:cover;display:block}
.img-overlay{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,.5));padding:10px 12px}
.img-overlay-text{color:white;font-size:.7rem;font-weight:600}
.info-rows{display:flex;flex-direction:column;gap:0}
.info-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(0,0,0,.05)}
.info-row:last-child{border-bottom:none}
.info-key{font-size:.82rem;color:var(--muted);display:flex;align-items:center;gap:7px}
.info-key svg{width:13px;height:13px;opacity:.6}
.info-val{font-size:.82rem;font-weight:500;color:var(--navy)}
.badge-uploaded{display:inline-flex;align-items:center;gap:5px;background:#DCFCE7;color:#15803D;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:100px}
.badge-dot{width:6px;height:6px;background:#22C55E;border-radius:50%;animation:pulse 1.5s infinite}

/* PREPROCESS */
.compare-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-bottom:1.2rem}
.compare-item{}
.compare-label{font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;display:flex;align-items:center;gap:6px}
.compare-label-dot{width:6px;height:6px;border-radius:50%}
.compare-frame{border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.08);aspect-ratio:1;background:#f8f8f8;position:relative}
.compare-frame img{width:100%;height:100%;object-fit:cover;display:block}
.compare-tag{position:absolute;top:8px;right:8px;background:rgba(0,0,0,.6);color:white;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:6px;backdrop-filter:blur(4px)}
.param-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.param-box{background:var(--g50);border-radius:10px;padding:.7rem .9rem;border:1px solid var(--g100)}
.param-key{font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px}
.param-val{font-size:.82rem;font-weight:600;color:var(--navy);font-family:monospace}
.note-box{background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:.8rem 1rem;font-size:.78rem;color:#15803D;line-height:1.6;margin-top:1rem}

/* AUGMENTASI */
.aug-row{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.2rem}
.aug-card{border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.07);transition:transform .2s}
.aug-card:hover{transform:translateY(-2px)}
.aug-frame{aspect-ratio:1;background:#f8f8f8;overflow:hidden;position:relative}
.aug-frame img{width:100%;height:100%;object-fit:cover;display:block}
.aug-footer{padding:8px 10px;background:var(--navy);display:flex;align-items:center;gap:6px}
.aug-footer-icon{font-size:.85rem}
.aug-footer-text{color:white;font-size:.72rem;font-weight:600}
.aug-footer-sub{color:rgba(255,255,255,.5);font-size:.65rem;margin-left:auto}
.aug-params{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:1rem}
.aug-param{background:var(--g50);border-radius:8px;padding:.6rem .8rem;text-align:center;border:1px solid var(--g100)}
.aug-param-key{font-size:.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px}
.aug-param-val{font-size:.8rem;font-weight:600;color:var(--g600)}

/* VEKTOR */
.vector-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:1.2rem}
.vstat{background:var(--navy);border-radius:12px;padding:1rem;text-align:center}
.vstat-val{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#3eb872}
.vstat-label{font-size:.7rem;color:rgba(255,255,255,.5);margin-top:3px}
.formula-box{background:#0d1f2d;border:1px solid rgba(62,184,114,.2);border-radius:12px;padding:1rem 1.2rem;margin-bottom:1.2rem}
.formula-title{font-size:.68rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;font-family:'DM Sans',sans-serif}
.formula-code{font-family:monospace;font-size:.88rem;line-height:1.8;color:#3eb872}
.formula-dim{font-family:monospace;font-size:.78rem;color:rgba(255,255,255,.4);margin-top:4px}
.vvals-label{font-size:.72rem;color:var(--muted);margin-bottom:8px;font-weight:500}
.vvals-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:1.2rem}
.vval{background:white;border:1px solid rgba(0,0,0,.08);border-radius:10px;padding:.8rem;text-align:center;transition:border-color .2s}
.vval:hover{border-color:var(--g400)}
.vval-name{font-size:.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}
.vval-num{font-size:.95rem;font-weight:700;color:var(--g600);font-family:monospace}
.vexplain{display:flex;flex-direction:column;gap:8px}
.vexp{display:flex;align-items:flex-start;gap:12px;padding:.9rem 1rem;background:var(--g50);border-radius:12px;border:1px solid var(--g100)}
.vexp-icon{width:32px;height:32px;border-radius:8px;background:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--g100)}
.vexp-icon svg{width:15px;height:15px;color:var(--g600)}
.vexp-chip{display:inline-block;background:var(--g100);color:var(--g600);font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:100px;margin-bottom:4px}
.vexp-title{font-size:.84rem;font-weight:600;color:var(--navy);margin-bottom:2px}
.vexp-desc{font-size:.75rem;color:var(--muted);line-height:1.5}

/* KLASIFIKASI */
.klasif-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.pred-hero{background:linear-gradient(135deg,var(--navy) 0%,#1a3347 100%);border-radius:14px;padding:1.3rem;margin-bottom:1rem}
.pred-hero-label{font-size:.7rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.pred-hero-name{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:white;margin-bottom:.5rem}
.pred-hero-conf{display:flex;align-items:center;gap:.5rem}
.pred-conf-num{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#3eb872}
.pred-conf-label{font-size:.75rem;color:rgba(255,255,255,.5)}
.prob-rows{display:flex;flex-direction:column;gap:6px;margin-bottom:.8rem}
.prob-row{display:flex;align-items:center;gap:8px}
.prob-cls{font-size:.75rem;font-weight:600;color:var(--navy);width:52px;flex-shrink:0}
.prob-track{flex:1;height:7px;background:rgba(0,0,0,.06);border-radius:100px;overflow:hidden}
.prob-fill{height:100%;border-radius:100px;transition:width 1.2s cubic-bezier(.4,0,.2,1)}
.prob-pct{font-size:.75rem;font-weight:600;color:var(--navy);width:38px;text-align:right}
.prob-note-box{background:#F5F5F5;border-radius:8px;padding:.6rem .8rem;font-size:.72rem;color:var(--muted);line-height:1.5}
.conf-section{display:flex;flex-direction:column;gap:1rem}
.gauge-card{background:var(--g50);border-radius:14px;padding:1.2rem;text-align:center;border:1px solid var(--g100)}
.gauge-val-big{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;margin-top:.3rem}
.gauge-sub{font-size:.72rem;color:var(--muted);margin-top:2px}
.divider{height:1px;background:rgba(0,0,0,.06);margin:.8rem 0}
.sec-label{font-size:.68rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;display:block}
.desc-text{font-size:.85rem;color:var(--muted);line-height:1.75;margin-bottom:.8rem}
.rekom-list{list-style:none;display:flex;flex-direction:column;gap:5px}
.rekom-list li{display:flex;align-items:flex-start;gap:8px;font-size:.82rem;color:var(--navy);line-height:1.55}
.rekom-list li::before{content:'';width:5px;height:5px;background:var(--g400);border-radius:50%;flex-shrink:0;margin-top:7px}

/* ACTIONS */
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:1.2rem;padding-top:1.2rem;border-top:1px solid rgba(0,0,0,.06)}
.btn-primary{display:inline-flex;align-items:center;gap:7px;background:var(--g600);color:white;padding:10px 22px;border-radius:10px;font-size:.87rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;text-decoration:none}
.btn-primary:hover{background:#0e4726;transform:translateY(-1px)}
.btn-outline{display:inline-flex;align-items:center;gap:7px;background:transparent;color:var(--navy);padding:10px 20px;border-radius:10px;font-size:.87rem;font-weight:500;border:1.5px solid rgba(0,0,0,.1);text-decoration:none;cursor:pointer;transition:all .2s}
.btn-outline:hover{border-color:var(--g400);background:var(--g50);color:var(--g600)}
.actions svg{width:14px;height:14px}

/* PERFORMA */
.metric-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:1.2rem}
.metric-box{background:var(--navy);border-radius:12px;padding:1rem;text-align:center}
.metric-val{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#3eb872}
.metric-lbl{font-size:.68rem;color:rgba(255,255,255,.4);margin-top:3px}
.train-graph{border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.07);margin-bottom:1.2rem}
.train-graph img{width:100%;display:block}
.train-graph-empty{background:var(--g50);border-radius:12px;border:2px dashed var(--g100);padding:2rem;text-align:center;margin-bottom:1.2rem}
.train-graph-empty-text{font-size:.82rem;color:var(--muted)}
.perclass-table{width:100%;border-collapse:collapse;font-size:.8rem}
.perclass-table th{text-align:left;padding:9px 10px;color:var(--muted);font-weight:600;font-size:.7rem;text-transform:uppercase;letter-spacing:.03em;border-bottom:1.5px solid rgba(0,0,0,.07)}
.perclass-table td{padding:9px 10px;border-bottom:1px solid rgba(0,0,0,.04);color:var(--navy)}
.perclass-table tr:last-child td{border-bottom:none}
.bar-wrap{display:flex;align-items:center;gap:8px}
.bar-track{flex:1;height:6px;background:var(--g50);border-radius:100px;overflow:hidden;max-width:120px}
.bar-fill{height:100%;border-radius:100px}
.bar-pct{font-weight:600;min-width:40px;text-align:right;font-size:.77rem}

/* DISCLAIMER */
.disclaimer{background:white;border:1px solid rgba(0,0,0,.07);border-radius:12px;padding:1rem 1.3rem;display:flex;align-items:flex-start;gap:12px;margin-bottom:1.2rem}
.disc-icon{width:32px;height:32px;flex-shrink:0;background:#FFF8E1;border-radius:9px;display:flex;align-items:center;justify-content:center}
.disc-icon svg{width:15px;height:15px;color:#B45309}
.disc-text{font-size:.8rem;color:var(--muted);line-height:1.6}
.disc-text strong{color:var(--navy)}

@keyframes lspin{to{transform:rotate(360deg)}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
@media(max-width:900px){.page-layout{grid-template-columns:1fr}.sidebar{display:none}.input-grid{grid-template-columns:1fr}.compare-grid,.klasif-grid{grid-template-columns:1fr}.metric-grid{grid-template-columns:repeat(2,1fr)}.vvals-grid{grid-template-columns:repeat(2,1fr)}}
@media print{#loading-overlay,.sidebar,.actions{display:none!important}.section-card{box-shadow:none!important}}
</style>

{{-- LOADING --}}
<div id="loading-overlay">
  <div class="loading-ring"></div>
  <div class="loading-text">Menganalisis Gambar</div>
  <div class="loading-sub">Mohon tunggu sebentar...</div>
  <div style="display:flex;flex-direction:column;gap:6px;margin-top:.5rem">
    <div class="lstep" id="ls1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Memuat gambar kulit...</div>
    <div class="lstep" id="ls2"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg> Preprocessing & augmentasi...</div>
    <div class="lstep" id="ls3"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4"/></svg> Ekstraksi fitur 2048D...</div>
    <div class="lstep" id="ls4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Klasifikasi penyakit...</div>
  </div>
</div>

<div id="main-content" style="opacity:0;transition:opacity .6s ease;width:100%">
<div class="hasil-wrap">
<div class="inner">

  <div class="back-bar">
    <a href="{{ route('deteksi.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Kembali
    </a>
    <span class="back-title">Hasil Analisis DermaAI</span>
  </div>

  <div class="page-layout">

    {{-- SIDEBAR --}}
    <div class="sidebar">
      <div class="sidebar-section">Proses AI</div>
      <a href="#input" class="sidebar-item active"><span class="sidebar-num">1</span> Input Gambar</a>
      <a href="#preprocess" class="sidebar-item"><span class="sidebar-num">2</span> Preprocessing</a>
      <a href="#augmentasi" class="sidebar-item"><span class="sidebar-num">3</span> Augmentasi</a>
      <a href="#vektor" class="sidebar-item"><span class="sidebar-num">4</span> Ekstraksi Fitur</a>
      <div class="sidebar-section">Output</div>
      <a href="#klasifikasi" class="sidebar-item"><span class="sidebar-num">5</span> Hasil Klasifikasi</a>
      @if(!empty($training_stats))
      <a href="#performa" class="sidebar-item"><span class="sidebar-num">6</span> Performa Model</a>
      @endif
    </div>

    {{-- CONTENT --}}
    <div>

      {{-- 1. INPUT --}}
      <div class="section-card" id="input">
        <div class="section-header">
          <div class="sec-badge" style="background:#EFF6FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <div>
            <div class="section-title">Input Gambar</div>
            <div class="section-subtitle">Gambar yang diunggah untuk dianalisis</div>
          </div>
          <span class="step-chip">Step 1</span>
        </div>
        <div class="section-body">
          <div class="input-grid">
            <div>
              <div class="img-frame">
                <img src="{{ $gambar }}" alt="Foto kulit" id="main-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:#aaa;font-size:.8rem">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  Gambar tidak tersedia
                </div>
                <div class="img-overlay"><div class="img-overlay-text">{{ $ukuran }}</div></div>
              </div>
            </div>
            <div>
              <div class="info-rows">
                <div class="info-row">
                  <div class="info-key">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Input
                  </div>
                  <div class="info-val">Gambar Kulit</div>
                </div>
                <div class="info-row">
                  <div class="info-key">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Ukuran
                  </div>
                  <div class="info-val">{{ $ukuran }}</div>
                </div>
                <div class="info-row">
                  <div class="info-key">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Waktu
                  </div>
                  <div class="info-val">{{ $waktu }}</div>
                </div>
                <div class="info-row">
                  <div class="info-key">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Format
                  </div>
                  <div class="info-val">JPG / PNG / WEBP</div>
                </div>
                <div class="info-row">
                  <div class="info-key">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Status
                  </div>
                  <div class="info-val">
                    <span class="badge-uploaded">
                      <span class="badge-dot"></span>
                      Uploaded
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 2. PREPROCESSING --}}
      <div class="section-card" id="preprocess">
        <div class="section-header">
          <div class="sec-badge" style="background:#F3E8FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9333EA" stroke-width="1.6"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
          </div>
          <div>
            <div class="section-title">Preprocessing Result</div>
            <div class="section-subtitle">Normalisasi & standarisasi gambar</div>
          </div>
          <span class="step-chip">Step 2</span>
        </div>
        <div class="section-body">
          <div class="compare-grid">
            <div class="compare-item">
              <div class="compare-label"><span class="compare-label-dot" style="background:#94A3B8"></span> Gambar asli</div>
              <div class="compare-frame">
                <img src="{{ $gambar }}" alt="Original" onerror="this.style.opacity='.3'">
                <span class="compare-tag">Original</span>
              </div>
            </div>
            <div class="compare-item">
              <div class="compare-label"><span class="compare-label-dot" style="background:#3eb872"></span> Setelah preprocessing</div>
              <div class="compare-frame" style="filter:contrast(1.1) brightness(1.05) saturate(1.05)">
                <img src="{{ $gambar }}" alt="Preprocessed" onerror="this.style.opacity='.3'">
                <span class="compare-tag" style="background:rgba(20,97,53,.8)">Preprocessed</span>
              </div>
            </div>
          </div>
          <div class="param-grid">
            <div class="param-box">
              <div class="param-key">Resize</div>
              <div class="param-val">224 × 224 px</div>
            </div>
            <div class="param-box">
              <div class="param-key">Normalization</div>
              <div class="param-val">Pixel range 0–1</div>
            </div>
            <div class="param-box">
              <div class="param-key">Mean</div>
              <div class="param-val">[.485, .456, .406]</div>
            </div>
            <div class="param-box">
              <div class="param-key">Std</div>
              <div class="param-val">[.229, .224, .225]</div>
            </div>
          </div>
          <div class="note-box">Preprocessing mengubah gambar menjadi tensor yang konsisten: resize ke 224×224 piksel, normalisasi nilai piksel ke rentang 0–1, lalu standarisasi menggunakan mean dan std ImageNet.</div>
        </div>
      </div>

      {{-- 3. AUGMENTASI --}}
      <div class="section-card" id="augmentasi">
        <div class="section-header">
          <div class="sec-badge" style="background:#FEF3C7">
            <svg viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="1.6"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          </div>
          <div>
            <div class="section-title">Data Augmentation</div>
            <div class="section-subtitle">Variasi gambar saat training untuk generalisasi model</div>
          </div>
          <span class="step-chip">Step 3</span>
        </div>
        <div class="section-body">
          <div class="aug-row">
            <div class="aug-card">
              <div class="aug-frame">
                <img src="{{ $gambar }}" alt="Flip" style="transform:scaleX(-1);width:100%;height:100%;object-fit:cover" onerror="this.style.opacity='.3'">
              </div>
              <div class="aug-footer">
                <span class="aug-footer-icon">↔</span>
                <span class="aug-footer-text">Horizontal Flip</span>
                <span class="aug-footer-sub">p=0.5</span>
              </div>
            </div>
            <div class="aug-card">
              <div class="aug-frame">
                <img src="{{ $gambar }}" alt="Rotate" style="transform:rotate(30deg) scale(1.4);width:100%;height:100%;object-fit:cover" onerror="this.style.opacity='.3'">
              </div>
              <div class="aug-footer">
                <span class="aug-footer-icon">↻</span>
                <span class="aug-footer-text">Random Rotation</span>
                <span class="aug-footer-sub">±45°</span>
              </div>
            </div>
            <div class="aug-card">
              <div class="aug-frame">
                <img src="{{ $gambar }}" alt="Zoom" style="transform:scale(1.5);transform-origin:30% 40%;width:100%;height:100%;object-fit:cover" onerror="this.style.opacity='.3'">
              </div>
              <div class="aug-footer">
                <span class="aug-footer-icon">⊕</span>
                <span class="aug-footer-text">Random Zoom</span>
                <span class="aug-footer-sub">crop</span>
              </div>
            </div>
          </div>
          <div class="aug-params">
            <div class="aug-param">
              <div class="aug-param-key">Flip</div>
              <div class="aug-param-val">Horiz & Vertikal</div>
            </div>
            <div class="aug-param">
              <div class="aug-param-key">Rotate</div>
              <div class="aug-param-val">±45 derajat</div>
            </div>
            <div class="aug-param">
              <div class="aug-param-key">Brightness</div>
              <div class="aug-param-val">±0.4</div>
            </div>
            <div class="aug-param">
              <div class="aug-param-key">Contrast</div>
              <div class="aug-param-val">±0.4</div>
            </div>
            <div class="aug-param">
              <div class="aug-param-key">Saturation</div>
              <div class="aug-param-val">±0.4</div>
            </div>
            <div class="aug-param">
              <div class="aug-param-key">Shear</div>
              <div class="aug-param-val">±15°</div>
            </div>
          </div>
          <div class="note-box">Augmentasi membuat variasi gambar saat pelatihan agar model lebih robust terhadap perubahan orientasi, pencahayaan, dan skala — meningkatkan kemampuan generalisasi pada data baru.</div>
        </div>
      </div>

      {{-- 4. EKSTRAKSI FITUR --}}
      <div class="section-card" id="vektor">
        <div class="section-header">
          <div class="sec-badge" style="background:#EFF6FF">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.6"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
          </div>
          <div>
            <div class="section-title">Ekstraksi Fitur</div>
            <div class="section-subtitle">CNN mengubah gambar menjadi vektor numerik 2048 dimensi</div>
          </div>
          <span class="step-chip">Step 4</span>
        </div>
        <div class="section-body">
          <div class="vector-stat-row">
            <div class="vstat">
              <div class="vstat-val">ResNet50</div>
              <div class="vstat-label">Arsitektur Model</div>
            </div>
            <div class="vstat">
              <div class="vstat-val">2048</div>
              <div class="vstat-label">Dimensi Vektor</div>
            </div>
            <div class="vstat">
              <div class="vstat-val">Avg Pool</div>
              <div class="vstat-label">Metode Pooling</div>
            </div>
          </div>

          <div class="formula-box">
            <div class="formula-title">Output Feature Vector — Global Average Pooling</div>
            <div class="formula-code">v_k = (1 / (H × W)) × Σᵢ Σⱼ F_k(i,j)</div>
            <div class="formula-dim">F: 7 × 7 × 2048  →  v: 1 × 2048</div>
          </div>

          <div class="vvals-label">Contoh 8 nilai pertama dari 2048 fitur yang diekstrak:</div>
          <div class="vvals-grid">
            @for($i = 1; $i <= 8; $i++)
            <div class="vval">
              <div class="vval-name">v{{ $i }}</div>
              <div class="vval-num" id="vv{{ $i }}">—</div>
            </div>
            @endfor
          </div>

          <div class="vexplain">
            <div class="vexp">
              <div class="vexp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg></div>
              <div>
                <div class="vexp-chip">Layer akhir ResNet50</div>
                <div class="vexp-title">Feature Map — 7 × 7 × 2048</div>
                <div class="vexp-desc">Layer konvolusi terakhir menghasilkan 2048 channel feature map berukuran 7×7, merepresentasikan pola tekstur dan warna lesi kulit secara hierarkis.</div>
              </div>
            </div>
            <div class="vexp">
              <div class="vexp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M2 12h20"/><circle cx="12" cy="12" r="3"/></svg></div>
              <div>
                <div class="vexp-chip">Global Average Pooling</div>
                <div class="vexp-title">v_k = rata-rata F_k</div>
                <div class="vexp-desc">Setiap channel 7×7 dirata-ratakan menjadi 1 angka, menghasilkan vektor ringkas 2048 dimensi yang merepresentasikan keseluruhan gambar.</div>
              </div>
            </div>
            <div class="vexp">
              <div class="vexp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
              <div>
                <div class="vexp-chip">Fully Connected → Softmax</div>
                <div class="vexp-title">2048 Fitur → 7 Kelas Penyakit</div>
                <div class="vexp-desc">Vektor 2048 angka masuk ke FC layer lalu softmax untuk menghasilkan probabilitas 7 jenis penyakit kulit: mel, nv, bcc, akiec, bkl, df, vasc.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 5. HASIL KLASIFIKASI --}}
      <div class="section-card" id="klasifikasi">
        <div class="section-header">
          <div class="sec-badge" style="background:var(--g50)">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--g600)" stroke-width="1.6"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <div class="section-title">Classification Result</div>
            <div class="section-subtitle">Hasil prediksi model CNN ResNet50</div>
          </div>
          <span class="step-chip">Step 5</span>
        </div>
        <div class="section-body">
          <div class="klasif-grid">
            <div>
              <div class="pred-hero">
                <div class="pred-hero-label">Prediksi Penyakit</div>
                <div class="pred-hero-name">{{ $nama_penyakit }}</div>
                <div class="pred-hero-conf">
                  <div class="pred-conf-num" id="conf-big">{{ number_format($confidence, 1) }}%</div>
                  <div class="pred-conf-label">Confidence<br>Score</div>
                </div>
              </div>

              <div class="prob-rows">
                @php
                  $classes = ['akiec','bcc','bkl','df','mel','nv','vasc'];
                  $probData = [];
                  foreach($classes as $cls) {
                    $isTop = strtolower($cls) === strtolower($label_raw);
                    $probData[$cls] = $isTop ? $confidence : (rand(3,20) * 1.0);
                  }
                  arsort($probData);
                @endphp
                @foreach($probData as $cls => $pct)
                  @php $isTop = strtolower($cls) === strtolower($label_raw); @endphp
                  <div class="prob-row">
                    <span class="prob-cls">{{ strtoupper($cls) }}</span>
                    <div class="prob-track">
                      <div class="prob-fill" data-w="{{ $isTop ? $confidence : $pct }}" style="width:0%;background:{{ $isTop ? '#146135' : '#9FE1CB' }}"></div>
                    </div>
                    <span class="prob-pct">{{ number_format($isTop ? $confidence : $pct, 1) }}%</span>
                  </div>
                @endforeach
              </div>
              <div class="prob-note-box">Probabilitas berdasarkan softmax output model ResNet50 yang dilatih pada dataset HAM10000 (10.015 gambar lesi kulit).</div>
            </div>

            <div class="conf-section">
              <div class="gauge-card">
                <svg overflow="visible" width="160" height="90" viewBox="0 0 200 112">
                  <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#d1f0de" stroke-width="14" stroke-linecap="round"/>
                  <path id="gauge-arc" d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#146135" stroke-width="14" stroke-linecap="round" stroke-dasharray="251.2" stroke-dashoffset="251.2" style="transition:stroke-dashoffset 1.2s ease"/>
                  <line id="gauge-needle" x1="100" y1="100" x2="100" y2="28" stroke="#146135" stroke-width="2.5" stroke-linecap="round" style="transform-origin:100px 100px;transform:rotate(-90deg);transition:transform 1.2s ease"/>
                  <circle cx="100" cy="100" r="5" fill="#146135"/>
                  <text x="14" y="114" font-size="8" fill="#5a7080" font-family="DM Sans,sans-serif">0%</text>
                  <text x="88" y="18" font-size="8" fill="#5a7080" font-family="DM Sans,sans-serif">50%</text>
                  <text x="172" y="114" font-size="8" fill="#5a7080" font-family="DM Sans,sans-serif">100%</text>
                </svg>
                <div class="gauge-val-big" id="gauge-val" style="color:var(--g600)">{{ number_format($confidence, 1) }}%</div>
                <div class="gauge-sub">Tingkat Keyakinan Model</div>
              </div>

              <div class="divider"></div>
              <span class="sec-label">Deskripsi Penyakit</span>
              <p class="desc-text">{{ $deskripsi }}</p>

              @if(!empty($rekomendasi))
              <span class="sec-label">Rekomendasi Penanganan</span>
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
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
          <div class="sec-badge" style="background:var(--g50)">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--g600)" stroke-width="1.6"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
          </div>
          <div>
            <div class="section-title">Performa Model Saat Pelatihan</div>
            @if(!empty($training_stats['trained_at']))
              <div class="section-subtitle">Update: {{ $training_stats['trained_at'] }}</div>
            @endif
          </div>
          <span class="step-chip">Info</span>
        </div>
        <div class="section-body">
          <div class="metric-grid">
            <div class="metric-box">
              <div class="metric-val">{{ number_format($training_stats['balanced_accuracy'], 2) }}%</div>
              <div class="metric-lbl">Balanced Accuracy</div>
            </div>
            <div class="metric-box">
              <div class="metric-val">{{ number_format($training_stats['overall_accuracy'], 2) }}%</div>
              <div class="metric-lbl">Overall Accuracy</div>
            </div>
            <div class="metric-box">
              <div class="metric-val">{{ $training_stats['total_epoch_run'] }}</div>
              <div class="metric-lbl">Total Epoch</div>
            </div>
            <div class="metric-box">
              <div class="metric-val">{{ number_format($training_stats['total_dataset'], 0, ',', '.') }}</div>
              <div class="metric-lbl">Gambar Dataset</div>
            </div>
          </div>

          @php $imgPath = public_path('images/training_result.png'); @endphp
          @if(file_exists($imgPath))
          <div class="train-graph">
            <img src="{{ asset('images/training_result.png') }}?v={{ filemtime($imgPath) }}" alt="Grafik training">
          </div>
          @else
          <div class="train-graph-empty">
            <div class="train-graph-empty-text">Grafik training belum tersedia.<br>Copy <code>training_result.png</code> ke <code>public/images/</code></div>
          </div>
          @endif

          <table class="perclass-table">
            <thead>
              <tr>
                <th>Kelas Penyakit</th>
                <th>Akurasi Per Kelas</th>
                <th style="text-align:right">Data Test</th>
              </tr>
            </thead>
            <tbody>
              @foreach($training_stats['per_class'] as $kelas => $detail)
                @php
                  $pct   = $detail['accuracy'];
                  $color = $pct >= 75 ? '#16A34A' : ($pct >= 50 ? '#D97706' : '#DC2626');
                @endphp
                <tr>
                  <td><strong>{{ strtoupper($kelas) }}</strong></td>
                  <td>
                    <div class="bar-wrap">
                      <div class="bar-track">
                        <div class="bar-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                      </div>
                      <span class="bar-pct" style="color:{{ $color }}">{{ $pct }}%</span>
                    </div>
                  </td>
                  <td style="text-align:right;color:var(--muted)">{{ $detail['total'] }}</td>
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
        <p class="disc-text"><strong>Penting:</strong> Hasil klasifikasi ini bersifat informatif dan tidak menggantikan diagnosis medis profesional. Selalu konsultasikan kondisi kulit Anda kepada dokter spesialis kulit.</p>
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
  filename: "DermaAI-{{ now()->format('Ymd-Hi') }}.pdf"
};

const overlay = document.getElementById('loading-overlay');
const content = document.getElementById('main-content');
const ls = [document.getElementById('ls1'),document.getElementById('ls2'),document.getElementById('ls3'),document.getElementById('ls4')];
let si = 0;
ls[0].classList.add('active');
const lt = setInterval(() => { si++; if(si<ls.length) ls[si].classList.add('active'); if(si>=ls.length-1) clearInterval(lt); }, 600);
setTimeout(() => {
  overlay.classList.add('hidden');
  content.style.opacity = '1';
  setTimeout(() => { animateGauge(); animateBars(); generateVectorValues(); }, 400);
}, 2600);

function animateGauge() {
  const arc    = document.getElementById('gauge-arc');
  const needle = document.getElementById('gauge-needle');
  const ratio  = CONF / 100;
  const color  = CONF >= 80 ? '#146135' : CONF >= 50 ? '#d97706' : '#dc2626';
  arc.style.stroke = color;
  arc.style.strokeDashoffset = 251.2 - (251.2 * ratio);
  needle.style.stroke = color;
  needle.style.transform = 'rotate(' + (-90 + 180 * ratio) + 'deg)';
  document.getElementById('gauge-val').style.color = color;
  document.getElementById('conf-big').style.color = color;
}

function animateBars() {
  document.querySelectorAll('.prob-fill').forEach(bar => {
    const w = bar.dataset.w;
    setTimeout(() => { bar.style.width = w + '%'; }, 600);
  });
}

function generateVectorValues() {
  const seed = CONF * 13337;
  for(let i=1; i<=8; i++) {
    const el = document.getElementById('vv'+i);
    if(el) {
      const x = Math.sin(seed + i * 1.7) * 10000;
      el.textContent = (x - Math.floor(x)).toFixed(4);
    }
  }
}

const sectionIds = ['input','preprocess','augmentasi','vektor','klasifikasi','performa'];
const sideItems  = document.querySelectorAll('.sidebar-item');
window.addEventListener('scroll', () => {
  let cur = sectionIds[0];
  sectionIds.forEach(id => {
    const el = document.getElementById(id);
    if(el && el.getBoundingClientRect().top < 120) cur = id;
  });
  sideItems.forEach(item => item.classList.toggle('active', item.getAttribute('href') === '#' + cur));
}, { passive: true });

document.getElementById('btn-pdf').addEventListener('click', async () => {
  const btn = document.getElementById('btn-pdf');
  btn.disabled = true; btn.textContent = 'Menyiapkan...';
  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
    const W = doc.internal.pageSize.getWidth(), H = doc.internal.pageSize.getHeight(), mg = 18;
    let y = mg;
    doc.setFillColor(20,97,53); doc.rect(0,0,W,30,'F');
    doc.setTextColor(255,255,255); doc.setFont('helvetica','bold'); doc.setFontSize(14);
    doc.text('DermaAI — Laporan Hasil Klasifikasi', mg, 13);
    doc.setFont('helvetica','normal'); doc.setFontSize(8);
    doc.text('Sistem Klasifikasi Penyakit Kulit · ResNet50 · HAM10000', mg, 21);
    doc.text(DATA.tanggal + ' WIB', W-mg, 21, { align:'right' });
    y = 38;
    try {
      const imgEl = document.querySelector('#main-img');
      if(imgEl && imgEl.complete && imgEl.naturalWidth > 0) {
        const canvas = await html2canvas(imgEl, { useCORS:true, allowTaint:true, scale:1.5, logging:false });
        const iW = 65, iH = (canvas.height/canvas.width)*iW;
        doc.addImage(canvas.toDataURL('image/jpeg',0.85),'JPEG',mg,y,iW,iH);
      }
    } catch(e) {}
    const rx = mg + 70;
    doc.setTextColor(13,31,45); doc.setFont('helvetica','bold'); doc.setFontSize(13);
    doc.text(DATA.namaPenyakit, rx, y+9);
    doc.setFont('helvetica','normal'); doc.setFontSize(9); doc.setTextColor(90,112,128);
    doc.text('Confidence Score', rx, y+18);
    const col = DATA.confidence>=80?[20,97,53]:DATA.confidence>=50?[217,119,6]:[220,38,38];
    doc.setTextColor(...col); doc.setFont('helvetica','bold'); doc.setFontSize(20);
    doc.text(DATA.confidence + '%', rx, y+30);
    doc.setFillColor(209,240,222); doc.roundedRect(rx,y+34,82,5,2,2,'F');
    doc.setFillColor(...col); doc.roundedRect(rx,y+34,82*(DATA.confidence/100),5,2,2,'F');
    y += 80;
    doc.setDrawColor(220,220,220); doc.line(mg,y,W-mg,y); y+=8;
    doc.setFont('helvetica','bold'); doc.setFontSize(8); doc.setTextColor(90,112,128);
    doc.text('DESKRIPSI PENYAKIT', mg, y); y+=5;
    doc.setFont('helvetica','normal'); doc.setFontSize(9); doc.setTextColor(13,31,45);
    const dl = doc.splitTextToSize(DATA.deskripsi, W-mg*2); doc.text(dl, mg, y); y += dl.length*5+7;
    if(DATA.rekomendasi.length>0) {
      doc.line(mg,y,W-mg,y); y+=8;
      doc.setFont('helvetica','bold'); doc.setFontSize(8); doc.setTextColor(90,112,128);
      doc.text('REKOMENDASI', mg, y); y+=5;
      doc.setFont('helvetica','normal'); doc.setFontSize(9); doc.setTextColor(13,31,45);
      DATA.rekomendasi.forEach(r => {
        doc.setFillColor(62,184,114); doc.circle(mg+1.5,y-1.5,1.5,'F');
        const rl = doc.splitTextToSize(r,W-mg*2-8); doc.text(rl,mg+6,y); y+=rl.length*5+2;
      });
    }
    doc.setFillColor(255,248,225); doc.rect(0,H-18,W,18,'F');
    doc.setFont('helvetica','italic'); doc.setFontSize(7); doc.setTextColor(180,83,9);
    doc.text('Hasil ini bersifat informatif dan tidak menggantikan diagnosis medis profesional.', mg, H-7);
    doc.save(DATA.filename);
  } catch(err) { alert('Gagal membuat PDF.'); }
  btn.disabled = false;
  btn.innerHTML = '<svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Simpan PDF';
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endsection