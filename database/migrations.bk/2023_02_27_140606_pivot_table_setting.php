<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PivotTableSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->table('settings')->insert(array(array('key' => 'PIVOT-SIP-START',
                                                  'value' => '2023-03-01'
                                                ),array('key' => 'PIVOT-SIP-END',
                                                                                      'value' => '2023-06-30'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-DASHBOARD',
                                                                                      'value' => '1'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-STARTSD',
                                                                                      'value' => '2023-03-01'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-STARTED',
                                                                                      'value' => '2023-07-31'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-AMOUNT',
                                                                                      'value' => '2000'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-MONTH',
                                                                                      'value' => '36'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-SEAT-AMOUNT',
                                                                                      'value' => '250000'
                                                                                    ),
                                                array('key' => 'PIVOT-SIP-TERMETTED',
                                                                                      'value' => '2023-07-31'
                                                                                    ),
                                            ),

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
