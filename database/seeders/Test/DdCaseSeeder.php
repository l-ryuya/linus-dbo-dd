<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdCaseSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_cases_202508070800.csv');
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
                'dd_case_id' => (int) $row['dd_case_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'customer_id' => (int) $row['customer_id'],
                'case_user_option_id' => empty($row['case_user_option_id']) ? null : (int) $row['case_user_option_id'],
                'started_at' => empty($row['started_at']) ? null : Carbon::parse($row['started_at']),
                'ended_at' => empty($row['ended_at']) ? null : Carbon::parse($row['ended_at']),
                'current_dd_step_type' => $row['current_dd_step_type'],
                'current_dd_step_code' => $row['current_dd_step_code'],
                'current_dd_status_type' => $row['current_dd_status_type'],
                'current_dd_status_code' => $row['current_dd_status_code'],
                'industry_check_reg_result' => $row['industry_check_reg_result'],
                'industry_check_web_result' => $row['industry_check_web_result'],
                'customer_risk_level' => $row['customer_risk_level'],
                'asf_check_result' => $row['asf_check_result'],
                'rep_check_result' => $row['rep_check_result'],
                'step_1_info' => $row['step_1_info'],
                'step_2_info' => $row['step_2_info'],
                'step_3_info' => $row['step_3_info'],
                'step_4_info' => $row['step_4_info'],
                'step_5_info' => $row['step_5_info'],
                'step_6_info' => $row['step_6_info'],
                'step_7_info' => $row['step_7_info'],
                'step_8_info' => $row['step_8_info'],
                'step_9_info' => $row['step_9_info'],
                'last_process_user_option_id' => empty($row['last_process_user_option_id']) ? null : (int) $row['last_process_user_option_id'],
                'last_process_datetime' => empty($row['last_process_datetime']) ? null : Carbon::parse($row['last_process_datetime']),
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_cases ALTER COLUMN dd_case_id RESTART WITH 1");

        DB::table('dd_cases')->insert($data);

        $maxId = DB::table('dd_cases')->max('dd_case_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_cases ALTER COLUMN dd_case_id RESTART WITH {$nextId}");
    }
}
