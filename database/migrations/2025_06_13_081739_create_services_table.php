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
        Schema::create('services', function (Blueprint $table) {
            $table->comment('サービス情報を管理するテーブル');

            // システム識別子
            $table->id('service_id')->comment('内部連番（PK）');
            $table->uuid('public_id')
                ->unique()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('外部公開用 UUID v4');

            // ビジネス識別子
            $table->unsignedBigInteger('tenant_id')
                ->comment('所属テナントID');
            $table->string('service_code')
                ->comment('サービスを一意に識別するコード');

            // 属性
            $table->string('service_status_type')
                ->comment('サービス提供ステータスの選択肢アイテム種別ID');
            $table->string('service_status_code')
                ->comment('サービス提供ステータスコード');
            $table->date('service_start_date')->nullable()
                ->comment('サービス提供開始日');
            $table->date('service_end_date')->nullable()
                ->comment('サービス提供終了日');
            $table->string('service_condition')->nullable()
                ->comment('メンテナンス・トラブル等の場合にサービスの状態を説明する項目');
            $table->string('service_admin_sys_user_code', 12)
                ->comment('サービスを管理するユーザーID M5システムユーザコード');
            $table->string('remarks')->nullable()
                ->comment('備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時');

            // 制約
            $table->unique(['tenant_id', 'service_code']);

            // 外部キー
            $table->foreign('tenant_id')
                ->references('tenant_id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign(['service_status_type', 'service_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // パーシャルインデックス
        DB::statement("CREATE INDEX idx_services_alive ON services (service_id) WHERE deleted_at IS NULL");
        DB::statement("CREATE INDEX idx_services_tenant ON services (tenant_id) WHERE deleted_at IS NULL");
        DB::statement("CREATE INDEX idx_services_tenant_status_alive ON services (tenant_id, service_status_code) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
