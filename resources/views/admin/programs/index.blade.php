@extends('layouts.admin')

@section('title', 'Manajemen Program')
@section('page_title', 'Daftar Program')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Program & Inisiatif DKST</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh portofolio program riset, inovasi, transfer teknologi, dan inkubasi bisnis.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Usulan Program
            </a>
        </div>
    </div>

    <!-- Stats & Filters Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        
        <!-- Status Counters Bar -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.programs.index') }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua: <span class="font-bold">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'DRAFT']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'DRAFT' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Draft: <span class="font-bold">{{ $stats['draft'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'SUBMITTED']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'SUBMITTED' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                Submitted: <span class="font-bold">{{ $stats['submitted'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'UNDER_REVIEW']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'UNDER_REVIEW' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Review: <span class="font-bold">{{ $stats['under_review'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'APPROVED']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'APPROVED' ? 'bg-teal-600 text-white' : 'bg-teal-50 text-teal-700 hover:bg-teal-100' }}">
                Approved: <span class="font-bold">{{ $stats['approved'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'IN_PROGRESS']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'IN_PROGRESS' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                In Progress: <span class="font-bold">{{ $stats['in_progress'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'COMPLETED']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'COMPLETED' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                Completed: <span class="font-bold">{{ $stats['completed'] }}</span>
            </a>
            <a href="{{ route('admin.programs.index', ['status' => 'REJECTED']) }}" class="px-3 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'REJECTED' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                Rejected: <span class="font-bold">{{ $stats['rejected'] }}</span>
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.programs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-100">
            <!-- Search Keyword -->
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode (DKST-PRG-...), nama program, atau PIC..."
                       class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                    <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Submitted</option>
                    <option value="UNDER_REVIEW" {{ request('status') === 'UNDER_REVIEW' ? 'selected' : '' }}>Under Review</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="sm:col-span-3 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                    Filter Program
                </button>
                @if (request()->hasAny(['search', 'status', 'year']))
                    <a href="{{ route('admin.programs.index') }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </form>

    </div>

    <!-- Programs Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kode & Nama Program</th>
                        <th class="px-4 py-4">PIC & Periode</th>
                        <th class="px-4 py-4">Anggaran</th>
                        <th class="px-4 py-4">Progres</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($programs as $program)
                        @php
                            $badges = [
                                'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'SUBMITTED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'UNDER_REVIEW' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'APPROVED' => 'bg-teal-50 text-teal-700 border-teal-200',
                                'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'IN_PROGRESS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'COMPLETED' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Code & Title -->
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $program->code }}</span>
                                <a href="{{ route('admin.programs.show', $program->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block max-w-sm truncate">
                                    {{ $program->name }}
                                </a>
                                <span class="text-[11px] text-slate-400 block mt-0.5 line-clamp-1">
                                    {{ $program->description ?? 'Tidak ada deskripsi.' }}
                                </span>
                            </td>

                            <!-- PIC & Period -->
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 text-xs">{{ $program->pic_display_name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $program->start_date ? $program->start_date->format('d M Y') : 'TBD' }}
                                    – 
                                    {{ $program->end_date ? $program->end_date->format('d M Y') : 'TBD' }}
                                </p>
                            </td>

                            <!-- Budget -->
                            <td class="px-4 py-4 font-mono font-semibold text-slate-900">
                                Rp {{ number_format($program->budget, 0, ',', '.') }}
                            </td>

                            <!-- Progress -->
                            <td class="px-4 py-4">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden mb-1">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $program->progress }}%"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-600">{{ $program->progress }}%</span>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badges[$program->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $program->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.programs.show', $program->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.programs.edit', $program->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100" title="Edit Program">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if (in_array($program->status, ['DRAFT', 'REJECTED']))
                                        <form method="POST" action="{{ route('admin.programs.destroy', $program->id) }}" onsubmit="return confirm('Hapus program [{{ $program->code }}] {{ $program->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100" title="Hapus Usulan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada data program yang ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba buat usulan program baru atau ubah filter pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($programs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
