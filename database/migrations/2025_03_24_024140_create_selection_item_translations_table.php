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
            $table->comment('選択肢アイテム翻訳');

            $table->string('selection_item_type')->comment('選択肢アイテム種別');
            $table->string('selection_item_code')->comment('選択肢アイテムコード');
            $table->char('language_code', 3)->comment('言語コード');
            $table->string('selection_item_name')->comment('選択肢アイテム名称');
            $table->string('selection_item_short_name')->nullable()->comment('選択肢アイテム短縮名称');
            $table->string('remarks')->nullable()->comment('備考');
            $table->unsignedBigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->primary(['selection_item_type', 'selection_item_code', 'language_code']);

            $table->foreign(['selection_item_type', 'selection_item_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        DB::statement('CREATE INDEX idx_selection_item_translation_deleted_null ON selection_item_translations(selection_item_type, selection_item_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_item_translations');
    }
};
