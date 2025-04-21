<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CountryRegionsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/country_region_translations.csv');
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
                "country_code_alpha3" => $row['country_code_alpha3'],
                "language_code" => $row['language_code'],
                "world_region" => $row['world_region'],
                "country_region_name" => $row['country_region_name'],
                "capital_name" => $row['capital_name'] ?? null,
                "remarks" => $row['remarks'] ?? null,
                "created_by" => 1, // デフォルトの作成者ID
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('country_regions_translations')->insert($data);
    }
}
