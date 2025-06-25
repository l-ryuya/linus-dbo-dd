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
        Schema::create('company_role_assignments', function (Blueprint $table) {
            $table->comment('法人と役割の割当 (N:M)。有効期間により履歴管理を行う');

            // ビジネス識別子
            $table->unsignedBigInteger('company_id')
                ->comment('割当対象となる法人のID (companies.company_id)');
            $table->unsignedBigInteger('role_id')
                ->comment('割り当てられた役割の ID (company_roles.role_id)');

            // 任意属性
            $table->date('valid_from')->default(DB::raw('current_date'))
                ->comment('役割適用開始日');
            $table->date('valid_to')->nullable()
                ->comment('役割適用終了日（NULL = 現在も有効）');
            $table->string('remarks')->nullable()
                ->comment('割当に関する備考');

            // 監査系
            $table->timestamp('created_at')->useCurrent()
                ->comment('レコード作成日時');
            $table->timestamp('updated_at')->useCurrent()
                ->comment('レコード更新日時');
            $table->timestamp('deleted_at')->nullable()
                ->comment('レコード削除日時（論理削除）');

            // 主キー
            $table->primary(['company_id', 'role_id', 'valid_from']);

            // 外部キー制約
            $table->foreign('company_id')
                ->references('company_id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('role_id')
                ->on('company_roles')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 論理削除されていない割当を高速取得するための部分インデックス
        DB::statement("CREATE INDEX idx_cra_deleted_null ON company_role_assignments (company_id, role_id) WHERE deleted_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_role_assignments');
    }
};
