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
        Schema::create('company_role_translations', function (Blueprint $table) {
            $table->comment('法人役割の多言語訳');

            // ビジネス識別子（複合主キー）
            $table->unsignedBigInteger('role_id')
                ->comment('参照先 role_id');
            $table->char('language_code', 3)
                ->comment('言語コード (ISO-639-3)');

            // 必須属性
            $table->string('role_name')
                ->comment('翻訳後の役割名');

            // 任意属性
            $table->string('role_short_name')->nullable()
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

            // 主キー
            $table->primary(['role_id', 'language_code']);

            // 外部キー制約
            $table->foreign('role_id')
                ->references('role_id')
                ->on('company_roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 論理削除されていないデータのみを対象にするパーシャルインデックス
        DB::statement("CREATE INDEX idx_crt_deleted_null ON company_role_translations (role_id, language_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_role_translations');
    }
};
