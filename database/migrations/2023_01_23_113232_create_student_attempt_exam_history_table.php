<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateStudentAttemptExamHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::STUDENT_ATTEMPT_EXAM_HISTORY_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::STUDENT_ATTEMPT_EXAM_HISTORY_ID_COL);
            $table->unsignedBigInteger(cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL)->nullable();
            $table->Integer(cn::STUDENT_ATTEMPT_EXAM_HISTORY_SELECTED_ANSWER_COL)->nullable();
            $table->enum(cn::STUDENT_ATTEMPT_EXAM_HISTORY_LANGUAGE_COL, ['en', 'ch']);
            $table->timestamps();
            // $table->softDeletes();

            $table->foreign(cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL)->references(cn::QUESTION_TABLE_ID_COL)->on(cn::QUESTION_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::STUDENT_ATTEMPT_EXAM_HISTORY_TABLE);
    }
}
