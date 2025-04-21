<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServicePlanTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/service_plan_translations.csv');
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
                "service_plan_code" => $row['service_plan_code'],
                "language_code" => $row['language_code'],
                "service_plan_name" => $row['service_plan_name'],
                "service_plan_description" => $row['service_plan_description'],
                "remarks" => $row['remarks'] ?? null,
                "created_by" => 1,
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('service_plan_translations')->insert($data);
    }
}
