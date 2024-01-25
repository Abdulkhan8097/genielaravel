<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingForSendNotificationNavImap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert(array(
                                            array('key' => 'NAV_EXP_CRON_NOTIFICATION_EMAIL',
                                                  'value' => 'dharmesh.patel@samco.in,prasad.wargad@samco.in',
                                                  'status' => 1),
                                            array('key' => 'NAV_EXP_DATA_READ_TO_EMAIL',
                                                  'value' => 'dailyuploads@samcomf.com',
                                                  'status' => 1),
                                            array('key' => 'NAV_EXP_DATA_READ_TO_EMAIL_PASS',
                                                  'value' => '4&aqAqu89F',
                                                  'status' => 1),
                                            array('key' => 'NAV_EXP_DATA_READ_TO_EMAIL_HOSTNAME',
                                                  'value' => 'mail.samcomf.com',
                                                  'status' => 1),
                                            array('key' => 'NAV_EXP_DATA_READ_FROM_EMAIL',
                                                  'value' => 'balkrushna@samco.in',
                                                  'status' => 1),
                                            array('key' => 'NAV_EXP_DATA_ZIP_FILE_PASS',
                                                  'value' => 'SA45#zc50i',
                                                  'status' => 1),
                                            array('key' => 'EXP_RATIO_EMAIL_SUBJECT_NAME',
                                                  'value' => 'FA-Web_SAMCO_EXP_RATIO',
                                                  'status' => 1),
                                            array('key' => 'NAV_EMAIL_SUBJECT_NAME',
                                                  'value' => 'FA-SAMCO_Website_SAMCO_NAV.zip',
                                                  'status' => 1),
                                            array('key' => 'NAV_EMAIL_ATTACHMENT_FILE_NAME',
                                                  'value' => 'Website_SAMCO_NAV.zip',
                                                  'status' => 1),
                                            array('key' => 'EXP_RATIO_EMAIL_ATTACHMENT_FILE_NAME',
                                                  'value' => 'Web_SAMCO_EXP_RATIO.zip',
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
