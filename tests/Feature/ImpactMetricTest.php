<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ImpactMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpactMetricTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_view_impact_dashboard(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/impact');

        $response->assertOk();
        $response->assertSee('Kinerja &amp; Dampak Inovasi DKST', false);
    }

    public function test_admin_can_create_impact_metric(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->post('/admin/impact', [
            'year' => 2026,
            'category' => ImpactMetric::CAT_STARTUP_GROWTH,
            'metric_name' => 'Jumlah Startup Binaan Lulus Tahap Seed',
            'target_value' => 20,
            'realized_value' => 18,
            'unit' => 'Startup',
            'description' => 'Startup yang memperoleh pendanaan lanjutan.',
        ]);

        $metric = ImpactMetric::where('metric_name', 'Jumlah Startup Binaan Lulus Tahap Seed')->first();
        $this->assertNotNull($metric);
        $this->assertSame(90.0, $metric->achievement_percentage);

        $response->assertRedirect('/admin/impact?year=2026');

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_IMPACT,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $metric->id,
        ]);
    }

    public function test_admin_can_update_impact_metric(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $metric = ImpactMetric::create([
            'year' => 2026,
            'category' => ImpactMetric::CAT_WORKFORCE,
            'metric_name' => 'Talenta Peneliti Tambahan',
            'target_value' => 50,
            'realized_value' => 30,
            'unit' => 'Orang',
            'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->put("/admin/impact/{$metric->id}", [
            'year' => 2026,
            'category' => ImpactMetric::CAT_WORKFORCE,
            'metric_name' => 'Talenta Peneliti Tambahan',
            'target_value' => 50,
            'realized_value' => 50,
            'unit' => 'Orang',
        ]);

        $response->assertRedirect('/admin/impact?year=2026');
        $metric->refresh();

        $this->assertSame(100.0, $metric->achievement_percentage);
    }

    public function test_admin_can_delete_impact_metric(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $metric = ImpactMetric::create([
            'year' => 2026,
            'category' => ImpactMetric::CAT_PATENT_HKI,
            'metric_name' => 'Metrik Sementara Hapus',
            'target_value' => 10,
            'realized_value' => 5,
            'unit' => 'Paten',
            'recorded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/impact/{$metric->id}");

        $response->assertRedirect('/admin/impact?year=2026');
        $this->assertDatabaseMissing('impact_metrics', ['id' => $metric->id]);
    }
}
