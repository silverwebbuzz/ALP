<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableWeatherDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::WEATHER_DETAIL_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::WEATHER_DETAIL_ID_COL);
            $table->LongText(cn::WEATHER_DETAIL_WEATHER_INFO_COL)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::WEATHER_DETAIL_TABLE);
    }
}
