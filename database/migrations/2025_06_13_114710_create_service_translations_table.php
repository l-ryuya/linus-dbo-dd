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
        Schema::create('service_translations', function (Blueprint $table) {
            $table->comment('サービス名称・説明の翻訳情報');

            // システム識別子
            $table->id('service_translation_id')
                ->comment('内部連番（PK）');

            // ビジネス識別子
            $table->unsignedBigInteger('service_id')
                ->comment('サービス内部連番');
            $table->char('language_code', 3)
                ->comment('言語コード (ISO-639-3)');

            // 属性
            $table->string('service_name')
                ->comment('翻訳済みサービス名');
            $table->text('service_description')
                ->comment('翻訳済みサービス説明');
            $table->string('remarks')->nullable()
                ->comment('翻訳備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 一意制約
            $table->unique(['service_id', 'language_code']);

            // 外部キー
            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 部分インデックス：削除されていない行に限定
        DB::statement("CREATE INDEX idx_st_language_alive ON service_translations (language_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
