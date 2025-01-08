<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create(
            [
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_default' => 1,
            ]
        );

        \App\Models\Information::factory()->create(
            [
                'last_name' => 'Nacua',
                'first_name' => 'Adeth',
                'gender' => 'female',
                'email_address' => 'adeth@gmail.com',
            ]
        );

        \App\Models\Staff::factory()->create(
            [
                'user_id' => 1,
                'info_id' => 1,
                'is_active' => 1,
            ]
        );
    }
}
