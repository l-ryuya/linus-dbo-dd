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
        Schema::create('companies', function (Blueprint $table) {
            $table->comment('法人テーブル（顧客・運営会社・協業先など）');

            // ビジネス識別子
            $table->id('company_id')->comment('内部用連番 PK');

            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('外部公開用 UUID v4');

            $table->string('company_code')->unique()
                ->comment('法人コード（外部公開用）');

            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナント ID（必須）');

            // 必須属性
            $table->string('legal_name_en')
                ->comment('法人正式名称（英語）');

            // 任意属性
            $table->string('short_name_en')->nullable()
                ->comment('法人略称（英語）');

            $table->char('country_code_alpha3', 3)->nullable()
                ->comment('所在地国コード (ISO-3166-1 alpha-3)');

            $table->string('postal')->nullable()
                ->comment('郵便番号');
            $table->string('state')->nullable()
                ->comment('都道府県・州');
            $table->string('city')->nullable()
                ->comment('市区町村');
            $table->string('street')->nullable()
                ->comment('番地');
            $table->string('building')->nullable()
                ->comment('建物名・部屋番号');
            $table->char('default_locale_code', 3)->nullable()
                ->comment('代表ロケール (ISO-639-3)');
            $table->string('website_url')->nullable()
                ->comment('公式 Web サイト URL');
            $table->string('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 外部キー
            $table->foreign('tenant_id')
                ->references('tenant_id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('country_code_alpha3')
                ->references('country_code_alpha3')
                ->on('country_regions')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // 複合一意制約（テナントごとの会社コード）
            $table->unique(['tenant_id', 'company_code']);
        });

        // 論理削除されていないデータのみ対象の部分インデックス
        DB::statement("CREATE INDEX idx_comp_deleted_null ON companies (company_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
