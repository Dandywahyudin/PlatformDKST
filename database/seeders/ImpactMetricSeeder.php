<?php

namespace Database\Seeders;

use App\Models\ImpactMetric;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImpactMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin?->id;

        $metrics = [
            // 2026 Metrics
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_STARTUP_GROWTH,
                'metric_name' => 'Jumlah Startup Terinkubasi & Terbina Aktif',
                'target_value' => 35,
                'realized_value' => 28,
                'unit' => 'Unit Startup',
                'description' => 'Target inkubasi startup berbasis riset dan teknologi dari sivitas akademika ITB.',
                'recorded_by' => $adminId,
            ],
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_PATENT_HKI,
                'metric_name' => 'Permohonan Paten & HKI Baru Terfasilitasi',
                'target_value' => 50,
                'realized_value' => 42,
                'unit' => 'Paten / HKI',
                'description' => 'Pendaftaran paten sederhana, paten biasa, dan hak cipta riset strategis ITB.',
                'recorded_by' => $adminId,
            ],
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_COMMERCIALIZATION,
                'metric_name' => 'Nilai Kontrak Lisensi & Komersialisasi Teknologi',
                'target_value' => 8500000000,
                'realized_value' => 6750000000,
                'unit' => 'Rupiah',
                'description' => 'Realisasi nilai komersialisasi teknologi dan royalti paten bersama mitra industri.',
                'recorded_by' => $adminId,
            ],
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_WORKFORCE,
                'metric_name' => 'Penyerapan Tenaga Kerja Berketerampilan Tinggi',
                'target_value' => 150,
                'realized_value' => 124,
                'unit' => 'Orang',
                'description' => 'Talenta rekayasa, peneliti, dan staf yang direkrut oleh startup dan tenant DKST.',
                'recorded_by' => $adminId,
            ],
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_FUNDING_INVESTMENT,
                'metric_name' => 'Pendanaan Eksternal & Investasi Ventura Terhimpun',
                'target_value' => 12000000000,
                'realized_value' => 9800000000,
                'unit' => 'Rupiah',
                'description' => 'Total investasi seed round, angel investor, dan grant venture capital bagi startup binaan.',
                'recorded_by' => $adminId,
            ],
            [
                'year' => 2026,
                'category' => ImpactMetric::CAT_SOCIO_ECONOMIC,
                'metric_name' => 'Mitra Industri & Pemda Pengguna Teknologi Terapan',
                'target_value' => 25,
                'realized_value' => 21,
                'unit' => 'Mitra Kerjasama',
                'description' => 'Kerjasama pemanfaatan teknologi tepat guna dan solusi inovasi bagi masyarakat dan industri.',
                'recorded_by' => $adminId,
            ],
        ];

        foreach ($metrics as $metric) {
            ImpactMetric::updateOrCreate(
                [
                    'year' => $metric['year'],
                    'metric_name' => $metric['metric_name'],
                ],
                $metric
            );
        }
    }
}
