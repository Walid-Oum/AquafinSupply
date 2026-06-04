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
    // 1. De vaste Admin
    \App\Models\User::factory()->create([
        'name' => 'Aquafin Admin',
        'email' => 'admin@aquafinsupply.be',
        'password' => bcrypt('Aquafin2026!'),
        'role' => 'admin',
    ]);

    // 2. De vaste Magazijn
    \App\Models\User::factory()->create([
        'name' => 'Magazijn Medewerker',
        'email' => 'magazijn@aquafinsupply.be',
        'password' => bcrypt('Magazijn2026!'),
        'role' => 'magazijn',
    ]);

    // 3. De vaste Technieker
    \App\Models\User::factory()->create([
        'name' => 'Technieker App',
        'email' => 'technieker@aquafinsupply.be',
        'password' => bcrypt('Technieker2026!'),
        'role' => 'technieker',
    ]);
}

}