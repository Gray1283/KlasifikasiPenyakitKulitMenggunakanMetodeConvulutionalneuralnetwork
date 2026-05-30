<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Klasifikasi Penyakit Kulit</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-50: #f0faf4;
      --green-100: #d1f0de;
      --green-200: #a3e0be;
      --green-400: #3eb872;
      --green-600: #146135;
      --green-700: #0e4726;
      --green-800: #092e18;
      --teal-400: #1d9e75;
      --teal-600: #0f6e56;
      --navy: #0d1f2d;
      --navy-soft: #1a2f42;
      --cream: #f9f7f2;
      --text-main: #0d1f2d;
      --text-muted: #5a7080;
      --radius: 14px;
      --radius-lg: 22px;
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--cream);
      color: var(--text-main);
      overflow-x: hidden;
    }

    /* ── NAVBAR ─────────────────────────────── */
    nav {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(249, 247, 242, 0.88);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(20, 97, 53, 0.1);
      padding: 0 5vw;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: 1.15rem;
      color: var(--green-600);
      text-decoration: none;
    }

    .nav-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--green-600);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .nav-logo-icon svg { width: 20px; height: 20px; }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 2rem;
      list-style: none;
    }

    .nav-links a {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-muted);
      text-decoration: none;
      transition: color 0.2s;
    }

    .nav-links a:hover { color: var(--green-600); }

    .btn-nav {
      background: var(--green-600) !important;
      color: white !important;
      padding: 8px 20px !important;
      border-radius: 8px !important;
      font-weight: 500 !important;
      transition: background 0.2s !important;
    }

    .btn-nav:hover { background: var(--green-700) !important; }

    /* ── HERO ────────────────────────────────── */
    .hero {
      min-height: 92vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      gap: 4rem;
      padding: 6rem 8vw 5rem;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -120px;
      right: -180px;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, var(--green-100) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero::after {
      content: '';
      position: absolute;
      bottom: -80px;
      left: 0;
      width: 350px;
      height: 350px;
      background: radial-gradient(circle, rgba(30, 158, 117, 0.08) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--green-100);
      color: var(--green-700);
      font-size: 0.8rem;
      font-weight: 600;
      padding: 5px 14px;
      border-radius: 100px;
      border: 1px solid var(--green-200);
      margin-bottom: 1.6rem;
      letter-spacing: 0.03em;
      text-transform: uppercase;
    }

    .hero-badge span { width: 6px; height: 6px; background: var(--green-400); border-radius: 50%; display: inline-block; }

    .hero h1 {
      font-family: 'Syne', sans-serif;
      font-size: clamp(2.4rem, 4vw, 3.6rem);
      font-weight: 800;
      line-height: 1.1;
      color: var(--navy);
      margin-bottom: 1.4rem;
    }

    .hero h1 em {
      font-style: normal;
      color: var(--green-600);
    }

    .hero p {
      font-size: 1.05rem;
      color: var(--text-muted);
      line-height: 1.75;
      max-width: 500px;
      margin-bottom: 2.4rem;
      font-weight: 300;
    }

    .hero-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--green-600);
      color: white;
      padding: 13px 28px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-primary:hover { background: var(--green-700); transform: translateY(-1px); }

    .btn-secondary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: transparent;
      color: var(--text-main);
      padding: 13px 24px;
      border-radius: 10px;
      font-weight: 500;
      font-size: 0.95rem;
      text-decoration: none;
      border: 1.5px solid rgba(0,0,0,0.12);
      transition: border-color 0.2s, background 0.2s;
    }

    .btn-secondary:hover { border-color: var(--green-400); background: var(--green-50); }

    .hero-stats {
      display: flex;
      gap: 2.5rem;
      margin-top: 3rem;
    }

    .stat-item {}

    .stat-num {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--navy);
    }

    .stat-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* ── HERO IMAGE SIDE ─────────────────────── */
    .hero-visual {
      position: relative;
    }

    .hero-image-wrap {
      border-radius: 24px;
      overflow: hidden;
      aspect-ratio: 4/5;
      position: relative;
    }

    .hero-image-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .hero-card-float {
      position: absolute;
      background: white;
      border-radius: 14px;
      padding: 14px 18px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.12);
      border: 1px solid rgba(255,255,255,0.8);
    }

    .hero-card-float.card-top {
      top: -18px;
      right: -22px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .hero-card-float.card-bottom {
      bottom: 24px;
      left: -22px;
      min-width: 200px;
    }

    .float-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--green-100);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .float-icon svg { width: 22px; height: 22px; color: var(--green-600); }

    .float-text-sm { font-size: 0.75rem; color: var(--text-muted); }
    .float-text-val { font-family: 'Syne', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--navy); }

    .accuracy-bar {
      margin-top: 8px;
      height: 5px;
      background: var(--green-100);
      border-radius: 10px;
      overflow: hidden;
    }

    .accuracy-bar-fill {
      height: 100%;
      width: 94%;
      background: var(--green-400);
      border-radius: 10px;
    }

    /* ── FEATURES ────────────────────────────── */
    .features {
      padding: 6rem 8vw;
      background: white;
    }

    .section-label {
      display: block;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--green-600);
      margin-bottom: 0.6rem;
    }

    .section-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(1.7rem, 3vw, 2.5rem);
      font-weight: 800;
      color: var(--navy);
      margin-bottom: 0.8rem;
    }

    .section-desc {
      color: var(--text-muted);
      font-size: 1rem;
      max-width: 520px;
      line-height: 1.75;
      font-weight: 300;
    }

    .features-header { margin-bottom: 3.5rem; }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }

    .feature-card {
      background: var(--cream);
      border-radius: var(--radius-lg);
      padding: 2rem 1.75rem;
      border: 1px solid rgba(20,97,53,0.08);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .feature-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0,0,0,0.07); }

    .feature-num {
      font-family: 'Syne', sans-serif;
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--green-100);
      line-height: 1;
      margin-bottom: 1.2rem;
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      background: var(--green-100);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.2rem;
    }

    .feature-icon svg { width: 24px; height: 24px; color: var(--green-600); }

    .feature-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 0.6rem;
    }

    .feature-desc {
      font-size: 0.9rem;
      color: var(--text-muted);
      line-height: 1.7;
    }

    /* ── ABOUT ───────────────────────────────── */
    #tentang {
      padding: 6rem 8vw;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6rem;
      align-items: center;
    }

    .about-visual {
      background: var(--green-600);
      border-radius: 24px;
      padding: 3rem;
      position: relative;
      overflow: hidden;
      min-height: 380px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }

    .about-visual::before {
      content: '';
      position: absolute;
      top: -60px;
      right: -60px;
      width: 280px;
      height: 280px;
      background: rgba(255,255,255,0.06);
      border-radius: 50%;
    }

    .about-visual::after {
      content: '';
      position: absolute;
      bottom: -80px;
      left: 40px;
      width: 220px;
      height: 220px;
      background: rgba(255,255,255,0.04);
      border-radius: 50%;
    }

    .about-visual-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: white;
      position: relative;
      z-index: 1;
      margin-bottom: 0.8rem;
    }

    .about-visual-sub {
      color: var(--green-200);
      font-size: 0.9rem;
      position: relative;
      z-index: 1;
      line-height: 1.6;
    }

    .method-chips {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 1.5rem;
      position: relative;
      z-index: 1;
    }

    .chip {
      background: rgba(255,255,255,0.12);
      color: white;
      font-size: 0.78rem;
      font-weight: 500;
      padding: 5px 14px;
      border-radius: 100px;
      border: 1px solid rgba(255,255,255,0.2);
    }

    .about-content .section-desc { max-width: 100%; margin-bottom: 2rem; }

    .about-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
    }

    .about-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 0.93rem;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .about-list li::before {
      content: '';
      width: 7px;
      height: 7px;
      background: var(--green-400);
      border-radius: 50%;
      flex-shrink: 0;
      margin-top: 7px;
    }

    /* ── HOW IT WORKS ────────────────────────── */
    .how-it-works {
      padding: 6rem 8vw;
      background: var(--navy);
      position: relative;
      overflow: hidden;
    }

    .how-it-works::before {
      content: '';
      position: absolute;
      top: -100px;
      right: -100px;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(62, 184, 114, 0.12) 0%, transparent 70%);
    }

    .how-it-works .section-label { color: var(--green-400); }
    .how-it-works .section-title { color: white; }
    .how-it-works .section-desc { color: rgba(255,255,255,0.5); }

    .steps-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      margin-top: 3.5rem;
      position: relative;
    }

    .steps-grid::before {
      content: '';
      position: absolute;
      top: 28px;
      left: 12%;
      right: 12%;
      height: 1px;
      background: rgba(255,255,255,0.08);
      z-index: 0;
    }

    .step-card {
      position: relative;
      z-index: 1;
      text-align: center;
    }

    .step-dot {
      width: 56px;
      height: 56px;
      background: rgba(62, 184, 114, 0.12);
      border: 1px solid rgba(62, 184, 114, 0.25);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.4rem;
    }

    .step-dot svg { width: 24px; height: 24px; color: var(--green-400); }

    .step-title {
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      color: white;
      margin-bottom: 0.5rem;
    }

    .step-desc {
      font-size: 0.82rem;
      color: rgba(255,255,255,0.45);
      line-height: 1.65;
    }

    /* ── FAQ ─────────────────────────────────── */
    #faq {
      padding: 6rem 8vw;
      background: white;
    }

    .faq-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-top: 3rem;
    }

    .faq-item {
      background: var(--cream);
      border-radius: var(--radius);
      padding: 1.6rem;
      border: 1px solid rgba(20,97,53,0.08);
      cursor: pointer;
      transition: background 0.2s;
    }

    .faq-item:hover { background: var(--green-50); }

    .faq-q {
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 0.6rem;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .faq-q-num {
      width: 22px;
      height: 22px;
      background: var(--green-100);
      color: var(--green-700);
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .faq-a {
      font-size: 0.88rem;
      color: var(--text-muted);
      line-height: 1.7;
      padding-left: 32px;
    }

    /* ── FOOTER ──────────────────────────────── */
    footer {
      background: var(--navy);
      color: white;
      padding: 4rem 8vw 0;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 1.8fr 1fr 1fr 1fr;
      gap: 3rem;
      padding-bottom: 3rem;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .footer-brand {}

    .footer-brand .brand-name {
      font-family: 'Syne', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: white;
      margin-bottom: 0.8rem;
    }

    .footer-brand p {
      font-size: 0.85rem;
      color: rgba(255,255,255,0.4);
      line-height: 1.7;
      max-width: 260px;
    }

    .footer-socials {
      display: flex;
      gap: 10px;
      margin-top: 1.5rem;
    }

    .social-btn {
      width: 36px;
      height: 36px;
      background: rgba(255,255,255,0.07);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(255,255,255,0.1);
      transition: background 0.2s;
    }

    .social-btn:hover { background: rgba(255,255,255,0.14); }
    .social-btn img { width: 16px; height: 16px; filter: brightness(0) invert(1); }

    .footer-col h5 {
      font-family: 'Syne', sans-serif;
      font-size: 0.85rem;
      font-weight: 700;
      color: white;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 1.2rem;
    }

    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; }

    .footer-col a {
      font-size: 0.85rem;
      color: rgba(255,255,255,0.4);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-col a:hover { color: var(--green-400); }

    .footer-bottom {
      text-align: center;
      padding: 1.2rem 0;
      font-size: 0.8rem;
      color: rgba(255,255,255,0.25);
    }

    /* ── RESPONSIVE ──────────────────────────── */
    @media (max-width: 900px) {
      .hero { grid-template-columns: 1fr; padding: 4rem 6vw 3rem; gap: 2.5rem; }
      .hero::before, .hero::after { display: none; }
      .hero-visual { max-width: 480px; margin: 0 auto; }
      #tentang { grid-template-columns: 1fr; gap: 2.5rem; }
      .features-grid { grid-template-columns: 1fr 1fr; }
      .steps-grid { grid-template-columns: 1fr 1fr; }
      .steps-grid::before { display: none; }
      .faq-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
      nav { padding: 0 4vw; }
      .nav-links { gap: 1.2rem; }
    }

    @media (max-width: 600px) {
      .features-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr; }
      .hero-stats { gap: 1.5rem; }
      .nav-links li:not(:last-child) { display: none; }
    }

    /* ── ANIMATIONS ──────────────────────────── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .hero-badge { animation: fadeUp 0.5s ease both; }
    .hero h1    { animation: fadeUp 0.5s 0.08s ease both; }
    .hero p     { animation: fadeUp 0.5s 0.16s ease both; }
    .hero-actions { animation: fadeUp 0.5s 0.22s ease both; }
    .hero-stats { animation: fadeUp 0.5s 0.3s ease both; }
    .hero-visual { animation: fadeUp 0.7s 0.15s ease both; }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.55; }
    }

    .hero-badge span { animation: pulse 2s infinite; }
  </style>
</head>
<body>

  <!-- ── NAVBAR ─────────────────────────────── -->
  <nav>
    <a href="#" class="nav-logo">
      <div class="nav-logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
        </svg>
      </div>
      Klasifikasi Penyakit Kulit CNN
    </a>
    <ul class="nav-links">
      <li><a href="#tentang">Tentang</a></li>
      <li><a href="#faq">FAQ</a></li>
      <li><a href="#kontak">Kontak</a></li>
      <li><a href="{{ route('login') }}" class="btn-nav">Masuk</a></li>
    </ul>
  </nav>

  <!-- ── HERO ──────────────────────────────── -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-badge">
        <span></span>
        CNN-Powered Diagnosis
      </div>
      <h1>Deteksi <em>Penyakit Kulit</em> Lebih Cepat & Akurat</h1>
      <p>Sistem berbasis Convolutional Neural Network (CNN) yang membantu mengidentifikasi jenis penyakit kulit dari foto — cepat, non-invasif, dan dapat diakses kapan saja.</p>
      <div class="hero-actions">
        <a href="{{ route('login') }}" class="btn-primary">
          Mulai Deteksi
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#tentang" class="btn-secondary">
          Pelajari Lebih Lanjut
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num">94%</div>
          <div class="stat-label">Akurasi Model</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">7+</div>
          <div class="stat-label">Kelas Penyakit</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">&lt;3s</div>
          <div class="stat-label">Waktu Analisis</div>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-image-wrap">
        <img src="https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=700&h=900&fit=crop&crop=center" alt="Dermatology check">
      </div>

      <!-- floating card top-right -->
      <div class="hero-card-float card-top">
        <div class="float-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color: var(--green-600)"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <div class="float-text-sm">Hasil Analisis</div>
          <div class="float-text-val">Terdeteksi</div>
        </div>
      </div>

      <!-- floating card bottom-left -->
      <div class="hero-card-float card-bottom">
        <div class="float-text-sm">Tingkat Akurasi</div>
        <div class="float-text-val">94.2%</div>
        <div class="accuracy-bar"><div class="accuracy-bar-fill"></div></div>
      </div>
    </div>
  </section>

  <!-- ── FEATURES ───────────────────────────── -->
  <section class="features">
    <div class="features-header">
      <span class="section-label">Fitur Unggulan</span>
      <h2 class="section-title">Mengapa Memilih DermaAI?</h2>
      <p class="section-desc">Didukung teknologi deep learning terkini untuk memberikan analisis citra kulit yang akurat dan cepat.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <div class="feature-title">Klasifikasi CNN Real-time</div>
        <div class="feature-desc">Model CNN dilatih pada ribuan citra klinis untuk mengenali berbagai kondisi kulit dengan akurasi tinggi dalam hitungan detik.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="feature-title">Data Aman & Terenkripsi</div>
        <div class="feature-desc">Semua foto dan data pengguna disimpan dengan enkripsi end-to-end. Privasi Anda adalah prioritas kami.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="feature-title">Laporan Hasil Terperinci</div>
        <div class="feature-desc">Dapatkan laporan hasil analisis yang lengkap disertai probabilitas untuk setiap kelas penyakit yang terdeteksi.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="feature-title">Analisis &lt;3 Detik</div>
        <div class="feature-desc">Proses inferensi yang dioptimalkan memungkinkan hasil analisis diperoleh hampir secara instan tanpa menunggu lama.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="feature-title">Multi-Pengguna</div>
        <div class="feature-desc">Sistem mendukung akses multi-pengguna dengan panel admin untuk memantau riwayat analisis pasien secara terpusat.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="feature-title">Riwayat Pemeriksaan</div>
        <div class="feature-desc">Pantau perkembangan kondisi kulit dari waktu ke waktu dengan riwayat pemeriksaan yang tersimpan otomatis.</div>
      </div>
    </div>
  </section>

  <!-- ── ABOUT ──────────────────────────────── -->
  <section id="tentang">
    <div class="about-visual">
      <div class="about-visual-title">Teknologi CNN untuk Diagnosa Lebih Baik</div>
      <div class="about-visual-sub">Convolutional Neural Network (CNN) adalah arsitektur deep learning yang terbukti unggul dalam tugas pengenalan dan klasifikasi citra medis.</div>
      <div class="method-chips">
        <span class="chip">CNN</span>
        <span class="chip">Deep Learning</span>
        <span class="chip">Image Classification</span>
        <span class="chip">Transfer Learning</span>
        <span class="chip">Softmax Output</span>
      </div>
    </div>
    <div class="about-content">
      <span class="section-label">Tentang Sistem</span>
      <h2 class="section-title">Sistem Pendukung Diagnosis Kulit</h2>
      <p class="section-desc">Platform ini dirancang untuk membantu tenaga kesehatan maupun masyarakat umum dalam mengidentifikasi penyakit kulit secara cepat menggunakan kecerdasan buatan berbasis CNN.</p>
      <ul class="about-list">
        <li>Mendeteksi berbagai jenis penyakit kulit dari foto yang diunggah pengguna</li>
        <li>Menggunakan model CNN yang dilatih pada dataset klinis tervalidasi</li>
        <li>Memberikan probabilitas tiap kelas untuk transparansi hasil analisis</li>
        <li>Tersedia untuk pasien dan dokter dalam satu platform terintegrasi</li>
        <li>Mendukung deteksi dini untuk penanganan yang lebih efektif</li>
      </ul>
    </div>
  </section>

  <!-- ── HOW IT WORKS ───────────────────────── -->
  <section class="how-it-works">
    <span class="section-label">Cara Kerja</span>
    <h2 class="section-title">Proses Analisis dalam 4 Langkah</h2>
    <p class="section-desc">Dari unggah foto hingga hasil diagnosis, semuanya berlangsung secara otomatis dan transparan.</p>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-dot">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <div class="step-title">Unggah Foto</div>
        <div class="step-desc">Ambil atau upload foto area kulit yang ingin diperiksa dengan jelas</div>
      </div>
      <div class="step-card">
        <div class="step-dot">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div class="step-title">Preprocessing</div>
        <div class="step-desc">Gambar diproses dan dinormalisasi sebelum memasuki model CNN</div>
      </div>
      <div class="step-card">
        <div class="step-dot">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="step-title">Inferensi CNN</div>
        <div class="step-desc">Model CNN menganalisis pola pada citra dan menghasilkan probabilitas setiap kelas</div>
      </div>
      <div class="step-card">
        <div class="step-dot">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
        </div>
        <div class="step-title">Hasil & Laporan</div>
        <div class="step-desc">Tampilkan hasil klasifikasi beserta rekomendasi dan laporan tersimpan otomatis</div>
      </div>
    </div>
  </section>

  <!-- ── FAQ ───────────────────────────────── -->
  <section id="faq">
    <span class="section-label">FAQ</span>
    <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
    <p class="section-desc">Temukan jawaban atas pertanyaan umum tentang sistem ini.</p>
    <div class="faq-grid">
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">1</span>Apa itu sistem klasifikasi penyakit kulit ini?</div>
        <div class="faq-a">Sistem berbasis AI yang membantu mengidentifikasi jenis penyakit kulit dari foto menggunakan model Convolutional Neural Network (CNN) yang telah dilatih pada data klinis.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">2</span>Seberapa akurat hasil analisisnya?</div>
        <div class="faq-a">Model mencapai akurasi rata-rata 94% pada data uji. Namun sistem ini bersifat pendukung keputusan dan tidak menggantikan diagnosis dokter spesialis kulit.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">3</span>Apakah data dan foto saya aman?</div>
        <div class="faq-a">Ya, semua data dan gambar tersimpan dengan enkripsi dan hanya digunakan untuk keperluan analisis. Kami tidak membagikan data kepada pihak ketiga.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">4</span>Penyakit kulit apa saja yang dapat dideteksi?</div>
        <div class="faq-a">Sistem saat ini dapat mengklasifikasikan 7 jenis kondisi kulit umum termasuk dermatitis, melanoma, psoriasis, dan beberapa kondisi lainnya.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">5</span>Apakah perlu membuat akun untuk menggunakan sistem?</div>
        <div class="faq-a">Ya, akun diperlukan agar riwayat pemeriksaan Anda tersimpan dan dapat dipantau perkembangannya dari waktu ke waktu.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><span class="faq-q-num">6</span>Berapa lama proses analisis berlangsung?</div>
        <div class="faq-a">Proses analisis berlangsung kurang dari 3 detik setelah foto berhasil diunggah dan diproses oleh sistem.</div>
      </div>
    </div>
  </section>

  <!-- ── FOOTER ─────────────────────────────── -->
  <footer id="kontak">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="brand-name">DermaAI</div>
        <p>Sistem Klasifikasi Penyakit Kulit berbasis Convolutional Neural Network — akurat, cepat, dan mudah diakses.</p>
        <div class="footer-socials">
          <a href="#" class="social-btn"><img src="https://cdn-icons-png.flaticon.com/512/5968/5968764.png" alt="Facebook"></a>
          <a href="#" class="social-btn"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384063.png" alt="Instagram"></a>
          <a href="#" class="social-btn"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter"></a>
        </div>
      </div>
      <div class="footer-col">
        <h5>Informasi</h5>
        <ul>
          <li><a href="#tentang">Tentang Sistem</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="#kontak">Hubungi Kami</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Layanan</h5>
        <ul>
          <li><a href="#">Analisis Foto Kulit</a></li>
          <li><a href="#">Riwayat Pemeriksaan</a></li>
          <li><a href="#">Panduan Pengguna</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Akun</h5>
        <ul>
          <li><a href="{{ route('login') }}">Masuk</a></li>
          <li><a href="#">Daftar</a></li>
          <li><a href="#">Kebijakan Privasi</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      Hak Cipta © 2026 DermaAI — Klasifikasi Penyakit Kulit CNN. Seluruh hak dilindungi.
    </div>
  </footer>

</body>
</html>