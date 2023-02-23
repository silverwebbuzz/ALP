<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
class AddLearningUnitIdInLearningObjectiveOrdering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_ORDERING_TABLE, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_ORDERING_SCHOOL_ID_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_LEARNING_UNIT_ID_COL)->nullable();
                $table->foreign(cn::LEARNING_OBJECTIVES_LEARNING_UNIT_ID_COL)->references(cn::LEARNING_UNITS_ID_COL)->on(cn::LEARNING_UNITS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::LEARNING_OBJECTIVES_ORDERING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::LEARNING_OBJECTIVES_LEARNING_UNIT_ID_COL]);
            $table->dropColumn(cn::LEARNING_OBJECTIVES_LEARNING_UNIT_ID_COL);
        });
    }
}
