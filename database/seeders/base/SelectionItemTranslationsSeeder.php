<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class SelectionItemTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF18_selection_item_translations.csv');
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
                'selection_item_type' => $row['selection_item_type'],
                'selection_item_code' => $row['selection_item_code'],
                'language_code' => $row['language_code'],
                'selection_item_name' => $row['selection_item_name'],
                'selection_item_short_name' => $row['selection_item_short_name'] ?? null,
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE selection_item_translations ALTER COLUMN selection_item_translation_id RESTART WITH 1");

        DB::table('selection_item_translations')->insert($data);

        $maxId = DB::table('selection_item_translations')->max('selection_item_translation_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE selection_item_translations ALTER COLUMN selection_item_translation_id RESTART WITH {$nextId}");
    }
}
