<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ConsultationService;
use App\Models\User;
use App\Services\ConsultationServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_view_services_list(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/services');

        $response->assertOk();
        $response->assertSee('Layanan &amp; Konsultasi DKST', false);
    }

    public function test_user_can_create_consultation_request_with_sequential_ticket(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $serviceService = new ConsultationServiceService;
        $expectedTicket = $serviceService->generateTicketNumber();

        $response = $this->actingAs($admin)->post('/admin/services', [
            'service_type' => ConsultationService::TYPE_VALUASI_TEKNOLOGI,
            'title' => 'Permohonan Valuasi Paten Kendaraan Listrik',
            'description' => 'Kami memerlukan bantuan valuasi teknologi paten baterai sebelum lisensi industri.',
            'institution' => 'FTMD ITB / PT Maju Teknologi',
            'phone' => '081234567890',
        ]);

        $service = ConsultationService::where('ticket_number', $expectedTicket)->first();
        $this->assertNotNull($service);
        $this->assertSame(ConsultationService::STATUS_PENDING, $service->status);
        $this->assertSame('Permohonan Valuasi Paten Kendaraan Listrik', $service->title);

        $response->assertRedirect("/admin/services/{$service->id}");

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_SERVICES,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $service->id,
        ]);
    }

    public function test_admin_can_schedule_consultation_session(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $consultant = User::factory()->create(['name' => 'Konsultan Senior DKST']);

        $service = ConsultationService::create([
            'ticket_number' => 'DKST-SRV-2026-0002',
            'service_type' => ConsultationService::TYPE_FASILITASI_HKI,
            'title' => 'Fasilitasi Paten Sederhana IoT',
            'description' => 'Pendampingan pendaftaran paten draft klaim.',
            'applicant_id' => $admin->id,
            'status' => ConsultationService::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/services/{$service->id}/schedule", [
            'consultant_id' => $consultant->id,
            'scheduled_at' => '2026-10-15 10:00:00',
            'meeting_link_or_location' => 'Ruang Rapat DKST ITB Lt. 2',
        ]);

        $response->assertRedirect("/admin/services/{$service->id}");
        $service->refresh();

        $this->assertSame(ConsultationService::STATUS_SCHEDULED, $service->status);
        $this->assertSame($consultant->id, $service->consultant_id);
        $this->assertSame('Ruang Rapat DKST ITB Lt. 2', $service->meeting_link_or_location);
    }

    public function test_admin_can_complete_consultation_session_with_notes(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $service = ConsultationService::create([
            'ticket_number' => 'DKST-SRV-2026-0003',
            'service_type' => ConsultationService::TYPE_INKUBASI_STARTUP,
            'title' => 'Inkubasi Startup HealthTech',
            'description' => 'Konsultasi model bisnis dan legalitas.',
            'applicant_id' => $admin->id,
            'status' => ConsultationService::STATUS_SCHEDULED,
        ]);

        $notes = 'Rekomendasi: Melakukan uji klinis tahap 1 dan mendaftarkan izin edar Kemenkes.';
        $actionPlan = 'Pemohon melengkapi dokumen perizinan dalam 30 hari kerja.';

        $response = $this->actingAs($admin)->post("/admin/services/{$service->id}/complete", [
            'consultation_notes' => $notes,
            'action_plan' => $actionPlan,
        ]);

        $response->assertRedirect("/admin/services/{$service->id}");
        $service->refresh();

        $this->assertSame(ConsultationService::STATUS_COMPLETED, $service->status);
        $this->assertSame($notes, $service->consultation_notes);
        $this->assertSame($actionPlan, $service->action_plan);
    }

    public function test_admin_can_reject_consultation_request(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $service = ConsultationService::create([
            'ticket_number' => 'DKST-SRV-2026-0004',
            'service_type' => ConsultationService::TYPE_LAINNYA,
            'title' => 'Permohonan Tidak Sesuai Tupoksi',
            'description' => 'Konsultasi di luar cakupan DKST.',
            'applicant_id' => $admin->id,
            'status' => ConsultationService::STATUS_PENDING,
        ]);

        $reason = 'Topik permohonan tidak masuk dalam cakupan layanan DKST ITB.';

        $response = $this->actingAs($admin)->post("/admin/services/{$service->id}/reject", [
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect("/admin/services/{$service->id}");
        $service->refresh();

        $this->assertSame(ConsultationService::STATUS_REJECTED, $service->status);
        $this->assertSame($reason, $service->rejection_reason);
    }
}
