<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SPK Diabetes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --primary-green: #146135;
      --dark-blue: #153e75;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <nav class="bg-[color:var(--primary-green)] text-white px-8 py-4 flex justify-between items-center shadow-md">
    <h1 class="text-xl font-bold">Skin Disease Classification</h1>
    <ul class="flex gap-6">
      <li><a href="#faq" class="hover:underline">FAQ</a></li>
      <li><a href="#kontak" class="hover:underline">Kontak</a></li>
      <li><a href="#tentang" class="hover:underline">Tentang Kami</a></li>
      <li><a href="{{ route('login') }}" class="hover:underline">Login</a></li>
    </ul>
  </nav>

  <!-- Hero -->
  <section class="py-24 bg-green-50 relative bg-cover bg-center" style="background-image: linear-gradient(rgba(20, 97, 53, 0.85), rgba(20, 97, 53, 0.85)), url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1920&h=1080&fit=crop');">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center gap-12 relative z-10">
      <div class="md:w-1/2 text-center md:text-left">
        <h2 class="text-4xl font-bold text-white mb-6">
          Klasifikasi Penyakit Kulit Menggunakan Metode Convolutional Neural Network
        </h2>
        <p class="text-white mb-8 leading-relaxed">
          Sistem ini membantu masyarakat dalam mendeteksi penyakit kulit secara dini menggunakan metode Convolutional Neural Network (CNN) berbasis kecerdasan buatan.        </p>
        <a href="{{ route('login') }}" class="inline-block bg-white text-[color:var(--primary-green)] px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
          Mulai Deteksi Sekarang
        </a>
      </div>
      <div class="md:w-1/2">
        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop" 
             alt="Medical Health Check" 
             class="rounded-lg shadow-xl w-full object-cover">
      </div>
    </div>
  </section>

  <!-- Tentang -->
  <section id="tentang" class="py-16 px-6 md:px-16 bg-white">
    <h3 class="text-2xl font-bold text-center text-[color:var(--primary-green)] mb-6">Tentang Sistem Kami</h3>
    <p class="max-w-3xl mx-auto text-center text-gray-700 leading-relaxed">
      Sistem Pendukung Keputusan (SPK) ini dirancang untuk membantu pengguna mengenali risiko awal penyakit Diabetes Melitus berdasarkan data kesehatan seperti usia, berat badan, tekanan darah, dan faktor risiko lainnya. Dengan teknologi berbasis machine learning dan metode Multi-Criteria Decision Making (MCDM), sistem ini memberikan analisis yang akurat untuk membantu deteksi dini serta pencegahan komplikasai lebih lanjut.
    </p>
  </section>

  <!-- FAQ -->
  <section id="faq" class="py-16 px-6 md:px-16 bg-green-50">
    <h3 class="text-2xl font-bold text-center text-[color:var(--primary-green)] mb-8">FAQ (Pertanyaan Umum)</h3>
    <div class="max-w-3xl mx-auto space-y-4 text-gray-700">
      <div>
        <p class="font-semibold">1. Apa itu Sistem Klasifikasi Penyakit Kulit?</p>
        <p>
            Sistem klasifikasi penyakit kulit adalah sistem berbasis komputer yang digunakan untuk membantu pengguna mengidentifikasi atau mengklasifikasikan penyakit kulit berdasarkan data atau citra kulit menggunakan metode kecerdasan buatan.
        </p>
      </div>
      <div>
        <p class="font-semibold">2. Bagaimana sistem ini mendeteksi penyakit kulit?</p>
        <p>
            Sistem menggunakan metode Convolutional Neural Network (CNN) untuk menganalisis citra kulit dan mengklasifikasikan jenis penyakit kulit.
        </p>
      </div>
      <div>
        <p class="font-semibold">3. Apakah data saya aman?</p>
        <p>
            Ya, data dan gambar kulit pengguna disimpan dengan aman dan hanya digunakan untuk analisis oleh sistem CNN.
        </p>
      </div>
    </div>
  </section>

  <!-- Kontak -->
  <section id="kontak" class="bg-[color:var(--dark-blue)] text-white py-12 px-8 md:px-16">
    <div class="grid md:grid-cols-5 gap-8">

      <!-- Kolom 1 -->
      <div>
        <h4 class="text-2xl font-bold mb-3">Deteksi Penyakit Kulit</h4>
        <p class="text-sm">Sistem Klasifikasi Penyakit Kulit<br>Menggunakan Metode CNN</p>
        <!-- <p class="mt-3 text-sm">Jl. Letjen Suprapto No. 20<br>Jakarta Pusat 10510<br>Telp. (021) 1234-5678</p> -->
      </div>

      <!-- Kolom 2 -->
      <div>
        <h5 class="font-semibold mb-3">Informasi</h5>
        <ul class="space-y-2 text-sm">
          <li><a href="#tentang" class="hover:underline">Tentang Sistem</a></li>
          <li><a href="#faq" class="hover:underline">FAQ</a></li>
          <li><a href="#kontak" class="hover:underline">Hubungi Kami</a></li>
        </ul>
      </div>

      <!-- Kolom 3 -->
      <div>
        <h5 class="font-semibold mb-3">Layanan</h5>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:underline">Konsultasi Risiko</a></li>
          <li><a href="#" class="hover:underline">Cek Hasil Analisis</a></li>
          <li><a href="#" class="hover:underline">Panduan Pengguna</a></li>
        </ul>
      </div>

      <!-- Kolom 4 -->
      <div>
        <h5 class="font-semibold mb-3">Tautan Cepat</h5>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:underline">Login</a></li>
          <li><a href="#" class="hover:underline">Daftar Akun</a></li>
          <li><a href="#" class="hover:underline">Kebijakan Privasi</a></li>
        </ul>
      </div>

      <!-- Kolom 5 -->
      <div>
        <h5 class="font-semibold mb-3">Ikuti Kami</h5>
        <div class="flex gap-3 mt-2">
          <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/5968/5968764.png" class="w-6 h-6" alt="Facebook"></a>
          <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384063.png" class="w-6 h-6" alt="Instagram"></a>
          <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" class="w-6 h-6" alt="Twitter"></a>
        </div>
      </div>
    </div>

    <!-- Garis Hijau Footer -->
    <div class="bg-[color:var(--primary-green)] text-center py-3 mt-10 text-sm">
      Hak Cipta © 2026 Klasifikasi Kulit CNN. Seluruh hak cipta dilindungi.
    </div>
  </section>

</body>
</html>