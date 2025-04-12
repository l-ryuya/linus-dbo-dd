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
        Schema::create('selection_items', function (Blueprint $table) {
            $table->comment('選択肢アイテム');

            $table->string('selection_item_type')->comment('選択肢アイテム種別');
            $table->string('selection_item_code')->comment('選択肢アイテムコード');
            $table->string('remarks')->nullable()->comment('備考');
            $table->unsignedBigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->primary(['selection_item_type', 'selection_item_code']);

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
