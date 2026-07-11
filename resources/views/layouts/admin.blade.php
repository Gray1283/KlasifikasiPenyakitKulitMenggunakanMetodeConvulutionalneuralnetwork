<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('pageTitle', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-[#f9f7f2] antialiased">

<div class="flex h-screen">

    {{-- SIDEBAR --}}
    @include('components.admin-sidebar')

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- TOPBAR --}}
        <header class="bg-white border-b-2 border-[#146135]/10 px-6 py-3 flex justify-between items-center shrink-0">
            <h1 class="text-base font-semibold text-gray-700">
                @yield('pageTitle', 'Dashboard Admin')
            </h1>

            {{-- Kanan: info user + logout --}}
            <div class="flex items-center gap-3">
                <div class="w-px h-6 bg-gray-200"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
                        <i class="fas fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-auto bg-[#f9f7f2]">
            <div class="p-6 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

    </div>

</div>
@stack('scripts')
</body>
</html>