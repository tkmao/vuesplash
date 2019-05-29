<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code', 5)->comment('プロジェクトコード');
            $table->string('name', 50)->collate('utf8mb4_general_ci')->comment('プロジェクト名');
            $table->unsignedInteger('category_id')->nullable()->comment('区分ID');
            $table->unsignedInteger('company_id')->nullable()->comment('企業ID');
            $table->unsignedInteger('user_id')->nullable()->comment('担当者ID');
            $table->unsignedInteger('status_id')->comment('プロジェクトステータス');
            $table->boolean('is_deleted')->comment('削除フラグ');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."projects COMMENT 'プロジェクトマスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('projects');
        Schema::enableForeignKeyConstraints();
    }
}
