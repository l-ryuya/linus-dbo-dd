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
        Schema::create('companies', function (Blueprint $table) {
            $table->comment('法人');

            $table->id('company_id')->comment('法人ID');
            $table->string('company_code')->unique()->comment('法人通番');
            $table->unsignedBigInteger('latest_dd_id')->comment('最新デューデリジェンスID');
            $table->string('organization_type_type')->comment('法人種別選択肢アイテム種別');
            $table->string('organization_type_code')->comment('法人種別');
            $table->string('company_status_type')->comment('法人ステータス選択肢アイテム種別');
            $table->string('company_status_code')->comment('法人ステータス');
            $table->string('second_language_type')->comment('副言語コード選択肢アイテム種別');
            $table->string('second_language_code')->comment('副言語コード');
            $table->string('company_name_en')->comment('法人名称（EN）');
            $table->string('company_name_sl')->nullable()->comment('法人名称（SL）');
            $table->string('company_short_name_en')->nullable()->comment('法人略称（EN）');
            $table->string('company_short_name_sl')->nullable()->comment('法人略称（SL）');
            $table->char('country_region_code', 3)->nullable()->comment('国・地域コード');
            $table->string('postal_code_en')->nullable()->comment('郵便番号（EN）');
            $table->string('postal_code_sl')->nullable()->comment('郵便番号（SL）');
            $table->string('prefecture_en')->nullable()->comment('都道府県（EN）');
            $table->string('prefecture_sl')->nullable()->comment('都道府県（SL）');
            $table->string('city_en')->nullable()->comment('市区町村（EN）');
            $table->string('city_sl')->nullable()->comment('市区町村（SL）');
            $table->string('street_en')->nullable()->comment('番地（EN）');
            $table->string('street_sl')->nullable()->comment('番地（SL）');
            $table->string('building_room_en')->nullable()->comment('建物名・部屋番号（EN）');
            $table->string('building_room_sl')->nullable()->comment('建物名・部屋番号（SL）');
            $table->char('nta_corporate_number', 13)->nullable()->comment('国税庁法人番号');
            $table->char('duns_number', 11)->nullable()->comment('D-U-N-S番号');
            $table->integer('founded_year')->nullable()->comment('設立時期（年）');
            $table->string('company_type_type')->nullable()->comment('会社形態選択肢アイテム種別');
            $table->string('company_type')->nullable()->comment('会社形態');
            $table->integer('employee_count')->nullable()->comment('従業員数');
            $table->char('capital_currency', 3)->nullable()->comment('資本金（通貨）');
            $table->decimal('capital_amount', 16, 2)->nullable()->comment('資本金（金額）');
            $table->string('website_en')->nullable()->comment('Webサイト（EN）');
            $table->string('website_sl')->nullable()->comment('Webサイト（SL）');
            $table->string('rep_risk_check_no')->nullable()->comment('風評リスクチェック受付番号');
            $table->string('dd_accept_no')->nullable()->comment('デューデリジェンス処理受付番号');
            $table->unsignedBigInteger('created_by')->nullable()->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->foreign('capital_currency')
                ->references('currency_code_alpha')
                ->on('currencies');
            $table->foreign('country_region_code')
                ->references('country_code_alpha3')
                ->on('country_regions');
            $table->foreign(['organization_type_type', 'organization_type_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
            $table->foreign(['company_status_type', 'company_status_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
            $table->foreign(['second_language_type', 'second_language_code'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
            $table->foreign(['company_type_type', 'company_type'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');

            $table->index('company_status_code');
            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement('CREATE INDEX idx_companies_deleted_null ON companies(company_id) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
