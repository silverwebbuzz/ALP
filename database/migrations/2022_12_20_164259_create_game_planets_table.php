<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateGamePlanetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GAME_PLANETS_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::GAME_PLANETS_ID_COL);
            $table->String(cn::GAME_PLANETS_NAME_COL)->nullable();
            $table->longText(cn::GAME_PLANETS_IMAGE_COL)->nullable();
            $table->enum(cn::GAME_PLANETS_STATUS_COL,['active','inactive'])->default('active');
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
        Schema::dropIfExists(cn::GAME_PLANETS_TABLE);
    }
}
