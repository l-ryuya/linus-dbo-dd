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
            $table->comment('顧客と締結したサービス契約を管理');

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
            $table->unsignedBigInteger('customer_id')->comment('顧客ID');
            $table->unsignedBigInteger('service_id')->comment('サービスID');
            $table->unsignedBigInteger('service_plan_id')->comment('サービスプランID');

            // 属性
            $table->string('customer_contact_user_name')->nullable()->comment('顧客側担当者氏名');
            $table->string('customer_contact_user_dept')->nullable()->comment('顧客側担当者部署');
            $table->string('customer_contact_user_title')->nullable()->comment('顧客側担当者役職');
            $table->string('customer_contact_user_mail')->nullable()->comment('顧客側担当者メール');

            $table->string('customer_contract_user_name')->nullable()->comment('顧客側契約担当者氏名');
            $table->string('customer_contract_user_dept')->nullable()->comment('顧客側契約担当者部署');
            $table->string('customer_contract_user_title')->nullable()->comment('顧客側契約担当者役職');
            $table->string('customer_contract_user_mail')->nullable()->comment('顧客側契約担当者メール');

            $table->string('contract_officer_sys_user_code', 12)->comment('サービス側契約担当者ユーザーID M5システムユーザコード');
            $table->string('contract_manager_sys_user_code', 12)->comment('サービス側契約担当管理者ユーザーID M5システムユーザコード');

            $table->string('contract_url')->nullable()->comment('契約書URL');
            $table->date('service_application_date')->comment('契約申込日');
            $table->date('contract_start_date')->nullable()->comment('契約開始日');
            $table->date('contract_end_date')->nullable()->comment('契約終了日');
            $table->date('contract_cancel_date')->nullable()->comment('解約日時');
            $table->string('contract_cancel_reason')->nullable()->comment('解約理由');

            $table->string('service_usage_status_type')->comment('サービス利用ステータス種別');
            $table->string('service_usage_status_code')->comment('サービス利用ステータスコード');
            $table->string('contract_status_type')->comment('契約ステータス種別');
            $table->string('contract_status_code')->comment('契約ステータスコード');
            $table->string('billing_cycle_type')->comment('請求サイクル種別');
            $table->string('billing_cycle_code')->comment('請求サイクルコード');
            $table->string('remarks')->nullable()->comment('備考');

            // 監査
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 制約
            $table->unique(['customer_id', 'service_contract_code']);

            // 外部キー
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers');

            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('service_plan_id')
                ->references('service_plan_id')
                ->on('service_plans')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['service_usage_status_type', 'service_usage_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['contract_status_type', 'contract_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['billing_cycle_type', 'billing_cycle_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // 通常インデックス
            $table->index('customer_id');
            $table->index(['service_usage_status_type', 'service_usage_status_code']);
            $table->index(['contract_status_type', 'contract_status_code']);
            $table->index('service_application_date');
        });

        // ID列にIDENTITYを追加する
        DB::statement("ALTER TABLE service_contracts ALTER COLUMN service_contract_id ADD GENERATED BY DEFAULT AS IDENTITY");
        // パーシャルインデックス（削除されていないデータ）
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
