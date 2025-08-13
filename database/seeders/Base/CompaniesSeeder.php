<?php

declare(strict_types=1);

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Base/csv/DF04_companies_202506061251.csv');
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
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'company_name_en' => $row['company_name_en'],
                'default_language_code' => $row['default_language_code'],
                'country_code_alpha3' => $row['country_code_alpha3'],
                'postal' => $row['postal'],
                'state' => $row['state'],
                'city' => $row['city'],
                'street' => $row['street'],
                'building' => $row['building'] ?? null,
                'website_url' => $row['website_url'] ?? null,
                'shareholders_url' => $row['shareholders_url'] ?? null,
                'executives_url' => $row['executives_url'] ?? null,
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE companies ALTER COLUMN company_id RESTART WITH 1");

        DB::table('companies')->insert($data);

        $maxId = DB::table('companies')->max('company_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE companies ALTER COLUMN company_id RESTART WITH {$nextId}");
    }
}
