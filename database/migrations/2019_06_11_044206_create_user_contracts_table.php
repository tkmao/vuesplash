<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザID');
            $table->unsignedInteger('usertype_id')->comment('ユーザタイプID');
            $table->unsignedInteger('workingtime_type')->comment('勤務形態、1:勤務時間変動、2:勤務時間固定');
            $table->unsignedInteger('worktime_day')->nullable()->comment('一日の勤務時間');
            $table->unsignedInteger('maxworktime_month')->nullable()->comment('月の上限勤務時間');
            $table->unsignedInteger('workingtime_min')->nullable()->comment('勤務時間下限');
            $table->unsignedInteger('workingtime_max')->nullable()->comment('勤務時間上限');
            $table->date('startdate')->comment('契約有効開始日');
            $table->date('enddate')->comment('契約有効終了日');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."user_contracts COMMENT 'ユーザ契約情報'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_contracts');
        Schema::enableForeignKeyConstraints();
    }
}
