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
        \App\Models\User::create([
            'name' => 'Admin Dashboard',
            'email' => 'admin@blitar.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // User Biasa
        \App\Models\User::create([
            'name' => 'User Biasa',
            'email' => 'user@blitar.go.id',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}
