<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\ApprovalAction;
use App\Models\AuditLog;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_program_can_be_submitted_for_approval(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0001',
            'name' => 'Program Inkubasi Hilirisasi Riset',
            'budget' => 250000000,
            'status' => Program::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post("/admin/programs/{$program->id}/submit");

        $response->assertRedirect("/admin/programs/{$program->id}");
        $program->refresh();

        $this->assertSame(Program::STATUS_SUBMITTED, $program->status);

        // Verify Approval record created
        $approval = Approval::where('program_id', $program->id)->latest()->first();
        $this->assertNotNull($approval);
        $this->assertSame(Approval::STATUS_PENDING, $approval->status);
        $this->assertSame($admin->id, $approval->requested_by);

        // Verify ApprovalAction created
        $this->assertDatabaseHas('approval_actions', [
            'approval_id' => $approval->id,
            'user_id' => $admin->id,
            'action' => ApprovalAction::ACTION_SUBMIT,
        ]);

        // Verify AuditLog recorded
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_PROGRAMS,
            'action' => AuditLog::ACTION_SUBMIT,
            'entity_id' => $program->id,
        ]);
    }

    public function test_admin_can_view_approvals_inbox(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0002',
            'name' => 'Program Inkubasi Startup AI',
            'budget' => 300000000,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $approval = Approval::create([
            'program_id' => $program->id,
            'requested_by' => $admin->id,
            'status' => Approval::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->get('/admin/approvals');

        $response->assertOk();
        $response->assertSee('Antrean Persetujuan Usulan Program');
        $response->assertSee($program->code);
    }

    public function test_admin_can_approve_submitted_program(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0003',
            'name' => 'Program Komersialisasi Paten',
            'budget' => 450000000,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $approval = Approval::create([
            'program_id' => $program->id,
            'requested_by' => $admin->id,
            'status' => Approval::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/approvals/{$approval->id}/approve", [
            'comment' => 'Proposal telah ditinjau dan memenuhi kriteria pendanaan DKST.',
        ]);

        $response->assertRedirect("/admin/approvals/{$approval->id}");
        $approval->refresh();
        $program->refresh();

        $this->assertSame(Approval::STATUS_APPROVED, $approval->status);
        $this->assertSame($admin->id, $approval->reviewer_id);
        $this->assertNotNull($approval->processed_at);
        $this->assertSame(Program::STATUS_APPROVED, $program->status);

        // Verify action log
        $this->assertDatabaseHas('approval_actions', [
            'approval_id' => $approval->id,
            'user_id' => $admin->id,
            'action' => ApprovalAction::ACTION_APPROVE,
        ]);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_APPROVALS,
            'action' => AuditLog::ACTION_APPROVE,
            'entity_id' => $program->id,
        ]);
    }

    public function test_admin_can_reject_submitted_program_with_mandatory_reason(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0004',
            'name' => 'Program Pengujian Prototipe',
            'budget' => 120000000,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $approval = Approval::create([
            'program_id' => $program->id,
            'requested_by' => $admin->id,
            'status' => Approval::STATUS_PENDING,
        ]);

        // Attempt rejection without reason -> validation error
        $failedResponse = $this->actingAs($admin)->post("/admin/approvals/{$approval->id}/reject", [
            'reason' => '',
        ]);
        $failedResponse->assertSessionHasErrors('reason');

        // Valid rejection
        $reasonText = 'Rincian anggaran RAB perlu disesuaikan dengan standar SBM ITB 2026.';
        $successResponse = $this->actingAs($admin)->post("/admin/approvals/{$approval->id}/reject", [
            'reason' => $reasonText,
        ]);

        $successResponse->assertRedirect("/admin/approvals/{$approval->id}");
        $approval->refresh();
        $program->refresh();

        $this->assertSame(Approval::STATUS_REJECTED, $approval->status);
        $this->assertSame($reasonText, $approval->reason);
        $this->assertSame(Program::STATUS_REJECTED, $program->status);

        // Verify action log
        $this->assertDatabaseHas('approval_actions', [
            'approval_id' => $approval->id,
            'user_id' => $admin->id,
            'action' => ApprovalAction::ACTION_REJECT,
            'comment' => $reasonText,
        ]);
    }

    public function test_rejected_program_can_be_resubmitted(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0005',
            'name' => 'Program Inkubasi Startup Revisi',
            'budget' => 200000000,
            'status' => Program::STATUS_REJECTED,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post("/admin/programs/{$program->id}/submit");

        $response->assertRedirect("/admin/programs/{$program->id}");
        $program->refresh();

        $this->assertSame(Program::STATUS_SUBMITTED, $program->status);
        $this->assertDatabaseHas('approvals', [
            'program_id' => $program->id,
            'status' => Approval::STATUS_PENDING,
        ]);
    }

    public function test_approved_program_can_be_started_and_completed(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0006',
            'name' => 'Program Akselerasi Startup Deeptech',
            'budget' => 500000000,
            'progress' => 0,
            'status' => Program::STATUS_APPROVED,
            'created_by' => $admin->id,
        ]);

        // Start program
        $startResponse = $this->actingAs($admin)->post("/admin/programs/{$program->id}/start");
        $startResponse->assertRedirect("/admin/programs/{$program->id}");
        $program->refresh();

        $this->assertSame(Program::STATUS_IN_PROGRESS, $program->status);

        // Complete program
        $completeResponse = $this->actingAs($admin)->post("/admin/programs/{$program->id}/complete");
        $completeResponse->assertRedirect("/admin/programs/{$program->id}");
        $program->refresh();

        $this->assertSame(Program::STATUS_COMPLETED, $program->status);
        $this->assertSame(100, $program->progress);
    }
}
