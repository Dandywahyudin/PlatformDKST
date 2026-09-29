@extends('layouts.admin')

@section('title', $program->name)
@section('page_title', 'Detail Program')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation & Status Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Program
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold border border-blue-200">
                    {{ $program->code }}
                </span>
                @php
                    $statusStyles = [
                        'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-200',
                        'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'UNDER_REVIEW' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'APPROVED' => 'bg-teal-50 text-teal-700 border-teal-200',
                        'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'IN_PROGRESS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'COMPLETED' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusStyles[$program->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $program->status }}
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">{{ $program->name }}</h2>
        </div>

        <!-- Quick Actions Row -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Workflow State Actions -->
            @if (in_array($program->status, ['DRAFT', 'REJECTED']))
                <form method="POST" action="{{ route('admin.programs.submit', $program->id) }}" onsubmit="return confirm('Ajukan usulan program ini untuk direview oleh pimpinan/reviewer?');" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ajukan Usulan (Submit)
                    </button>
                </form>
            @elseif ($program->status === 'SUBMITTED' || $program->status === 'UNDER_REVIEW')
                @if ($program->latestApproval)
                    <a href="{{ route('admin.approvals.show', $program->latestApproval->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Buka Review Approval
                    </a>
                @endif
            @elseif ($program->status === 'APPROVED')
                <form method="POST" action="{{ route('admin.programs.start', $program->id) }}" onsubmit="return confirm('Mulai pelaksanaan program ini secara resmi?');" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Mulai Pelaksanaan (Start)
                    </button>
                </form>
            @elseif ($program->status === 'IN_PROGRESS')
                <form method="POST" action="{{ route('admin.programs.complete', $program->id) }}" onsubmit="return confirm('Tandai program ini telah selesai 100%?');" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Tandai Selesai (Complete)
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.programs.edit', $program->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Program
            </a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Budget -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Anggaran Biaya</span>
            <div class="text-xl font-bold font-mono text-slate-900 mt-1">
                Rp {{ number_format($program->budget, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Alokasi Dana Kegiatan</span>
        </div>

        <!-- Progress -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Progres Realisasi</span>
            <div class="flex items-center justify-between mt-1 mb-2">
                <span class="text-xl font-bold text-blue-600 font-mono">{{ $program->progress }}%</span>
                <span class="text-[11px] text-slate-400">{{ $program->status === 'COMPLETED' ? 'Selesai' : 'Sedang Berjalan' }}</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $program->progress }}%"></div>
            </div>
        </div>

        <!-- PIC -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Penanggung Jawab (PIC)</span>
            <div class="text-base font-bold text-slate-900 mt-1 truncate">
                {{ $program->pic_display_name }}
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">{{ $program->pic?->position ?? 'Koordinator Program' }}</span>
        </div>

        <!-- Period -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Jadwal Pelaksanaan</span>
            <div class="text-xs font-semibold text-slate-800 mt-1">
                {{ $program->start_date ? $program->start_date->format('d M Y') : 'TBD' }}
                – 
                {{ $program->end_date ? $program->end_date->format('d M Y') : 'TBD' }}
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Diusulkan oleh {{ $program->creator?->name ?? 'Sistem' }}</span>
        </div>
    </div>

    <!-- Two Column Detail Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column Left (2 cols): Description, Documents & Tasks -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Description Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Deskripsi & Ruang Lingkup Program</h3>
                <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $program->description ?? 'Belum ada deskripsi yang dicantumkan untuk program ini.' }}
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Dokumen & Berkas Terlampir</h3>
                        <p class="text-xs text-slate-500">Proposal, TOR, RAB, dan Surat Persetujuan</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                        {{ $program->documents->count() }} Berkas
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($program->documents as $doc)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] uppercase">
                                    {{ $doc->file_type ?? 'DOC' }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $doc->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $doc->formatted_size }} • Diunggah oleh {{ $doc->uploader?->name }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                {{ $doc->category }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Belum ada dokumen yang diunggah untuk program ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Task & Pekerjaan Terkait</h3>
                        <p class="text-xs text-slate-500">Penugasan aktivitas dan deadline tim program</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700">
                        {{ $program->tasks->count() }} Task
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse ($program->tasks as $task)
                        <div class="p-3 rounded-2xl bg-slate-50/70 border border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $task->title }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">PIC: {{ $task->assignee?->name ?? 'Belum ada' }} • Deadline: {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                {{ $task->status }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Belum ada penugasan task untuk program ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Column Right (1 col): Team Members & Workflow Timeline -->
        <div class="space-y-6">

            <!-- Team Members Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Anggota Tim Pelaksana</h3>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                        {{ $program->members->count() }} Anggota
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($program->members as $member)
                        <div class="py-2.5 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $member->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ $member->position ?? 'Anggota Tim' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada anggota tim yang ditambahkan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Workflow & Approvals History -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Riwayat Approval & Status</h3>
                
                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse ($program->approvals as $approval)
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full {{ $approval->status === 'APPROVED' ? 'bg-teal-500' : ($approval->status === 'REJECTED' ? 'bg-rose-500' : 'bg-amber-500') }} ring-4 ring-white"></div>
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-slate-900 block">Status: {{ $approval->status }}</span>
                                <p class="text-[11px] text-slate-500">Diajukan oleh: {{ $approval->requester?->name }}</p>
                                @if ($approval->reviewer)
                                    <p class="text-[11px] text-slate-500">Reviewer: {{ $approval->reviewer->name }}</p>
                                @endif
                                @if ($approval->reason)
                                    <p class="text-xs text-slate-700 bg-slate-50 p-2 rounded-xl border border-slate-100 mt-1 italic">"{{ $approval->reason }}"</p>
                                @endif
                                <span class="text-[10px] text-slate-400 block">{{ $approval->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-slate-400 ring-4 ring-white"></div>
                            <span class="text-xs font-semibold text-slate-800 block">Draft Usulan Dibuat</span>
                            <span class="text-[10px] text-slate-400 block">{{ $program->created_at->format('d M Y H:i') }}</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
