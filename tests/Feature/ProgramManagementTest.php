<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use App\Services\ProgramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_create_program_with_proposal_pdf_and_doc_files(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $staff = User::factory()->create(['name' => 'Staf Pengembang']);

        $proposalPdf = UploadedFile::fake()->create('proposal_program_dkst.pdf', 500, 'application/pdf');
        $torDoc = UploadedFile::fake()->create('tor_program.docx', 300, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->post('/admin/programs', [
            'name' => 'Program Inkubasi Startup DeepTech',
            'description' => 'Proposal inkubasi startup deeptech 2026',
            'pic_id' => $staff->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-11-30',
            'budget' => 750000000,
            'proposal_file' => $proposalPdf,
            'additional_files' => [$torDoc],
        ]);

        $program = Program::where('name', 'Program Inkubasi Startup DeepTech')->first();
        $this->assertNotNull($program);
        $response->assertRedirect("/admin/programs/{$program->id}");

        // Assert documents exist in database
        $this->assertDatabaseHas('documents', [
            'program_id' => $program->id,
            'name' => 'Dokumen Proposal - Program Inkubasi Startup DeepTech',
            'file_type' => 'pdf',
            'category' => Document::CATEGORY_PROPOSAL,
        ]);

        $this->assertDatabaseHas('documents', [
            'program_id' => $program->id,
            'file_name' => 'tor_program.docx',
            'file_type' => 'docx',
            'category' => Document::CATEGORY_OTHER,
        ]);

        // Assert file stored in fake storage
        $doc = $program->documents()->where('category', Document::CATEGORY_PROPOSAL)->first();
        $this->assertNotNull($doc);
        Storage::disk('public')->assertExists($doc->file_path);

        // Test downloading the document
        $downloadResponse = $this->actingAs($admin)->get("/admin/documents/{$doc->id}/download");
        $downloadResponse->assertOk();

        // Test previewing the PDF document
        $previewResponse = $this->actingAs($admin)->get("/admin/documents/{$doc->id}/preview");
        $previewResponse->assertOk();
    }

    public function test_program_creation_fails_when_uploading_invalid_file_type(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $invalidFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($admin)->post('/admin/programs', [
            'name' => 'Program Invalid File Test',
            'start_date' => '2026-03-01',
            'end_date' => '2026-11-30',
            'budget' => 100000000,
            'proposal_file' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['proposal_file']);
    }

    public function test_director_can_download_and_preview_program_documents(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $directorRole = Role::where('slug', 'director')->first();
        $director = User::factory()->create();
        $director->roles()->attach($directorRole);

        $file = UploadedFile::fake()->create('usulan_resmi.pdf', 200, 'application/pdf');
        $filePath = $file->store('documents/programs', 'public');

        $program = Program::create([
            'code' => 'DKST-PRG-2026-0100',
            'name' => 'Program Uji Direktur Doc',
            'budget' => 200000000,
            'status' => Program::STATUS_SUBMITTED,
            'created_by' => $admin->id,
        ]);

        $doc = $program->documents()->create([
            'name' => 'Dokumen Usulan Program',
            'file_path' => $filePath,
            'file_name' => 'usulan_resmi.pdf',
            'file_type' => 'pdf',
            'file_size' => 200 * 1024,
            'category' => Document::CATEGORY_PROPOSAL,
            'uploaded_by' => $admin->id,
        ]);

        $downloadResponse = $this->actingAs($director)->get("/director/documents/{$doc->id}/download");
        $downloadResponse->assertOk();

        $previewResponse = $this->actingAs($director)->get("/director/documents/{$doc->id}/preview");
        $previewResponse->assertOk();
    }
}
