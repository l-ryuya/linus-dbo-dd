<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class UserOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/user_options.csv');
        if (!file_exists($filePath)) {
            Log::error("CSV file not found: " . $filePath);
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $now = Carbon::now();

        foreach ($csv as $row) {
            $data[] = [
                'public_id' => $row['public_id'],
                'company_id' => empty($row['company_id']) ? null : (int) $row['company_id'],
                'tenant_id' => empty($row['tenant_id']) ? null : (int) $row['tenant_id'],
                'customer_id' => empty($row['customer_id']) ? null : (int) $row['customer_id'],
                'service_id' => empty($row['service_id']) ? null : (int) $row['service_id'],
                'sys_user_code' => $row['sys_user_code'],
                'sys_organization_code' => $row['sys_organization_code'],
                'platform_user' => (bool) $row['platform_user'],
                'user_name' => $row['user_name'],
                'user_mail' => $row['user_mail'],
                'user_icon_url' => $row['user_icon_url'] ?? null,
                'country_code_alpha3' => $row['country_code_alpha3'],
                'language_code' => $row['language_code'],
                'time_zone_id' => (int) $row['time_zone_id'],
                'date_format' => $row['date_format'],
                'phone_number' => $row['phone_number'] ?? null,
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::table('user_options')->insert($data);

        $maxId = DB::table('user_options')->max('user_option_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE user_options ALTER COLUMN user_option_id RESTART WITH {$nextId}");
    }
}
