<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnAnswerOderingHistoryStudentQuestionAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE, function (Blueprint $table) {
            $table->after(cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL, function($table){
                $table->String(cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL)->nullable();
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
        Schema::table(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL);
        });
    }
}
