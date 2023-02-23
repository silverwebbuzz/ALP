<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeGradeIdAndSubjectIdInStrandUnitsObjectivesMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL)->nullable()->change();
            $table->unsignedBigInteger(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
           
        });
    }
}
