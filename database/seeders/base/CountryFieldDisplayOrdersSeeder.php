<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CountryFieldDisplayOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF04_country_field_display_orders_202506061250.csv');
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
                'country_code_alpha3' => $row['country_code_alpha3'],
                'name_fields_order' => $row['name_fields_order'],
                'address_fields_order' => $row['address_fields_order'],
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE country_field_display_orders ALTER COLUMN country_field_display_order_id RESTART WITH 1");

        DB::table('country_field_display_orders')->insert($data);

        $maxId = DB::table('country_field_display_orders')->max('country_field_display_order_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE country_field_display_orders ALTER COLUMN country_field_display_order_id RESTART WITH {$nextId}");
    }
}
