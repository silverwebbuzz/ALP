<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class UpdateEnumCreditPointTypeInUserCreditPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `".cn::USER_CREDIT_POINT_HISTORY."` CHANGE `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` ENUM('submission','accuracy','ability','manual_credit_point') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `".cn::USER_CREDIT_POINT_HISTORY."` CHANGE `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` `".cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL."` ENUM('after_submission','accuracy_level','ability_level','manual_credit_point') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }
}
