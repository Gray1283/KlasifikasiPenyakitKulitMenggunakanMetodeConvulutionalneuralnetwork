<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-custom-green { background-color: #146135; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-lg shadow-md w-full max-w-sm">
    <!-- Header -->
    <div class="bg-custom-green h-4 rounded-t-lg"></div>

    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-1 text-gray-800">Daftar Akun Baru</h2>
        <p class="text-sm text-gray-600 mb-5">
            Mulai pantau kesehatan Anda dengan mudah
        </p>

        {{-- ❌ Error validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Register -->
        <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap"
                    class="w-full mt-1 p-2 rounded-md focus:outline-none focus:ring-1
                    {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border focus:ring-custom-green' }}"
                >

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

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

            {{-- Role (Hidden by default, show dengan ?admin=1 di URL) --}}
            @if(request()->get('admin') == '1')
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">
                    Role
                </label>
                <select
                    id="role"
                    name="role"
                    class="w-full mt-1 p-2 rounded-md focus:outline-none focus:ring-1
                    {{ $errors->has('role') ? 'border-red-500 focus:ring-red-500' : 'border focus:ring-custom-green' }}"
                >
                    <option value="pengguna" {{ old('role') == 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>

                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

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

            {{-- Button --}}
            <button
                type="submit"
                class="w-full bg-custom-green text-white py-2 rounded-md hover:opacity-90 transition">
                Daftar
            </button>
        </form>

        <p class="text-center text-sm text-gray-700 mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}"
               class="text-blue-600 font-medium hover:underline">
                Masuk
            </a>
        </p>
    </div>
</div>

</body>
</html>