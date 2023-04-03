<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableActivityLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::ACTIVITY_LOG_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::ACTIVITY_LOG_ID_COL);
            $table->unsignedBigInteger(cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::ACTIVITY_LOG_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::ACTIVITY_LOG_USER_ID_COL)->nullable();
            $table->LongText(cn::ACTIVITY_LOG_ACTIVITY_LOG_COL)->nullable();
            $table->timestamps();
            $table->foreign(cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::ACTIVITY_LOG_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::ACTIVITY_LOG_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::ACTIVITY_LOG_TABLE, function($table) {
            $table->dropForeign(cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL.'_foreign');
            $table->dropForeign(cn::ACTIVITY_LOG_SCHOOL_ID_COL.'_foreign');
            $table->dropForeign(cn::ACTIVITY_LOG_USER_ID_COL.'_foreign');
        });
        Schema::dropIfExists(cn::ACTIVITY_LOG_TABLE);
    }
}
