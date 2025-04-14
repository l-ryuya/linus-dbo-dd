<?php

declare(strict_types=1);

namespace Database\Seeders\tests;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DueDiligencesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/tests/csv/due_diligences.csv');

        if (!file_exists($filePath)) {
            Log::error("CSV file not found: " . $filePath);
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $now = Carbon::now();

        $admin = User::where('user_code', 'SYS-000001')->first();

        foreach ($csv as $row) {
            $data[] = [
                'dd_id' => empty($row['dd_id']) ? null : $row['dd_id'],
                'dd_code' => empty($row['dd_code']) ? null : $row['dd_code'],
                'target_company_dd_id' => empty($row['target_company_dd_id']) ? null : $row['target_company_dd_id'],
                'parent_company_dd_id' => empty($row['parent_company_dd_id']) ? null : $row['parent_company_dd_id'],
                'dd_entity_type_type' => $row['dd_entity_type_type'],
                'dd_entity_type_code' => $row['dd_entity_type_code'],
                'dd_relation_type_type' => empty($row['dd_relation_type_type']) ? null : $row['dd_relation_type_type'],
                'dd_relation_type_code' => empty($row['dd_relation_type_code']) ? null : $row['dd_relation_type_code'],
                'company_name' => empty($row['company_name']) ? null : $row['company_name'],
                'location_country' => empty($row['location_country']) ? null : $row['location_country'],
                'location_postal_code' => empty($row['location_postal_code']) ? null : $row['location_postal_code'],
                'location_prefecture' => empty($row['location_prefecture']) ? null : $row['location_prefecture'],
                'location_city' => empty($row['location_city']) ? null : $row['location_city'],
                'location_street' => empty($row['location_street']) ? null : $row['location_street'],
                'location_building_room' => empty($row['location_building_room']) ? null : $row['location_building_room'],
                'nta_corporate_number' => empty($row['nta_corporate_number']) ? null : $row['nta_corporate_number'],
                'founded_year' => empty($row['founded_year']) ? null : $row['founded_year'],
                'company_type_type' => empty($row['company_type_type']) ? null : $row['company_type_type'],
                'company_type' => empty($row['company_type']) ? null : $row['company_type'],
                'employee_count' => empty($row['employee_count']) ? null : $row['employee_count'],
                'capital_currency' => empty($row['capital_currency']) ? null : $row['capital_currency'],
                'capital_amount' => empty($row['capital_amount']) ? null : $row['capital_amount'],
                'website_jp' => empty($row['website_jp']) ? null : $row['website_jp'],
                'website_en' => empty($row['website_en']) ? null : $row['website_en'],
                'main_clients' => empty($row['main_clients']) ? null : $row['main_clients'],
                'main_banks' => empty($row['main_banks']) ? null : $row['main_banks'],
                'investment_sources' => empty($row['investment_sources']) ? null : $row['investment_sources'],
                'investment_targets' => empty($row['investment_targets']) ? null : $row['investment_targets'],
                'individual_last_name' => empty($row['individual_last_name']) ? null : $row['individual_last_name'],
                'individual_middle_name' => empty($row['individual_middle_name']) ? null : $row['individual_middle_name'],
                'individual_first_name' => empty($row['individual_first_name']) ? null : $row['individual_first_name'],
                'position' => empty($row['position']) ? null : $row['position'],
                'nationality' => empty($row['nationality']) ? null : $row['nationality'],
                'gender_type' => empty($row['gender_type']) ? null : $row['gender_type'],
                'gender' => empty($row['gender']) ? null : $row['gender'],
                'date_of_birth' => empty($row['date_of_birth']) ? null : $row['date_of_birth'],
                'place_of_birth' => empty($row['place_of_birth']) ? null : $row['place_of_birth'],
                'email_address' => empty($row['email_address']) ? null : $row['email_address'],
                'dd_status_type' => empty($row['dd_status_type']) ? null : $row['dd_status_type'],
                'dd_status' => empty($row['dd_status']) ? null : $row['dd_status'],
                'dd_start_date' => empty($row['dd_start_date']) ? null : $row['dd_start_date'],
                'dd_end_date' => empty($row['dd_end_date']) ? null : $row['dd_end_date'],
                'next_dd_date' => empty($row['next_dd_date']) ? null : $row['next_dd_date'],
                'under_continuous_dd' => isset($row['under_continuous_dd']) ? filter_var($row['under_continuous_dd'], FILTER_VALIDATE_BOOLEAN) : null,
                'rep_check_api_reception_id' => empty($row['rep_check_api_reception_id']) ? null : $row['rep_check_api_reception_id'],
                'rep_check_api_message' => empty($row['rep_check_api_message']) ? null : $row['rep_check_api_message'],
                'rep_check_api_status' => empty($row['rep_check_api_status']) ? null : $row['rep_check_api_status'],
                'dd_api_reception_id' => empty($row['dd_api_reception_id']) ? null : $row['dd_api_reception_id'],
                'dd_api_message' => empty($row['dd_api_message']) ? null : $row['dd_api_message'],
                'dd_api_status' => empty($row['dd_api_status']) ? null : $row['dd_api_status'],
                'ai_dd_result' => empty($row['ai_dd_result']) ? null : $row['ai_dd_result'],
                'ai_dd_completed_date' => empty($row['ai_dd_completed_date']) ? null : $row['ai_dd_completed_date'],
                'ai_dd_comment' => empty($row['ai_dd_comment']) ? null : $row['ai_dd_comment'],
                'primary_dd_result' => empty($row['primary_dd_result']) ? null : $row['primary_dd_result'],
                'primary_dd_user_id' => empty($row['primary_dd_user_id']) ? null : $row['primary_dd_user_id'],
                'primary_dd_completed_date' => empty($row['primary_dd_completed_date']) ? null : $row['primary_dd_completed_date'],
                'primary_dd_comment' => empty($row['primary_dd_comment']) ? null : $row['primary_dd_comment'],
                'final_dd_result' => empty($row['final_dd_result']) ? null : $row['final_dd_result'],
                'final_dd_user_id' => empty($row['final_dd_user_id']) ? null : $row['final_dd_user_id'],
                'final_dd_completed_date' => empty($row['final_dd_completed_date']) ? null : $row['final_dd_completed_date'],
                'final_dd_comment' => empty($row['final_dd_comment']) ? null : $row['final_dd_comment'],
                'created_by' => $admin->user_id,
                'created_at' => $now,
                'updated_by' => $admin->user_id,
                'updated_at' => $now,
                'deleted_by' => null,
                'deleted_at' => null,
            ];
        }

        DB::table('due_diligences')->insert($data);
    }
}
