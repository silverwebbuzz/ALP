<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldStageIdInLearningUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_UNITS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_UNITS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_UNITS_STAGE_ID_COL)->nullable();
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
        Schema::table(cn::LEARNING_UNITS_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::LEARNING_UNITS_STAGE_ID_COL);
        });
    }
}
