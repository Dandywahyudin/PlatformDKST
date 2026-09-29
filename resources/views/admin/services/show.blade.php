@extends('layouts.admin')

@section('title', "Tiket [{$service->ticket_number}] — Layanan & Konsultasi")

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ scheduleModal: false, completeModal: false, rejectModal: false }">

    <!-- Top Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.services.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">{{ $service->ticket_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $service->status_badge_class }}">{{ $service->status }}</span>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $service->title }}</h2>
            </div>
        </div>

        <!-- Workflow Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @can('update', $service)
                @if(in_array($service->status, [\App\Models\ConsultationService::STATUS_PENDING, \App\Models\ConsultationService::STATUS_IN_REVIEW]))
                    <button @click="scheduleModal = true" class="inline-flex items-center px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Jadwalkan Konsultasi
                    </button>
                    <button @click="rejectModal = true" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition-colors">
                        Tolak
                    </button>
                @elseif($service->status === \App\Models\ConsultationService::STATUS_SCHEDULED)
                    <button @click="completeModal = true" class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Selesaikan Sesi & Input Hasil
                    </button>
                @endif

                <a href="{{ route('admin.services.edit', $service->id) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Edit Data
                </a>
            @endcan

            @can('delete', $service)
                <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket permohonan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors" title="Hapus Tiket">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Main Grid Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Service Information & Notes -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Kebutuhan & Topik</h3>
                    <p class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $service->description }}</p>
                </div>

                @if($service->consultation_notes || $service->action_plan)
                    <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-4">
                        @if($service->consultation_notes)
                            <div>
                                <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-1">Catatan & Rekomendasi Konsultan:</h4>
                                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $service->consultation_notes }}</p>
                            </div>
                        @endif

                        @if($service->action_plan)
                            <div class="pt-3 border-t border-blue-100">
                                <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-1">Rencana Aksi Tindak Lanjut:</h4>
                                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $service->action_plan }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if($service->rejection_reason)
                    <div class="p-4 bg-rose-50 rounded-2xl border border-rose-200">
                        <p class="text-xs font-bold text-rose-800 uppercase tracking-wider mb-1">Catatan Penolakan:</p>
                        <p class="text-xs text-rose-700">{{ $service->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Schedule & Meeting Info Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal & Lokasi Sesi Konsultasi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-slate-400 font-medium mb-1">Waktu Pelaksanaan</p>
                        @if($service->scheduled_at)
                            <p class="text-sm font-bold text-slate-900">{{ $service->scheduled_at->format('l, d F Y') }}</p>
                            <p class="text-slate-600 mt-0.5">Pukul {{ $service->scheduled_at->format('H:i') }} WIB</p>
                        @else
                            <p class="text-slate-400 italic">Belum dijadwalkan</p>
                        @endif
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-slate-400 font-medium mb-1">Lokasi / Tautan Pertemuan</p>
                        @if($service->meeting_link_or_location)
                            <p class="text-xs font-semibold text-slate-800 break-all">{{ $service->meeting_link_or_location }}</p>
                        @else
                            <p class="text-slate-400 italic">Belum ditentukan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Applicant & Consultant Details -->
        <div class="space-y-6">
            <!-- Applicant Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Pemohon</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        {{ substr($service->applicant?->name ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">{{ $service->applicant?->name ?? '-' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $service->applicant?->email ?? '-' }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Unit / Instansi:</span>
                        <span class="font-medium text-slate-700">{{ $service->institution ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">No. Kontak:</span>
                        <span class="font-medium text-slate-700">{{ $service->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal Diajukan:</span>
                        <span class="font-medium text-slate-700">{{ $service->created_at->format('d M Y H:i') }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Consultant PIC Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konsultan / PIC DKST</h3>
                @if($service->consultant)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            {{ substr($service->consultant->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $service->consultant->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $service->consultant->position ?? 'Konsultan DKST' }}</p>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-center">
                        <p class="text-xs text-amber-700 font-medium">Belum ada konsultan yang ditugaskan.</p>
                        <p class="text-[10px] text-amber-600 mt-1">Gunakan tombol "Jadwalkan Konsultasi" untuk menugaskan konsultan.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- MODAL 1: Schedule Consultation -->
    <div x-show="scheduleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-6" @click.away="scheduleModal = false">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Jadwalkan Sesi Konsultasi</h3>
                <p class="text-xs text-slate-500 mt-1">Tentukan konsultan penanggung jawab, tanggal waktu pelaksanaan, dan lokasi pertemuan.</p>
            </div>

            <form method="POST" action="{{ route('admin.services.schedule', $service->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konsultan PIC DKST <span class="text-rose-500">*</span></label>
                    <select name="consultant_id" required class="w-full text-xs rounded-xl border-slate-200 py-2.5">
                        <option value="">-- Pilih Konsultan --</option>
                        @foreach($consultants as $c)
                            <option value="{{ $c->id }}" {{ $service->consultant_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Waktu Sesi Konsultasi <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="scheduled_at" required value="{{ $service->scheduled_at ? $service->scheduled_at->format('Y-m-d\TH:i') : '' }}" class="w-full text-xs rounded-xl border-slate-200 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lokasi / Tautan Video Meet <span class="text-rose-500">*</span></label>
                    <input type="text" name="meeting_link_or_location" required value="{{ $service->meeting_link_or_location }}" placeholder="Ruang Rapat DKST ITB Lt. 2 / https://meet.google.com/..." class="w-full text-xs rounded-xl border-slate-200 py-2.5">
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="scheduleModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold shadow-sm">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Complete Consultation Session -->
    <div x-show="completeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-6" @click.away="completeModal = false">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Selesaikan Sesi Konsultasi</h3>
                <p class="text-xs text-slate-500 mt-1">Masukkan catatan hasil diskusi, poin rekomendasi, dan rencana aksi tindak lanjut pemohon.</p>
            </div>

            <form method="POST" action="{{ route('admin.services.complete', $service->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan & Rekomendasi Konsultan <span class="text-rose-500">*</span></label>
                    <textarea name="consultation_notes" rows="4" required placeholder="Tuliskan simpulan konsultasi, rekomendasi strategi HKI, atau langkah hilirisasi.." class="w-full text-xs rounded-xl border-slate-200 py-2.5">{{ $service->consultation_notes }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Rencana Aksi Tindak Lanjut (Action Plan)</label>
                    <textarea name="action_plan" rows="3" placeholder="Contoh: Pemohon melengkapi draf klaim paten paling lambat 14 hari kerja.." class="w-full text-xs rounded-xl border-slate-200 py-2.5">{{ $service->action_plan }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="completeModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm">
                        Selesaikan & Simpan Hasil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Reject Consultation Request -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-6" @click.away="rejectModal = false">
            <div>
                <h3 class="text-lg font-bold text-rose-900">Tolak Permohonan Konsultasi</h3>
                <p class="text-xs text-slate-500 mt-1">Berikan alasan penolakan atau instruksi perbaikan permohonan kepada pemohon.</p>
            </div>

            <form method="POST" action="{{ route('admin.services.reject', $service->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" rows="4" required placeholder="Contoh: Permohonan belum melampirkan ringkasan deskripsi invensi teknologi.." class="w-full text-xs rounded-xl border-slate-200 py-2.5"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm">
                        Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
