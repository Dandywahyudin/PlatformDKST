<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Program;
use App\Models\ProgramEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonevTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_view_monev_list(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/monev');

        $response->assertOk();
        $response->assertSee('Monitoring &amp; Evaluasi (Monev)', false);
    }

    public function test_user_can_create_monev_report_and_sync_program_progress(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0010',
            'name' => 'Program Inkubasi Inovasi Robotika',
            'budget' => 400000000,
            'progress' => 0,
            'status' => Program::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/monev', [
            'program_id' => $program->id,
            'evaluation_period' => ProgramEvaluation::PERIOD_Q2,
            'evaluation_date' => '2026-06-30',
            'progress_percentage' => 45,
            'budget_realization' => 175000000,
            'achievements' => 'Prototipe robot industri v1.0 berhasil diuji di lab.',
            'obstacles' => 'Komponen micro-controller sempat tertahan di bea cukai.',
            'recommendations' => 'Segera siapkan rencana pendaftaran paten desain.',
            'score' => 88,
        ]);

        $evaluation = ProgramEvaluation::where('program_id', $program->id)->first();
        $this->assertNotNull($evaluation);
        $this->assertSame(45, $evaluation->progress_percentage);
        $this->assertSame(88, $evaluation->score);

        $response->assertRedirect("/admin/monev/{$evaluation->id}");

        // Program progress synced
        $program->refresh();
        $this->assertSame(45, $program->progress);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_MONEV,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $evaluation->id,
        ]);
    }

    public function test_user_can_update_monev_report(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0011',
            'name' => 'Program Pengembangan Nanomaterial',
            'budget' => 300000000,
            'progress' => 30,
            'status' => Program::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);

        $evaluation = ProgramEvaluation::create([
            'program_id' => $program->id,
            'evaluator_id' => $admin->id,
            'evaluation_period' => ProgramEvaluation::PERIOD_Q1,
            'evaluation_date' => '2026-03-31',
            'progress_percentage' => 30,
            'budget_realization' => 90000000,
            'score' => 80,
            'status' => ProgramEvaluation::STATUS_REVIEWED,
        ]);

        $response = $this->actingAs($admin)->put("/admin/monev/{$evaluation->id}", [
            'program_id' => $program->id,
            'evaluation_period' => ProgramEvaluation::PERIOD_Q1,
            'evaluation_date' => '2026-03-31',
            'progress_percentage' => 35,
            'budget_realization' => 95000000,
            'achievements' => 'Capaian revisi meningkat.',
            'score' => 85,
        ]);

        $response->assertRedirect("/admin/monev/{$evaluation->id}");
        $evaluation->refresh();

        $this->assertSame(35, $evaluation->progress_percentage);
        $this->assertSame(85, $evaluation->score);
    }
}
