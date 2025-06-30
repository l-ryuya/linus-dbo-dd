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
        Schema::create('service_contracts', function (Blueprint $table) {
            $table->comment('サービス契約テーブル');

            // システム識別子
            $table->unsignedBigInteger('service_contract_id')
                ->primary()
                ->comment('内部用連番 PK');
            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('外部公開用 UUID v4');

            // ビジネス識別子
            $table->string('service_contract_code')->comment('サービス契約を一意に識別するコード');
            $table->unsignedBigInteger('tenant_id')->comment('所属テナントID');
            $table->unsignedBigInteger('customer_id')->comment('顧客ID');
            $table->unsignedBigInteger('service_id')->comment('サービスID');
            $table->unsignedBigInteger('service_plan_id')->comment('サービスプランID');

            // 属性
            $table->string('contract_name')->comment('契約書名');
            $table->char('contract_language', 3)->comment('契約書言語（eng・jpn・cmn・yeu）');

            $table->string('contract_status_type')->comment('契約ステータス種別');
            $table->string('contract_status_code')->comment('契約ステータスコード');

            $table->string('service_usage_status_type')->comment('サービス利用ステータス種別');
            $table->string('service_usage_status_code')->comment('サービス利用ステータスコード');

            $table->date('contract_date')->nullable()->comment('契約締結日');
            $table->date('contract_start_date')->nullable()->comment('契約開始日');
            $table->date('contract_end_date')->nullable()->comment('契約終了日');
            $table->boolean('contract_auto_update')->nullable()->comment('契約自動更新');

            // 顧客連絡担当者
            $table->string('customer_contact_user_name')->comment('顧客側担当者氏名');
            $table->string('customer_contact_user_dept')->nullable()->comment('顧客側担当者部署');
            $table->string('customer_contact_user_title')->nullable()->comment('顧客側担当者役職');
            $table->string('customer_contact_user_mail')->comment('顧客側担当者メール');

            // 顧客契約担当者
            $table->string('customer_contract_user_name')->comment('顧客側契約担当者氏名');
            $table->string('customer_contract_user_dept')->nullable()->comment('顧客側契約担当者部署');
            $table->string('customer_contract_user_title')->nullable()->comment('顧客側契約担当者役職');
            $table->string('customer_contract_user_mail')->comment('顧客側契約担当者メール');

            // 顧客支払担当者
            $table->string('customer_payment_user_name')->comment('顧客側支払担当者氏名');
            $table->string('customer_payment_user_dept')->nullable()->comment('顧客側支払担当者部署');
            $table->string('customer_payment_user_title')->nullable()->comment('顧客側支払担当者役職');
            $table->string('customer_payment_user_mail')->comment('顧客側支払担当者メール');

            $table->unsignedBigInteger('service_rep_user_option_id')->comment('サービス担当者ユーザー設定ID');
            $table->unsignedBigInteger('service_mgr_user_option_id')->comment('サービス管理者ユーザー設定ID');

            $table->string('contract_preview_pdf_url')->nullable()->comment('契約書URL');

            $table->string('billing_cycle_type')->comment('請求サイクル種別');
            $table->string('billing_cycle_code')->comment('請求サイクルコード');

            $table->string('remarks')->nullable()->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()->comment('レコード削除日時');

            // 一意制約
            $table->unique(['customer_id', 'service_contract_code'], 'uq_sc_cust_contract_code');

            // 外部キー
            $table->foreign('customer_id', 'fk_sc_customer')
                ->references('customer_id')->on('customers');

            $table->foreign('service_id', 'fk_sc_service')
                ->references('service_id')->on('services')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('service_plan_id', 'fk_sc_service_plan')
                ->references('service_plan_id')->on('service_plans')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign(['service_usage_status_type', 'service_usage_status_code'], 'fk_sc_service_usage_status')
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign(['contract_status_type', 'contract_status_code'], 'fk_sc_contract_status')
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign(['billing_cycle_type', 'billing_cycle_code'], 'fk_sc_billing_cycle')
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('cascade')->onDelete('restrict');

            // インデックス
            $table->index('customer_id', 'idx_sc_customer_id');
            $table->index(['service_usage_status_type', 'service_usage_status_code'], 'idx_sc_service_usage_status');
            $table->index(['contract_status_type', 'contract_status_code'], 'idx_sc_contracts_status');
        });

        // ID列にIDENTITYを追加する
        DB::statement("ALTER TABLE service_contracts ALTER COLUMN service_contract_id ADD GENERATED BY DEFAULT AS IDENTITY");
        // パーシャルインデックス（論理削除されていない契約のみ）
        DB::statement("CREATE INDEX idx_service_contracts_deleted_null ON service_contracts(customer_id, service_contract_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_contracts');
    }
};
