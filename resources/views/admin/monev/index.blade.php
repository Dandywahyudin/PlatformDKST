@extends('layouts.admin')

@section('title', 'Monitoring & Evaluasi (Monev) Program')

@section('content')
<div class="space-y-6">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Monitoring & Evaluasi (Monev)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan berkala progres milestone, realisasi anggaran, kendala, dan penilaian capaian program inovasi DKST.</p>
        </div>
        <div class="flex items-center space-x-3">
            @can('create', \App\Models\ProgramEvaluation::class)
            <a href="{{ route('admin.monev.create') }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Input Laporan Monev
            </a>
            @endcan
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Laporan Monev</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Seluruh periode evaluasi</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider">Rata-rata Skor Penilaian</p>
            <div class="flex items-baseline space-x-2 mt-1">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['avg_score'] }}</p>
                <span class="text-xs text-slate-400">/ 100</span>
            </div>
            <p class="text-[10px] text-emerald-600/80 mt-0.5 font-medium">Kategori Kinerja Program</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-blue-500 uppercase tracking-wider">Rata-rata Progres Fisik</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['avg_progress'] }}%</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $stats['avg_progress']) }}%"></div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-indigo-500 uppercase tracking-wider">Total Realisasi Anggaran</p>
            <p class="text-xl font-bold text-indigo-700 mt-1 font-mono">Rp {{ number_format($stats['total_realization'], 0, ',', '.') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Penyerapan dana program</p>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.monev.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-4 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama program atau kode.." 
                       class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="program_id" class="w-full text-xs rounded-xl border-slate-200 py-2.5 text-slate-700">
                    <option value="">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>
                            [{{ $p->code }}] {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="evaluation_period" class="w-full text-xs rounded-xl border-slate-200 py-2.5 text-slate-700">
                    <option value="">Semua Periode</option>
                    <option value="TRIWULAN_1" {{ request('evaluation_period') === 'TRIWULAN_1' ? 'selected' : '' }}>Triwulan I (Q1)</option>
                    <option value="TRIWULAN_2" {{ request('evaluation_period') === 'TRIWULAN_2' ? 'selected' : '' }}>Triwulan II (Q2)</option>
                    <option value="TRIWULAN_3" {{ request('evaluation_period') === 'TRIWULAN_3' ? 'selected' : '' }}>Triwulan III (Q3)</option>
                    <option value="TRIWULAN_4" {{ request('evaluation_period') === 'TRIWULAN_4' ? 'selected' : '' }}>Triwulan IV (Q4)</option>
                    <option value="MIDTERM" {{ request('evaluation_period') === 'MIDTERM' ? 'selected' : '' }}>Mid-Term</option>
                    <option value="FINAL" {{ request('evaluation_period') === 'FINAL' ? 'selected' : '' }}>Final</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'program_id', 'evaluation_period']))
                    <a href="{{ route('admin.monev.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Monev Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Program & Periode</th>
                        <th class="px-4 py-4">Tanggal Evaluasi</th>
                        <th class="px-4 py-4">Progres Capaian</th>
                        <th class="px-4 py-4">Realisasi Anggaran</th>
                        <th class="px-4 py-4">Skor Evaluasi</th>
                        <th class="px-4 py-4">Evaluator</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($evaluations as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $item->program?->code }}</span>
                                <a href="{{ route('admin.monev.show', $item->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block max-w-sm truncate">
                                    {{ $item->program?->name }}
                                </a>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $item->period_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                {{ $item->evaluation_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-800">{{ $item->progress_percentage }}%</span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $item->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono font-semibold text-slate-900">
                                Rp {{ number_format($item->budget_realization, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $item->score >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($item->score >= 60 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                    {{ $item->score }} / 100
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-700">
                                <p class="font-semibold">{{ $item->evaluator?->name ?? 'Evaluator' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.monev.show', $item->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="font-medium text-slate-600">Belum ada laporan monitoring dan evaluasi.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Input Laporan Monev" untuk menambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($evaluations->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
