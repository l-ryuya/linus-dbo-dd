<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\base\UserStatusSeeder;
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
        $this->call([
            UserStatusSeeder::class,
        ]);

        User::factory()->create([
            'status' => 'approval',
            'email' => 'test@bizdevforge.local',
        ]);
    }
}
