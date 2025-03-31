<?php

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServicePlansSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/service_plans.csv');
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
                "service_plan_status_type" => $row['service_plan_status_type'],
                "service_plan_status" => $row['service_plan_status'],
                "billing_cycle" => (int) $row['billing_cycle'],
                "unit_price" => (float) $row['unit_price'],
                "service_start_date" => empty($row['service_start_date']) ? null : $row['service_start_date'],
                "service_end_date" => empty($row['service_end_date']) ? null : $row['service_end_date'],
                "created_by" => 1,
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('service_plans')->insert($data);
    }
}
