<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::REGIONS_TABLE, function (Blueprint $table) {
            $table->id(cn::REGIONS_ID_COL);
            $table->LongText(cn::REGIONS_REGION_EN_COL)->nullable();
            $table->LongText(cn::REGIONS_REGION_CH_COL)->nullable();
            $table->enum(cn::REGIONS_STATUS_COL, ['active', 'inactive'])->default('active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::REGIONS_TABLE);
    }
}
