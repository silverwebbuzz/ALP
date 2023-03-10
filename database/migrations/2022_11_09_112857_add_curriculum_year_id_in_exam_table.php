<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCurriculumYearIdInExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_ID_COLS, function($table){
                $table->unsignedBigInteger(cn::EXAM_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::EXAM_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::EXAM_CURRICULUM_YEAR_ID_COL);
        });
    }
}
