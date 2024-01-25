<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvestorOrderChangeCommentOfFieldOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // updating the comment
        DB::statement("ALTER TABLE `investor_order` CHANGE `order_status` `order_status` TINYINT(1) NULL DEFAULT NULL COMMENT 'Order Status: 0 = pending, 1 = failed, 2 = success, 3 = cancelled, 4 = payment under verification';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // making the field comment like the one present before updating
        DB::statement("ALTER TABLE `investor_order` CHANGE `order_status` `order_status` TINYINT(1) NULL DEFAULT NULL COMMENT 'Order Status: 0 = pending, 1 = failed, 2 = success, 3 = cancelled';");
    }
}
