<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CompanyRolesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF04_company_roles_202506061253.csv');
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
                'role_id' => (int) $row['role_id'],
                'role_code' => $row['role_code'],
                'role_name' => $row['role_name'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : $row['created_at'],
                'updated_at' => empty($row['updated_at']) ? $now : $row['updated_at'],
                'deleted_at' => empty($row['deleted_at']) ? null : $row['deleted_at'],
            ];
        }

        DB::table('company_roles')->insert($data);
    }
}
