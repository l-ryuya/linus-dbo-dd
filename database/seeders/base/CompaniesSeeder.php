<?php

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
        $filePath = database_path('seeders/base/csv/companies.csv');
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
                "company_code" => $row['company_code'],
                "latest_dd_id" => (int) $row['latest_dd_id'],
                "organization_type_type" => $row['organization_type_type'],
                "organization_type_code" => $row['organization_type_code'],
                "company_status_type" => $row['company_status_type'],
                "company_status_code" => $row['company_status_code'],
                "second_language_type" => $row['second_language_type'],
                "second_language_code" => $row['second_language_code'],
                "company_name_en" => $row['company_name_en'],
                "company_name_sl" => $row['company_name_sl'],
                "company_short_name_en" => $row['company_short_name_en'] ?? null,
                "company_short_name_sl" => $row['company_short_name_sl'] ?? null,
                "country_region_code" => $row['country_region_code'] ?? null,
                "postal_code_en" => $row['postal_code_en'] ?? null,
                "postal_code_sl" => $row['postal_code_sl'] ?? null,
                "prefecture_en" => $row['prefecture_en'] ?? null,
                "prefecture_sl" => $row['prefecture_sl'] ?? null,
                "city_en" => $row['city_en'] ?? null,
                "city_sl" => $row['city_sl'] ?? null,
                "street_en" => $row['street_en'] ?? null,
                "street_sl" => $row['street_sl'] ?? null,
                "building_room_en" => $row['building_room_en'] ?? null,
                "building_room_sl" => $row['building_room_sl'] ?? null,
                "nta_corporate_number" => $row['nta_corporate_number'] ?? null,
                "duns_number" => $row['duns_number'] ?? null,
                "founded_year" => (int) ($row['founded_year'] ?? 0),
                "company_type_type" => $row['company_type_type'] ?? null,
                "company_type" => $row['company_type'] ?? null,
                "employee_count" => (int) ($row['employee_count'] ?? 0),
                "capital_currency" => $row['capital_currency'] ?? null,
                "capital_amount" => (float) ($row['capital_amount'] ?? 0),
                "website_en" => $row['website_en'] ?? null,
                "website_sl" => $row['website_sl'] ?? null,
                "dd_accept_no" => $row['dd_accept_no'] ?? null,
                "created_by" => 1, // デフォルトの作成者ID
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('companies')->insert($data);
    }
}
