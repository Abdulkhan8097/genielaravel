<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCartOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_cart_order` ADD `term_accept` TINYINT NULL DEFAULT '0' COMMENT 'general Terms and conditions' AFTER `broker_id`;");
        DB::statement("ALTER TABLE `investor_cart_order` ADD `stp_term_accept` TINYINT NULL DEFAULT '0' COMMENT 'STP Terms and conditions' AFTER `broker_id`;");
        DB::statement("ALTER TABLE `investor_order` ADD `term_accept` TINYINT NULL DEFAULT '0' COMMENT 'general Terms and conditions' AFTER `broker_id`, ADD `stp_term_accept` TINYINT NULL DEFAULT '0' COMMENT 'STP Terms and conditions' AFTER `term_accept`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
