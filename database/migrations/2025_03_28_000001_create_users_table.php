<?php

use App\Enums\RoleType;
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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('ユーザー');

            $table->id('user_id')->comment('ユーザーID');
            $table->string('user_code')->unique()->comment('ユーザー通番');
            $table->unsignedBigInteger('company_id')->comment('法人ID');
            $table->unsignedBigInteger('latest_dd_id')->nullable()->comment('最新デューデリジェンスID');
            $table->string('user_status_type')->comment('ユーザーステータス選択肢アイテム種別');
            $table->string('user_status')->comment('ユーザーステータス');
            $table->string('last_name_en')->nullable()->comment('姓（EN）');
            $table->string('last_name_sl')->nullable()->comment('姓（SL）');
            $table->string('middle_name_en')->nullable()->comment('ミドルネーム（EN）');
            $table->string('middle_name_sl')->nullable()->comment('ミドルネーム（SL）');
            $table->string('first_name_en')->nullable()->comment('名（EN）');
            $table->string('first_name_sl')->nullable()->comment('名（SL）');
            $table->string('position_en')->nullable()->comment('役職（EN）');
            $table->string('position_sl')->nullable()->comment('役職（SL）');
            $table->string('email')->unique()->comment('メールアドレス');

            // 仮実装、認証機能で使うカラム
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('roles')->default(RoleType::Customer->value);
            $table->rememberToken();

            $table->string('mobile_phone')->nullable()->comment('携帯電話番号');
            $table->char('nationality_code', 3)->nullable()->comment('国籍コード');
            $table->string('gender_type')->nullable()->comment('性別選択肢アイテム種別');
            $table->string('gender')->nullable()->comment('性別');
            $table->date('date_of_birth')->nullable()->comment('生年月日');
            $table->string('place_of_birth_en')->nullable()->comment('出身地（EN）');
            $table->string('place_of_birth_sl')->nullable()->comment('出身地（SL）');
            $table->string('profile_en')->nullable()->comment('プロファイル（EN）');
            $table->string('profile_sl')->nullable()->comment('プロファイル（SL）');
            $table->unsignedBigInteger('created_by')->nullable()->comment('作成ユーザー');
            $table->timestamp('created_at')->comment('作成日時');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新ユーザー');
            $table->timestamp('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除ユーザー');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');

            $table->foreign('company_id')
                ->references('company_id')
                ->on('companies');
            $table->foreign(['user_status_type', 'user_status'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
            $table->foreign(['gender_type', 'gender'])
                ->references(['selection_item_type', 'selection_item_code'])
                ->on('selection_items');
            $table->foreign('nationality_code')
                ->references('country_code_alpha3')
                ->on('country_regions');

            $table->index('company_id');
            $table->index('user_status');
            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement('CREATE INDEX idx_users_deleted_null ON users(user_id) WHERE deleted_at IS NULL');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
