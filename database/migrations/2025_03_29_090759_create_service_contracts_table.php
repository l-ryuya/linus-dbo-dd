<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_contracts', function (Blueprint $table) {
            $table->comment('サービス契約');

            $table->id('service_contract_id')->comment('サービス契約ID');
            $table->string('service_contract_code')->unique()->comment('サービス契約通番');
            $table->bigInteger('company_id')->comment('法人ID');
            $table->string('department_name_en')->nullable()->comment('部署名（EN）');
            $table->string('department_name_sl')->nullable()->comment('部署名（SL）');
            $table->string('service_code')->comment('サービスコード');
            $table->string('service_plan_code')->comment('サービスプランコード');
            $table->string('service_usage_status_type')->comment('サービス利用ステータス選択肢アイテム種別');
            $table->string('service_usage_status_code')->comment('サービス利用ステータス');
            $table->string('service_contract_status_type')->comment('サービス契約ステータス選択肢アイテム種別');
            $table->string('service_contract_status_code')->comment('サービス契約ステータス');
            $table->string('payment_cycle_type')->comment('支払サイクル選択肢アイテム種別');
            $table->string('payment_cycle_code')->comment('支払サイクル');
            $table->bigInteger('responsible_user_id')->comment('担当者ユーザーID');
            $table->bigInteger('contract_manager_user_id')->comment('契約担当者ユーザーID');
            $table->date('service_application_date')->comment('サービス申込日');
            $table->date('service_start_date')->nullable()->comment('サービス利用開始日');
            $table->date('service_end_date')->nullable()->comment('サービス利用終了日');
            $table->string('service_contract_url')->nullable()->comment('サービス契約書URL');
            $table->bigInteger('created_by')->nullable()->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('company_id')->references('company_id')->on('companies');
            $table->foreign('service_code')->references('service_code')->on('services')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['service_code', 'service_plan_code'])
                ->references(['service_code', 'service_plan_code'])->on('service_plans')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('responsible_user_id')->references('user_id')->on('users');
            $table->foreign('contract_manager_user_id')->references('user_id')->on('users');
            $table->foreign(['service_usage_status_type', 'service_usage_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['service_contract_status_type', 'service_contract_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['payment_cycle_type', 'payment_cycle_code'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');

            $table->index('company_id');
            $table->index(['service_usage_status_code', 'service_contract_status_code']);
            $table->index('service_application_date');
        });

        DB::statement('CREATE INDEX idx_service_contracts_deleted_null ON service_contracts(service_contract_id) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_contracts');
    }
};
