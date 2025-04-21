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
        Schema::create('country_regions_translations', function (Blueprint $table) {
            $table->comment('国・地域翻訳');

            $table->char('country_code_alpha3', 3)->comment('国名コード（alpha-3）');
            $table->char('language_code', 3)->comment('言語コード');
            $table->string('world_region')->comment('世界地域');
            $table->string('country_region_name')->comment('国・地域名称');
            $table->string('capital_name')->nullable()->comment('首都名称');
            $table->string('remarks')->nullable()->comment('備考');
            $table->bigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['country_code_alpha3', 'language_code']);

            $table->foreign('country_code_alpha3')
                ->references('country_code_alpha3')
                ->on('country_regions')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');

            $table->index('language_code');
            $table->index(['country_code_alpha3', 'language_code']);
        });

        DB::statement('CREATE INDEX idx_country_regions_deleted_null ON country_regions_translations(country_code_alpha3, language_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_regions_translations');
    }
};
