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
        Schema::create('service_plans', function (Blueprint $table) {
            $table->comment('サービスプラン情報を管理するテーブル');

            // システム識別子
            $table->id('service_plan_id')
                ->comment('内部連番（PK）');
            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('uuid_generate_v7()'))
                ->comment('外部公開用 UUID v7');

            // ビジネス識別子
            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナントID');
            $table->unsignedBigInteger('service_id')
                ->comment('サービスID');
            $table->string('service_plan_code')
                ->comment('サービスプランを識別する文字列');

            // 属性
            $table->string('service_plan_status_type')
                ->comment('サービスプラン提供ステータスの選択肢アイテム種別');
            $table->string('service_plan_status_code')
                ->comment('サービスプラン提供ステータスコード');
            $table->integer('billing_cycle')
                ->comment('課金・契約の単位期間（例：1ヶ月）');
            $table->decimal('unit_price')
                ->comment('単位期間あたりの料金');
            $table->date('service_start_date')->nullable()
                ->comment('サービスプラン提供が開始された日');
            $table->date('service_end_date')->nullable()
                ->comment('サービスプラン提供が終了する日');
            $table->string('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコードが作成された日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコードが更新された日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコードが削除された日時');

            // ユニーク制約
            $table->unique(['service_id', 'service_plan_code']);

            // 外部キー
            $table->foreign('tenant_id')
                ->references('tenant_id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign(['service_plan_status_type', 'service_plan_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
        });

        // パーシャルインデックス：削除されていない service_id ごとの抽出
        DB::statement("CREATE INDEX idx_service_plans_service_alive ON service_plans (service_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_plans');
    }
};
