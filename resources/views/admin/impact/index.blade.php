@extends('layouts.admin')

@section('title', 'Kinerja & Dampak Inovasi DKST')

@section('content')
<div class="space-y-8">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kinerja & Dampak Inovasi DKST</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan capaian indikator kinerja utama (IKU), dampak hilirisasi riset, pertumbuhan startup, dan komersialisasi teknologi.</p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Year Selector -->
            <form method="GET" action="{{ route('admin.impact.index') }}" class="flex items-center space-x-2">
                <select name="year" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border-slate-200 py-2 px-3 bg-white text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </form>

            @can('manage', \App\Models\ImpactMetric::class)
            <a href="{{ route('admin.impact.create') }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Indikator
            </a>
            @endcan
        </div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-900 to-indigo-950 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[11px] font-semibold tracking-wider uppercase text-blue-300">Rata-rata Ketercapaian IKU ({{ $selectedYear }})</p>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-extrabold">{{ $averageAchievement }}%</span>
                    <span class="text-xs text-blue-200 font-medium">dari target tahunan</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 mt-4 overflow-hidden">
                    <div class="bg-blue-400 h-2 rounded-full" style="width: {{ min(100, $averageAchievement) }}%"></div>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-wider uppercase text-emerald-600">Target Tercapai & Melampaui</p>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $achievedCount }}</span>
                    <span class="text-xs text-slate-400">/ {{ $totalMetricsCount }} Indikator</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">
                {{ $totalMetricsCount > 0 ? round(($achievedCount / $totalMetricsCount) * 100, 1) : 0 }}% indikator dampak telah memenuhi target.
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-wider uppercase text-indigo-600">Fokus Pilar Dampak DKST</p>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-900">6</span>
                    <span class="text-xs text-slate-400">Kategori Strategis</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">Inkubasi, Paten/HKI, Komersialisasi, Talenta, Pendanaan & Mitra.</p>
        </div>
    </div>

    <!-- Impact Metrics Cards by Category -->
    <div class="space-y-6">
        <h3 class="text-base font-bold text-slate-900">Rincian Capaian Indikator Kinerja & Dampak (Tahun {{ $selectedYear }})</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($metrics as $item)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $item->category_label }}
                            </span>
                            <span class="text-xs font-bold {{ $item->achievement_percentage >= 100 ? 'text-emerald-600' : ($item->achievement_percentage >= 75 ? 'text-blue-600' : 'text-amber-600') }}">
                                {{ $item->achievement_percentage }}%
                            </span>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-900 line-clamp-2">{{ $item->metric_name }}</h4>
                            @if($item->description)
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $item->description }}</p>
                            @endif
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $item->achievement_percentage >= 100 ? 'bg-emerald-500' : ($item->achievement_percentage >= 75 ? 'bg-blue-600' : 'bg-amber-500') }}" 
                                 style="width: {{ min(100, $item->achievement_percentage) }}%"></div>
                        </div>

                        <!-- Target vs Realized Details -->
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                            <div>
                                <p class="text-[10px] uppercase font-semibold text-slate-400">Target:</p>
                                <p class="font-bold text-slate-800 font-mono">
                                    @if(str_contains(strtolower($item->unit), 'rupiah'))
                                        Rp {{ number_format($item->target_value, 0, ',', '.') }}
                                    @else
                                        {{ number_format($item->target_value, 0, ',', '.') }} {{ $item->unit }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-semibold text-slate-400">Realisasi:</p>
                                <p class="font-bold text-slate-900 font-mono">
                                    @if(str_contains(strtolower($item->unit), 'rupiah'))
                                        Rp {{ number_format($item->realized_value, 0, ',', '.') }}
                                    @else
                                        {{ number_format($item->realized_value, 0, ',', '.') }} {{ $item->unit }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Manage Actions -->
                    @can('manage', \App\Models\ImpactMetric::class)
                    <div class="flex items-center justify-end space-x-2 pt-4 mt-4 border-t border-slate-100">
                        <a href="{{ route('admin.impact.edit', $item->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-slate-50 transition-colors" title="Edit Indikator">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.impact.destroy', $item->id) }}" onsubmit="return confirm('Hapus indikator ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Indikator">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                    <p class="font-medium text-slate-600">Belum ada data indikator kinerja dampak untuk tahun {{ $selectedYear }}.</p>
                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Indikator" untuk memulai pencatatan target & realisasi dampak.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
