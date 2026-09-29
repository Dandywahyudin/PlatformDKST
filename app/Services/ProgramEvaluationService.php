<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Program;
use App\Models\ProgramEvaluation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgramEvaluationService
{
    /**
     * Create a new program evaluation record.
     */
    public function createEvaluation(array $data, User $evaluator): ProgramEvaluation
    {
        return DB::transaction(function () use ($data, $evaluator) {
            $data['evaluator_id'] = $data['evaluator_id'] ?? $evaluator->id;
            $data['status'] = $data['status'] ?? ProgramEvaluation::STATUS_REVIEWED;

            $evaluation = ProgramEvaluation::create($data);

            // If progress percentage is provided, synchronize program progress if approved/reviewed
            if (isset($data['progress_percentage'])) {
                $program = $evaluation->program;
                if ($program) {
                    $program->update(['progress' => $data['progress_percentage']]);
                }
            }

            AuditLogService::log(
                AuditLog::MODULE_MONEV,
                AuditLog::ACTION_CREATE,
                "Menginput laporan monev [{$evaluation->period_label}] program: {$evaluation->program?->name}",
                $evaluation,
                null,
                $evaluation->toArray()
            );

            return $evaluation;
        });
    }

    /**
     * Update an existing evaluation.
     */
    public function updateEvaluation(ProgramEvaluation $evaluation, array $data, User $user): ProgramEvaluation
    {
        return DB::transaction(function () use ($evaluation, $data) {
            $oldValues = $evaluation->toArray();
            $evaluation->update($data);

            if (isset($data['progress_percentage'])) {
                $program = $evaluation->program;
                if ($program) {
                    $program->update(['progress' => $data['progress_percentage']]);
                }
            }

            AuditLogService::log(
                AuditLog::MODULE_MONEV,
                AuditLog::ACTION_UPDATE,
                "Memperbarui laporan monev [{$evaluation->period_label}] program: {$evaluation->program?->name}",
                $evaluation,
                $oldValues,
                $evaluation->fresh()->toArray()
            );

            return $evaluation;
        });
    }

    /**
     * Delete an evaluation record.
     */
    public function deleteEvaluation(ProgramEvaluation $evaluation, User $user): void
    {
        DB::transaction(function () use ($evaluation) {
            $programName = $evaluation->program?->name;
            $period = $evaluation->period_label;

            $evaluation->delete();

            AuditLogService::log(
                AuditLog::MODULE_MONEV,
                AuditLog::ACTION_DELETE,
                "Menghapus laporan monev [{$period}] program: {$programName}",
                $evaluation,
                ['id' => $evaluation->id],
                null
            );
        });
    }
}
