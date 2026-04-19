<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-custom-green { background-color: #146135; }
        .text-custom-green { color: #146135; }
        .border-custom-green { border-color: #146135; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-lg shadow-md w-full max-w-sm">
    <div class="bg-custom-green h-4 rounded-t-lg"></div>

    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-1 text-gray-800">Masuk</h2>
        <p class="text-sm text-gray-600 mb-5">
            Deteksi kesehatan Anda sejak dini <span class="text-green-600">💚</span>
        </p>

        {{-- ❌ Error login --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- ✅ Success message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    class="w-full mt-1 p-2 rounded-md focus:outline-none focus:ring-1
                    {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border focus:ring-custom-green' }}"
                >

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    class="w-full mt-1 p-2 rounded-md focus:outline-none focus:ring-1
                    {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500' : 'border focus:ring-custom-green' }}"
                >

                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lupa Password --}}
            <div class="text-left">
                <a href="{{ route('forgot.password') }}"
                   class="text-sm text-blue-600 hover:underline">
                    Lupa Password?
                </a>
            </div>

            {{-- Button --}}
            <button
                type="submit"
                class="w-full bg-custom-green text-white py-2 rounded-md hover:opacity-90 transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-700 mt-5">
            Belum Punya Akun?
            <a href="{{ route('register') }}"
               class="text-blue-600 font-medium hover:underline">
                Daftar
            </a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('logout'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Logout Berhasil',
        // text: '{{ session('logout') }}',
        confirmButtonColor: '#16a34a',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif
</body>
</html>
