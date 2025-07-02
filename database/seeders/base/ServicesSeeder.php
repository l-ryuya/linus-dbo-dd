<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/services.csv');
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
                "tenant_id" => $row['tenant_id'],
                "public_id" => $row['public_id'],
                "service_code" => $row['service_code'],
                "service_status_type" => $row['service_status_type'],
                "service_status_code" => $row['service_status_code'],
                "service_start_date" => empty($row['service_start_date']) ? null : $row['service_start_date'],
                "service_end_date" => empty($row['service_end_date']) ? null : $row['service_end_date'],
                "service_condition" => $row['service_condition'] ?? null,
                "service_admin_sys_user_code" => $row['service_admin_sys_user_code'],
                "service_sys_organization_code" => $row['service_sys_organization_code'],
                "dd_plan" => $row['dd_plan'],
                "remarks" => $row['remarks'] ?? null,
                "created_at" => $now,
                "updated_at" => $now,
                "deleted_at" => null,
            ];
        }

        DB::table('services')->insert($data);

        $maxId = DB::table('services')->max('service_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE services ALTER COLUMN service_id RESTART WITH {$nextId}");
    }
}
