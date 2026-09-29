<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Digital & AI Platform DKST',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama platform sistem informasi',
            ],
            [
                'key' => 'app_institution',
                'value' => 'Direktorat Kawasan Sains dan Teknologi ITB',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Institusi',
                'description' => 'Nama lembaga pemilik sistem',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Zona Waktu',
                'description' => 'Timezone default aplikasi',
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Format Tanggal',
                'description' => 'Format tampilan tanggal standar',
            ],
            [
                'key' => 'max_upload_size_mb',
                'value' => '25',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Batas Unggah Berkas (MB)',
                'description' => 'Ukuran maksimal berkas dokumen yang dapat diunggah',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
