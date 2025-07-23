<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ContractWidgetSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/contract_widget_settings.csv');
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
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'service_id' => (int) $row['service_id'],
                'service_plan_id' => (int) $row['service_plan_id'],
                'contract_template_id' => $row['contract_template_id'],
                'contract_language' => $row['contract_language'],
                'widget_name' => $row['widget_name'],
                'widget_type' => $row['widget_type'],
                'widget_source_table' => $row['widget_source_table'],
                'widget_source_column' => $row['widget_source_column'],
                'widget_x_coord' => (int) $row['widget_x_coord'],
                'widget_y_coord' => (int) $row['widget_y_coord'],
                'widget_width' => (int) $row['widget_width'],
                'widget_height' => (int) $row['widget_height'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        // シーケンスの再始動
        DB::statement("ALTER TABLE contract_widget_settings ALTER COLUMN contract_widget_setting_id RESTART WITH 1");

        DB::table('contract_widget_settings')->insert($data);

        $maxId = DB::table('contract_widget_settings')->max('contract_widget_setting_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE contract_widget_settings ALTER COLUMN contract_widget_setting_id RESTART WITH {$nextId}");
    }
}
