<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalAction;
use App\Models\AuditLog;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApprovalService
{
    /**
     * Submit a program for review.
     */
    public function submitProgram(Program $program, User $requester): Approval
    {
        if (! in_array($program->status, [Program::STATUS_DRAFT, Program::STATUS_REJECTED])) {
            throw new InvalidArgumentException("Program dengan status {$program->status} tidak dapat diajukan kembali.");
        }

        return DB::transaction(function () use ($program, $requester) {
            $oldStatus = $program->status;
            $program->update(['status' => Program::STATUS_SUBMITTED]);

            $approval = Approval::create([
                'program_id' => $program->id,
                'requested_by' => $requester->id,
                'status' => Approval::STATUS_PENDING,
            ]);

            ApprovalAction::create([
                'approval_id' => $approval->id,
                'user_id' => $requester->id,
                'action' => ApprovalAction::ACTION_SUBMIT,
                'comment' => 'Mengajukan usulan program untuk direview.',
            ]);

            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_SUBMIT,
                "Mengajukan usulan program: [{$program->code}] {$program->name}",
                $program,
                ['status' => $oldStatus],
                ['status' => Program::STATUS_SUBMITTED]
            );

            return $approval;
        });
    }

    /**
     * Approve a program.
     */
    public function approveProgram(Approval $approval, User $reviewer, ?string $comment = null): void
    {
        if ($approval->status !== Approval::STATUS_PENDING) {
            throw new InvalidArgumentException('Usulan ini sudah tidak dalam status pending review.');
        }

        DB::transaction(function () use ($approval, $reviewer, $comment) {
            $program = $approval->program;
            $oldStatus = $program->status;

            $approval->update([
                'reviewer_id' => $reviewer->id,
                'status' => Approval::STATUS_APPROVED,
                'processed_at' => now(),
            ]);

            ApprovalAction::create([
                'approval_id' => $approval->id,
                'user_id' => $reviewer->id,
                'action' => ApprovalAction::ACTION_APPROVE,
                'comment' => $comment ?? 'Usulan program disetujui.',
            ]);

            $program->update(['status' => Program::STATUS_APPROVED]);

            AuditLogService::log(
                AuditLog::MODULE_APPROVALS,
                AuditLog::ACTION_APPROVE,
                "Menyetujui usulan program: [{$program->code}] {$program->name}",
                $program,
                ['status' => $oldStatus],
                ['status' => Program::STATUS_APPROVED]
            );
        });
    }

    /**
     * Reject a program with a mandatory reason.
     */
    public function rejectProgram(Approval $approval, User $reviewer, string $reason): void
    {
        if ($approval->status !== Approval::STATUS_PENDING) {
            throw new InvalidArgumentException('Usulan ini sudah tidak dalam status pending review.');
        }

        if (empty(trim($reason))) {
            throw new InvalidArgumentException('Alasan penolakan wajib diisi.');
        }

        DB::transaction(function () use ($approval, $reviewer, $reason) {
            $program = $approval->program;
            $oldStatus = $program->status;

            $approval->update([
                'reviewer_id' => $reviewer->id,
                'status' => Approval::STATUS_REJECTED,
                'reason' => $reason,
                'processed_at' => now(),
            ]);

            ApprovalAction::create([
                'approval_id' => $approval->id,
                'user_id' => $reviewer->id,
                'action' => ApprovalAction::ACTION_REJECT,
                'comment' => $reason,
            ]);

            $program->update(['status' => Program::STATUS_REJECTED]);

            AuditLogService::log(
                AuditLog::MODULE_APPROVALS,
                AuditLog::ACTION_REJECT,
                "Menolak usulan program: [{$program->code}] {$program->name}. Alasan: {$reason}",
                $program,
                ['status' => $oldStatus],
                ['status' => Program::STATUS_REJECTED, 'reason' => $reason]
            );
        });
    }

    /**
     * Start execution of an approved program.
     */
    public function startProgram(Program $program, User $user): void
    {
        if (! in_array($program->status, [Program::STATUS_APPROVED, Program::STATUS_DRAFT])) {
            throw new InvalidArgumentException('Hanya program yang telah disetujui (Approved) yang dapat dimulai.');
        }

        DB::transaction(function () use ($program) {
            $oldStatus = $program->status;

            $program->update([
                'status' => Program::STATUS_IN_PROGRESS,
                'start_date' => $program->start_date ?? now()->toDateString(),
            ]);

            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_UPDATE,
                "Memulai pelaksanaan program: [{$program->code}] {$program->name}",
                $program,
                ['status' => $oldStatus],
                ['status' => Program::STATUS_IN_PROGRESS]
            );
        });
    }

    /**
     * Complete an in-progress program.
     */
    public function completeProgram(Program $program, User $user): void
    {
        if ($program->status !== Program::STATUS_IN_PROGRESS) {
            throw new InvalidArgumentException('Hanya program yang sedang berjalan (In Progress) yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($program) {
            $oldStatus = $program->status;

            $program->update([
                'status' => Program::STATUS_COMPLETED,
                'progress' => 100,
                'end_date' => $program->end_date ?? now()->toDateString(),
            ]);

            AuditLogService::log(
                AuditLog::MODULE_PROGRAMS,
                AuditLog::ACTION_UPDATE,
                "Menyelesaikan program: [{$program->code}] {$program->name} (Progres 100%)",
                $program,
                ['status' => $oldStatus, 'progress' => $program->progress],
                ['status' => Program::STATUS_COMPLETED, 'progress' => 100]
            );
        });
    }
}
