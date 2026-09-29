<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Program;
use App\Models\User;
use App\Services\ProgramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_program_list(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/programs');

        $response->assertOk();
        $response->assertSee('Program');
        $response->assertSee('Buat Usulan Program');
    }

    public function test_program_service_generates_sequential_codes(): void
    {
        $service = new ProgramService;
        $year = (int) date('Y');

        $code1 = $service->generateProgramCode($year);
        $this->assertSame("DKST-PRG-{$year}-0001", $code1);

        Program::create([
            'code' => $code1,
            'name' => 'Program 1',
            'budget' => 100000000,
            'status' => Program::STATUS_DRAFT,
        ]);

        $code2 = $service->generateProgramCode($year);
        $this->assertSame("DKST-PRG-{$year}-0002", $code2);
    }

    public function test_admin_can_create_new_program_draft(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $staff = User::factory()->create(['name' => 'Staf Pengembang']);

        $response = $this->actingAs($admin)->post('/admin/programs', [
            'name' => 'Program Akselerasi Inkubasi 2026',
            'description' => 'Deskripsi program akselerasi',
            'pic_id' => $staff->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-11-30',
            'budget' => 500000000,
            'members' => [$staff->id],
        ]);

        $year = (int) date('Y', strtotime('2026-03-01'));
        $expectedCode = "DKST-PRG-{$year}-0001";

        $program = Program::where('code', $expectedCode)->first();
        $this->assertNotNull($program);
        $this->assertSame('Program Akselerasi Inkubasi 2026', $program->name);
        $this->assertSame(Program::STATUS_DRAFT, $program->status);
        $this->assertSame($staff->id, $program->pic_id);
        $this->assertCount(1, $program->members);

        $response->assertRedirect("/admin/programs/{$program->id}");

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_PROGRAMS,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $program->id,
        ]);
    }

    public function test_admin_can_update_program_and_progress(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $program = Program::create([
            'code' => 'DKST-PRG-2026-0099',
            'name' => 'Nama Awal',
            'budget' => 100000000,
            'progress' => 0,
            'status' => Program::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->put("/admin/programs/{$program->id}", [
            'name' => 'Nama Usulan Terupdate',
            'description' => 'Deskripsi baru',
            'budget' => 150000000,
            'progress' => 25,
            'start_date' => '2026-04-01',
            'end_date' => '2026-12-31',
        ]);

        $response->assertRedirect("/admin/programs/{$program->id}");
        $program->refresh();

        $this->assertSame('Nama Usulan Terupdate', $program->name);
        $this->assertSame(25, $program->progress);
        $this->assertEquals('150000000.00', $program->budget);
    }

    public function test_admin_can_delete_draft_or_rejected_program(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $program = Program::create([
            'code' => 'DKST-PRG-2026-0005',
            'name' => 'Program Draft Hapus',
            'budget' => 50000000,
            'status' => Program::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/programs/{$program->id}");

        $response->assertRedirect('/admin/programs');
        $this->assertSoftDeleted($program);
    }

    public function test_admin_cannot_delete_active_in_progress_program(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $program = Program::create([
            'code' => 'DKST-PRG-2026-0006',
            'name' => 'Program Aktif Berjalan',
            'budget' => 250000000,
            'status' => Program::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/programs/{$program->id}");

        $response->assertForbidden();
        $this->assertNotSoftDeleted($program);
    }
}
