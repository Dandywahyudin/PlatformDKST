@extends('layouts.admin')

@section('title', "Laporan Monev — [{$evaluation->program?->code}]")

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Top Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.monev.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">{{ $evaluation->program?->code }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $evaluation->status_badge_class }}">{{ $evaluation->period_label }}</span>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $evaluation->program?->name }}</h2>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @can('update', $evaluation)
                <a href="{{ route('admin.monev.edit', $evaluation->id) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Edit Laporan
                </a>
            @endcan
            @can('delete', $evaluation)
                <form method="POST" action="{{ route('admin.monev.destroy', $evaluation->id) }}" onsubmit="return confirm('Hapus laporan monev ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors" title="Hapus Laporan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Metrics Highlight Card -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tanggal Evaluasi</p>
            <p class="text-base font-bold text-slate-900 mt-1">{{ $evaluation->evaluation_date->format('d F Y') }}</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-blue-500 uppercase tracking-wider">Progres Fisik</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $evaluation->progress_percentage }}%</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-indigo-500 uppercase tracking-wider">Realisasi Anggaran</p>
            <p class="text-xl font-bold text-indigo-700 mt-1 font-mono">Rp {{ number_format($evaluation->budget_realization, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider">Skor Penilaian</p>
            <div class="flex items-baseline space-x-1 mt-1">
                <p class="text-2xl font-bold text-emerald-600">{{ $evaluation->score }}</p>
                <span class="text-xs text-slate-400">/ 100</span>
            </div>
        </div>
    </div>

    <!-- Evaluation Narrative Details -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Capaian Output & Milestone:</h3>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                {{ $evaluation->achievements ?: 'Tidak ada catatan capaian khusus.' }}
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xs font-bold text-rose-500 uppercase tracking-wider mb-2">Kendala & Hambatan:</h3>
                <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100 text-xs text-rose-900 leading-relaxed whitespace-pre-line">
                    {{ $evaluation->obstacles ?: 'Tidak ada kendala kritis yang dilaporkan.' }}
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Rekomendasi & Arahan Evaluator:</h3>
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs text-blue-900 leading-relaxed whitespace-pre-line">
                    {{ $evaluation->recommendations ?: 'Belum ada catatan rekomendasi.' }}
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div>
                <span>Evaluator: </span>
                <span class="font-bold text-slate-800">{{ $evaluation->evaluator?->name ?? '-' }}</span>
            </div>
            <div>
                <span>Terakhir Diperbarui: </span>
                <span class="font-semibold">{{ $evaluation->updated_at->format('d M Y H:i') }} WIB</span>
            </div>
        </div>
    </div>

</div>
@endsection
