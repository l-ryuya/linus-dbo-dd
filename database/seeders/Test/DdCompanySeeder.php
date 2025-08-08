<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdCompanySeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_companies_202508070800.csv');
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
                'dd_company_id' => (int) $row['dd_company_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'dd_entity_id' => (int) $row['dd_entity_id'],
                'company_name' => $row['company_name'],
                'nta_corporate_number' => $row['nta_corporate_number'],
                'location_code_alpha3' => $row['location_code_alpha3'],
                'founded_year' => (int) $row['founded_year'],
                'is_listed' => filter_var($row['is_listed'], FILTER_VALIDATE_BOOLEAN),
                'employee_count' => (int) $row['employee_count'],
                'capital_currency' => $row['capital_currency'],
                'capital_amount' => (float) $row['capital_amount'],
                'website_jp' => $row['website_jp'],
                'website_en' => $row['website_en'],
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_companies ALTER COLUMN dd_company_id RESTART WITH 1");

        DB::table('dd_companies')->insert($data);

        $maxId = DB::table('dd_companies')->max('dd_company_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_companies ALTER COLUMN dd_company_id RESTART WITH {$nextId}");
    }
}

