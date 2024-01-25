<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Putinsipdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->table('settings')->insert(array(array('key' => 'PUTIN-SIP-START',
                                                  'value' => '2022-07-01'
                                                ),array('key' => 'PUTIN-SIP-END',
                                                                                      'value' => '2022-09-30'
                                                                                    ),
                                                array('key' => 'PUTIN-SIP-DASHBOARD',
                                                                                      'value' => '1'
                                                                                    )
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
