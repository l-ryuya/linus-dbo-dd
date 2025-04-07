<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->comment('サービス');

            $table->string('service_code')->primary()->comment('サービスコード');
            $table->string('service_status_type')->comment('サービス提供ステータス選択肢アイテム種別');
            $table->string('service_status_code')->comment('サービス提供ステータス');
            $table->date('service_start_date')->nullable()->comment('サービス提供開始日');
            $table->date('service_end_date')->nullable()->comment('サービス提供終了日');
            $table->string('service_condition')->nullable()->comment('サービス状態');
            $table->bigInteger('service_admin_user_id')->comment('サービス管理者ユーザーID');
            $table->bigInteger('created_by')->comment('作成ユーザー');
            $table->timestamp('created_at');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable();

            $table->foreign(['service_status_type', 'service_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->foreign('service_admin_user_id')
                ->references('user_id')
                ->on('users');

            $table->index('service_status_code');
            $table->index('service_admin_user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement('CREATE INDEX idx_services_deleted_null ON services(service_code) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
