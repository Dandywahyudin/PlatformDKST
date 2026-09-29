@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pengguna
            </a>
            <h2 class="text-xl font-bold text-slate-900">Edit Pengguna: {{ $user->name }}</h2>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section: Data Akun -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informasi Akun</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="password"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Section: Profil & Organisasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Profil & Status</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-xs font-semibold text-slate-700 mb-1">Jabatan / Posisi</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Akun <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section: Role & Hak Akses -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-2">Penugasan Role <span class="text-rose-500">*</span></h3>
                <p class="text-xs text-slate-500 mb-4">Pilih role hak akses untuk pengguna ini.</p>
                
                @php
                    $currentRoleIds = old('roles', $user->roles->pluck('id')->all());
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($roles as $role)
                        <label class="relative flex items-start p-3.5 rounded-2xl border border-slate-200 hover:border-blue-300 bg-slate-50/50 hover:bg-blue-50/30 cursor-pointer transition-all">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       {{ in_array($role->id, $currentRoleIds) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            </div>
                            <div class="ml-3 text-xs">
                                <span class="font-bold text-slate-900 block">{{ $role->name }}</span>
                                <span class="text-slate-500 text-[11px] block mt-0.5">{{ $role->description ?? 'Hak akses modul terkait.' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="text-[11px] text-rose-500 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
