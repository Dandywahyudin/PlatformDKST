<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_role_list(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/roles');

        $response->assertOk();
        $response->assertSee('Hak Akses Pengguna');
        $response->assertSee('Administrator');
    }

    public function test_admin_can_create_new_role_with_permissions(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $permIds = Permission::whereIn('slug', ['programs.view', 'programs.create'])->pluck('id')->all();

        $response = $this->actingAs($admin)->post('/admin/roles', [
            'name' => 'Koordinator Inovasi',
            'slug' => 'koordinator-inovasi',
            'description' => 'Mengkoordinasikan program inovasi',
            'permissions' => $permIds,
        ]);

        $response->assertRedirect('/admin/roles');
        $this->assertDatabaseHas('roles', [
            'name' => 'Koordinator Inovasi',
            'slug' => 'koordinator-inovasi',
        ]);

        $newRole = Role::where('slug', 'koordinator-inovasi')->first();
        $this->assertCount(2, $newRole->permissions);

        // Audit log verified
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_ROLES,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $newRole->id,
        ]);
    }

    public function test_admin_can_update_role(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $role = Role::create([
            'name' => 'Role Uji',
            'slug' => 'role-uji',
            'description' => 'Role untuk pengujian',
        ]);

        $permIds = Permission::whereIn('slug', ['dashboard.view', 'users.view'])->pluck('id')->all();

        $response = $this->actingAs($admin)->put("/admin/roles/{$role->id}", [
            'name' => 'Role Uji Diperbarui',
            'slug' => 'role-uji-diperbarui',
            'description' => 'Deskripsi baru',
            'permissions' => $permIds,
        ]);

        $response->assertRedirect('/admin/roles');
        $role->refresh();

        $this->assertSame('Role Uji Diperbarui', $role->name);
        $this->assertSame('role-uji-diperbarui', $role->slug);
        $this->assertCount(2, $role->permissions);
    }

    public function test_core_system_roles_cannot_be_deleted(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        $response = $this->actingAs($admin)->delete("/admin/roles/{$adminRole->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('roles', ['slug' => 'admin']);
    }

    public function test_custom_role_can_be_deleted(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $customRole = Role::create([
            'name' => 'Role Sementara',
            'slug' => 'role-sementara',
            'description' => 'Akan dihapus',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/roles/{$customRole->id}");

        $response->assertRedirect('/admin/roles');
        $this->assertDatabaseMissing('roles', ['slug' => 'role-sementara']);
    }
}
