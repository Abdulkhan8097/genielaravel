<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordAllowFreshFolioToAllInvestorsInSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('settings')->insert(array(
                                            array('key' => 'ALLOW_FRESH_FOLIO_CREATION_TO_OTHER_THAN_OFFLINE_INVESTOR',
                                                  'value' => 'no',
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
