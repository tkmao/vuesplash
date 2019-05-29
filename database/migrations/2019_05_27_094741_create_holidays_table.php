<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('日付');
            $table->string('name', 20)->collate('utf8mb4_general_ci')->comment('休日名');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."holidays COMMENT '休日マスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('holidays');
        Schema::enableForeignKeyConstraints();
    }
}
