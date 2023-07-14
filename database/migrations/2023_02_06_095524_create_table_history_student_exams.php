<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableHistoryStudentExams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::HISTORY_STUDENT_EXAMS_ID_COL);
            $table->unsignedBigInteger(cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL);
            $table->unsignedBigInteger(cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL);
            $table->Integer(cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL)->nullable();
            $table->string(cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_EXAMS_BEFORE_EMOJI_ID_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_EXAMS_AFTER_EMOJI_ID_COL)->nullable();
            $table->Integer(cn::HISTORY_STUDENT_EXAMS_TOTAL_SECONDS_COL)->nullable();

            $table->foreign(cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::HISTORY_STUDENT_EXAMS_TABLE);
    }
}
