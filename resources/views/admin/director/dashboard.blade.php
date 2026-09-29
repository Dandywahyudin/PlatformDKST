@extends('layouts.admin')

@section('title', 'Dashboard Direktur — Program & Monitoring Evaluasi')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto pb-12">

    <!-- 1. HEADER SECTION -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Portal Eksekutif DKST ITB</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Direktur</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Ringkasan Eksekutif Program, Monitoring, Evaluasi & Dampak Inovasi</p>
        </div>

        <!-- Right Controls: User Profile Info & Year/Period Filter -->
        <div class="flex flex-wrap items-center gap-3 sm:gap-4 relative z-10">
            <!-- Period Filter Form -->
            <form method="GET" action="{{ route('director.dashboard') }}" class="flex items-center space-x-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 pl-2">Tahun:</span>
                <select name="year" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border-none bg-white py-1.5 px-3 text-slate-800 shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Director Profile Card Strip -->
            <div class="flex items-center space-x-3 pl-3 sm:border-l border-slate-200">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-sm shadow-md">
                    {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 leading-tight">{{ Auth::user()->name ?? 'Direktur DKST' }}</p>
                    <span class="inline-block mt-0.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                        Direktur / Executive
                    </span>
                </div>
            </div>
        </div>

        <!-- Background Ambient Accent -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 2. RINGKASAN UTAMA (5 KPI CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Program -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:border-slate-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Program</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalPrograms }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Seluruh portofolio inovasi</p>
            </div>
        </div>

        <!-- Card 2: Program Berjalan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:border-blue-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between text-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Program Berjalan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-2xl sm:text-3xl font-extrabold text-blue-600 tracking-tight">{{ $inProgressPrograms }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Status aktif (In Progress)</p>
            </div>
        </div>

        <!-- Card 3: Program Selesai -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:border-emerald-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between text-emerald-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Program Selesai</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $completedPrograms }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Target selesai 100%</p>
            </div>
        </div>

        <!-- Card 4: Program Perlu Perhatian -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:border-rose-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between text-rose-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Perlu Perhatian</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">{{ $attentionProgramsCount }}</p>
                <p class="text-[11px] text-rose-500/80 font-medium mt-1">Ditolak / Progres lambat</p>
            </div>
        </div>

        <!-- Card 5: Rata-rata Capaian -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:border-indigo-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between text-indigo-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Rata-rata Capaian</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline space-x-1">
                    <p class="text-2xl sm:text-3xl font-extrabold text-indigo-700 tracking-tight">{{ $avgProgress }}%</p>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $avgProgress) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 & 4. STATUS PROGRAM & MONITORING HEALTH -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 3. Distribusi Status Program (Left 7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Distribusi Status Portofolio Program</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Sebaran tahapan seluruh program inovasi DKST saat ini.</p>
                </div>
                <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <!-- Modern Clean Status Bar Distribution -->
            <div class="space-y-3 pt-2">
                @php
                    $statusMeta = [
                        'IN_PROGRESS' => ['label' => 'Sedang Berjalan (In Progress)', 'color' => 'bg-blue-600', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        'APPROVED' => ['label' => 'Disetujui & Siap Berjalan', 'color' => 'bg-teal-500', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200'],
                        'COMPLETED' => ['label' => 'Selesai (Completed)', 'color' => 'bg-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'SUBMITTED' => ['label' => 'Menunggu Approval (Submitted)', 'color' => 'bg-amber-500', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
                        'DRAFT' => ['label' => 'Draf Usulan (Draft)', 'color' => 'bg-slate-400', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200'],
                        'REJECTED' => ['label' => 'Perlu Revisi (Rejected)', 'color' => 'bg-rose-500', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200'],
                    ];
                @endphp

                @foreach($statusMeta as $stKey => $meta)
                    @php
                        $stCount = $statusDistribution[$stKey] ?? 0;
                        $stPct = $totalPrograms > 0 ? round(($stCount / $totalPrograms) * 100) : 0;
                    @endphp
                    <div class="flex items-center text-xs">
                        <div class="w-48 font-medium text-slate-700 truncate">{{ $meta['label'] }}</div>
                        <div class="flex-1 mx-3">
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="{{ $meta['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $stPct }}%"></div>
                            </div>
                        </div>
                        <div class="w-16 text-right font-bold text-slate-900">{{ $stCount }} <span class="text-[10px] text-slate-400 font-normal">({{ $stPct }}%)</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Monitoring Program Health (Right 5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-5">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Kesehatan Pelaksanaan Program</h3>
                <p class="text-xs text-slate-500 mt-0.5">Klasifikasi performa program aktif berdasarkan target progres.</p>
            </div>

            <div class="space-y-4">
                <!-- On Track -->
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <div>
                            <p class="text-xs font-bold text-emerald-950">Sesuai Target (On Track)</p>
                            <p class="text-[10px] text-emerald-700">Progres fisik &gt;= 60%</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-base font-extrabold text-emerald-800">{{ $monitoringHealth['on_track'] }}</p>
                        <p class="text-[10px] font-semibold text-emerald-600">{{ $monitoringHealth['on_track_pct'] }}%</p>
                    </div>
                </div>

                <!-- At Risk -->
                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div>
                            <p class="text-xs font-bold text-amber-950">Perlu Perhatian (At Risk)</p>
                            <p class="text-[10px] text-amber-700">Progres fisik 25% - 59%</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-base font-extrabold text-amber-800">{{ $monitoringHealth['at_risk'] }}</p>
                        <p class="text-[10px] font-semibold text-amber-600">{{ $monitoringHealth['at_risk_pct'] }}%</p>
                    </div>
                </div>

                <!-- Delayed -->
                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <div>
                            <p class="text-xs font-bold text-rose-950">Terlambat (Delayed)</p>
                            <p class="text-[10px] text-rose-700">Progres fisik &lt; 25%</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-base font-extrabold text-rose-800">{{ $monitoringHealth['delayed'] }}</p>
                        <p class="text-[10px] font-semibold text-rose-600">{{ $monitoringHealth['delayed_pct'] }}%</p>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 italic text-center">
                Total program aktif yang dimonitor: <span class="font-bold text-slate-700">{{ $inProgressPrograms }} program</span>
            </p>
        </div>
    </div>

    <!-- 5. TOP PROGRAM YANG PERLU PERHATIAN (EXECUTIVE TABLE) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span>
                    Program yang Membutuhkan Perhatian Direktur
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar program dengan status revisi/approval tertunda atau berpotensi deviasi jadwal.</p>
            </div>
            <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Buka Manajemen Program &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Nama Program</th>
                        <th class="px-4 py-3.5">PIC Pelaksana</th>
                        <th class="px-4 py-3.5">Progres Saat Ini</th>
                        <th class="px-4 py-3.5">Alokasi Anggaran</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Batas Waktu</th>
                        <th class="px-6 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attentionPrograms as $prog)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $prog->code }}</span>
                                <a href="{{ route('admin.programs.show', $prog->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block max-w-xs truncate">
                                    {{ $prog->name }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-slate-700 font-medium">
                                {{ $prog->pic_display_name }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-800">{{ $prog->progress }}%</span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $prog->progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono text-slate-800 font-medium">
                                Rp {{ number_format($prog->budget, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4">
                                @if($prog->status === 'REJECTED')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Perlu Revisi</span>
                                @elseif($prog->status === 'SUBMITTED' || $prog->status === 'UNDER_REVIEW')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Review</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $prog->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                {{ $prog->end_date ? $prog->end_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.programs.show', $prog->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                                    Tinjau Program &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-xs">
                                Seluruh program saat ini berjalan sesuai target tanpa kendala kritis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. KINERJA & DAMPAK INOVASI DKST -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Capaian Kinerja & Dampak Inovasi (Tahun {{ $selectedYear }})</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pengukuran indikator outcome, paten HKI, inkubasi bisnis, dan komersialisasi teknologi.</p>
            </div>
            <a href="{{ route('admin.impact.index', ['year' => $selectedYear]) }}" class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs transition-colors">
                Kelola Detail Indikator &rarr;
            </a>
        </div>

        <!-- Metric Summary Chips -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Rata-rata Ketercapaian</p>
                    <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $impactAvgAchievement }}%</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                    %
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-emerald-700">Indikator Memenuhi Target</p>
                    <p class="text-xl font-extrabold text-emerald-800 mt-0.5">{{ $impactAchievedCount }} <span class="text-xs font-normal text-emerald-600">/ {{ $impactTotalCount }}</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                    ✓
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-amber-700">Dalam Proses Ketercapaian</p>
                    <p class="text-xl font-extrabold text-amber-800 mt-0.5">{{ $impactUnachievedCount }} <span class="text-xs font-normal text-amber-600">Indikator</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    ⏳
                </div>
            </div>
        </div>

        <!-- Key Outcomes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
            @foreach($impactMetrics->take(6) as $im)
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-sm transition-all space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-700">
                            {{ $im->category_label }}
                        </span>
                        <span class="text-xs font-bold {{ $im->achievement_percentage >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">
                            {{ $im->achievement_percentage }}%
                        </span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 line-clamp-1">{{ $im->metric_name }}</p>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $im->achievement_percentage >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ min(100, $im->achievement_percentage) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <span>Target: {{ number_format($im->target_value, 0, ',', '.') }} {{ $im->unit }}</span>
                        <span class="font-bold text-slate-800">{{ number_format($im->realized_value, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 7 & 8. MONITORING TERBARU & LAYANAN KONSULTASI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 7. Monitoring & Evaluasi Terbaru (Left 7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Laporan Monitoring & Evaluasi Terbaru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan penilaian berkala dan hasil audit progres milestone.</p>
                </div>
                <a href="{{ route('admin.monev.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Buka Monev &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Program & Periode</th>
                            <th class="px-3 py-3">Progres</th>
                            <th class="px-3 py-3">Skor</th>
                            <th class="px-3 py-3">Reviewer</th>
                            <th class="px-3 py-3 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentEvaluations as $eval)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $eval->program?->code }}</span>
                                    <p class="font-bold text-slate-900 truncate max-w-[180px]">{{ $eval->program?->name }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $eval->period_label }}</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-800">
                                    {{ $eval->progress_percentage }}%
                                </td>
                                <td class="px-3 py-3">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $eval->score >= 80 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $eval->score }} / 100
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-600 truncate max-w-[100px]">
                                    {{ $eval->evaluator?->name ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <a href="{{ route('admin.monev.show', $eval->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                        Lihat &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                    Belum ada laporan evaluasi terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 8. Layanan & Konsultasi Summary (Right 5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Layanan & Konsultasi Inovasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pendampingan tenant, valuasi, dan fasilitasi HKI.</p>
                </div>
                <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Buka Layanan &rarr;
                </a>
            </div>

            <!-- Mini stats -->
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100">
                    <p class="font-extrabold text-blue-700 text-lg">{{ $servicesSummary['active'] }}</p>
                    <p class="text-[10px] font-semibold text-blue-600">Aktif/Terjadwal</p>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                    <p class="font-extrabold text-emerald-700 text-lg">{{ $servicesSummary['completed'] }}</p>
                    <p class="text-[10px] font-semibold text-emerald-600">Selesai</p>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-100">
                    <p class="font-extrabold text-amber-700 text-lg">{{ $servicesSummary['pending_action'] }}</p>
                    <p class="text-[10px] font-semibold text-amber-600">Menunggu Respon</p>
                </div>
            </div>

            <!-- Priority Consultations List -->
            <div class="space-y-2.5 pt-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Permohonan Menunggu Tindak Lanjut:</p>
                @forelse($priorityConsultations as $srv)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="min-w-0 pr-2">
                            <span class="font-mono text-[9px] text-blue-600 font-bold block">{{ $srv->ticket_number }}</span>
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $srv->title }}</p>
                            <p class="text-[10px] text-slate-400">{{ $srv->applicant?->name }} ({{ $srv->institution ?: 'Pemohon' }})</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border shrink-0 {{ $srv->status_badge_class }}">
                            {{ $srv->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-2">Tidak ada permohonan yang menunggu tindak lanjut.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 9. AKTIVITAS TERBARU (EXECUTIVE AUDIT TRAIL) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900">Aktivitas & Log Penting Terkini</h3>
        <div class="divide-y divide-slate-100">
            @forelse($recentActivities as $act)
                <div class="py-3 flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[11px] shrink-0 mt-0.5">
                        {{ substr($act->user_name ?? 'S', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800">{{ $act->description }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Oleh <span class="font-medium text-slate-600">{{ $act->user_name ?? 'Sistem' }}</span> • Modul: <span class="font-semibold text-blue-600">{{ $act->module }}</span>
                        </p>
                    </div>
                    <span class="text-[10px] text-slate-400 shrink-0">{{ $act->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada aktivitas yang tercatat.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
