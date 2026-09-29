@extends('layouts.admin')

@section('title', 'Dashboard Direktur — DKST ITB')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10 print:p-0 print:space-y-4">

    <!-- 1. HEADER SECTION -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4 print:border-none print:shadow-none print:p-0">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <span>DKST ITB</span>
                <span>•</span>
                <span class="text-blue-700">Eksekutif</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Dashboard Direktur</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ringkasan Eksekutif Program, Pengambilan Keputusan Strategis & Capaian IKU</p>
        </div>

        <!-- Filter, Export & Identity -->
        <div class="flex flex-wrap items-center gap-3 print:hidden">
            <!-- Period Filter -->
            <form method="GET" action="{{ route('director.dashboard') }}" class="flex items-center space-x-2">
                <label for="year-select" class="text-xs font-medium text-slate-600">Tahun:</label>
                <select id="year-select" name="year" onchange="this.form.submit()" class="text-xs font-semibold rounded-lg border-slate-300 bg-white py-1.5 pl-3 pr-8 text-slate-800 shadow-sm focus:border-blue-600 focus:ring-1 focus:ring-blue-600 cursor-pointer">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Export / Print -->
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs border border-slate-300 transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Laporan</span>
            </button>

            <!-- User Badge -->
            <div class="hidden sm:flex items-center space-x-2.5 pl-3 border-l border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-slate-800 text-white font-semibold text-xs flex items-center justify-center">
                    {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                </div>
                <div class="text-left leading-tight">
                    <p class="text-xs font-semibold text-slate-900">{{ Auth::user()->name ?? 'Direktur DKST' }}</p>
                    <span class="text-[11px] text-slate-500 font-normal">Direktur</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. STRATEGIC ALERT BAR (Action Required) -->
    @if($pendingApprovalsCount > 0 || $attentionProgramsCount > 0)
        <div class="bg-white rounded-xl p-4 border-l-4 border-l-amber-500 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start space-x-3">
                <div class="p-1 rounded bg-amber-50 text-amber-700 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Perhatian Direktur</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Terdapat <strong class="text-slate-900">{{ $pendingApprovalsCount }} usulan</strong> menunggu persetujuan dan <strong class="text-slate-900">{{ $attentionProgramsCount }} program</strong> memerlukan intervensi evaluasi.
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                @if($pendingApprovalsCount > 0)
                    <a href="{{ route('director.approvals.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-colors shadow-sm">
                        Proses Persetujuan ({{ $pendingApprovalsCount }}) &rarr;
                    </a>
                @endif
                <a href="{{ route('director.programs.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors">
                    Lihat Program
                </a>
            </div>
        </div>
    @endif

    <!-- 3. STRATEGIC METRICS SUMMARY (5 KPI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Card 1: Total Portofolio -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Portofolio</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="mt-2">
                <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $totalPrograms }} <span class="text-xs font-normal text-slate-400">program</span></p>
                <p class="text-xs text-slate-500 mt-1">Total Alokasi: <span class="font-semibold text-slate-700">Rp {{ number_format($totalBudget / 1000000, 0, ',', '.') }} Jt</span></p>
            </div>
        </div>

        <!-- Card 2: Realisasi Anggaran -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Realisasi Anggaran</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $budgetRealizationPct }}%</p>
                    <span class="text-xs text-slate-500">Rp {{ number_format($realizedBudget / 1000000, 0, ',', '.') }} Jt</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-slate-700 h-1.5 rounded-full" style="width: {{ min(100, $budgetRealizationPct) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Progres Program -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Rata-rata Progres</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $avgProgress }}%</p>
                    <span class="text-xs text-slate-500">{{ $inProgressPrograms }} aktif berjalan</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $avgProgress) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 4: Capaian IKU -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Capaian IKU Inovasi</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $impactAvgAchievement }}%</p>
                    <span class="text-xs font-medium text-emerald-700">{{ $impactAchievedCount }}/{{ $impactTotalCount }} Tercapai</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $impactAvgAchievement) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 5: Antrean Keputusan -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Antrean Persetujuan</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl font-bold {{ $pendingApprovalsCount > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight">
                        {{ $pendingApprovalsCount }} <span class="text-xs font-normal text-slate-400">usulan</span>
                    </p>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($pendingApprovalsCount > 0)
                        <a href="{{ route('director.approvals.index') }}" class="font-medium text-blue-600 hover:text-blue-800">Review Usulan &rarr;</a>
                    @else
                        <span>Tidak ada antrean</span>
                    @endif
                </p>
            </div>
        </div>

    </div>

    <!-- 4. SIKLUS PORTOFOLIO & KESEHATAN PELAKSANAAN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 4A. Distribusi Status Portofolio (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-xl p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Distribusi Status Portofolio</h2>
                    <p class="text-xs text-slate-500">Sebaran tahapan program inovasi DKST ITB.</p>
                </div>
                <a href="{{ route('director.programs.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                    Buka Portofolio &rarr;
                </a>
            </div>

            <div class="space-y-2.5 pt-1">
                @php
                    $statusMeta = [
                        'IN_PROGRESS' => ['label' => 'Sedang Berjalan (In Progress)', 'color' => 'bg-blue-600'],
                        'APPROVED' => ['label' => 'Disetujui / Siap Jalan', 'color' => 'bg-teal-600'],
                        'COMPLETED' => ['label' => 'Selesai (Completed)', 'color' => 'bg-emerald-600'],
                        'SUBMITTED' => ['label' => 'Menunggu Persetujuan', 'color' => 'bg-amber-500'],
                        'DRAFT' => ['label' => 'Draf Usulan', 'color' => 'bg-slate-400'],
                        'REJECTED' => ['label' => 'Perlu Revisi', 'color' => 'bg-rose-500'],
                    ];
                @endphp

                @foreach($statusMeta as $stKey => $meta)
                    @php
                        $stCount = $statusDistribution[$stKey] ?? 0;
                        $stPct = $totalPrograms > 0 ? round(($stCount / $totalPrograms) * 100) : 0;
                    @endphp
                    <div class="flex items-center text-xs py-1 border-b border-slate-50 last:border-none">
                        <div class="w-48 font-medium text-slate-700 truncate">{{ $meta['label'] }}</div>
                        <div class="flex-1 mx-3">
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="{{ $meta['color'] }} h-2 rounded-full" style="width: {{ $stPct }}%"></div>
                            </div>
                        </div>
                        <div class="w-16 text-right font-semibold text-slate-800">{{ $stCount }} <span class="text-slate-400 font-normal">({{ $stPct }}%)</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4B. Kesehatan Pelaksanaan Program (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Kesehatan Pelaksanaan Program</h2>
                <p class="text-xs text-slate-500">Evaluasi performa {{ $inProgressPrograms }} program aktif berdasarkan target progres.</p>
            </div>

            <div class="space-y-2.5">
                <!-- On Track -->
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <div>
                            <span class="font-semibold text-slate-800 block">Sesuai Target (On Track)</span>
                            <span class="text-slate-500 text-[11px]">Progres &ge; 60%</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-900 block">{{ $monitoringHealth['on_track'] }} program</span>
                        <span class="text-slate-500 text-[11px]">{{ $monitoringHealth['on_track_pct'] }}%</span>
                    </div>
                </div>

                <!-- At Risk -->
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <div>
                            <span class="font-semibold text-slate-800 block">Perlu Perhatian (At Risk)</span>
                            <span class="text-slate-500 text-[11px]">Progres 25% - 59%</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-900 block">{{ $monitoringHealth['at_risk'] }} program</span>
                        <span class="text-slate-500 text-[11px]">{{ $monitoringHealth['at_risk_pct'] }}%</span>
                    </div>
                </div>

                <!-- Delayed -->
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <div>
                            <span class="font-semibold text-slate-800 block">Terlambat (Delayed)</span>
                            <span class="text-slate-500 text-[11px]">Progres &lt; 25%</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-900 block">{{ $monitoringHealth['delayed'] }} program</span>
                        <span class="text-slate-500 text-[11px]">{{ $monitoringHealth['delayed_pct'] }}%</span>
                    </div>
                </div>
            </div>

            <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 flex justify-between">
                <span>Monitoring berkala</span>
                <span class="font-medium text-slate-700">{{ $inProgressPrograms }} program berjalan</span>
            </div>
        </div>
    </div>

    <!-- 5. PROGRAM MEMBUTUHKAN KEPUTUSAN / PERHATIAN DIREKTUR -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Program Membutuhkan Perhatian & Keputusan</h2>
                <p class="text-xs text-slate-500">Daftar usulan baru, program tertunda, atau program dengan deviasi jadwal.</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($pendingApprovalsCount > 0)
                    <a href="{{ route('director.approvals.index') }}" class="inline-flex items-center px-2.5 py-1 rounded border border-amber-300 bg-amber-50 text-amber-800 text-xs font-medium hover:bg-amber-100">
                        Antrean Approval ({{ $pendingApprovalsCount }})
                    </a>
                @endif
                <a href="{{ route('director.programs.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                    Semua Program &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Kode & Nama Program</th>
                        <th class="px-4 py-3">PIC / Unit</th>
                        <th class="px-4 py-3">Anggaran</th>
                        <th class="px-4 py-3">Progres</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Rekomendasi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($attentionPrograms as $prog)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-[11px] text-slate-500 block">{{ $prog->code }}</span>
                                <a href="{{ route('director.programs.show', $prog->id) }}" class="font-semibold text-slate-900 hover:text-blue-600 max-w-xs truncate block">
                                    {{ $prog->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 font-medium">
                                {{ $prog->pic_display_name }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700">
                                Rp {{ number_format($prog->budget, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-slate-700">{{ $prog->progress }}%</span>
                                    <div class="w-14 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-{{ $prog->progress < 30 ? 'rose' : 'amber' }}-600 h-1.5 rounded-full" style="width: {{ $prog->progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($prog->status === 'REJECTED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Perlu Revisi</span>
                                @elseif($prog->status === 'SUBMITTED' || $prog->status === 'UNDER_REVIEW')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Menunggu Review</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $prog->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                @if($prog->status === 'SUBMITTED' || $prog->status === 'UNDER_REVIEW')
                                    <span class="text-amber-800 font-medium">Tinjau usulan proposal</span>
                                @elseif($prog->progress < 30)
                                    <span class="text-rose-800 font-medium">Evaluasi khusus jadwal</span>
                                @else
                                    <span class="text-slate-600">Monitoring milestone</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($prog->status === 'SUBMITTED' || $prog->status === 'UNDER_REVIEW')
                                    <a href="{{ route('director.approvals.index') }}" class="inline-flex items-center px-2.5 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs transition-colors shadow-sm">
                                        Tinjau &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('director.programs.show', $prog->id) }}" class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors">
                                        Detail &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-500 text-xs">
                                Tidak ada program yang memerlukan perhatian khusus saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. CAPAIAN INDIKATOR KINERJA UTAMA (IKU STRATEGIS) -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Capaian Indikator Kinerja Utama (IKU Inovasi {{ $selectedYear }})</h2>
                <p class="text-xs text-slate-500">Metrik luaran strategis hilirisasi, inkubasi, paten, dan kerjasama industri.</p>
            </div>
            <a href="{{ route('admin.impact.index', ['year' => $selectedYear]) }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                Kelola Data IKU &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-1">
            @foreach($impactMetrics->take(6) as $im)
                <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500">
                            {{ $im->category_label }}
                        </span>
                        <span class="font-bold {{ $im->achievement_percentage >= 100 ? 'text-emerald-700' : 'text-blue-700' }}">
                            {{ $im->achievement_percentage }}%
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-900 line-clamp-1">{{ $im->metric_name }}</p>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $im->achievement_percentage >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}" style="width: {{ min(100, $im->achievement_percentage) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-600">
                        <span>Target: {{ number_format($im->target_value, 0, ',', '.') }} {{ $im->unit }}</span>
                        <span class="font-semibold text-slate-900">Realisasi: {{ number_format($im->realized_value, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 7. EVALUASI MONEV & OVERVIEW LAYANAN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 7A. Evaluasi Monev Terbaru (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Evaluasi Monev Terbaru</h2>
                    <p class="text-xs text-slate-500">Penilaian capaian milestone dan catatan evaluator.</p>
                </div>
                <a href="{{ route('admin.monev.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                    Buka Monev &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-3.5 py-2.5">Program</th>
                            <th class="px-3 py-2.5">Progres</th>
                            <th class="px-3 py-2.5">Skor</th>
                            <th class="px-3 py-2.5">Evaluator</th>
                            <th class="px-3 py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentEvaluations as $eval)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3.5 py-2.5">
                                    <span class="font-mono text-[10px] text-slate-500 block">{{ $eval->program?->code }}</span>
                                    <p class="font-medium text-slate-900 truncate max-w-[180px]">{{ $eval->program?->name }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $eval->period_label }}</span>
                                </td>
                                <td class="px-3 py-2.5 font-medium text-slate-800">
                                    {{ $eval->progress_percentage }}%
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold {{ $eval->score >= 80 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $eval->score }} / 100
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 truncate max-w-[100px]">
                                    {{ $eval->evaluator?->name ?? '-' }}
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <a href="{{ route('admin.monev.show', $eval->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3.5 py-5 text-center text-slate-500">
                                    Belum ada laporan evaluasi terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 7B. Layanan & Konsultasi Overview (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Layanan & Konsultasi Inovasi</h2>
                    <p class="text-xs text-slate-500">Permohonan fasilitasi HKI, valuasi, dan konsultasi.</p>
                </div>
                <a href="{{ route('admin.services.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                    Buka Layanan &rarr;
                </a>
            </div>

            <!-- Stats strip -->
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="font-bold text-slate-900 text-base">{{ $servicesSummary['active'] }}</p>
                    <p class="text-[11px] text-slate-500">Aktif / Jadwal</p>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="font-bold text-emerald-700 text-base">{{ $servicesSummary['completed'] }}</p>
                    <p class="text-[11px] text-slate-500">Selesai</p>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="font-bold text-amber-700 text-base">{{ $servicesSummary['pending_action'] }}</p>
                    <p class="text-[11px] text-slate-500">Menunggu</p>
                </div>
            </div>

            <!-- Priority Consultations -->
            <div class="space-y-2 pt-1">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Permohonan Terbaru:</p>
                @forelse($priorityConsultations as $srv)
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <div class="min-w-0 pr-2">
                            <span class="font-mono text-[10px] text-slate-500 block">{{ $srv->ticket_number }}</span>
                            <p class="font-medium text-slate-900 truncate">{{ $srv->title }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $srv->applicant?->name }} ({{ $srv->institution ?: 'Pemohon' }})</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium border shrink-0 {{ $srv->status_badge_class }}">
                            {{ $srv->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-2 text-center">Tidak ada permohonan yang menunggu tindak lanjut.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
