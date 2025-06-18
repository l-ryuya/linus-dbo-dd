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
        Schema::create('selection_item_translations', function (Blueprint $table) {
            $table->comment('選択肢アイテム翻訳情報を管理する');

            // 主キー列
            $table->string('selection_item_type')
                ->comment('選択肢アイテムの種類を示す文字列');
            $table->string('selection_item_code')
                ->comment('選択肢アイテムを識別する文字列');
            $table->char('language_code', 3)
                ->comment('ISO 639-3 言語コード (3 文字)');

            // 翻訳情報
            $table->string('selection_item_name')
                ->comment('対象言語に翻訳された選択肢アイテム名称');
            $table->string('selection_item_short_name')->nullable()
                ->comment('対象言語に翻訳された選択肢アイテム短縮名称');
            $table->string('remarks')->nullable()
                ->comment('翻訳や言語情報に関する備考');

            // 監査列
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコードが作成された日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコードが更新された日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコードが削除された日時');

            // 主キー
            $table->primary(['selection_item_type', 'selection_item_code', 'language_code']);

            // 一意制約（Laravelでは主キーと重複するがDDLと同様に記載）
            $table->unique(['selection_item_type', 'selection_item_code', 'language_code'], 'fk_selection_item_translations_selection_items');

            // 外部キー制約
            $table->foreign(['selection_item_type', 'selection_item_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // 通常インデックス
            $table->index('language_code');
        });

        // パーシャルインデックス
        DB::statement("CREATE INDEX idx_selection_item_translation_deleted_null ON selection_item_translations (selection_item_type, selection_item_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_item_translations');
    }
};
