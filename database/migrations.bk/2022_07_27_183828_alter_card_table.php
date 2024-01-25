<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->statement("ALTER TABLE `investor_cart_order` ADD `broker_id` VARCHAR(200) NULL DEFAULT NULL COMMENT 'ARN code of a broker/partner/distributor ' AFTER `folio_number`;");
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
