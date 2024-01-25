<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordsInSettingsTableRegardingActiveShareEmailNotify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding entries into settings table regarding UPI & Bank Transfer mode enabling
        DB::connection('invdb')->table('settings')->insert(array(
                                                        array('key' => 'ACTIVE_SHARE_EMAIL_NOTIFY_TO',
                                                            'value' => 'rankmf_dev@samco.in',
                                                            'status' => 1)
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
        Schema::table('settings_table_regarding__active__share__email__notify', function (Blueprint $table) {
            //
        });
    }
}
