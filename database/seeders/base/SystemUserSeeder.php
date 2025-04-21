<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use App\Enums\RoleType;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('company_code', 'C-000001')->first();

        User::factory()->create([
            'user_code' => 'SYS-000001',
            'company_id' => $company->company_id,
            'user_status_type' => 'user_status',
            'user_status' => 'Active',
            'last_name_en' => 'Yamada',
            'last_name_sl' => '山田',
            'middle_name_en' => '',
            'middle_name_sl' => '',
            'first_name_en' => 'Tarou',
            'first_name_sl' => '太郎',
            'position_en' => 'System Administrator',
            'position_sl' => 'システム管理者',
            'email' => 'admin@bizdevforge.dev.dsbizdev.com',
            'password' => Hash::make('Password1'),
            'roles' => RoleType::Admin->value,
        ]);

        User::factory()->create([
            'user_code' => 'SYS-000002',
            'company_id' => $company->company_id,
            'user_status_type' => 'user_status',
            'user_status' => 'Active',
            'last_name_en' => 'Yamada',
            'last_name_sl' => '山田',
            'middle_name_en' => '',
            'middle_name_sl' => '',
            'first_name_en' => 'Jirou',
            'first_name_sl' => '次郎',
            'position_en' => 'Service Manager',
            'position_sl' => 'サービス管理者',
            'email' => 'service.manager@bizdevforge.dev.dsbizdev.com',
            'password' => Hash::make('Password1'),
            'roles' => RoleType::ServiceManager->value,
        ]);
    }
}
