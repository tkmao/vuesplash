<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->collate('utf8mb4_general_ci')->comment('プロジェクトステータス名');
            $table->boolean('is_deleted')->comment('削除フラグ');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."project_statuses COMMENT 'プロジェクトステータスマスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('project_statuses');
        Schema::enableForeignKeyConstraints();
    }
}
