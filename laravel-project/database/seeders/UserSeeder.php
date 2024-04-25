<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'gp-admin',
            'email' => 'admin@example.com',
            'is_admin' => 1,
        ]);

        foreach (range(1, 5) as $i) {
            User::factory()->create([
                'name' => 'gp-creator' . $i,
                'email' => 'creator' . $i . '@example.com',
                'is_admin' => 0,
            ]);
        }
    }
}
