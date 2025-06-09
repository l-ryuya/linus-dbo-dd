<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('selection_items', function (Blueprint $table) {
            $table->comment('選択肢アイテム情報を管理する');

            // ビジネス識別子
            $table->string('selection_item_type')
                ->comment('選択肢アイテムの種類を示す文字列');
            $table->string('selection_item_code')
                ->comment('選択肢アイテムを識別する文字列');

            // 任意属性
            $table->string('remarks')->nullable()
                ->comment('ステータスに関する備考・補足情報');

            // 監査系（作成・更新・論理削除）
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコードが作成された日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコードが更新された日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコードが削除された日時');

            // 主キー
            $table->primary(['selection_item_type', 'selection_item_code']);

            // インデックス
            $table->index('selection_item_type');
        });

        DB::statement('CREATE INDEX idx_selection_items_deleted_null ON selection_items(selection_item_type, selection_item_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_items');
    }
};
