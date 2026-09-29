<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectorDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_director_can_access_director_dashboard(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        // Create sample programs
        Program::create([
            'code' => 'DKST-PRG-2026-0001',
            'name' => 'Program Inkubasi Bisnis Deeptech',
            'budget' => 500000000,
            'progress' => 65,
            'status' => Program::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);

        Program::create([
            'code' => 'DKST-PRG-2026-0002',
            'name' => 'Program Fasilitasi Paten Strategis',
            'budget' => 200000000,
            'progress' => 20,
            'status' => Program::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get('/director/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard Direktur');
        $response->assertSee('Ringkasan Eksekutif Program');
        $response->assertSee('Total Portofolio');
        $response->assertSee('Realisasi Anggaran');
        $response->assertSee('Kesehatan Pelaksanaan Program');
        $response->assertSee('Capaian Indikator Kinerja Utama');
    }

    public function test_director_dashboard_with_year_filter(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/director/dashboard?year=2026');

        $response->assertOk();
        $response->assertSee('Tahun 2026');
    }

    public function test_director_user_redirected_to_director_dashboard_from_root_dashboard(): void
    {
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create([
            'email' => 'director@dkst.itb.ac.id',
            'status' => 'active',
        ]);
        $director->roles()->sync([$directorRole->id]);

        $response = $this->actingAs($director)->get('/dashboard');

        $response->assertRedirect('/director/dashboard');
    }

    public function test_admin_user_redirected_to_admin_dashboard_from_root_dashboard(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_director_can_access_approvals_inbox(): void
    {
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create([
            'email' => 'director.approval@dkst.itb.ac.id',
            'status' => 'active',
        ]);
        $director->roles()->sync([$directorRole->id]);

        $response = $this->actingAs($director)->get(route('director.approvals.index'));

        $response->assertOk();
        $response->assertSee('Antrean Persetujuan Usulan Program');
    }

    public function test_director_can_approve_submitted_program_via_director_approvals_route(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create([
            'email' => 'director.reviewer@dkst.itb.ac.id',
            'status' => 'active',
        ]);
        $director->roles()->sync([$directorRole->id]);

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0099',
            'name' => 'Program Inkubasi Flagship 2026',
            'budget' => 350000000,
            'progress' => 0,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $approval = Approval::create([
            'program_id' => $program->id,
            'requested_by' => $admin->id,
            'status' => Approval::STATUS_PENDING,
        ]);

        $showResponse = $this->actingAs($director)->get(route('director.approvals.show', $approval->id));
        $showResponse->assertOk();
        $showResponse->assertSee('Tinjau Usulan');

        $response = $this->actingAs($director)->post(route('director.approvals.approve', $approval->id), [
            'comment' => 'Disetujui untuk diimplementasikan via portal direktur.',
        ]);

        $response->assertRedirect(route('director.approvals.show', $approval->id));
        $this->assertDatabaseHas('approvals', [
            'id' => $approval->id,
            'status' => Approval::STATUS_APPROVED,
            'reviewer_id' => $director->id,
        ]);
        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'status' => Program::STATUS_APPROVED,
        ]);
    }

    public function test_director_can_reject_submitted_program_with_reason_via_director_route(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create([
            'email' => 'director.reject@dkst.itb.ac.id',
            'status' => 'active',
        ]);
        $director->roles()->sync([$directorRole->id]);

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0098',
            'name' => 'Program Riset Belum Lengkap',
            'budget' => 150000000,
            'progress' => 0,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $approval = Approval::create([
            'program_id' => $program->id,
            'requested_by' => $admin->id,
            'status' => Approval::STATUS_PENDING,
        ]);

        $response = $this->actingAs($director)->post(route('director.approvals.reject', $approval->id), [
            'reason' => 'RAB dan rincian deliverable perlu dilengkapi terlebih dahulu.',
        ]);

        $response->assertRedirect(route('director.approvals.show', $approval->id));
        $this->assertDatabaseHas('approvals', [
            'id' => $approval->id,
            'status' => Approval::STATUS_REJECTED,
            'reviewer_id' => $director->id,
            'reason' => 'RAB dan rincian deliverable perlu dilengkapi terlebih dahulu.',
        ]);
        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'status' => Program::STATUS_REJECTED,
        ]);
    }

    public function test_director_can_view_and_approve_program_via_director_program_route(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create([
            'email' => 'director.direct@dkst.itb.ac.id',
            'status' => 'active',
        ]);
        $director->roles()->sync([$directorRole->id]);

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0097',
            'name' => 'Program Akselerasi Startup 2026',
            'budget' => 450000000,
            'progress' => 0,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $viewResponse = $this->actingAs($director)->get(route('director.programs.show', $program->id));
        $viewResponse->assertOk();

        $response = $this->actingAs($director)->post(route('director.programs.approve', $program->id), [
            'comment' => 'Disetujui langsung oleh direktur.',
        ]);

        $response->assertRedirect(route('director.programs.show', $program->id));
        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'status' => Program::STATUS_APPROVED,
        ]);
    }
}
