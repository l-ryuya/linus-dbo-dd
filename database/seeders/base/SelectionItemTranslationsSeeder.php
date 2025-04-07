<?php

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
        $filePath = database_path('seeders/base/csv/selection_item_translations.csv');
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
                "selection_item_type" => $row['selection_item_type'],
                "selection_item_code" => $row['selection_item_code'],
                "language_code" => $row['language_code'],
                "selection_item_name" => $row['selection_item_name'],
                "selection_item_short_name" => $row['selection_item_short_name'] ?? null,
                "remarks" => $row['remarks'] ?? null,
                "created_by" => 1, // デフォルトの作成者ID
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('selection_item_translations')->insert($data);
    }
}
