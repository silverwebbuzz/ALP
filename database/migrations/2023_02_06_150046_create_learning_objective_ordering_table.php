<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateLearningObjectiveOrderingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::LEARNING_OBJECTIVES_ORDERING_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::LEARNING_OBJECTIVES_ORDERING_ID_COL);
            $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_ORDERING_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_OBJECTIVE_ID_COL)->nullable();
            $table->Integer(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_POSITION_COL)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign(cn::LEARNING_OBJECTIVES_ORDERING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_OBJECTIVE_ID_COL)->references(cn::LEARNING_OBJECTIVES_ID_COL)->on(cn::LEARNING_OBJECTIVES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::LEARNING_UNIT_ORDERING_TABLE);
    }
}
