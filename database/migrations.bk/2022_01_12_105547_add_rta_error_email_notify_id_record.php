<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRtaErrorEmailNotifyIdRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding an email notification record for RTA API error
        DB::table('settings')->insert(array(array('key' => 'RTA_API_ERROR_EMAIL_NOTIFY_TO',
                                                  'value' => 'rankmf_dev@samco.in'
                                                )
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
