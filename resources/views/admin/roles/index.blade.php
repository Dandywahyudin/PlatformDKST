@extends('layouts.admin')

@section('title', 'Role & Hak Akses')
@section('page_title', 'Manajemen Role')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Role & Hak Akses Pengguna</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola tingkat wewenang dan izin granular untuk setiap peran di platform DKST.</p>
        </div>
        <div>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Role Baru
            </a>
        </div>
    </div>

    <!-- Role Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($roles as $role)
            @php
                $isCoreRole = in_array(strtolower($role->slug), ['admin', 'director', 'staff']);
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between">
                <div>
                    <!-- Top Badges -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $role->slug === 'admin' ? 'bg-indigo-600' : 'bg-blue-500' }}"></span>
                            <span class="font-mono text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ $role->slug }}</span>
                        </div>
                        @if ($isCoreRole)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                Sistem Inti
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Kustom
                            </span>
                        @endif
                    </div>

                    <!-- Role Name & Description -->
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $role->name }}</h3>
                    <p class="text-xs text-slate-500 min-h-[32px]">{{ $role->description ?? 'Tidak ada deskripsi tambahan.' }}</p>

                    <!-- Metrics Stats -->
                    <div class="grid grid-cols-2 gap-3 my-5 pt-4 border-t border-slate-100">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase block">Hak Akses</span>
                            <span class="text-base font-bold text-slate-900 block mt-0.5">
                                {{ $role->permissions_count }} / {{ $totalPermissions }}
                            </span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase block">Pengguna</span>
                            <span class="text-base font-bold text-slate-900 block mt-0.5">
                                {{ $role->users_count }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('admin.roles.show', $role->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat Izin →
                    </a>
                    <div class="flex items-center space-x-1">
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="p-2 text-slate-400 hover:text-amber-600 rounded-xl hover:bg-slate-100 transition-colors" title="Edit Role">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>

                        @if (! $isCoreRole)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->name }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-slate-100 transition-colors" title="Hapus Role">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
