<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServiceTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/service_translations.csv');
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
                "service_id" => $row['service_id'],
                "language_code" => $row['language_code'],
                "service_name" => $row['service_name'],
                "service_description" => $row['service_description'],
                "remarks" => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::table('service_translations')->insert($data);

        $maxId = DB::table('service_translations')->max('service_translation_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE service_translations ALTER COLUMN service_translation_id RESTART WITH {$nextId}");
    }
}
