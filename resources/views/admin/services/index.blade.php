@extends('layouts.admin')

@section('title', 'Layanan & Konsultasi DKST')

@section('content')
<div class="space-y-6">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Layanan & Konsultasi DKST</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola permohonan pendampingan, valuasi teknologi, fasilitasi paten HKI, inkubasi, dan konsultasi inovasi.</p>
        </div>
        <div class="flex items-center space-x-3">
            @can('create', \App\Models\ConsultationService::class)
            <a href="{{ route('admin.services.create') }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajukan Permohonan Baru
            </a>
            @endcan
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Permohonan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider">Menunggu Respon</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-blue-500 uppercase tracking-wider">Dalam Penelaahan</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_review'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-semibold text-purple-500 uppercase tracking-wider">Dijadwalkan</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['scheduled'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm col-span-2 lg:col-span-1">
            <p class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider">Selesai Dikonsultasikan</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <!-- Status Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.services.index') }}" 
               class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ !request()->filled('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua: <span class="font-bold">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'PENDING']) }}" 
               class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'PENDING' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Menunggu: <span class="font-bold">{{ $stats['pending'] }}</span>
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'SCHEDULED']) }}" 
               class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'SCHEDULED' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                Dijadwalkan: <span class="font-bold">{{ $stats['scheduled'] }}</span>
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'COMPLETED']) }}" 
               class="px-3.5 py-1.5 rounded-xl font-semibold transition-colors {{ request('status') === 'COMPLETED' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                Selesai: <span class="font-bold">{{ $stats['completed'] }}</span>
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.services.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-100">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tiket, judul konsultasi, pemohon, instansi.." 
                       class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="sm:col-span-5">
                <select name="service_type" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500 text-slate-700">
                    <option value="">Semua Jenis Layanan</option>
                    <option value="VALUASI_TEKNOLOGI" {{ request('service_type') === 'VALUASI_TEKNOLOGI' ? 'selected' : '' }}>Valuasi Teknologi & Paten</option>
                    <option value="FASILITASI_HKI" {{ request('service_type') === 'FASILITASI_HKI' ? 'selected' : '' }}>Fasilitasi Pendaftaran HKI / Paten</option>
                    <option value="INKUBASI_STARTUP" {{ request('service_type') === 'INKUBASI_STARTUP' ? 'selected' : '' }}>Pendampingan & Inkubasi Startup</option>
                    <option value="HILIRISASI_INDUSTRI" {{ request('service_type') === 'HILIRISASI_INDUSTRI' ? 'selected' : '' }}>Konsultasi Hilirisasi Industri</option>
                    <option value="LEGALITAS_KONTRAK" {{ request('service_type') === 'LEGALITAS_KONTRAK' ? 'selected' : '' }}>Penyusunan Kontrak Lisensi & Kerjasama</option>
                    <option value="LAINNYA" {{ request('service_type') === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'service_type', 'status']))
                    <a href="{{ route('admin.services.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Services Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nomor Tiket & Layanan</th>
                        <th class="px-4 py-4">Pemohon & Unit</th>
                        <th class="px-4 py-4">Konsultan PIC</th>
                        <th class="px-4 py-4">Jadwal Sesi</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($services as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] text-blue-600 font-bold block">{{ $item->ticket_number }}</span>
                                <a href="{{ route('admin.services.show', $item->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block max-w-sm truncate">
                                    {{ $item->title }}
                                </a>
                                <span class="text-[11px] text-slate-500 block mt-0.5">{{ $item->service_type_label }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800">{{ $item->applicant?->name ?? 'Pemohon' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $item->institution ?? 'Sivitas ITB / Eksternal' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @if($item->consultant)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                            {{ substr($item->consultant->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $item->consultant->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum didisposisi</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                @if($item->scheduled_at)
                                    <span class="font-semibold text-slate-800">{{ $item->scheduled_at->format('d M Y') }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ $item->scheduled_at->format('H:i') }} WIB</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->status_badge_class }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.services.show', $item->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="font-medium text-slate-600">Belum ada permohonan layanan & konsultasi yang sesuai filter.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Ajukan Permohonan Baru" untuk menambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $services->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
