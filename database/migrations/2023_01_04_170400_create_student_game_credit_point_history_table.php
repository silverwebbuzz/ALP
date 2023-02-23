<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateStudentGameCreditPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_ID_COL);
            $table->unsignedBigInteger(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_GAME_ID_COL);
            $table->unsignedBigInteger(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_PLANET_ID_COL);
            $table->unsignedBigInteger(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_USER_ID_COL);
            $table->Integer(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_CURRENT_CREDIT_POINT_COL)->nullable();
            $table->Integer(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCT_CURRENT_STEP_COL)->nullable();
            $table->Integer(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCTED_STEPS_COL)->nullable();
            $table->Integer(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_INCREASED_STEPS_COL)->nullable();
            $table->Integer(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_REMAINING_CREDIT_POINT_COL)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_GAME_ID_COL)->references(cn::GAME_TABLE_ID_COL)->on(cn::GAME_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_PLANET_ID_COL)->references(cn::GAME_PLANETS_ID_COL)->on(cn::GAME_PLANETS_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::STUDENT_GAME_CREDIT_POINT_HISTORY_TABLE);
    }
}
