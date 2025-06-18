<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CountryRegionsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF16_country_regions.csv');
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
                'country_code_alpha3' => $row['country_code_alpha3'],
                'country_code_alpha2' => $row['country_code_alpha2'],
                'country_code_numeric' => (int) $row['country_code_numeric'],
                'world_region_type' => $row['world_region_type'],
                'world_region_code' => $row['world_region_code'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => $row['created_at'] ?? $now,
                'updated_at' => $row['updated_at'] ?? $now,
                'deleted_at' => $row['deleted_at'] ?? null,
            ];
        }

        DB::table('country_regions')->insert($data);
    }
}
