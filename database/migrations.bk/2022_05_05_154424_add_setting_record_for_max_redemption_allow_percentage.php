<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingRecordForMaxRedemptionAllowPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding setting record for allowing maximum redemption amount percentage
        DB::table('settings')->insert(array(
                                            array('key' => 'MAXIMUM_REDEMPTION_AMOUNT_PERCENTAGE',
                                                  'value' => 5,
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
