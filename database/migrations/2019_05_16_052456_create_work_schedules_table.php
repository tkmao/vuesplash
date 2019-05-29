<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザID');
            $table->date('workdate')->comment('日付');
            $table->unsignedInteger('week_number')->comment('週番号');
            $table->string('detail', 500)->collate('utf8mb4_general_ci')->nullable()->comment('仕事詳細');
            $table->string('starttime_hh', 2)->nullable()->comment('出勤時刻HH');
            $table->string('starttime_mm', 2)->nullable()->comment('出勤時刻mm');
            $table->string('endtime_hh', 2)->nullable()->comment('退勤時刻HH');
            $table->string('endtime_mm', 2)->nullable()->comment('退勤時刻mm');
            $table->float('breaktime', 5, 2)->nullable()->comment('休憩時間');
            $table->float('breaktime_midnight', 5, 2)->nullable()->comment('休憩時間');
            $table->boolean('is_paid_holiday')->comment('有休フラグ');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."work_schedules COMMENT '勤務表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('work_schedules');
        Schema::enableForeignKeyConstraints();
    }
}
