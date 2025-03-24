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
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->comment('ユーザーステータス');

            $table->string('status', 32)->primary()->comment('ステータス');
            $table->string('description')->default('')->comment('説明');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statuses');
    }
};
