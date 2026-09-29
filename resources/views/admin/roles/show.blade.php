@extends('layouts.admin')

@section('title', 'Detail Role & Hak Akses')
@section('page_title', 'Detail Role')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.roles.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Role
            </a>
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-bold text-slate-900">{{ $role->name }}</h2>
                <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                    {{ $role->slug }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $role->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Role & Izin
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (1 col): Assigned Users -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pengguna dengan Role Ini</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">
                        {{ $role->users->count() }} Orang
                    </span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                    @forelse ($role->users as $user)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-bold text-slate-900 hover:text-blue-600 block">
                                        {{ $user->name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 block">{{ $user->email }}</span>
                                </div>
                            </div>
                            <span class="w-2 h-2 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada pengguna yang memiliki role ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column (2 cols): Permissions List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Daftar Hak Akses yang Diberikan</h3>
                    <span class="text-xs font-semibold text-slate-500">{{ $role->permissions->count() }} Permission Aktif</span>
                </div>

                <div class="space-y-6">
                    @forelse ($groupedPermissions as $module => $permissions)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <h4 class="font-bold text-xs text-slate-900 uppercase tracking-wider">Modul {{ $module }}</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($permissions as $perm)
                                    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-start space-x-2">
                                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <span class="text-xs font-semibold text-slate-800 block">{{ $perm->name }}</span>
                                            <span class="font-mono text-[10px] text-slate-400 block">{{ $perm->slug }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Role ini belum memiliki hak akses.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
