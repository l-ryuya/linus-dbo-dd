<?php

declare(strict_types=1);

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CompanyRoleTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Base/csv/DF04_company_role_translations.csv');
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
                'company_role_id' => (int) $row['company_role_id'],
                'language_code' => $row['language_code'],
                'company_role_name' => $row['company_role_name'],
                'company_role_short_name' => $row['company_role_short_name'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE company_role_translations ALTER COLUMN company_role_translation_id RESTART WITH 1");

        DB::table('company_role_translations')->insert($data);

        $maxId = DB::table('company_role_translations')->max('company_role_translation_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE company_role_translations ALTER COLUMN company_role_translation_id RESTART WITH {$nextId}");
    }
}
