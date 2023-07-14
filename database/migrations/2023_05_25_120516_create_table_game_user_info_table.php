<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableGameUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GAME_USER_INFO_TABLE, function (Blueprint $table) {
            $table->id();
            $table->String(cn::GAME_USER_INFO_USERNAME_COL)->nullable();
            $table->String(cn::GAME_USER_INFO_PASSWORD_COL)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GAME_USER_INFO_TABLE);
    }
}
