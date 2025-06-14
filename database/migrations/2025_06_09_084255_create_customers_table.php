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
        Schema::create('customers', function (Blueprint $table) {
            $table->comment('SaaS 顧客アカウント固有情報（companies と 1:1）');

            // ビジネス識別子
            $table->id('customer_id')->comment('内部用連番 PK');

            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('uuid_generate_v7()'))
                ->comment('外部公開用 UUID v7');

            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナント ID（必須）');

            $table->unsignedBigInteger('company_id')
                ->unique()
                ->comment('紐付く法人 (companies.company_id) — 1:1 関係');

            $table->string('sys_organization_code', 12)
                ->comment('m5 システム組織コード');

            // 必須属性
            $table->string('customer_code')->unique()
                ->comment('システム内で顧客を識別するユニークなコード');

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

            // 複合一意制約
            $table->unique(['tenant_id', 'customer_code']);

            // インデックス（ステータス検索用）
            $table->index('customer_status_code');
        });

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
