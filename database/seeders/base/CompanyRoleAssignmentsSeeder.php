<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CompanyRoleAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF04_company_role_assignments_202506061253.csv');
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
                'company_id' => (int) $row['company_id'],
                'company_role_id' => (int) $row['company_role_id'],
                'valid_from' => $row['valid_from'],
                'valid_to' => empty($row['valid_to']) ? null : $row['valid_to'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::table('company_role_assignments')->insert($data);

        $maxId = DB::table('company_role_assignments')->max('company_role_assignment_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE company_role_assignments ALTER COLUMN company_role_assignment_id RESTART WITH {$nextId}");
    }
}
