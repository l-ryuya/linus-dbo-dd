<?php

declare(strict_types=1);

namespace Database\Seeders\initial;

use App\Enums\RoleType;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/initial/csv/users.csv');
        if (!file_exists($filePath)) {
            Log::error("CSV file not found: " . $filePath);
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $now = Carbon::now();

        $admin = User::where('user_code', 'SYS-000001')->first();
        $company = Company::where('company_code', 'C-000001')->first();

        foreach ($csv as $row) {
            $data[] = [
                "user_code" => $row['user_code'],
                "company_id" => $company->company_id,
                "latest_dd_id" => empty($row['latest_dd_id']) ? null : (int) $row['latest_dd_id'],
                "user_status_type" => $row['user_status_type'],
                "user_status" => $row['user_status'],
                "last_name_en" => $row['last_name_en'] ?? null,
                "last_name_sl" => $row['last_name_sl'] ?? null,
                "middle_name_en" => $row['middle_name_en'] ?? null,
                "middle_name_sl" => $row['middle_name_sl'] ?? null,
                "first_name_en" => $row['first_name_en'] ?? null,
                "first_name_sl" => $row['first_name_sl'] ?? null,
                "position_en" => $row['position_en'] ?? null,
                "position_sl" => $row['position_sl'] ?? null,
                "email" => $row['email'],
                "password" => Hash::make('Password1'),
                "roles" => RoleType::Admin->value,
                "mobile_phone" => $row['mobile_phone'] ?? null,
                "nationality_code" => $row['nationality_code'] ?? null,
                "gender_type" => $row['gender_type'] ?? null,
                "gender" => $row['gender'] ?? null,
                "date_of_birth" => empty($row['date_of_birth']) ? null : $row['date_of_birth'],
                "place_of_birth_en" => $row['place_of_birth_en'] ?? null,
                "place_of_birth_sl" => $row['place_of_birth_sl'] ?? null,
                "profile_en" => $row['profile_en'] ?? null,
                "profile_sl" => $row['profile_sl'] ?? null,
                "created_by" => $admin->user_id,
                "created_at" => $now,
                "updated_by" => $admin->user_id,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('users')->insert($data);
    }
}
