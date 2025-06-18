<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF04_companies_202506061251.csv');
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
                'company_code' => $row['company_code'],
                'tenant_id' => (int) $row['tenant_id'],
                'legal_name_en' => $row['legal_name_en'],
                'short_name_en' => $row['short_name_en'],
                'country_code_alpha3' => $row['country_code_alpha3'],
                'postal' => $row['postal'],
                'state' => $row['state'],
                'city' => $row['city'],
                'street' => $row['street'],
                'building' => $row['building'],
                'default_locale_code' => $row['default_locale_code'] ?? null,
                'website_url' => $row['website_url'] ?? null,
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : $row['created_at'],
                'updated_at' => empty($row['updated_at']) ? $now : $row['updated_at'],
                'deleted_at' => empty($row['deleted_at']) ? null : $row['deleted_at'],
            ];
        }

        DB::table('companies')->insert($data);
    }
}
