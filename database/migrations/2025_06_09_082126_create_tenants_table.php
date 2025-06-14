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
        Schema::create('tenants', function (Blueprint $table) {
            $table->comment('販売先（テナント）マスタ');

            // ビジネス識別子
            $table->id('tenant_id')->comment('内部用連番 PK');

            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('uuid_generate_v7()'))
                ->comment('外部公開用 UUID v7');

            $table->string('tenant_code')
                ->unique()
                ->comment('テナントコード（外部公開用）');

            $table->string('sys_organization_code', 12)
                ->comment('m5 システム組織コード');

            $table->string('tenant_name')
                ->comment('テナント名（会社名）');

            // 任意属性
            $table->string('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('削除日時');
        });

        // パーシャルインデックス（論理削除されていない行のみ）
        DB::statement("CREATE INDEX idx_tenants_deleted_null ON tenants (tenant_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
