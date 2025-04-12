<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use App\Models\User;
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

        $admin = User::where('user_code', 'SYS-000001')->first();

        foreach ($csv as $row) {
            $data[] = [
                "service_code" => $row['service_code'],
                "service_status_type" => $row['service_status_type'],
                "service_status_code" => $row['service_status_code'],
                "service_start_date" => empty($row['service_start_date']) ? null : $row['service_start_date'],
                "service_end_date" => empty($row['service_end_date']) ? null : $row['service_end_date'],
                "service_condition" => $row['service_condition'] ?? null,
                "service_admin_user_id" => $admin->user_id,
                "created_by" => 1,
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('services')->insert($data);
    }
}
