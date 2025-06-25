<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_plan_translations', function (Blueprint $table) {
            $table->comment('サービスプラン名称・説明の翻訳情報');

            // ビジネス識別子
            $table->unsignedBigInteger('service_plan_id')
                ->comment('対象サービスプランID');
            $table->char('language_code', 3)
                ->comment('言語コード (ISO-639-3)');

            // 属性
            $table->string('service_plan_name')
                ->comment('翻訳済みサービスプラン名称');
            $table->string('service_plan_description')
                ->comment('翻訳済みサービスプラン説明');
            $table->string('remarks')->nullable()
                ->comment('サービスプラン翻訳備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 複合主キー
            $table->primary(['service_plan_id', 'language_code']);

            // 外部キー
            $table->foreign('service_plan_id')
                ->references('service_plan_id')
                ->on('service_plans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 論理削除されていない行の部分インデックス
        DB::statement("CREATE INDEX idx_spt_language_alive ON service_plan_translations (service_plan_id, language_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_plan_translations');
    }
};
