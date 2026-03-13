<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('basic_auth_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password'); // ハッシュ化して保存
            $table->string('description')->nullable(); // 用途メモ
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('basic_auth_users');
    }
};