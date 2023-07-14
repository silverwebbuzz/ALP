<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableUsedQuestionAnswerCounts extends Migration
{
    public function up()
    {
        Schema::create(cn::USED_QUESTION_ANSWER_COUNT_TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger(cn::USED_QUESTION_ANSWER_QUESTION_ID_COL)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_QUESTION_COUNT_COL)->default(0)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_ANSWER1_COUNT_COL)->default(0)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_ANSWER2_COUNT_COL)->default(0)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_ANSWER3_COUNT_COL)->default(0)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_ANSWER4_COUNT_COL)->default(0)->nullable();
            $table->Integer(cn::USED_QUESTION_ANSWER_ANSWER5_COUNT_COL)->default(0)->nullable();

            $table->foreign(cn::USED_QUESTION_ANSWER_QUESTION_ID_COL)->references(cn::QUESTION_TABLE_ID_COL)->on(cn::QUESTION_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }
    
    public function down()
    {
        Schema::table(cn::USED_QUESTION_ANSWER_COUNT_TABLE, function($table) {
            $table->dropForeign(cn::USED_QUESTION_ANSWER_QUESTION_ID_COL.'_foreign');
        });
        Schema::dropIfExists(cn::USED_QUESTION_ANSWER_COUNT_TABLE);
    }
}
