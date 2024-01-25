<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderChangeCommentOfColumnRtaOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE investor_order CHANGE rta_order_status rta_order_status tinyint(1) NULL DEFAULT '0' COMMENT 'RTA order status: 0 = pending, 1 = failed, 2 = success, 3 = rejected, 4 = cancelled' AFTER cancellation_date;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE investor_order CHANGE rta_order_status rta_order_status tinyint(1) NULL DEFAULT '0' COMMENT 'RTA order status: 0 = pending, 1 = failed, 2 = success, 3 = cancelled' AFTER cancellation_date;");
    }
}
