<?php

declare(strict_types=1);

namespace Database\Seeders\Base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class TenantsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/Base/csv/DF04_tenants_202506061254.csv');
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
                'tenant_id' => (int) $row['tenant_id'],
                'public_id' => $row['public_id'],
                'tenant_code' => $row['tenant_code'],
                'tenant_name' => $row['tenant_name'],
                'sys_organization_code' => $row['sys_organization_code'],
                'customers_sys_organization_code' => $row['customers_sys_organization_code'],
                'remarks' => $row['remarks'] ?? null,
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE tenants ALTER COLUMN tenant_id RESTART WITH 1");

        DB::table('tenants')->insert($data);

        $maxId = DB::table('tenants')->max('tenant_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE tenants ALTER COLUMN tenant_id RESTART WITH {$nextId}");
    }
}
