<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableLearningUnitsProgressReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::LEARNING_UNITS_PROGRESS_REPORT_TABLE, function (Blueprint $table) {
            $table->id(cn::LEARNING_UNITS_PROGRESS_REPORT_ID_COL);
            $table->unsignedBigInteger(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID);
            $table->LongText(cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL)->nullable();
            $table->LongText(cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL)->nullable();
            $table->LongText(cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL)->nullable();
            $table->timestamps();
            $table->foreign(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::LEARNING_UNITS_PROGRESS_REPORT_TABLE);
    }
}
