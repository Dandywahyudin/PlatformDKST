@extends('layouts.admin')

@section('title', 'Edit Laporan Monitoring & Evaluasi (Monev)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.monev.show', $evaluation->id) }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Edit Laporan Monev</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui hasil evaluasi untuk program [{{ $evaluation->program?->code }}].</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.monev.update', $evaluation->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Grid 1: Program & Periode -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Program DKST yang Dievaluasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="program_id" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id', $evaluation->program_id) == $p->id ? 'selected' : '' }}>
                                [{{ $p->code }}] {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Periode Evaluasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="evaluation_period" required class="w-full text-xs rounded-xl border-slate-200 py-3 focus:border-blue-500 focus:ring-blue-500">
                        <option value="TRIWULAN_1" {{ old('evaluation_period', $evaluation->evaluation_period) === 'TRIWULAN_1' ? 'selected' : '' }}>Triwulan I (Q1)</option>
                        <option value="TRIWULAN_2" {{ old('evaluation_period', $evaluation->evaluation_period) === 'TRIWULAN_2' ? 'selected' : '' }}>Triwulan II (Q2)</option>
                        <option value="TRIWULAN_3" {{ old('evaluation_period', $evaluation->evaluation_period) === 'TRIWULAN_3' ? 'selected' : '' }}>Triwulan III (Q3)</option>
                        <option value="TRIWULAN_4" {{ old('evaluation_period', $evaluation->evaluation_period) === 'TRIWULAN_4' ? 'selected' : '' }}>Triwulan IV (Q4)</option>
                        <option value="MIDTERM" {{ old('evaluation_period', $evaluation->evaluation_period) === 'MIDTERM' ? 'selected' : '' }}>Evaluasi Paruh Waktu (Mid-Term)</option>
                        <option value="FINAL" {{ old('evaluation_period', $evaluation->evaluation_period) === 'FINAL' ? 'selected' : '' }}>Evaluasi Akhir (Final)</option>
                        <option value="MONTHLY" {{ old('evaluation_period', $evaluation->evaluation_period) === 'MONTHLY' ? 'selected' : '' }}>Monitoring Bulanan</option>
                    </select>
                </div>
            </div>

            <!-- Grid 2: Tanggal, Progres, Realisasi Anggaran, Skor -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Evaluasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="evaluation_date" value="{{ old('evaluation_date', $evaluation->evaluation_date->format('Y-m-d')) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Progres Capaian (%) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="progress_percentage" min="0" max="100" value="{{ old('progress_percentage', $evaluation->progress_percentage) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Realisasi Anggaran (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="1000" name="budget_realization" min="0" value="{{ old('budget_realization', (int)$evaluation->budget_realization) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Skor Evaluasi (0-100) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="0" max="100" name="score" value="{{ old('score', $evaluation->score) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Achievements -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Capaian Output & Milestone
                </label>
                <textarea name="achievements" rows="3" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('achievements', $evaluation->achievements) }}</textarea>
            </div>

            <!-- Obstacles & Recommendations -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kendala & Hambatan Pelaksanaan
                    </label>
                    <textarea name="obstacles" rows="3" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('obstacles', $evaluation->obstacles) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Rekomendasi & Arahan Reviewer
                    </label>
                    <textarea name="recommendations" rows="3" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('recommendations', $evaluation->recommendations) }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.monev.show', $evaluation->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
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
