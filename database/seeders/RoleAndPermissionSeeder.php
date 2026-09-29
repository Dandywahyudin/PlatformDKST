<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'Dashboard', 'description' => 'Melihat ringkasan statistik dan KPI'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'Users', 'description' => 'Melihat daftar dan detail pengguna'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'Users', 'description' => 'Menambahkan pengguna baru'],
            ['name' => 'Update Users', 'slug' => 'users.update', 'module' => 'Users', 'description' => 'Memperbarui data pengguna'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'Users', 'description' => 'Menghapus data pengguna'],

            // Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'Roles', 'description' => 'Melihat daftar role & permission'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'Roles', 'description' => 'Menambahkan role baru'],
            ['name' => 'Update Roles', 'slug' => 'roles.update', 'module' => 'Roles', 'description' => 'Memperbarui role & hak akses'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'Roles', 'description' => 'Menghapus role'],

            // Programs
            ['name' => 'View Programs', 'slug' => 'programs.view', 'module' => 'Programs', 'description' => 'Melihat daftar dan detail program'],
            ['name' => 'Create Programs', 'slug' => 'programs.create', 'module' => 'Programs', 'description' => 'Membuat program baru'],
            ['name' => 'Update Programs', 'slug' => 'programs.update', 'module' => 'Programs', 'description' => 'Memperbarui program'],
            ['name' => 'Delete Programs', 'slug' => 'programs.delete', 'module' => 'Programs', 'description' => 'Menghapus program draft/rejected'],
            ['name' => 'Approve Programs', 'slug' => 'programs.approve', 'module' => 'Programs', 'description' => 'Menyetujui usulan program'],
            ['name' => 'Reject Programs', 'slug' => 'programs.reject', 'module' => 'Programs', 'description' => 'Menolak usulan program'],

            // Approvals
            ['name' => 'View Approvals', 'slug' => 'approvals.view', 'module' => 'Approvals', 'description' => 'Melihat antrean approval'],
            ['name' => 'Process Approvals', 'slug' => 'approvals.process', 'module' => 'Approvals', 'description' => 'Memproses persetujuan atau penolakan usulan'],

            // Documents
            ['name' => 'View Documents', 'slug' => 'documents.view', 'module' => 'Documents', 'description' => 'Melihat daftar dokumen'],
            ['name' => 'Upload Documents', 'slug' => 'documents.upload', 'module' => 'Documents', 'description' => 'Mengunggah berkas dokumen'],
            ['name' => 'Download Documents', 'slug' => 'documents.download', 'module' => 'Documents', 'description' => 'Mengunduh berkas dokumen'],
            ['name' => 'Delete Documents', 'slug' => 'documents.delete', 'module' => 'Documents', 'description' => 'Menghapus berkas dokumen'],

            // Tasks
            ['name' => 'View Tasks', 'slug' => 'tasks.view', 'module' => 'Tasks', 'description' => 'Melihat daftar task'],
            ['name' => 'Create Tasks', 'slug' => 'tasks.create', 'module' => 'Tasks', 'description' => 'Membuat task baru'],
            ['name' => 'Update Tasks', 'slug' => 'tasks.update', 'module' => 'Tasks', 'description' => 'Memperbarui dan menyelesaikan task'],
            ['name' => 'Delete Tasks', 'slug' => 'tasks.delete', 'module' => 'Tasks', 'description' => 'Menghapus task'],

            // Layanan & Konsultasi (Services)
            ['name' => 'View Services', 'slug' => 'services.view', 'module' => 'Services', 'description' => 'Melihat daftar & detail permohonan layanan konsultasi'],
            ['name' => 'Create Services', 'slug' => 'services.create', 'module' => 'Services', 'description' => 'Mengajukan permohonan layanan & konsultasi baru'],
            ['name' => 'Update Services', 'slug' => 'services.update', 'module' => 'Services', 'description' => 'Memperbarui status, jadwal, disposisi & hasil konsultasi'],
            ['name' => 'Delete Services', 'slug' => 'services.delete', 'module' => 'Services', 'description' => 'Menghapus permohonan layanan konsultasi'],

            // Monitoring & Evaluasi (Monev)
            ['name' => 'View Monev', 'slug' => 'monev.view', 'module' => 'Monev', 'description' => 'Melihat data dan laporan monitoring evaluasi program'],
            ['name' => 'Create Monev', 'slug' => 'monev.create', 'module' => 'Monev', 'description' => 'Menginput laporan monev / evaluasi milestone program'],
            ['name' => 'Update Monev', 'slug' => 'monev.update', 'module' => 'Monev', 'description' => 'Memperbarui monev & memberikan penilaian/rekomendasi'],
            ['name' => 'Delete Monev', 'slug' => 'monev.delete', 'module' => 'Monev', 'description' => 'Menghapus laporan monev'],

            // Kinerja & Dampak (Impact)
            ['name' => 'View Impact Metrics', 'slug' => 'impact.view', 'module' => 'Impact', 'description' => 'Melihat dashboard capaian metrik & dampak inovasi DKST'],
            ['name' => 'Manage Impact Metrics', 'slug' => 'impact.manage', 'module' => 'Impact', 'description' => 'Mengelola target dan realisasi indikator kinerja dampak'],

            // Notifications
            ['name' => 'View Notifications', 'slug' => 'notifications.view', 'module' => 'Notifications', 'description' => 'Melihat daftar notifikasi'],

            // Audit Logs
            ['name' => 'View Audit Logs', 'slug' => 'audit_logs.view', 'module' => 'Audit Logs', 'description' => 'Melihat log rekam jejak aktivitas sistem'],

            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'Settings', 'description' => 'Mengelola pengaturan sistem'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 1. Role: ADMIN
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Akses penuh ke seluruh modul sistem DKST',
            ]
        );
        $allPermissionIds = Permission::pluck('id')->all();
        $adminRole->permissions()->sync($allPermissionIds);

        // 2. Role: DIRECTOR
        $directorRole = Role::updateOrCreate(
            ['slug' => 'director'],
            [
                'name' => 'Director',
                'description' => 'Direktur DKST ITB — Akses eksekutif, persetujuan strategis, evaluasi & dampak',
            ]
        );
        $directorPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'programs.view',
            'approvals.view',
            'approvals.process',
            'documents.view',
            'documents.download',
            'tasks.view',
            'services.view',
            'services.update',
            'monev.view',
            'monev.update',
            'impact.view',
            'impact.manage',
            'audit_logs.view',
            'notifications.view',
        ])->pluck('id')->all();
        $directorRole->permissions()->sync($directorPermissions);

        // 3. Role: REVIEWER / EVALUATOR
        $reviewerRole = Role::updateOrCreate(
            ['slug' => 'reviewer'],
            [
                'name' => 'Reviewer',
                'description' => 'Reviewer & Evaluator DKST — Penilaian usulan program, monev berkala & konsultasi',
            ]
        );
        $reviewerPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'programs.view',
            'approvals.view',
            'approvals.process',
            'documents.view',
            'documents.download',
            'monev.view',
            'monev.create',
            'monev.update',
            'services.view',
            'notifications.view',
        ])->pluck('id')->all();
        $reviewerRole->permissions()->sync($reviewerPermissions);

        // 4. Role: STAFF
        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Staf Operasional DKST — Pelaksana program, layanan konsultasi, task & monev',
            ]
        );
        $staffPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'programs.view',
            'programs.create',
            'programs.update',
            'documents.view',
            'documents.upload',
            'documents.download',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'services.view',
            'services.create',
            'services.update',
            'monev.view',
            'monev.create',
            'monev.update',
            'impact.view',
            'impact.manage',
            'notifications.view',
        ])->pluck('id')->all();
        $staffRole->permissions()->sync($staffPermissions);

        // 5. Role: EXTERNAL_USER (Tenant / Inovator / Dosen Peneliti)
        $externalRole = Role::updateOrCreate(
            ['slug' => 'external_user'],
            [
                'name' => 'External User / Inovator',
                'description' => 'Pengusul / Mitra / Startup Tenant binaan DKST',
            ]
        );
        $externalPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'programs.view',
            'services.view',
            'services.create',
            'documents.view',
            'documents.download',
            'notifications.view',
        ])->pluck('id')->all();
        $externalRole->permissions()->sync($externalPermissions);
    }
}
