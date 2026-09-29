<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProgramService
{
    /**
     * Generate the next sequential program code for the specified year.
     * Format: DKST-PRG-YYYY-XXXX (e.g. DKST-PRG-2026-0001)
     */
    public function generateProgramCode(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = "DKST-PRG-{$year}-";

        // Include trashed records to avoid primary/unique key collisions
        $lastProgram = Program::withTrashed()
            ->where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        if (! $lastProgram) {
            $nextNumber = 1;
        } else {
            $lastCode = $lastProgram->code;
            $lastNumber = (int) substr($lastCode, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('%s%04d', $prefix, $nextNumber);
    }

    /**
     * Create a new program in draft status.
     *
     * @param  array<string, mixed>  $data
     */
    public function createProgram(array $data, User $creator): Program
    {
        return DB::transaction(function () use ($data, $creator) {
            $year = ! empty($data['start_date']) ? (int) date('Y', strtotime($data['start_date'])) : (int) date('Y');
            $code = $this->generateProgramCode($year);

            $picUser = ! empty($data['pic_id']) ? User::find($data['pic_id']) : null;

            $program = Program::create([
                'code' => $code,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'pic_id' => $picUser?->id,
                'pic_name' => $picUser?->name ?? ($data['pic_name'] ?? null),
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'budget' => $data['budget'] ?? 0,
                'progress' => 0,
                'status' => Program::STATUS_DRAFT,
                'created_by' => $creator->id,
            ]);

            if (! empty($data['members']) && is_array($data['members'])) {
                $memberSync = [];
                foreach ($data['members'] as $memberId) {
                    $memberSync[$memberId] = ['role' => 'MEMBER'];
                }
                $program->members()->sync($memberSync);
            }

            // Handle Proposal Document Upload (PDF / DOC / DOCX)
            if (isset($data['proposal_file']) && $data['proposal_file'] instanceof UploadedFile) {
                $file = $data['proposal_file'];
                $path = $file->store('documents/programs', 'public');
                Document::create([
                    'program_id' => $program->id,
                    'uploaded_by' => $creator->id,
                    'name' => 'Dokumen Proposal - '.$program->name,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => strtolower($file->getClientOriginalExtension()),
                    'file_size' => $file->getSize(),
                    'category' => Document::CATEGORY_PROPOSAL,
                    'description' => 'Dokumen proposal usulan program resmi yang diunggah saat pengajuan.',
                ]);
            }

            // Handle Additional Supporting Documents
            if (! empty($data['additional_files']) && is_array($data['additional_files'])) {
                foreach ($data['additional_files'] as $file) {
                    if ($file instanceof UploadedFile) {
                        $path = $file->store('documents/programs', 'public');
                        Document::create([
                            'program_id' => $program->id,
                            'uploaded_by' => $creator->id,
                            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => strtolower($file->getClientOriginalExtension()),
                            'file_size' => $file->getSize(),
                            'category' => Document::CATEGORY_OTHER,
                            'description' => 'Dokumen lampiran pendukung usulan program.',
                        ]);
                    }
                }
            }

            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_CREATE,
                "Membuat usulan program baru: [{$program->code}] {$program->name}",
                $program,
                null,
                $program->only(['code', 'name', 'budget', 'status', 'start_date', 'end_date'])
            );

            return $program;
        });
    }

    /**
     * Update an existing program.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProgram(Program $program, array $data, User $updater): Program
    {
        return DB::transaction(function () use ($program, $data, $updater) {
            $oldData = $program->only(['name', 'description', 'pic_id', 'start_date', 'end_date', 'budget', 'progress', 'status']);

            $picUser = ! empty($data['pic_id']) ? User::find($data['pic_id']) : null;

            $updateFields = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'pic_id' => $picUser?->id,
                'pic_name' => $picUser?->name ?? ($data['pic_name'] ?? $program->pic_name),
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'budget' => $data['budget'] ?? 0,
            ];

            if (isset($data['progress'])) {
                $updateFields['progress'] = (int) $data['progress'];
            }

            $program->update($updateFields);

            if (isset($data['members']) && is_array($data['members'])) {
                $memberSync = [];
                foreach ($data['members'] as $memberId) {
                    $memberSync[$memberId] = ['role' => 'MEMBER'];
                }
                $program->members()->sync($memberSync);
            }

            // Handle New Proposal Document Upload if provided
            if (isset($data['proposal_file']) && $data['proposal_file'] instanceof UploadedFile) {
                $file = $data['proposal_file'];
                $path = $file->store('documents/programs', 'public');
                Document::create([
                    'program_id' => $program->id,
                    'uploaded_by' => $updater->id,
                    'name' => 'Dokumen Proposal (Revisi) - '.$program->name,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => strtolower($file->getClientOriginalExtension()),
                    'file_size' => $file->getSize(),
                    'category' => Document::CATEGORY_PROPOSAL,
                    'description' => 'Dokumen proposal usulan program diperbarui.',
                ]);
            }

            // Handle Additional Supporting Documents
            if (! empty($data['additional_files']) && is_array($data['additional_files'])) {
                foreach ($data['additional_files'] as $file) {
                    if ($file instanceof UploadedFile) {
                        $path = $file->store('documents/programs', 'public');
                        Document::create([
                            'program_id' => $program->id,
                            'uploaded_by' => $updater->id,
                            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => strtolower($file->getClientOriginalExtension()),
                            'file_size' => $file->getSize(),
                            'category' => Document::CATEGORY_OTHER,
                            'description' => 'Dokumen lampiran pendukung usulan program.',
                        ]);
                    }
                }
            }

            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_UPDATE,
                "Memperbarui data program: [{$program->code}] {$program->name}",
                $program,
                $oldData,
                $program->only(['name', 'description', 'pic_id', 'start_date', 'end_date', 'budget', 'progress'])
            );

            return $program;
        });
    }

    /**
     * Delete a program (soft delete).
     */
    public function deleteProgram(Program $program, User $deleter): bool
    {
        return DB::transaction(function () use ($program) {
            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_DELETE,
                "Menghapus program: [{$program->code}] {$program->name}",
                $program
            );

            return (bool) $program->delete();
        });
    }
}
