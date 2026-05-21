<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@blitar.go.id'],
            [
                'name' => 'Admin Dashboard',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'modules' => ['mari', 'masn', 'mesra'],
            ]
        );

        // User Biasa (Full access)
        \App\Models\User::updateOrCreate(
            ['email' => 'user@blitar.go.id'],
            [
                'name' => 'User Biasa',
                'password' => bcrypt('password'),
                'role' => 'user',
                'modules' => ['mari', 'masn', 'mesra'],
            ]
        );

        // User MARI (Only MARI access)
        \App\Models\User::updateOrCreate(
            ['email' => 'user.mari@blitar.go.id'],
            [
                'name' => 'User MARI',
                'password' => bcrypt('password'),
                'role' => 'user',
                'modules' => ['mari'],
            ]
        );
    }
}
