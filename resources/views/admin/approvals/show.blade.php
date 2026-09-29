@extends('layouts.admin')

@section('title', 'Review Persetujuan Usulan')
@section('page_title', 'Tinjau Usulan')

@section('content')
@php
    $isDirectorPortal = request()->routeIs('director.*') || (auth()->user()?->isDirector() && !auth()->user()?->isAdmin());
    $approvalIndexRoute = $isDirectorPortal ? 'director.approvals.index' : 'admin.approvals.index';
    $approvalApproveRoute = $isDirectorPortal ? 'director.approvals.approve' : 'admin.approvals.approve';
    $approvalRejectRoute = $isDirectorPortal ? 'director.approvals.reject' : 'admin.approvals.reject';
    $programShowRoute = $isDirectorPortal ? 'director.programs.show' : 'admin.programs.show';
    $documentDownloadRoute = $isDirectorPortal ? 'director.documents.download' : 'admin.documents.download';
    $documentPreviewRoute = $isDirectorPortal ? 'director.documents.preview' : 'admin.documents.preview';
@endphp
<div x-data="{ rejectModalOpen: false, approveModalOpen: false }" class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route($approvalIndexRoute) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Antrean Approval
            </a>
            <div class="flex items-center space-x-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Tinjau Usulan: {{ $approval->program?->name }}</h2>
            </div>
            <span class="font-mono text-xs text-blue-600 font-bold mt-1 block">Kode: {{ $approval->program?->code }}</span>
        </div>

        <!-- Action Buttons (When Pending) -->
        @if ($approval->status === 'PENDING')
            <div class="flex items-center space-x-2">
                <button @click="rejectModalOpen = true" class="px-4 py-2.5 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-bold transition-all shadow-sm">
                    Tolak Usulan
                </button>
                <button @click="approveModalOpen = true" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition-all">
                    ✓ Setujui Program
                </button>
            </div>
        @endif
    </div>

    <!-- Status Resolution Banner (When already Processed) -->
    @if ($approval->status === 'APPROVED')
        <div class="bg-teal-50 border border-teal-200 p-5 rounded-3xl flex items-start space-x-3 text-teal-900">
            <svg class="w-6 h-6 text-teal-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold text-sm">Usulan Telah Disetujui (Approved)</h3>
                <p class="text-xs text-teal-700 mt-0.5">Disetujui oleh <span class="font-semibold">{{ $approval->reviewer?->name }}</span> pada {{ $approval->processed_at?->format('d M Y H:i') }}.</p>
                <div class="mt-2">
                    <a href="{{ route($programShowRoute, $approval->program_id) }}" class="text-xs font-bold text-teal-800 underline">Lihat Halaman Detail Program →</a>
                </div>
            </div>
        </div>
    @elseif ($approval->status === 'REJECTED')
        <div class="bg-rose-50 border border-rose-200 p-5 rounded-3xl flex items-start space-x-3 text-rose-900">
            <svg class="w-6 h-6 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold text-sm">Usulan Telah Ditolak (Rejected)</h3>
                <p class="text-xs text-rose-700 mt-0.5">Ditolak oleh <span class="font-semibold">{{ $approval->reviewer?->name }}</span> pada {{ $approval->processed_at?->format('d M Y H:i') }}.</p>
                <div class="mt-2 p-3 bg-white/80 rounded-xl border border-rose-100 text-xs text-rose-800">
                    <span class="font-bold block mb-0.5">Catatan Alasan Penolakan:</span>
                    "{{ $approval->reason }}"
                </div>
            </div>
        </div>
    @endif

    <!-- Proposal Details Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Anggaran yang Diajukan</span>
            <div class="text-xl font-bold font-mono text-slate-900 mt-1">
                Rp {{ number_format($approval->program?->budget ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Pengusul Program</span>
            <div class="text-sm font-bold text-slate-900 mt-1">
                {{ $approval->requester?->name }}
            </div>
            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $approval->created_at->format('d M Y H:i') }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Penanggung Jawab (PIC)</span>
            <div class="text-sm font-bold text-slate-900 mt-1">
                {{ $approval->program?->pic_display_name }}
            </div>
            <span class="text-[11px] text-slate-400 mt-0.5 block">
                {{ $approval->program?->start_date ? $approval->program->start_date->format('d M Y') : 'TBD' }} – {{ $approval->program?->end_date ? $approval->program->end_date->format('d M Y') : 'TBD' }}
            </span>
        </div>
    </div>

    <!-- Description & Scope -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ruang Lingkup & Latar Belakang Usulan</h3>
        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">
            {{ $approval->program?->description ?? 'Tidak ada deskripsi tambahan pada usulan ini.' }}
        </div>
    </div>

    <!-- Attached Documents -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Dokumen Pendukung Usulan</h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                {{ $approval->program?->documents->count() ?? 0 }} Berkas
            </span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($approval->program?->documents ?? [] as $doc)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl {{ in_array(strtolower($doc->file_type ?? ''), ['pdf']) ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center font-bold text-[10px] uppercase shrink-0">
                            {{ $doc->file_type ?? 'DOC' }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $doc->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $doc->formatted_size }} • Kategori: {{ $doc->category }} • Oleh: {{ $doc->uploader?->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                        @if(in_array(strtolower($doc->file_type ?? ''), ['pdf', 'png', 'jpg', 'jpeg']))
                            <a href="{{ route($documentPreviewRoute, $doc->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Lihat
                            </a>
                        @endif
                        <a href="{{ route($documentDownloadRoute, $doc->id) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold inline-flex items-center transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada dokumen proposal yang dilampirkan.</p>
            @endforelse
        </div>
    </div>

    <!-- Approval Actions Timeline -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Riwayat Log Aksi Approval</h3>
        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @foreach ($approval->actions as $act)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full {{ $act->action === 'APPROVE' ? 'bg-teal-500' : ($act->action === 'REJECT' ? 'bg-rose-500' : 'bg-blue-600') }} ring-4 ring-white"></div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-slate-900">{{ $act->action }}</span>
                            <span class="text-[10px] text-slate-400">• oleh {{ $act->user?->name }}</span>
                        </div>
                        @if ($act->comment)
                            <p class="text-xs text-slate-600 mt-1 italic bg-slate-50 p-2 rounded-xl border border-slate-100">"{{ $act->comment }}"</p>
                        @endif
                        <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $act->created_at->format('d M Y H:i:s') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL: Approve Confirmation -->
    <div x-show="approveModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div @click.outside="approveModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Persetujuan Usulan</h3>
            <p class="text-xs text-slate-600">
                Apakah Anda yakin ingin menyetujui program <span class="font-semibold text-slate-900">[{{ $approval->program?->code }}] {{ $approval->program?->name }}</span>?
            </p>

            <form method="POST" action="{{ route($approvalApproveRoute, $approval->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="comment" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="comment" id="comment" rows="3" placeholder="Catatan persetujuan dari reviewer/direktur..."
                              class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="approveModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30">
                        Ya, Setujui Program
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Reject Confirmation -->
    <div x-show="rejectModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div @click.outside="rejectModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <h3 class="text-base font-bold text-rose-600">Tolak Usulan Program</h3>
            <p class="text-xs text-slate-600">
                Sesuai standar operasional, Anda <span class="font-semibold text-slate-900">wajib mencantumkan alasan penolakan / catatan revisi</span> untuk pengusul.
            </p>

            <form method="POST" action="{{ route($approvalRejectRoute, $approval->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan / Catatan Perbaikan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" id="reason" rows="4" required placeholder="Contoh: Rincian anggaran biaya perlu disesuaikan dengan standar ITB..."
                              class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-md shadow-rose-600/30">
                        Tolak Usulan Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
