@extends('layouts.admin')

@section('title', 'Buat Usulan Program Baru')
@section('page_title', 'Buat Program')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div>
        <a href="{{ route('admin.programs.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center mb-1">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Program
        </a>
        <h2 class="text-xl font-bold text-slate-900">Form Pengusulan Program DKST</h2>
        <p class="text-xs text-slate-500 mt-0.5">Kode program akan di-generate otomatis oleh sistem dengan format <code>DKST-PRG-YYYY-XXXX</code>.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.programs.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Pokok Usulan -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informasi Pokok Usulan</h3>
                <div class="space-y-4">
                    <!-- Program Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Usulan Program <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Program Akselerasi Inkubasi Startup DeepTech 2026" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Ruang Lingkup Program</label>
                        <textarea name="description" id="description" rows="4" placeholder="Jelaskan latar belakang, tujuan, target luaran, dan ruang lingkup pelaksanaan program..."
                                  class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Anggaran & Jadwal -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Anggaran & Jadwal Pelaksanaan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Budget -->
                    <div>
                        <label for="budget" class="block text-xs font-semibold text-slate-700 mb-1">Anggaran Biaya (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-semibold">Rp</span>
                            <input type="number" step="1000" min="0" name="budget" id="budget" value="{{ old('budget', 0) }}" required
                                   class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 font-mono @error('budget') border-rose-500 @enderror">
                        </div>
                        @error('budget')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                               class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('end_date') border-rose-500 @enderror">
                        @error('end_date')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Person In Charge (PIC) & Tim -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Penanggung Jawab & Anggota Tim</h3>
                <div class="space-y-4">
                    <!-- PIC Selection -->
                    <div>
                        <label for="pic_id" class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC)</label>
                        <select name="pic_id" id="pic_id" class="w-full text-xs rounded-xl border-slate-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih PIC dari Akun Pengguna Terdaftar --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ old('pic_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->position ?? $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Team Members Checkboxes -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Anggota Tim Pelaksana (Opsional)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                            @foreach ($users as $u)
                                <label class="flex items-center p-2 rounded-xl bg-white border border-slate-200 hover:border-blue-300 cursor-pointer text-xs">
                                    <input type="checkbox" name="members[]" value="{{ $u->id }}"
                                           {{ in_array($u->id, old('members', [])) ? 'checked' : '' }}
                                           class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-blue-500 border-slate-300 mr-2">
                                    <span class="font-medium text-slate-800">{{ $u->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Berkas Proposal & Dokumen Pendukung (PDF/DOC) -->
            <div x-data="{ proposalFileName: '', proposalFileSize: '', additionalCount: 0 }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Unggah Berkas Proposal & Lampiran</h3>
                        <p class="text-xs text-slate-500">Lampirkan berkas dokumen proposal resmi untuk ditinjau oleh pimpinan/reviewer.</p>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">PDF</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">DOC / DOCX</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Utama: Proposal File Upload -->
                    <div>
                        <label for="proposal_file" class="block text-xs font-semibold text-slate-700 mb-1">
                            Berkas Utama Proposal (PDF / DOC / DOCX)
                        </label>
                        <div class="relative border-2 border-dashed rounded-2xl p-4 text-center transition-colors hover:border-blue-400 bg-slate-50/50 @error('proposal_file') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror">
                            <input type="file" name="proposal_file" id="proposal_file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                   @change="
                                       if ($event.target.files.length > 0) {
                                           proposalFileName = $event.target.files[0].name;
                                           proposalFileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                                       } else {
                                           proposalFileName = '';
                                           proposalFileSize = '';
                                       }
                                   "
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-blue-600">Klik untuk memilih berkas proposal</span> atau seret ke area ini
                                </div>
                                <p class="text-[11px] text-slate-400">Mendukung format: <strong class="text-slate-600">.PDF, .DOC, .DOCX</strong> (Maksimal 20 MB)</p>
                            </div>

                            <!-- Selected File Indicator -->
                            <template x-if="proposalFileName">
                                <div class="mt-3 p-2.5 bg-white rounded-xl border border-blue-200 flex items-center justify-between text-xs shadow-sm">
                                    <div class="flex items-center space-x-2 truncate">
                                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">📄</span>
                                        <span class="font-semibold text-slate-800 truncate" x-text="proposalFileName"></span>
                                    </div>
                                    <span class="text-[11px] font-mono font-medium text-slate-500 shrink-0 ml-2" x-text="proposalFileSize"></span>
                                </div>
                            </template>
                        </div>
                        @error('proposal_file')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lampiran Tambahan (Optional Additional Documents) -->
                    <div>
                        <label for="additional_files" class="block text-xs font-semibold text-slate-700 mb-1">
                            Berkas Pendukung / Lampiran Tambahan (TOR, RAB, Format Excel, dll.)
                        </label>
                        <input type="file" name="additional_files[]" id="additional_files" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                               @change="additionalCount = $event.target.files.length"
                               class="w-full text-xs rounded-xl border-slate-200 py-2 focus:border-blue-500 focus:ring-blue-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="text-[11px] text-slate-400 mt-1">Dapat memilih lebih dari satu berkas (Multi-file upload). Maksimal 20 MB per berkas.</p>
                        @error('additional_files.*')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.programs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                    Simpan sebagai Draft
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
