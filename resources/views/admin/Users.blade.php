@extends('layouts.admin')

@section('pageTitle', 'Manajemen User')

@section('content')
<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen User</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola akun pengguna sistem</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-white transition-colors hover:opacity-90"
                style="background:#146135;">
            <i class="fas fa-plus text-xs"></i>
            Tambah User
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 text-green-700 text-sm border border-green-100">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-100">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

   <div class="grid grid-cols-3 gap-4 mb-6">

    <!-- Total User -->
    <div class="bg-white rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
        <div class="bg-green-100 text-green-700 p-3 rounded-lg">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">Total User</p>
            <p class="text-2xl font-semibold" style="color:#146135;">{{ $users->count() }}</p>
        </div>
    </div>

    <!-- Pengguna -->
    <div class="bg-white rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
        <div class="bg-slate-100 text-slate-500 p-3 rounded-lg">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">Pengguna</p>
            <p class="text-2xl font-semibold text-gray-700">{{ $users->where('role','pengguna')->count() }}</p>
        </div>
    </div>

    <!-- Admin -->
    <div class="bg-white rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
        <div class="bg-amber-100 text-amber-600 p-3 rounded-lg">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">Admin</p>
            <p class="text-2xl font-semibold text-amber-600">{{ $users->where('role','admin')->count() }}</p>
        </div>
    </div>

</div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
            <p class="text-sm font-medium text-gray-600">Daftar Pengguna</p>
            <span class="text-xs text-gray-400">{{ $users->count() }} user terdaftar</span>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#f0f7f2;">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-gray-50 hover:bg-gray-50/60 transition-colors">
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                                 style="background: {{ $user->role === 'admin' ? '#b8860b' : '#146135' }};">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-700 text-sm">{{ $user->name }}</p>
                                @if($user->id_user === auth()->user()->id_user)
                                    <p class="text-[10px] text-green-600">Anda</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-sm">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-medium
                            {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-gray-100 text-gray-500' }}">
                            @if($user->role === 'admin')
                                <i class="fas fa-shield-halved text-[9px] mr-0.5"></i>
                            @endif
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="openEdit({{ $user->id_user }}, '{{ addslashes($user->name) }}', '{{ $user->role }}')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-green-700 hover:bg-green-50 hover:text-green-800 transition-colors border border-green-100">
                                <i class="fas fa-pen text-xs"></i>
                                Edit
                            </button>
                            @if($user->id_user !== auth()->user()->id_user)
                            <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST"
                                  onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors border border-red-100">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                    Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-200">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <i class="fas fa-users text-3xl text-gray-200 mb-3 block"></i>
                        <p class="text-gray-400 text-sm">Belum ada user terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center"
     style="background:rgba(0,0,0,0.35);">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Tambah User Baru</h3>
                <p class="text-xs text-gray-400 mt-0.5">Isi data pengguna dengan lengkap</p>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="px-6 py-5 flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Masukkan nama lengkap"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                @error('name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="contoh@email.com"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                @error('email')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Password</label>
                    <input type="password" name="password"
                           placeholder="Min. 6 karakter"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                    @error('password')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Konfirmasi</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Ulangi password"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Role</label>
                <select name="role"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:border-green-400 transition-colors">
                    <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button"
                        onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-colors hover:opacity-90"
                        style="background:#146135;">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center"
     style="background:rgba(0,0,0,0.35);">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Edit User</h3>
                <p class="text-xs text-gray-400 mt-0.5">Ubah data pengguna</p>
            </div>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <form id="formEdit" method="POST" class="px-6 py-5 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Lengkap</label>
                <input type="text" id="editName" name="name"
                       placeholder="Masukkan nama lengkap"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Role</label>
                <select id="editRole" name="role"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:border-green-400 transition-colors">
                    <option value="pengguna">Pengguna</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="border-t border-gray-50 pt-4">
                <p class="text-xs font-medium text-gray-500 mb-3">
                    Reset Password
                    <span class="text-gray-300 font-normal">(opsional)</span>
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="password" name="password"
                               placeholder="Password baru"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                    </div>
                    <div>
                        <input type="password" name="password_confirmation"
                               placeholder="Konfirmasi"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-green-400 transition-colors placeholder:text-gray-300">
                    </div>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button"
                        onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-colors hover:opacity-90"
                        style="background:#146135;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>
    document.getElementById('modalTambah').classList.remove('hidden');
</script>
@endif

<script>
function openEdit(id, name, role) {
    document.getElementById('editName').value = name;
    document.getElementById('editRole').value = role;
    document.getElementById('formEdit').action = '/admin/users/' + id;
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>

@endsection