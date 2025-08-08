<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdStepResultSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_step_results_202508070801.csv');
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
                'dd_step_result_id' => (int) $row['dd_step_result_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'dd_case_id' => (int) $row['dd_case_id'],
                'dd_entity_id' => (int) $row['dd_entity_id'],
                'dd_step_id' => (int) $row['dd_step_id'],
                'dd_result_type_type' => $row['dd_result_type_type'],
                'dd_result_type_code' => $row['dd_result_type_code'],
                'dd_result_code_type' => $row['dd_result_code_type'],
                'dd_result_code_code' => $row['dd_result_code_code'],
                'step_result_user_option_id' => empty($row['step_result_user_option_id']) ? null : (int) $row['step_result_user_option_id'],
                'step_result_comment' => $row['step_result_comment'],
                'step_result_completed_at' => empty($row['step_result_completed_at']) ? null : Carbon::parse($row['step_result_completed_at']),
                'dd_result_feedback_type' => empty($row['dd_result_feedback_type']) ? null : $row['dd_result_feedback_type'],
                'dd_result_feedback_code' => empty($row['dd_result_feedback_code']) ? null : $row['dd_result_feedback_code'],
                'exchange_name' => $row['exchange_name'],
                'securities_code' => $row['securities_code'],
                'step_result_evidence' => empty($row['step_result_evidence']) ? null : $row['step_result_evidence'],
                'wc1_case_urls' => empty($row['wc1_case_urls']) ? null : $row['wc1_case_urls'],
                'decision_role' => empty($row['decision_role']) ? null : $row['decision_role'],
                'is_confirmed' => filter_var($row['is_confirmed'], FILTER_VALIDATE_BOOLEAN),
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_step_results ALTER COLUMN dd_step_result_id RESTART WITH 1");

        DB::table('dd_step_results')->insert($data);

        $maxId = DB::table('dd_step_results')->max('dd_step_result_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_step_results ALTER COLUMN dd_step_result_id RESTART WITH {$nextId}");
    }
}

