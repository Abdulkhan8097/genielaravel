<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordInSettingsForAmcStartDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding setting record for allowing maximum number of banks to be added
        DB::table('settings')->insert(array(
                                            array('key' => 'SAMCO_AMC_START_DATE',
                                                  'value' => '2022-01-17',
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
    }
}
