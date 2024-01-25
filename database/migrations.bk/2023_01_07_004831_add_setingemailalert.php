<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSetingemailalert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->table('settings')->insert(array(array('key' => 'EMPANELEMENT_ALERT_EMAIL',
                                                  'value' => 'mannu.rajak@samcomf.com'
                                                ),array('key' =>'EMPANELEMENT_ALERT_NAME',
                                                  'value' => 'Mannu'
                                                ),array('key' => 'EMPANELEMENT_ALERT_MOBILE',
                                                  'value' => '9702790831'
                                                ))

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
