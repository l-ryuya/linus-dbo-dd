<?php

declare(strict_types=1);

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Base/csv/DF16_services_202507090834.csv');
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
                "billing_service_id" => $row['billing_service_id'],
                "service_status_type" => $row['service_status_type'],
                "service_status_code" => $row['service_status_code'],
                "service_start_date" => empty($row['service_start_date']) ? null : $row['service_start_date'],
                "service_end_date" => empty($row['service_end_date']) ? null : $row['service_end_date'],
                "service_condition" => $row['service_condition'] ?? null,
                "service_dept_group_email" => $row['service_dept_group_email'],
                "backoffice_group_email" => $row['backoffice_group_email'],
                "service_mgr_user_option_id" => (int) $row['service_mgr_user_option_id'],
                "service_mgr_sys_user_code" => $row['service_mgr_sys_user_code'],
                "service_sys_organization_code" => $row['service_sys_organization_code'],
                "dd_plan" => $row['dd_plan'],
                "remarks" => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        // シーケンスの初期化
        DB::statement("ALTER TABLE services ALTER COLUMN service_id RESTART WITH 1");

        DB::table('services')->insert($data);

        $maxId = DB::table('services')->max('service_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE services ALTER COLUMN service_id RESTART WITH {$nextId}");
    }
}
