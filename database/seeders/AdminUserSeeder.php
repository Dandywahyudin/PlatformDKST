<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@dkst.itb.ac.id'],
            [
                'name' => 'Administrator DKST',
                'password' => Hash::make('password'),
                'status' => 'active',
                'position' => 'Super Administrator',
                'phone' => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
