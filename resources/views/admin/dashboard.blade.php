@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Ringkasan & KPI')

@section('content')
<div class="space-y-6">

    <!-- Welcome & Quick Action Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-60 h-60 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-400/30 mb-2">
                    Direktorat Kawasan Sains & Teknologi ITB
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                    Portal terpadu untuk monitoring program inovasi, persetujuan usulan, manajemen dokumen, serta penugasan operasional DKST.
                </p>
            </div>
            <div class="flex items-center space-x-3 self-stretch sm:self-auto">
                <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-semibold shadow-lg shadow-blue-600/30 hover:scale-[1.02] transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Usulan Program
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Metrics Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Users -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total User</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($kpis['total_users']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Pengguna Terdaftar</p>
        </div>

        <!-- Total Programs -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Program</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($kpis['total_programs']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Seluruh Inisiatif</p>
        </div>

        <!-- Active Programs -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktif Berjalan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($kpis['active_programs']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Approved & In Progress</p>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Butuh Review</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ number_format($kpis['pending_approvals']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Menunggu Persetujuan</p>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Task</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($kpis['pending_tasks']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Todo / In Progress</p>
        </div>

        <!-- Total Documents -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dokumen</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($kpis['total_documents']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Arsip Tersimpan</p>
        </div>
    </div>

    <!-- Program Pipeline Breakdown Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pipeline Status Program DKST</h3>
                <p class="text-xs text-slate-500">Distribusi usulan dan program aktif berdasarkan fase workflow</p>
            </div>
            <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <!-- DRAFT -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Draft</span>
                <span class="text-xl font-bold text-slate-700 block mt-1">{{ $programStatusCounts['DRAFT'] }}</span>
            </div>
            <!-- SUBMITTED -->
            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-center">
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Submitted</span>
                <span class="text-xl font-bold text-blue-700 block mt-1">{{ $programStatusCounts['SUBMITTED'] }}</span>
            </div>
            <!-- UNDER_REVIEW -->
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-center">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Reviewing</span>
                <span class="text-xl font-bold text-amber-700 block mt-1">{{ $programStatusCounts['UNDER_REVIEW'] }}</span>
            </div>
            <!-- APPROVED -->
            <div class="p-3.5 rounded-2xl bg-teal-50 border border-teal-200 text-center">
                <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider block">Approved</span>
                <span class="text-xl font-bold text-teal-700 block mt-1">{{ $programStatusCounts['APPROVED'] }}</span>
            </div>
            <!-- REJECTED -->
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-center">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Rejected</span>
                <span class="text-xl font-bold text-rose-700 block mt-1">{{ $programStatusCounts['REJECTED'] }}</span>
            </div>
            <!-- IN_PROGRESS -->
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">In Progress</span>
                <span class="text-xl font-bold text-emerald-700 block mt-1">{{ $programStatusCounts['IN_PROGRESS'] }}</span>
            </div>
            <!-- COMPLETED -->
            <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-center">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Completed</span>
                <span class="text-xl font-bold text-indigo-700 block mt-1">{{ $programStatusCounts['COMPLETED'] }}</span>
            </div>
        </div>
    </div>

    <!-- Two-Column Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column Left (2 cols wide): Pending Approvals & Recent Programs -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Pending Approvals Quick-Actions -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h3 class="text-base font-bold text-slate-900">Antrean Persetujuan Program</h3>
                    </div>
                    <a href="{{ route('admin.approvals.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Semua Approval →</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($pendingApprovals as $approval)
                        <div class="p-5 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 font-mono">{{ $approval->program?->code }}</span>
                                    <span class="text-xs text-slate-400">• Diajukan oleh {{ $approval->requester?->name }}</span>
                                </div>
                                <p class="text-sm font-bold text-slate-900">{{ $approval->program?->name }}</p>
                                <p class="text-xs text-slate-500">Anggaran: Rp {{ number_format($approval->program?->budget ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.approvals.show', $approval->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                                    Review Usulan
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium">Tidak ada usulan program yang menunggu persetujuan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Programs Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Daftar Program Terbaru</h3>
                    <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Kode & Nama Program</th>
                                <th class="px-4 py-3">PIC</th>
                                <th class="px-4 py-3">Progress</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentPrograms as $program)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-[10px] text-blue-600 block">{{ $program->code }}</span>
                                        <a href="{{ route('admin.programs.show', $program->id) }}" class="text-sm font-bold text-slate-900 hover:text-blue-600 truncate block max-w-xs">
                                            {{ $program->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        {{ $program->pic_display_name }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden mb-1">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $program->progress }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-semibold text-slate-500">{{ $program->progress }}%</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $badges = [
                                                'DRAFT' => 'bg-slate-100 text-slate-700',
                                                'SUBMITTED' => 'bg-blue-100 text-blue-700',
                                                'UNDER_REVIEW' => 'bg-amber-100 text-amber-700',
                                                'APPROVED' => 'bg-teal-100 text-teal-700',
                                                'REJECTED' => 'bg-rose-100 text-rose-700',
                                                'IN_PROGRESS' => 'bg-emerald-100 text-emerald-700',
                                                'COMPLETED' => 'bg-indigo-100 text-indigo-700',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $badges[$program->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $program->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.programs.show', $program->id) }}" class="text-slate-400 hover:text-blue-600 font-medium">
                                            Detail →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada program yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Column Right (1 col wide): Tasks, Documents & System Activity -->
        <div class="space-y-6">

            <!-- Open Tasks Widget -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tugas Aktif</h3>
                    <a href="{{ route('admin.tasks.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Semua →</a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentTasks as $task)
                        <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-xs font-bold text-slate-900 leading-snug">{{ $task->title }}</p>
                                @php
                                    $pBadges = [
                                        'LOW' => 'bg-slate-200 text-slate-700',
                                        'MEDIUM' => 'bg-blue-100 text-blue-700',
                                        'HIGH' => 'bg-amber-100 text-amber-700',
                                        'URGENT' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $pBadges[$task->priority] ?? 'bg-slate-100' }}">
                                    {{ $task->priority }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2">
                                <span>{{ $task->assignee?->name ?? 'Belum ditugaskan' }}</span>
                                <span>{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Tidak ada tugas aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity / Audit Log Stream -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Sistem</h3>
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Log →</a>
                </div>

                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse ($recentActivities as $activity)
                        <div class="relative group">
                            <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-blue-600 ring-4 ring-white"></div>
                            <p class="text-xs font-semibold text-slate-800">{{ $activity->description ?? $activity->action }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity->user_name ?? 'Sistem' }} • {{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada catatan aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
