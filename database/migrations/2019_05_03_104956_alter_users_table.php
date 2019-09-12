<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 80)->after('remember_token')->unique()->nullable()->collate('utf8mb4_general_ci')->comment('APIトークン');
            $table->date('hiredate')->after('api_token')->comment('入社日');
            $table->unsignedInteger('paid_holiday')->after('hiredate')->comment('有給日数');
            $table->boolean('is_admin')->after('paid_holiday')->comment('管理者フラグ');
            $table->boolean('is_deleted')->after('is_admin')->comment('削除フラグ');
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."users COMMENT 'ユーザマスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token');
            $table->dropColumn('hiredate');
            $table->dropColumn('paid_holiday');
            $table->dropColumn('is_admin');
            $table->dropColumn('is_deleted');
        });
    }
}
