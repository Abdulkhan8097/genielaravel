<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRtaApiExecutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `rta_api_execution_status` ADD `order_cancellation_saved` tinyint NULL DEFAULT '0' COMMENT 'Cancellation details status: 0=pending, 1=failed, 2=success' AFTER `redemption_details_saved`;");
        DB::statement("ALTER TABLE `investor_order` ADD `order_cancellation_saved` tinyint NULL DEFAULT '0' COMMENT 'Cancellation save details api status: 0=pending, 1=failed, 2=success' AFTER `redemption_details_saved`, ADD `cancellation_order_id` int NULL COMMENT 'RTA cancellation order id' AFTER `order_cancellation_saved`;");

        DB::table('settings')->insert(array(
                                            array('key' => 'MAXMIMUM_SIP_END_DATE',
                                                  'value' => '2099-12-31',
                                                  'status' => 1),
                                        )
                                    );
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
