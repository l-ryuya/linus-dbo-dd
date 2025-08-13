<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdStepSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_steps_202508070802.csv');
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
                'dd_step_id' => (int) $row['dd_step_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'dd_case_id' => (int) $row['dd_case_id'],
                'dd_step_type' => $row['dd_step_type'],
                'dd_step_code' => $row['dd_step_code'],
                'step_user_option_id' => empty($row['step_user_option_id']) ? null : (int) $row['step_user_option_id'],
                'step_comment' => $row['step_comment'] ?? null,
                'step_completed_at' => empty($row['step_completed_at']) ? null : Carbon::parse($row['step_completed_at']),
                'is_updated' => $row['is_updated'] === 'true' || $row['is_updated'] === 1 || $row['is_updated'] === '1',
                'rerun_required' => $row['rerun_required'] === 'true' || $row['rerun_required'] === 1 || $row['rerun_required'] === '1',
                'dd_evidence_blob' => empty($row['dd_evidence_blob']) ? null : $row['dd_evidence_blob'],
                'dd_relations_snapshot' => empty($row['dd_relations_snapshot']) ? null : $row['dd_relations_snapshot'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_steps ALTER COLUMN dd_step_id RESTART WITH 1");

        DB::table('dd_steps')->insert($data);

        $maxId = DB::table('dd_steps')->max('dd_step_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_steps ALTER COLUMN dd_step_id RESTART WITH {$nextId}");
    }
}
