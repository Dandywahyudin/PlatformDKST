@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page_title', 'Detail Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pengguna
            </a>
            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Akun
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column Left: Profile Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 mx-auto mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h3 class="text-base font-bold text-slate-900">{{ $user->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
                <p class="text-xs font-semibold text-blue-600 mt-1">{{ $user->position ?? 'Staff DKST' }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ $user->status === 'active' ? 'Akun Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mt-6 text-left space-y-3 pt-4 border-t border-slate-100 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. Telepon</span>
                        <span class="font-medium text-slate-800">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Terdaftar Sejak</span>
                        <span class="font-medium text-slate-800">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Login Terakhir</span>
                        <span class="font-medium text-slate-800">{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Role & Wewenang</h4>
                <div class="space-y-3">
                    @forelse ($user->roles as $role)
                        <div class="p-3 rounded-2xl bg-blue-50/50 border border-blue-100">
                            <span class="font-bold text-xs text-blue-900">{{ $role->name }}</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $role->description }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada role yang diberikan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column Right: Related Programs, Tasks & Audit Activity -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Program Terkait -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <h4 class="text-sm font-bold text-slate-900 mb-4">Program Terkait (PIC & Pembuat)</h4>
                @php
                    $allPrograms = $user->programsCreated->merge($user->programsLead)->unique('id');
                @endphp

                <div class="space-y-3">
                    @forelse ($allPrograms as $program)
                        <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <div>
                                <span class="font-mono text-[10px] text-blue-600 block">{{ $program->code }}</span>
                                <a href="{{ route('admin.programs.show', $program->id) }}" class="text-xs font-bold text-slate-900 hover:text-blue-600">
                                    {{ $program->name }}
                                </a>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $program->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada program yang terkait dengan pengguna ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Aktivitas Audit -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <h4 class="text-sm font-bold text-slate-900 mb-4">Aktivitas Terkini Pengguna</h4>
                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse ($recentActivities as $activity)
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-blue-600 ring-4 ring-white"></div>
                            <p class="text-xs font-semibold text-slate-800">{{ $activity->description ?? $activity->action }}</p>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $activity->created_at->format('d M Y H:i:s') }} (IP: {{ $activity->ip_address ?? '127.0.0.1' }})</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada catatan aktivitas dari pengguna ini.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
