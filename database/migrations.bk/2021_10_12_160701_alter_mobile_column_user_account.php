<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMobileColumnUserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account`
        CHANGE `mobile_amfi` `mobile_amfi` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'amfi api mobile number' AFTER `email_amfi`,
        CHANGE `mobile` `mobile` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'mobile' AFTER `email`");
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
