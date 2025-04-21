<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('due_diligences', function (Blueprint $table) {
            $table->comment('デューデリジェンス');

            $table->id('dd_id')->comment('デューデリジェンスID');
            $table->string('dd_code')->unique()->comment('デューデリジェンス通番');
            $table->bigInteger('target_company_dd_id')->nullable()->comment('デューデリジェンス（取引対象法人）ID');
            $table->bigInteger('parent_company_dd_id')->nullable()->comment('デューデリジェンス（所属法人）ID');
            $table->string('dd_entity_type_type')->comment('デューデリジェンス対象種別 選択肢アイテム種別');
            $table->string('dd_entity_type_code')->comment('デューデリジェンス対象種別');
            $table->string('dd_relation_type_type')->nullable()->comment('デューデリジェンス関連種別 選択肢アイテム種別');
            $table->string('dd_relation_type_code')->nullable()->comment('デューデリジェンス関連種別');
            $table->string('company_name')->nullable()->comment('法人名称');
            $table->char('location_country', 3)->nullable()->comment('所在地_国名');
            $table->string('location_postal_code')->nullable()->comment('所在地_郵便番号');
            $table->string('location_prefecture')->nullable()->comment('所在地_都道府県');
            $table->string('location_city')->nullable()->comment('所在地_市区町村');
            $table->string('location_street')->nullable()->comment('所在地_番地');
            $table->string('location_building_room')->nullable()->comment('所在地_建物名_部屋番号');
            $table->string('nta_corporate_number')->nullable()->comment('国税庁法人番号');
            $table->integer('founded_year')->nullable()->comment('設立時期（年）');
            $table->string('company_type_type')->nullable()->comment('会社形態 選択肢アイテム種別');
            $table->string('company_type')->nullable()->comment('会社形態');
            $table->integer('employee_count')->nullable()->comment('従業員数');
            $table->char('capital_currency', 3)->nullable()->comment('資本金（通貨）');
            $table->decimal('capital_amount', 16, 2)->nullable()->comment('資本金（金額）');
            $table->string('website_jp')->nullable()->comment('Webサイト（日）');
            $table->string('website_en')->nullable()->comment('Webサイト（英）');
            $table->jsonb('main_clients')->nullable()->comment('主要取引先');
            $table->jsonb('main_banks')->nullable()->comment('取引銀行');
            $table->jsonb('investment_sources')->nullable()->comment('出資元');
            $table->jsonb('investment_targets')->nullable()->comment('出資先');
            $table->decimal('shareholding_ratio', 6, 3)->nullable()->comment('持株比率');
            $table->string('individual_last_name')->nullable()->comment('個人氏名（姓）');
            $table->string('individual_middle_name')->nullable()->comment('個人氏名（ミドルネーム）');
            $table->string('individual_first_name')->nullable()->comment('個人氏名（名）');
            $table->string('position')->nullable()->comment('役職');
            $table->string('nationality')->nullable()->comment('国籍');
            $table->string('gender_type')->nullable()->comment('性別 選択肢アイテム種別');
            $table->string('gender')->nullable()->comment('性別');
            $table->date('date_of_birth')->nullable()->comment('生年月日');
            $table->string('place_of_birth')->nullable()->comment('出身地');
            $table->string('email_address')->nullable()->comment('メールアドレス');
            $table->string('dd_status_type')->comment('デューデリジェンスステータス 選択肢アイテム種別');
            $table->string('dd_status')->comment('デューデリジェンスステータス');
            $table->date('dd_start_date')->comment('デューデリジェンス対象開始日');
            $table->date('dd_end_date')->nullable()->comment('デューデリジェンス対象終了日');
            $table->date('next_dd_date')->nullable()->comment('次回デューデリジェンス予定日');
            $table->boolean('under_continuous_dd')->nullable()->comment('継続審査中');
            $table->string('rep_check_api_reception_id')->nullable()->comment('風評チェックAPI受付ID');
            $table->string('rep_check_api_message')->nullable()->comment('風評チェックAPIメッセージ');
            $table->string('rep_check_api_status')->nullable()->comment('風評チェックAPIステータス');
            $table->string('dd_api_reception_id')->nullable()->comment('デューデリジェンスAPI受付ID');
            $table->string('dd_api_message')->nullable()->comment('デューデリジェンスAPIメッセージ');
            $table->string('dd_api_status')->nullable()->comment('デューデリジェンスAPIステータス');
            $table->string('ai_dd_result')->nullable()->comment('AI審査結果');
            $table->date('ai_dd_completed_date')->nullable()->comment('AI審査完了日');
            $table->string('ai_dd_comment')->nullable()->comment('AI審査コメント');
            $table->string('primary_dd_result')->nullable()->comment('一次審査結果');
            $table->bigInteger('primary_dd_user_id')->nullable()->comment('一次審査担当ユーザー');
            $table->date('primary_dd_completed_date')->nullable()->comment('一次審査完了日');
            $table->string('primary_dd_comment')->nullable()->comment('一次審査コメント');
            $table->string('final_dd_result')->nullable()->comment('最終審査結果');
            $table->bigInteger('final_dd_user_id')->nullable()->comment('最終審査担当ユーザー');
            $table->date('final_dd_completed_date')->nullable()->comment('最終審査完了日');
            $table->string('final_dd_comment')->nullable()->comment('最終審査コメント');
            $table->bigInteger('created_by')->nullable()->comment('作成ユーザー');
            $table->timestamp('created_at')->nullable()->comment('作成日時');
            $table->bigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->nullable()->comment('更新日時');
            $table->bigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->foreign(['dd_entity_type_type', 'dd_entity_type_code'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['dd_relation_type_type', 'dd_relation_type_code'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['dd_status_type', 'dd_status'])
                ->references(['selection_item_type', 'selection_item_code'])->on('selection_items')
                ->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('primary_dd_user_id')->references('user_id')->on('users');
            $table->foreign('final_dd_user_id')->references('user_id')->on('users');
            $table->foreign('parent_company_dd_id')->references('dd_id')->on('due_diligences');
            $table->foreign('target_company_dd_id')->references('dd_id')->on('due_diligences');

            $table->index('dd_code');
            $table->index('dd_status');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('dd_entity_type_code');
            $table->index('dd_relation_type_code');
            $table->index('target_company_dd_id');
            $table->index('parent_company_dd_id');
        });

        DB::statement('CREATE INDEX idx_due_diligences_deleted_null ON due_diligences(dd_id) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('due_diligences');
    }
};
