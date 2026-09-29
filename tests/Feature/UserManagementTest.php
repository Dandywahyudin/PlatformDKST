<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee($admin->name);
    }

    public function test_admin_can_create_new_user_with_roles(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $staffRole = Role::where('slug', 'staff')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@dkst.itb.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'position' => 'Staf Inovasi',
            'phone' => '081298765432',
            'status' => 'active',
            'roles' => [$staffRole->id],
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@dkst.itb.ac.id',
            'position' => 'Staf Inovasi',
            'status' => 'active',
        ]);

        $newUser = User::where('email', 'budi.santoso@dkst.itb.ac.id')->first();
        $this->assertTrue($newUser->hasRole('staff'));

        // Verify audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'module' => AuditLog::MODULE_USERS,
            'action' => AuditLog::ACTION_CREATE,
            'entity_id' => $newUser->id,
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $user = User::factory()->create(['status' => 'active']);
        $directorRole = Role::where('slug', 'director')->first();

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => 'Nama Terupdate',
            'email' => $user->email,
            'position' => 'Direktur Utama',
            'phone' => '08111222333',
            'status' => 'active',
            'roles' => [$directorRole->id],
        ]);

        $response->assertRedirect('/admin/users');
        $user->refresh();

        $this->assertSame('Nama Terupdate', $user->name);
        $this->assertSame('Direktur Utama', $user->position);
        $this->assertTrue($user->hasRole('director'));
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-status");

        $response->assertRedirect();
        $this->assertSame('inactive', $user->fresh()->status);

        // Toggle back to active
        $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-status");
        $this->assertSame('active', $user->fresh()->status);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        $this->assertSoftDeleted($user);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted($admin);
    }
}
