<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_admin_user_with_full_permissions(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasPermission('users.create'));
        $this->assertTrue($admin->hasPermission('programs.approve'));
        $this->assertTrue($admin->hasPermission('settings.manage'));
    }

    public function test_non_admin_user_without_permission_is_denied(): void
    {
        $this->seed();

        $staffRole = Role::where('slug', 'staff')->first();
        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->attach($staffRole);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->hasPermission('users.create'));
    }

    public function test_user_can_be_assigned_role_and_permission(): void
    {
        $this->seed();

        $user = User::factory()->create(['status' => 'active']);
        $role = Role::where('slug', 'admin')->first();

        $user->assignRole($role);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasPermission('users.view'));
    }
}
