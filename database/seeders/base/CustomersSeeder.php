<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/DF04_customers_202506061254.csv');
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
                'customer_id' => (int) $row['customer_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => $row['tenant_id'],
                'company_id' => $row['company_id'],
                'sys_organization_code' => $row['sys_organization_code'],
                'customer_code' => $row['customer_code'],
                'customer_status_type' => $row['customer_status_type'],
                'customer_status_code' => $row['customer_status_code'],
                'created_at' => empty($row['created_at']) ? $now : $row['created_at'],
                'updated_at' => empty($row['updated_at']) ? $now : $row['updated_at'],
                'deleted_at' => empty($row['deleted_at']) ? null : $row['deleted_at'],
            ];
        }

        DB::table('customers')->insert($data);
    }
}

