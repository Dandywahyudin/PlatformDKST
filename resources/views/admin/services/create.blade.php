@extends('layouts.admin')

@section('title', 'Ajukan Permohonan Layanan & Konsultasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.services.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Form Pengajuan Layanan & Konsultasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan permohonan konsultasi inovasi, pendampingan valuasi, paten, atau komersialisasi.</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-6">
            @csrf

            <!-- Grid 1: Service Type & Title -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Layanan & Konsultasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="service_type" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Jenis Layanan --</option>
                        <option value="VALUASI_TEKNOLOGI" {{ old('service_type') === 'VALUASI_TEKNOLOGI' ? 'selected' : '' }}>Valuasi Teknologi & Paten</option>
                        <option value="FASILITASI_HKI" {{ old('service_type') === 'FASILITASI_HKI' ? 'selected' : '' }}>Fasilitasi Pendaftaran HKI / Paten</option>
                        <option value="INKUBASI_STARTUP" {{ old('service_type') === 'INKUBASI_STARTUP' ? 'selected' : '' }}>Pendampingan & Inkubasi Startup</option>
                        <option value="HILIRISASI_INDUSTRI" {{ old('service_type') === 'HILIRISASI_INDUSTRI' ? 'selected' : '' }}>Konsultasi Hilirisasi Industri</option>
                        <option value="LEGALITAS_KONTRAK" {{ old('service_type') === 'LEGALITAS_KONTRAK' ? 'selected' : '' }}>Penyusunan Kontrak Lisensi & Kerjasama</option>
                        <option value="LAINNYA" {{ old('service_type') === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('service_type')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Topik / Judul Konsultasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Valuasi Paten Sensor IoT untuk Industri Migas"
                           class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                    @error('title')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Kebutuhan & Latar Belakang <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="4" required placeholder="Jelaskan secara ringkas deskripsi teknologi, kebutuhan pendampingan, atau kendala yang dihadapi.."
                          class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid 2: Applicant, Institution, Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pemohon / Akun Pengusul
                    </label>
                    <select name="applicant_id" class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        @foreach($applicants as $user)
                            <option value="{{ $user->id }}" {{ (old('applicant_id', auth()->id()) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Unit / Fakultas / Startup / Instansi
                    </label>
                    <input type="text" name="institution" value="{{ old('institution') }}" placeholder="Contoh: STEI ITB / PT Inovasi Nusantara"
                           class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Kontak / WhatsApp
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890"
                           class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Grid 3: Optional Consultant & Schedule Assignment (Admin/Staff only) -->
            @can('update', \App\Models\ConsultationService::class)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <p class="text-xs font-bold text-slate-800 uppercase tracking-wider">Disposisi Konsultan & Jadwal Awal (Opsional)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Staf / Konsultan Penanggung Jawab</label>
                        <select name="consultant_id" class="w-full text-xs rounded-xl border-slate-200 py-2.5 bg-white">
                            <option value="">-- Belum Didisposisi --</option>
                            @foreach($consultants as $c)
                                <option value="{{ $c->id }}" {{ old('consultant_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Rencana Jadwal Konsultasi</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 bg-white">
                    </div>
                </div>
            </div>
            @endcan

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Daftarkan Permohonan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
