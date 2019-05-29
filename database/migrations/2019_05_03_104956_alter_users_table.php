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
            $table->string('api_token', 60)->after('remember_token')->unique()->nullable()->collate('utf8mb4_general_ci')->comment('APIトークン');
            $table->unsignedInteger('usertype_id')->after('password')->comment('ユーザタイプID');
            $table->unsignedInteger('workingtime_type')->after('usertype_id')->comment('勤務形態、1:勤務時間変動、2:勤務時間固定');
            $table->unsignedInteger('worktime_day')->after('workingtime_type')->nullable()->comment('一日の勤務時間');
            $table->unsignedInteger('maxworktime_month')->after('worktime_day')->nullable()->comment('月の上限勤務時間');
            $table->unsignedInteger('workingtime_min')->after('maxworktime_month')->nullable()->comment('勤務時間下限');
            $table->unsignedInteger('workingtime_max')->after('workingtime_min')->nullable()->comment('勤務時間上限');
            $table->date('hiredate')->after('workingtime_max')->comment('入社日');
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
            $table->dropColumn('usertype_id');
            $table->dropColumn('workingtime_type');
            $table->dropColumn('worktime_day');
            $table->dropColumn('maxworktime_month');
            $table->dropColumn('workingtime_min');
            $table->dropColumn('workingtime_max');
            $table->dropColumn('hiredate');
            $table->dropColumn('paid_holiday');
            $table->dropColumn('is_admin');
            $table->dropColumn('is_deleted');
        });
    }
}
