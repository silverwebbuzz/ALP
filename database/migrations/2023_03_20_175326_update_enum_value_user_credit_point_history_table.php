<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class UpdateEnumValueUserCreditPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `".cn::USER_CREDIT_POINT_HISTORY."` CHANGE `".cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL."` `".cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL."` ENUM('test','exercise','self_learning','assessment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `".cn::USER_CREDIT_POINT_HISTORY."` CHANGE `".cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL."` `".cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL."` ENUM('assignment', 'self_learning') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }
}
