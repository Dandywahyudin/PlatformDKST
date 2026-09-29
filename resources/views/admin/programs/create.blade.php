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
        <form method="POST" action="{{ route('admin.programs.store') }}" class="space-y-6">
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
