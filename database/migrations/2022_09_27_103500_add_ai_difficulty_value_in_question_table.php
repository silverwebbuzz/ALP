<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddAiDifficultyValueInQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_DIFFICULTY_LEVEL_COL, function($table){
                $table->String(cn::QUESTION_AI_DIFFICULTY_VALUE)->nullable();
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
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::QUESTION_AI_DIFFICULTY_VALUE);
        });
    }
}
