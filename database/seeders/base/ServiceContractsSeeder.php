<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ServiceContractsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/base/csv/service_contracts.csv');
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
                'service_contract_id' => (int) $row['service_contract_id'],
                'public_id' => $row['public_id'],
                'tenant_id' => (int) $row['tenant_id'],
                'customer_id' => (int) $row['customer_id'],
                'service_id' => (int) $row['service_id'],
                'service_plan_id' => (int) $row['service_plan_id'],
                'contract_name' => $row['contract_name'],
                'contract_language' => $row['contract_language'],
                'customer_contact_user_name' => $row['customer_contact_user_name'],
                'customer_contact_user_dept' => $row['customer_contact_user_dept'],
                'customer_contact_user_title' => $row['customer_contact_user_title'],
                'customer_contact_user_email' => $row['customer_contact_user_email'],
                'customer_contract_user_name' => $row['customer_contract_user_name'],
                'customer_contract_user_dept' => $row['customer_contract_user_dept'],
                'customer_contract_user_title' => $row['customer_contract_user_title'],
                'customer_contract_user_email' => $row['customer_contract_user_email'],
                'customer_payment_user_name' => $row['customer_payment_user_name'],
                'customer_payment_user_dept' => $row['customer_payment_user_dept'],
                'customer_payment_user_title' => $row['customer_payment_user_title'],
                'customer_payment_user_email' => $row['customer_payment_user_email'],
                'service_rep_user_option_id' => (int) $row['service_rep_user_option_id'],
                'service_mgr_user_option_id' => (int) $row['service_mgr_user_option_id'],
                'contract_preview_pdf_url' => $row['contract_preview_pdf_url'],
                'contract_date' => empty($row['contract_date']) ? null : $row['contract_date'],
                'contract_start_date' => empty($row['contract_start_date']) ? null : $row['contract_start_date'],
                'contract_end_date' => empty($row['contract_end_date']) ? null : $row['contract_end_date'],
                'contract_auto_update' => isset($row['contract_auto_update']) ? (bool) $row['contract_auto_update'] : null,
                'service_usage_status_type' => $row['service_usage_status_type'],
                'service_usage_status_code' => $row['service_usage_status_code'],
                'contract_status_type' => $row['contract_status_type'],
                'contract_status_code' => $row['contract_status_code'],
                'invoice_remind_days' => empty($row['invoice_remind_days']) ? null : $row['invoice_remind_days'],
                'billing_cycle_type' => $row['billing_cycle_type'],
                'billing_cycle_code' => $row['billing_cycle_code'],
                'remarks' => $row['remarks'],
                'created_at' => empty($row['created_at']) ? $now : Carbon::parse($row['created_at']),
                'updated_at' => empty($row['updated_at']) ? $now : Carbon::parse($row['updated_at']),
                'deleted_at' => empty($row['deleted_at']) ? null : Carbon::parse($row['deleted_at']),
            ];
        }

        DB::statement("ALTER TABLE service_contracts ALTER COLUMN service_contract_id RESTART WITH 1");

        DB::table('service_contracts')->insert($data);

        $maxId = DB::table('service_contracts')->max('service_contract_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE service_contracts ALTER COLUMN service_contract_id RESTART WITH {$nextId}");
    }
}
