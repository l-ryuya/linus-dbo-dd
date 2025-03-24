<?php

namespace Database\Seeders\base;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_statuses')->insert([
            'status' => 'under_review',
            'description' => '審査中',
        ]);

        DB::table('user_statuses')->insert([
            'status' => 'reviewed',
            'description' => '審査済み',
        ]);

        DB::table('user_statuses')->insert([
            'status' => 'approval',
            'description' => '承認',
        ]);

        DB::table('user_statuses')->insert([
            'status' => 'reject',
            'description' => '却下',
        ]);
    }
}
