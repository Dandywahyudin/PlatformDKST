@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page_title', 'Edit Role')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div>
        <a href="{{ route('admin.roles.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Role
        </a>
        <h2 class="text-xl font-bold text-slate-900">Edit Role: {{ $role->name }}</h2>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Info Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informasi Dasar Role</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Role <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1">Slug / Identifier <span class="text-rose-500">*</span></label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $role->slug) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-mono @error('slug') border-rose-500 @enderror">
                    @error('slug')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Singkat Wewenang</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $role->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Permissions Assignment Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Hak Akses Granular (Permissions) <span class="text-rose-500">*</span></h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan izin aksi yang diperbolehkan untuk pengguna dengan role ini.</p>
                </div>
            </div>

            @error('permissions')
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-medium">
                    {{ $message }}
                </div>
            @enderror

            @php
                $assignedIds = old('permissions', $currentPermissionIds);
            @endphp

            <!-- Grouped Permissions by Module -->
            <div class="space-y-6">
                @foreach ($groupedPermissions as $module => $permissions)
                    <div x-data="{
                        allChecked: false,
                        checkAll() {
                            let checkboxes = this.$el.querySelectorAll('.perm-checkbox');
                            checkboxes.forEach(cb => cb.checked = this.allChecked);
                        }
                    }" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-3">
                        
                        <!-- Module Header & Select All -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span class="font-bold text-xs text-slate-900 uppercase tracking-wider">Modul {{ $module }}</span>
                            </div>
                            <label class="inline-flex items-center text-[11px] font-semibold text-slate-600 cursor-pointer hover:text-blue-600">
                                <input type="checkbox" x-model="allChecked" @change="checkAll()" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-blue-500 border-slate-300 mr-1.5">
                                Pilih Semua
                            </label>
                        </div>

                        <!-- Permissions Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            @foreach ($permissions as $perm)
                                <label class="flex items-start p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-blue-300 cursor-pointer transition-all">
                                    <div class="flex items-center h-4 mt-0.5">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                               {{ in_array($perm->id, $assignedIds) ? 'checked' : '' }}
                                               class="perm-checkbox w-3.5 h-3.5 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                                    </div>
                                    <div class="ml-2.5 text-[11px]">
                                        <span class="font-semibold text-slate-800 block">{{ $perm->name }}</span>
                                        <span class="font-mono text-[10px] text-slate-400 block">{{ $perm->slug }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
