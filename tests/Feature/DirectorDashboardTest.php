<?php

namespace Tests\Feature;

use App\Models\Program;
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

        $response = $this->actingAs($admin)->get('/admin/director/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard Direktur');
        $response->assertSee('Ringkasan Eksekutif Program');
        $response->assertSee('Total Program');
        $response->assertSee('Program Berjalan');
        $response->assertSee('Program Selesai');
        $response->assertSee('Perlu Perhatian');
        $response->assertSee('Kesehatan Pelaksanaan Program');
    }

    public function test_director_dashboard_with_year_filter(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/director/dashboard?year=2026');

        $response->assertOk();
        $response->assertSee('Tahun 2026');
    }
}
