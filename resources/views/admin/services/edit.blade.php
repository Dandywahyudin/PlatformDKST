@extends('layouts.admin')

@section('title', "Edit Tiket [{$service->ticket_number}] — Layanan & Konsultasi")

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.services.show', $service->id) }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Edit Data Layanan & Konsultasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi tiket permohonan [{{ $service->ticket_number }}].</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.services.update', $service->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Grid 1: Service Type, Status & Title -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Layanan <span class="text-rose-500">*</span>
                    </label>
                    <select name="service_type" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="VALUASI_TEKNOLOGI" {{ old('service_type', $service->service_type) === 'VALUASI_TEKNOLOGI' ? 'selected' : '' }}>Valuasi Teknologi & Paten</option>
                        <option value="FASILITASI_HKI" {{ old('service_type', $service->service_type) === 'FASILITASI_HKI' ? 'selected' : '' }}>Fasilitasi Pendaftaran HKI / Paten</option>
                        <option value="INKUBASI_STARTUP" {{ old('service_type', $service->service_type) === 'INKUBASI_STARTUP' ? 'selected' : '' }}>Pendampingan & Inkubasi Startup</option>
                        <option value="HILIRISASI_INDUSTRI" {{ old('service_type', $service->service_type) === 'HILIRISASI_INDUSTRI' ? 'selected' : '' }}>Konsultasi Hilirisasi Industri</option>
                        <option value="LEGALITAS_KONTRAK" {{ old('service_type', $service->service_type) === 'LEGALITAS_KONTRAK' ? 'selected' : '' }}>Penyusunan Kontrak Lisensi & Kerjasama</option>
                        <option value="LAINNYA" {{ old('service_type', $service->service_type) === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Permohonan
                    </label>
                    <select name="status" class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="PENDING" {{ old('status', $service->status) === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                        <option value="IN_REVIEW" {{ old('status', $service->status) === 'IN_REVIEW' ? 'selected' : '' }}>IN_REVIEW</option>
                        <option value="SCHEDULED" {{ old('status', $service->status) === 'SCHEDULED' ? 'selected' : '' }}>SCHEDULED</option>
                        <option value="COMPLETED" {{ old('status', $service->status) === 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                        <option value="REJECTED" {{ old('status', $service->status) === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                        <option value="CANCELLED" {{ old('status', $service->status) === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Topik / Judul Konsultasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $service->title) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Kebutuhan & Latar Belakang <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="4" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $service->description) }}</textarea>
            </div>

            <!-- Grid 2: Applicant, Institution, Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pemohon
                    </label>
                    <select name="applicant_id" class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        @foreach($applicants as $user)
                            <option value="{{ $user->id }}" {{ old('applicant_id', $service->applicant_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Unit / Instansi
                    </label>
                    <input type="text" name="institution" value="{{ old('institution', $service->institution) }}" class="w-full text-xs rounded-xl border-slate-200 py-3">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Kontak
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $service->phone) }}" class="w-full text-xs rounded-xl border-slate-200 py-3">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.services.show', $service->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
