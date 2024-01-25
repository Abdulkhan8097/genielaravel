<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserAccountMobileVerify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account`
        CHANGE `PAN` `PAN` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'PAN Number' AFTER `ARN`,
        ADD `mobile_verify` tinyint NOT NULL DEFAULT '0' COMMENT '1=>YES, 0=>NO' AFTER `email_verify`");
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
