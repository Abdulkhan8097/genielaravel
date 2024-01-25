<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordInSettingForB30city extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->statement("ALTER TABLE `settings` CHANGE `value` `value` text COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Value which needs to be used' AFTER `key`;");
        // adding setting record for b30 city brokerage structure
        DB::connection('invdb')->table('settings')->insert(array(
                                            array('key' => 'B30_CITIES_DEFAULT',
                                                  'value' => 'AHMEDABAD, ALLAHABAD, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI',
                                                  'status' => 1),
                                            array('key' => 'B30_CITIES_FY_2021_2022',
                                                  'value' => 'AHMEDABAD, ALLAHABAD, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI',
                                                  'status' => 1),
                                            array('key' => 'B30_CITIES_FY_2022_2023',
                                                  'value' => 'AHMEDABAD, AGRA, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI',
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
