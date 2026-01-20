<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'test@example.com',
            'is_admin' => true,
        ]);

        // Create guest user
        User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'is_admin' => false,
        ]);

        $this->command->info('Admin and Guest Users created successfully!');
    }
}
