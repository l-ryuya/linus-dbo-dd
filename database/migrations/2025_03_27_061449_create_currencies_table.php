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
        Schema::create('currencies', function (Blueprint $table) {
            $table->comment('通貨');

            $table->char('currency_code_alpha', 3)->primary()->comment('通貨コード（alpha）');
            $table->smallInteger('currency_code_numeric')->unique()->comment('通貨コード（numeric）');
            $table->string('currency_symbol', 5)->nullable()->comment('通貨記号');
            $table->string('currency_name_en')->comment('通貨名称（EN）');
            $table->string('currency_name_ja')->comment('通貨名称（JA）');
            $table->smallInteger('decimal_digits')->comment('小数点以下桁数');
            $table->string('remarks')->nullable()->comment('備考');
            $table->unsignedBigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement('CREATE INDEX idx_currencies_deleted_null ON currencies(currency_code_alpha) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
