<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdRelationSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_relations_202508070801.csv');
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
                'dd_relation_id' => (int) $row['dd_relation_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'dd_case_id' => (int) $row['dd_case_id'],
                'dd_entity_id' => (int) $row['dd_entity_id'],
                'dd_relation_type' => $row['dd_relation_type'],
                'dd_relation_code' => $row['dd_relation_code'],
                'shareholding_ratio' => is_numeric($row['shareholding_ratio']) ? (float) $row['shareholding_ratio'] : null,
                'dd_relation_status' => $row['dd_relation_status'],
                'is_confirmed' => filter_var($row['is_confirmed'], FILTER_VALIDATE_BOOLEAN),
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_relations ALTER COLUMN dd_relation_id RESTART WITH 1");

        DB::table('dd_relations')->insert($data);

        $maxId = DB::table('dd_relations')->max('dd_relation_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_relations ALTER COLUMN dd_relation_id RESTART WITH {$nextId}");
    }
}
