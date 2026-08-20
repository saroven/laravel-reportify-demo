<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fixed Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'Admin',
            'status' => 'Active',
            'phone' => '+1 (555) 019-2834',
            'department' => 'Management',
        ]);

        // Fixed Test User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'User',
            'status' => 'Active',
            'phone' => '+1 (555) 014-9821',
            'department' => 'Engineering',
        ]);

        // 50 Dummy Users
        User::factory(50)->create();
    }
}
