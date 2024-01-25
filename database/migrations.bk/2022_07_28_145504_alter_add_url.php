<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     DB::connection('invdb')->statement("ALTER TABLE `investor_cart_order` ADD `payment_url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'payment Url' AFTER `broker_id`;");
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
