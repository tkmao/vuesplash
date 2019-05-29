<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_works', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_schedule_id')->comment('勤務表ID');
            $table->unsignedInteger('project_id')->comment('プロジェクトID');
            $table->float('worktime', 5, 2)->comment('勤務時間');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."project_works COMMENT 'プロジェクト時間'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('project_works');
        Schema::enableForeignKeyConstraints();
    }
}
