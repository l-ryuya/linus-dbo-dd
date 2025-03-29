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
        Schema::create('service_plan_translations', function (Blueprint $table) {
            $table->comment('サービスプラン翻訳');

            $table->string('service_code')->comment('サービスコード');
            $table->string('service_plan_code')->comment('サービスプランコード');
            $table->char('language_code', 3)->comment('言語コード');
            $table->string('service_plan_name')->comment('サービスプラン名称');
            $table->string('service_plan_description')->comment('サービスプラン概要');
            $table->string('remarks')->nullable()->comment('備考');
            $table->bigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['service_code', 'service_plan_code', 'language_code']);

            $table->foreign(['service_code', 'service_plan_code'])
                ->references(['service_code', 'service_plan_code'])
                ->on('service_plans')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');

            $table->index('language_code');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });

        DB::statement('CREATE INDEX idx_service_plan_translation_deleted_null ON service_plan_translations(service_code, service_plan_code, language_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_plan_translations');
    }
};
