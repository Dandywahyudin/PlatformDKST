@extends('layouts.admin')

@section('title', 'Approval Usulan Program')
@section('page_title', 'Antrean Approval')

@section('content')
<div class="space-y-6">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Antrean Persetujuan Usulan Program</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tinjau, setujui, atau tolak usulan program baru dari direktorat dan staf pelaksana.</p>
        </div>
    </div>

    <!-- Stats & Status Filter Tabs -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.approvals.index') }}" class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua: <span class="font-bold">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('admin.approvals.index', ['status' => 'PENDING']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'PENDING' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Menunggu Review: <span class="font-bold">{{ $stats['pending'] }}</span>
            </a>
            <a href="{{ route('admin.approvals.index', ['status' => 'APPROVED']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'APPROVED' ? 'bg-teal-600 text-white' : 'bg-teal-50 text-teal-700 hover:bg-teal-100' }}">
                Disetujui: <span class="font-bold">{{ $stats['approved'] }}</span>
            </a>
            <a href="{{ route('admin.approvals.index', ['status' => 'REJECTED']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'REJECTED' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                Ditolak: <span class="font-bold">{{ $stats['rejected'] }}</span>
            </a>
        </div>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('admin.approvals.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-100">
            <div class="sm:col-span-9 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode program, nama usulan, atau pengusul..."
                       class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div class="sm:col-span-3 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.approvals.index') }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </form>

    </div>

    <!-- Approvals Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Usulan Program</th>
                        <th class="px-4 py-4">Pengusul</th>
                        <th class="px-4 py-4">Anggaran Diajukan</th>
                        <th class="px-4 py-4">Tanggal Pengajuan</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($approvals as $approval)
                        @php
                            $statusStyles = [
                                'PENDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'APPROVED' => 'bg-teal-50 text-teal-700 border-teal-200',
                                'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Program Code & Title -->
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $approval->program?->code }}</span>
                                <a href="{{ route('admin.approvals.show', $approval->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block max-w-sm truncate">
                                    {{ $approval->program?->name }}
                                </a>
                            </td>

                            <!-- Requester -->
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800">{{ $approval->requester?->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $approval->requester?->email }}</p>
                            </td>

                            <!-- Budget -->
                            <td class="px-4 py-4 font-mono font-semibold text-slate-900">
                                Rp {{ number_format($approval->program?->budget ?? 0, 0, ',', '.') }}
                            </td>

                            <!-- Submitted Date -->
                            <td class="px-4 py-4 text-slate-500">
                                {{ $approval->created_at->format('d M Y H:i') }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusStyles[$approval->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $approval->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.approvals.show', $approval->id) }}" class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                                    Tinjau Usulan →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada data usulan persetujuan.</p>
                                <p class="text-xs text-slate-400 mt-1">Seluruh usulan yang diajukan akan muncul di antrean ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($approvals->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $approvals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
