<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingForCvlKra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('settings')->insert(array(
                                            array('key' => 'HIT_CIVIL_KRA_AFTER_ESIGN',
                                                  'value' => 0,
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
