<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordForSAMCOMFFUNDIDInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert(array(array('key' => 'SAMCOMF_FUND_ID_AT_RTA',
                                                  'value' => 188,
                                                  'status' => 1
                                                ),
                                            array('key' => 'SAMCOMF_SIP_ENACH_BRANCH_CODE',
                                                  'value' => 'WB99',
                                                  'status' => 1
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
    }
}
