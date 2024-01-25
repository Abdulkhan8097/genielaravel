<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCartitemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_cart_order_items` ADD `selected_day` VARCHAR(122) NULL DEFAULT NULL COMMENT 'it is use for stp order it store day' AFTER `frequency_type`;");
        DB::statement("ALTER TABLE `investor_cart_order_items` ADD `scheme_code_to` VARCHAR(122) NULL DEFAULT NULL COMMENT 'it is use for switch order to store to scheme' AFTER `scheme_code`;");
        DB::statement("ALTER TABLE `investor_order` ADD `scheme_code_to` VARCHAR(122) NULL DEFAULT NULL COMMENT 'it is use for switch,stp,stp order to store to scheme' AFTER `scheme_code`;");
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
