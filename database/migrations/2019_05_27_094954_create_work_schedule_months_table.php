<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkScheduleMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_schedule_months', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザid');
            $table->unsignedInteger('yearmonth')->comment('年月');
            $table->boolean('is_subumited')->comment('提出済FLG');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."work_schedule_months COMMENT '勤務表提出状況'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('work_schedule_months');
        Schema::enableForeignKeyConstraints();
    }
}
