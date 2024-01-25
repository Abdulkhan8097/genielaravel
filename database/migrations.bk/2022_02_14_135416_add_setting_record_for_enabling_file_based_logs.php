<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingRecordForEnablingFileBasedLogs extends Migration
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
                                            array('key' => 'ENABLE_FILE_BASED_LOGS',
                                                  'value' => 1,
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
