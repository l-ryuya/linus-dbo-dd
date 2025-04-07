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
        Schema::create('service_plans', function (Blueprint $table) {
            $table->comment('サービスプラン');

            $table->string('service_code')->comment('サービスコード');
            $table->string('service_plan_code')->comment('サービスプランコード');
            $table->string('service_plan_status_type')->comment('サービスプラン提供ステータス選択肢アイテム種別');
            $table->string('service_plan_status')->comment('サービスプラン提供ステータス');
            $table->integer('billing_cycle')->comment('単位期間');
            $table->decimal('unit_price', 10, 2)->comment('単位金額');
            $table->date('service_start_date')->nullable()->comment('サービスプラン提供開始日');
            $table->date('service_end_date')->nullable()->comment('サービスプラン提供終了日');
            $table->bigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['service_code', 'service_plan_code']);

            $table->foreign('service_code')
                ->references('service_code')
                ->on('services');
            $table->foreign(['service_plan_status_type', 'service_plan_status'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');

            $table->index('service_plan_status');
            $table->index('billing_cycle');
            $table->index('unit_price');
        });

        DB::statement('CREATE INDEX idx_service_plans_deleted_null ON service_plans(service_code, service_plan_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_plans');
    }
};
