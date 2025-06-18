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
        Schema::create('company_name_translations', function (Blueprint $table) {
            $table->comment('法人名称の多言語訳');

            // ビジネス識別子（複合主キー）
            $table->unsignedBigInteger('company_id')
                ->comment('参照先法人ID');
            $table->char('language_code', 3)
                ->comment('言語コード (ISO-639-3)');

            // 必須属性
            $table->string('legal_name')
                ->comment('翻訳後の正式名称');

            // 任意属性
            $table->string('short_name')->nullable()
                ->comment('翻訳後の略称');
            $table->string('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 複合主キー
            $table->primary(['company_id', 'language_code']);

            // 外部キー制約
            $table->foreign('company_id')
                ->references('company_id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 論理削除されていない行の部分インデックス
        DB::statement("CREATE INDEX idx_cnt_deleted_null ON company_name_translations (company_id, language_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_name_translations');
    }
};
