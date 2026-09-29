@extends('layouts.admin')

@section('title', 'Tambah Indikator Kinerja & Dampak')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.impact.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Tambah Indikator Capaian Dampak</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan metrik IKU/indikator kinerja dampak inovasi DKST.</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.impact.store') }}" class="space-y-6">
            @csrf

            <!-- Grid 1: Year & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tahun Indikator <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" min="2020" max="2035" required
                           class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Dampak <span class="text-rose-500">*</span>
                    </label>
                    <select name="category" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="STARTUP_GROWTH" {{ old('category') === 'STARTUP_GROWTH' ? 'selected' : '' }}>Pertumbuhan Startup</option>
                        <option value="PATENT_HKI" {{ old('category') === 'PATENT_HKI' ? 'selected' : '' }}>Paten & HKI</option>
                        <option value="COMMERCIALIZATION" {{ old('category') === 'COMMERCIALIZATION' ? 'selected' : '' }}>Komersialisasi & Royalti</option>
                        <option value="WORKFORCE" {{ old('category') === 'WORKFORCE' ? 'selected' : '' }}>Penyerapan Tenaga Kerja</option>
                        <option value="FUNDING_INVESTMENT" {{ old('category') === 'FUNDING_INVESTMENT' ? 'selected' : '' }}>Pendanaan & Investasi</option>
                        <option value="SOCIO_ECONOMIC" {{ old('category') === 'SOCIO_ECONOMIC' ? 'selected' : '' }}>Dampak Sosial & Mitra</option>
                    </select>
                </div>
            </div>

            <!-- Metric Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Indikator Kinerja <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="metric_name" value="{{ old('metric_name') }}" required placeholder="Contoh: Jumlah Paten Riset Granted dan Siap Hilirisasi"
                       class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Grid 2: Target, Realized & Unit -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Target Tahunan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="any" name="target_value" value="{{ old('target_value', 0) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Realisasi Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="any" name="realized_value" value="{{ old('realized_value', 0) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Satuan / Unit <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="unit" value="{{ old('unit', 'Unit') }}" required placeholder="Unit Startup / Paten / Rupiah / Orang"
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan & Definisi Operasional
                </label>
                <textarea name="description" rows="3" placeholder="Jelaskan ruang lingkup pengukuran indikator.."
                          class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.impact.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-all">
                    Simpan Indikator
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
