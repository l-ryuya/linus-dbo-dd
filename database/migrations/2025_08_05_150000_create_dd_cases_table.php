<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dd_cases', function (Blueprint $table) {
            $table->comment('デューデリジェンスケーステーブル');

            // システム識別子
            $table->unsignedBigInteger('dd_case_id')
                ->primary()
                ->comment('内部用連番 PK');
            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('外部公開用 UUID v4');

            // 属性
            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナントID');
            $table->unsignedBigInteger('customer_id')
                ->comment('顧客ID');

            // DDケースNo生成用
            $table->unsignedBigInteger('dd_case_no_seq')
                ->comment('DDケースNo.生成用カウンタ');
            $table->text('dd_case_no')
                ->storedAs("'DDC-' || extract(year from created_at)::text || '-' || lpad(dd_case_no_seq::text, 4, '0')")
                ->comment('DDケースNo（外部公開用）');

            $table->unsignedBigInteger('case_user_option_id')
                ->comment('ケース担当者のユーザー設定ID');
            $table->timestamp('started_at')->useCurrent()
                ->comment('ケース開始日付');
            $table->timestamp('ended_at')->nullable()
                ->comment('ケース終了日付');

            $table->string('current_dd_step_type')
                ->default('dd_step')
                ->comment('現在のDDステップ種別');
            $table->string('current_dd_step_code')
                ->comment('現在のDDステップコード');

            $table->string('current_dd_status_type')
                ->default('dd_status')
                ->comment('現在のDDステータス種別');
            $table->string('current_dd_status_code')
                ->comment('現在のDDステータスコード');

            $table->string('industry_check_reg_result')
                ->default('--')
                ->comment('業種業態チェック（登記簿）結果');
            $table->string('industry_check_web_result')
                ->default('--')
                ->comment('業種業態チェック（Web）結果');
            $table->string('customer_risk_level')
                ->default('--')
                ->comment('顧客リスクレベル');
            $table->string('asf_check_result')
                ->default('--')
                ->comment('反社チェック結果');
            $table->string('rep_check_result')
                ->default('--')
                ->comment('風評チェック結果');

            $table->string('overall_result')
                ->default('--')
                ->comment('総合判定結果');

            $table->string('step_1_info')->nullable()
                ->comment('ステップ1の情報（"RERUN" 等）');
            $table->string('step_2_info')->nullable()
                ->comment('ステップ2の情報（"UPDATED" 等）');
            $table->string('step_3_info')->nullable()
                ->comment('ステップ3の情報（"UPDATED" 等）');
            $table->string('step_4_info')->nullable()
                ->comment('ステップ4の情報（"UPDATED" 等）');
            $table->string('step_5_info')->nullable()
                ->comment('ステップ5の情報（"UPDATED" 等）');
            $table->string('step_6_info')->nullable()
                ->comment('ステップ6の情報（"UPDATED" 等）');
            $table->string('step_7_info')->nullable()
                ->comment('ステップ7の情報（"RERUN" 等）');
            $table->string('step_8_info')->nullable()
                ->comment('ステップ8の情報（"UPDATED" 等）');
            $table->string('step_9_info')->nullable()
                ->comment('ステップ9の情報（"UPDATED" 等）');

            $table->unsignedBigInteger('last_process_user_option_id')->nullable()
                ->comment('最新処理ユーザーのユーザ-設定ID');
            $table->timestamp('last_process_datetime')->nullable()
                ->comment('最新処理日時');

            $table->text('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時（論理削除）');

            // 外部キー
            $table->foreign('tenant_id')
                ->references('tenant_id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign(['current_dd_step_type', 'current_dd_step_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['current_dd_status_type', 'current_dd_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('case_user_option_id')
                ->references('user_option_id')
                ->on('user_options')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // ID列にIDENTITYを追加する
        DB::statement("ALTER TABLE dd_cases ALTER COLUMN dd_case_id ADD GENERATED BY DEFAULT AS IDENTITY");
        DB::statement("ALTER TABLE dd_cases ALTER COLUMN dd_case_no_seq ADD GENERATED ALWAYS AS IDENTITY");
        // ユニーク制約
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT uq_dd_case_no UNIQUE (dd_case_no)");
        // CHECK制約
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_current_dd_step_type CHECK (current_dd_step_type = 'dd_step')");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_current_dd_status_type CHECK (current_dd_status_type = 'dd_status')");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_industry_check_reg_result CHECK (industry_check_reg_result IN ('OK','NG','WD','NS','--'))");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_industry_check_web_result CHECK (industry_check_web_result IN ('OK','NG','WD','NS','--'))");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_customer_risk_level CHECK (customer_risk_level IN ('LOW','HIGH','--'))");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_asf_check_result CHECK (asf_check_result IN ('OK','NG','--'))");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_rep_check_result CHECK (rep_check_result IN ('OK','NG','--'))");
        DB::statement("ALTER TABLE dd_cases ADD CONSTRAINT chk_overall_result CHECK (overall_result IN ('OK','NG','--'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dd_cases');
    }
};
