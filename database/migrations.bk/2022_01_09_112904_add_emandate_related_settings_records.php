<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmandateRelatedSettingsRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding entries related to e-mandate minimum/multiplier amount
        DB::table('settings')->insert(array(
                                            array('key' => 'EMANDATE_MINIMUM_AMOUNT',
                                                  'value' => 10000,
                                                  'status' => 1),
                                            array('key' => 'EMANDATE_AMOUNT_MULTIPLIER',
                                                  'value' => 5000,
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
