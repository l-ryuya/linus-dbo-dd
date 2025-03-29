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
        Schema::create('country_regions', function (Blueprint $table) {
            $table->comment('国・地域');

            $table->char('country_code_alpha3', 3)->primary()->comment('国名コード（alpha-3）');
            $table->char('country_code_alpha2', 2)->unique()->comment('国名コード（alpha-2）');
            $table->smallInteger('country_code_numeric')->unique()->comment('国名コード（numeric）');
            $table->string('world_region_type')->comment('世界地域選択肢アイテム種別');
            $table->string('world_region_code')->comment('世界地域コード');
            $table->string('remarks')->nullable()->comment('備考');
            $table->unsignedBigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->foreign(['world_region_type', 'world_region_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->index(['world_region_type', 'world_region_code']);
        });

        DB::statement('CREATE INDEX idx_countries_deleted_null ON country_regions(country_code_alpha3) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_regions');
    }
};
