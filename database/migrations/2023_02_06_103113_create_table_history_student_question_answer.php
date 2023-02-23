<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableHistoryStudentQuestionAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::HISTORY_STUDENT_QUESTION_ANSWER_ID_COL);
            $table->unsignedBigInteger(cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL);
            $table->unsignedBigInteger(cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL);
            $table->unsignedBigInteger(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL);
            $table->Integer(cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_QUESTION_ANSWER_NO_OF_SECOND_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL)->nullable();
            $table->string(cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL)->nullable();

            $table->foreign(cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL)->references(cn::QUESTION_TABLE_ID_COL)->on(cn::QUESTION_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE);
    }
}
