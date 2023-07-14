<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
class AddDescriptionAndImageColInGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::GAME_TABLE, function (Blueprint $table) {
            $table->after(cn::GAME_NAME_COL, function($table){
                $table->String(cn::GAME_DESCRIPTION_COL)->nullable();
                $table->String(cn::GAME_IMAGE_PATH_COL)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::GAME_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::GAME_DESCRIPTION_COL);
            $table->dropColumn(cn::GAME_IMAGE_PATH_COL);
        });
    }
}
