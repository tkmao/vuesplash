<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->collate('utf8mb4_general_ci')->comment('企業名');
            $table->string('zipcode', 8)->collate('utf8mb4_general_ci')->nullable()->comment('郵便番号');
            $table->string('address', 50)->collate('utf8mb4_general_ci')->nullable()->comment('住所');
            $table->string('phone', 20)->collate('utf8mb4_general_ci')->nullable()->comment('電話番号');
            $table->string('fax', 20)->collate('utf8mb4_general_ci')->nullable()->comment('FAX番号');
            $table->boolean('is_deleted')->comment('削除フラグ');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ".DB::getTablePrefix()."companies COMMENT '企業マスタ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('companies');
        Schema::enableForeignKeyConstraints();
    }
}
