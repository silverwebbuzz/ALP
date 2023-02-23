<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddGradeIdColInGamePlanetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::GAME_PLANETS_TABLE, function (Blueprint $table) {
            $table->after(cn::GAME_PLANETS_NAME_COL, function($table){
                $table->unsignedBigInteger(cn::GAME_PLANETS_GRADE_ID_COL)->nullable();
                $table->foreign(cn::GAME_PLANETS_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::GAME_PLANETS_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::GAME_PLANETS_GRADE_ID_COL]);
            $table->dropColumn(cn::GAME_PLANETS_GRADE_ID_COL);
        });

    }
}
