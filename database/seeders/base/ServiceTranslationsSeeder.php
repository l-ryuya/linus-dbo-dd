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
                "service_code" => $row['service_code'],
                "language_code" => $row['language_code'],
                "service_name" => $row['service_name'],
                "service_description" => $row['service_description'],
                "remarks" => $row['remarks'] ?? null,
                "created_by" => 1,
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('service_translations')->insert($data);
    }
}
