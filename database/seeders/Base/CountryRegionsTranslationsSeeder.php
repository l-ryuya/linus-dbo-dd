<?php

declare(strict_types=1);

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CountryRegionsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Base/csv/DF16_country_region_translations.csv');
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
                'language_code' => $row['language_code'],
                'world_region' => $row['world_region'],
                'country_region_name' => $row['country_region_name'],
                'capital_name' => $row['capital_name'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE country_regions_translations ALTER COLUMN country_region_translation_id RESTART WITH 1");

        DB::table('country_regions_translations')->insert($data);

        $maxId = DB::table('country_regions_translations')->max('country_region_translation_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE country_regions_translations ALTER COLUMN country_region_translation_id RESTART WITH {$nextId}");
    }
}
