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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('ユーザー');

            $table->id();
            $table->string('status', 32)->default('under_review')->comment('状態');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('last_name', 64);
            $table->string('first_name', 64);
            $table->string('middle_name', 64)->default('');
            $table->string('position', 64)->comment('役職');
            // 言語コード https://ja.wikipedia.org/wiki/ISO_639-1%E3%82%B3%E3%83%BC%E3%83%89%E4%B8%80%E8%A6%A7
            $table->string('language_code', 2)->default('ja')->comment('言語コード');
            $table->timestamps();

            $table->foreign('status')->references('status')->on('user_statuses');
        });

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
