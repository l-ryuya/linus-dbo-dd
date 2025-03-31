<?php

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CurrenciesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/currencies.csv');
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
                "currency_code_alpha" => $row['currency_code_alpha'],
                "currency_code_numeric" => (int) $row['currency_code_numeric'],
                "currency_symbol" => $row['currency_symbol'] ?? null,
                "currency_name_en" => $row['currency_name_en'],
                "currency_name_ja" => $row['currency_name_ja'],
                "decimal_digits" => (int) $row['decimal_digits'],
                "remarks" => $row['remarks'] ?? null,
                "created_by" => 1, // デフォルトの作成者ID
                "created_at" => $now,
                "updated_by" => 1,
                "updated_at" => $now,
                "deleted_by" => null,
                "deleted_at" => null,
            ];
        }

        DB::table('currencies')->insert($data);
    }
}
