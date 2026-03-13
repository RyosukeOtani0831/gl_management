<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            // 'updated_at' カラムが存在しない場合に追加
            if (!Schema::hasColumn('user', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }
    
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            // 'updated_at' カラムを削除
            $table->dropColumn('updated_at');
        });
    }
}
