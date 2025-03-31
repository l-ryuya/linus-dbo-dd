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
        Schema::create('service_translations', function (Blueprint $table) {
            $table->comment('サービス翻訳');

            $table->string('service_code')->comment('サービスコード');
            $table->char('language_code', 3)->comment('言語コード');
            $table->string('service_name')->comment('サービス名称');
            $table->text('service_description')->comment('サービス概要');
            $table->string('remarks')->nullable()->comment('備考');
            $table->bigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['service_code', 'language_code']);

            $table->foreign('service_code')->references('service_code')->on('services')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('created_by')->references('user_id')->on('users');
            $table->foreign('updated_by')->references('user_id')->on('users');
            $table->foreign('deleted_by')->references('user_id')->on('users');

            $table->index('language_code');
        });

        DB::statement('CREATE INDEX idx_service_translation_deleted_null ON service_translations(service_code, language_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
