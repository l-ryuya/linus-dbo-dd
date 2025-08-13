<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE VIEW vw_dd_case_summary AS
            SELECT
                c.dd_case_id,
                c.dd_case_no,
                c.current_dd_status_code AS dd_case_status_code,
                c.tenant_id,
                c.customer_id,

                -- ステップ
                s.dd_step_code AS step_code,
                c.started_at,
                c.ended_at,
                s.step_user_option_id AS step_action_user_id,
                s.step_comment AS action_comment,
                s.step_completed_at AS action_at,

                -- 判定 & エビデンス
                r.dd_result_type_code AS result_type,
                r.dd_result_code_code AS result_code,
                r.step_result_user_option_id AS result_action_user_id,
                r.step_result_comment AS result_action_comment,
                r.step_result_completed_at AS result_action_at,
                r.wc1_case_urls AS dd_evidence_urls,
                r.step_result_evidence AS dd_evidence_blob,

                -- エンティティ（NULL なら案件単位の判定）
                e.dd_entity_id,
                e.dd_entity_type_code AS entity_type,

                co.company_name,
                CONCAT(ind.first_name, COALESCE(\' \' || ind.middle_name, \'\'), \' \', ind.last_name) AS individual_name,

                -- 関係性
                rel.dd_relation_code AS relation_type,
                rel.shareholding_ratio
            FROM   dd_cases c
            LEFT JOIN dd_steps s ON s.dd_case_id = c.dd_case_id
            LEFT JOIN dd_step_results r ON r.dd_step_id = s.dd_step_id
            LEFT JOIN dd_entities e ON e.dd_entity_id = r.dd_entity_id
            LEFT JOIN dd_companies co ON co.dd_entity_id = e.dd_entity_id
            LEFT JOIN dd_individuals ind ON ind.dd_entity_id = e.dd_entity_id
            LEFT JOIN dd_relations rel
                   ON rel.dd_case_id = c.dd_case_id
                  AND rel.dd_entity_id = e.dd_entity_id

            WHERE  c.deleted_at IS NULL
              AND (s.deleted_at IS NULL OR s.dd_step_id IS NULL)
              AND (r.deleted_at IS NULL OR r.dd_step_result_id IS NULL)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_dd_case_summary');
    }
};
