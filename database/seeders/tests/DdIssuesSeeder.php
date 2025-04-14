<?php

declare(strict_types=1);

namespace Database\Seeders\tests;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdIssuesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/tests/csv/dd_issues.csv');

        if (!file_exists($filePath)) {
            Log::error('CSV file not found: ' . $filePath);
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $now = Carbon::now();

        $admin = User::where('user_code', 'SYS-000001')->first();

        foreach ($csv as $row) {
            $data[] = [
                'dd_issue_code' => empty($row['dd_issue_code']) ? null : $row['dd_issue_code'],
                'dd_id' => empty($row['dd_id']) ? null : (int) $row['dd_id'],
                'ai_dd_result' => empty($row['ai_dd_result']) ? null : filter_var($row['ai_dd_result'], FILTER_VALIDATE_BOOLEAN),
                'ai_dd_completed_date' => empty($row['ai_dd_completed_date']) ? null : $row['ai_dd_completed_date'],
                'ai_dd_issue_comment' => empty($row['ai_dd_issue_comment']) ? null : $row['ai_dd_issue_comment'],
                'primary_dd_result' => empty($row['primary_dd_result']) ? null : filter_var($row['primary_dd_result'], FILTER_VALIDATE_BOOLEAN),
                'primary_dd_user_id' => empty($row['primary_dd_user_id']) ? null : $row['primary_dd_user_id'],
                'primary_dd_completed_date' => empty($row['primary_dd_completed_date']) ? null : $row['primary_dd_completed_date'],
                'primary_dd_issue_comment' => empty($row['primary_dd_issue_comment']) ? null : $row['primary_dd_issue_comment'],
                'final_dd_result' => empty($row['final_dd_result']) ? null : filter_var($row['final_dd_result'], FILTER_VALIDATE_BOOLEAN),
                'final_dd_user_id' => empty($row['final_dd_user_id']) ? null : $row['final_dd_user_id'],
                'final_dd_completed_date' => empty($row['final_dd_completed_date']) ? null : $row['final_dd_completed_date'],
                'final_dd_issue_comment' => empty($row['final_dd_issue_comment']) ? null : $row['final_dd_issue_comment'],
                'dd_issue_evidences' => empty($row['dd_issue_evidences']) ? null : $row['dd_issue_evidences'],
                'created_by' => $admin->user_id,
                'created_at' => $now,
                'updated_by' => $admin->user_id,
                'updated_at' => $now,
                'deleted_by' => null,
                'deleted_at' => null,
            ];
        }

        DB::table('dd_issues')->insert($data);
    }
}
