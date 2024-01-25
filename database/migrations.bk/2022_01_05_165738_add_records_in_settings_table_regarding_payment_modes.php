<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordsInSettingsTableRegardingPaymentModes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding entries into settings table regarding UPI & Bank Transfer mode enabling
        DB::table('settings')->insert(array(
                                            array('key' => 'UPI_PAYMENT_ENABLED',
                                                  'value' => 1,
                                                  'status' => 1),
                                            array('key' => 'UPI_PAYMENT_WAIT_TIMEOUT',
                                                  'value' => '5 minutes',
                                                  'status' => 1),
                                            array('key' => 'BANK_TRANSFER_ENABLED',
                                                  'value' => 1,
                                                  'status' => 1)
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
