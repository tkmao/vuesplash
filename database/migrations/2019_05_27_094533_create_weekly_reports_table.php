<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザid');
            $table->unsignedInteger('week_number')->comment('週番号');
            $table->unsignedInteger('project_id')->nullable()->comment('プロジェクトID');
            $table->string('nextweek_schedule', 300)->collate('utf8mb4_general_ci')->nullable()->comment('来週の作業');
            $table->string('thismonth_dayoff', 300)->collate('utf8mb4_general_ci')->nullable()->comment('今月の休暇');
            $table->string('site_information', 300)->collate('utf8mb4_general_ci')->nullable()->comment('現場情報');
            $table->string('opinion', 300)->collate('utf8mb4_general_ci')->nullable()->comment('所感');
            $table->boolean('is_subumited')->comment('提出済FLG');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."weekly_reports COMMENT '週報'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('weekly_reports');
        Schema::enableForeignKeyConstraints();
    }
}
