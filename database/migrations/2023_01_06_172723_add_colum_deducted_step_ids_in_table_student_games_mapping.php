<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumDeductedStepIdsInTableStudentGamesMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::STUDENT_GAMES_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::STUDENT_GAMES_MAPPING_INCREASED_STEP_IDS_COL, function($table){
                $table->longText(cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL)->nullable();
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
        Schema::table(cn::STUDENT_GAMES_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL);
        });
    }
}
