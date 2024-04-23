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
        \App\Models\User::factory()->create([
            'name' => 'gp-admin',
            'email' => 'admin@example.com',
            'is_admin' => 1,
        ]);

        foreach (range(1, 5) as $i) {
            \App\Models\User::factory()->create([
                'name' => 'gp-creator' . $i,
                'email' => 'creator' . $i . '@example.com',
                'is_admin' => 0,
            ]);
        }
    }
}
