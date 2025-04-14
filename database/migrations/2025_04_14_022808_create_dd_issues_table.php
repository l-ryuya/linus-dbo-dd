<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dd_issues', function (Blueprint $table) {
            $table->comment('デューデリジェンス疑義');

            $table->id('dd_issue_id')->comment('DD疑義ID');
            $table->string('dd_issue_code')->unique()->comment('DD疑義通番');
            $table->bigInteger('dd_id')->comment('デューデリジェンスID');
            $table->boolean('ai_dd_result')->nullable()->comment('AI審査結果');
            $table->date('ai_dd_completed_date')->nullable()->comment('AI審査完了日');
            $table->string('ai_dd_issue_comment')->nullable()->comment('AI審査疑義コメント');
            $table->boolean('primary_dd_result')->nullable()->comment('一次審査結果');
            $table->bigInteger('primary_dd_user_id')->nullable()->comment('一次審査担当ユーザー');
            $table->date('primary_dd_completed_date')->nullable()->comment('一次審査完了日');
            $table->string('primary_dd_issue_comment')->nullable()->comment('一次審査疑義コメント');
            $table->boolean('final_dd_result')->nullable()->comment('最終審査結果');
            $table->bigInteger('final_dd_user_id')->nullable()->comment('最終審査担当ユーザー');
            $table->date('final_dd_completed_date')->nullable()->comment('最終審査完了日');
            $table->string('final_dd_issue_comment')->nullable()->comment('最終審査疑義コメント');
            $table->jsonb('dd_issue_evidences')->nullable()->comment('DD疑義エビデンス');
            $table->bigInteger('created_by')->nullable()->comment('作成ユーザー');
            $table->timestamp('created_at')->nullable()->comment('作成日時');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->nullable()->comment('更新日時');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->foreign('dd_id')->references('dd_id')->on('due_diligences');
            $table->foreign('primary_dd_user_id')->references('user_id')->on('users');
            $table->foreign('final_dd_user_id')->references('user_id')->on('users');

            $table->index('dd_issue_code');
            $table->index('dd_id');
            $table->index('primary_dd_user_id');
            $table->index('final_dd_user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement('CREATE INDEX idx_dd_issues_deleted_null ON dd_issues(dd_issue_id) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('dd_issues');
    }
};
