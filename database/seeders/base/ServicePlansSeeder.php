<?php

declare(strict_types=1);

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
                "service_plan_id" => $row['service_plan_id'],
                "tenant_id" => $row['tenant_id'],
                "public_id" => $row['public_id'],
                "service_id" => $row['service_id'],
                "service_plan_code" => $row['service_plan_code'],
                "service_plan_status_type" => $row['service_plan_status_type'],
                "service_plan_status_code" => $row['service_plan_status_code'],
                "billing_cycle" => (int) $row['billing_cycle'],
                "unit_price" => (float) $row['unit_price'],
                "service_start_date" => empty($row['service_start_date']) ? null : $row['service_start_date'],
                "service_end_date" => empty($row['service_end_date']) ? null : $row['service_end_date'],
                "remarks" => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::table('service_plans')->insert($data);

        $maxId = DB::table('service_plans')->max('service_plan_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE service_plans ALTER COLUMN service_plan_id RESTART WITH {$nextId}");
    }
}
