<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DdIndividualSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Test/csv/DF07_dd_individuals_202508070801.csv');
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
                'dd_individual_id' => (int) $row['dd_individual_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'dd_entity_id' => (int) $row['dd_entity_id'],
                'full_name' => $row['full_name'],
                'first_name' => $row['first_name'] ?? null,
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'] ?? null,
                'position' => $row['position'],
                'nationality_code_alpha3' => $row['nationality_code_alpha3'],
                'gender_type' => $row['gender_type'],
                'gender_code' => $row['gender_code'],
                'date_of_birth' => $row['date_of_birth'],
                'place_of_birth' => $row['place_of_birth'],
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE dd_individuals ALTER COLUMN dd_individual_id RESTART WITH 1");

        DB::table('dd_individuals')->insert($data);

        $maxId = DB::table('dd_individuals')->max('dd_individual_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE dd_individuals ALTER COLUMN dd_individual_id RESTART WITH {$nextId}");
    }
}
