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
        Schema::create('country_regions', function (Blueprint $table) {
            $table->comment('国・地域情報を管理する');

            // ビジネス識別子
            $table->char('country_code_alpha3', 3)
                ->primary()
                ->comment('国コード（ISO 3166-1 alpha-3、例: JPN）');
            $table->char('country_code_alpha2', 2)
                ->unique()
                ->comment('国コード（ISO 3166-1 alpha-2、例: JP）');
            $table->smallInteger('country_code_numeric')
                ->unique()
                ->comment('国コード（ISO 3166-1 numeric、例: 392）');

            // 必須属性
            $table->string('world_region_type')
                ->comment('世界地域の選択肢アイテム種別');
            $table->string('world_region_code')
                ->comment('世界地域コード');

            // 任意属性
            $table->string('remarks')->nullable()
                ->comment('備考欄');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコードが作成された日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコードが更新された日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコードが削除された日時');

            // 外部キー制約
            $table->foreign(['world_region_type', 'world_region_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // 通常インデックス
            $table->index(['world_region_type', 'world_region_code']);
        });

        // パーシャルインデックス（論理削除されていない行）
        DB::statement("CREATE INDEX idx_country_regions_deleted_null ON country_regions (country_code_alpha3) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_regions');
    }
};
