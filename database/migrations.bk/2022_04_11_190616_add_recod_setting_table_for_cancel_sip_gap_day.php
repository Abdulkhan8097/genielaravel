<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecodSettingTableForCancelSipGapDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding setting record for sip cancel gap day
        DB::table('settings')->insert(array(
                                            array('key' => 'SIP_CANCEL_GAP_DAY',
                                                  'value' => 10,
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
