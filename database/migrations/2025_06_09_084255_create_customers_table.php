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
        Schema::create('customers', function (Blueprint $table) {
            $table->comment('SaaS 顧客アカウント固有情報（companies と 1:1）');

            // ビジネス識別子
            $table->id('customer_id')->comment('内部用連番 PK');

            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('外部公開用 UUID v4');

            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナント ID（必須）');

            $table->unsignedBigInteger('company_id')
                ->unique()
                ->comment('紐付く法人 (companies.company_id) — 1:1 関係');

            $table->string('sys_organization_code', 12)
                ->comment('m5 システム組織コード');

            // 状態属性
            $table->string('customer_status_type')
                ->comment('顧客ステータスタイプ (selection_items.selection_item_type)');
            $table->string('customer_status_code')
                ->comment('顧客ステータスコード (selection_items.selection_item_code)');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時（論理削除）');

            // 外部キー
            $table->foreign('tenant_id')
                ->references('tenant_id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('company_id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign(['customer_status_type', 'customer_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // インデックス（ステータス検索用）
            $table->index('customer_status_code');
        });

        DB::statement("ALTER TABLE customers ADD COLUMN customer_code_seq BIGINT GENERATED ALWAYS AS IDENTITY");
        DB::statement("COMMENT ON COLUMN customers.customer_code_seq IS '顧客コード生成用カウンタ'");
        // 生成列とユニーク制約（PostgreSQLのSQLレベルで追加）
        DB::statement("ALTER TABLE customers ADD COLUMN customer_code TEXT GENERATED ALWAYS AS ('CO-' || lpad(customer_code_seq::text, 6, '0')) STORED");
        DB::statement("ALTER TABLE customers ADD CONSTRAINT uq_customer_code UNIQUE (customer_code)");
        DB::statement("COMMENT ON COLUMN customers.customer_code IS '顧客コード（外部公開用）'");
        DB::statement("ALTER TABLE customers ADD CONSTRAINT customers_tenant_id_customer_code_unique UNIQUE (tenant_id, customer_code)");

        // パーシャルインデックス（論理削除されていない行）
        DB::statement("CREATE INDEX idx_customers_deleted_null ON customers (customer_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
