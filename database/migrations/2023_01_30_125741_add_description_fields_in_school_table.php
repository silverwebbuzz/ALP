<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddDescriptionFieldsInSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SCHOOL_SCHOOL_CITY, function($table){
                $table->LongText(cn::SCHOOL_DESCRIPTION_EN_COL)->nullable();
                $table->LongText(cn::SCHOOL_DESCRIPTION_CH_COL)->nullable();
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
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn([cn::SCHOOL_DESCRIPTION_EN_COL,cn::SCHOOL_DESCRIPTION_CH_COL]);
        });
    }
}
