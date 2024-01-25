<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountTypeUserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account`
        ADD `account_type` varchar(100) NULL COMMENT 'account type' AFTER `branch_add`,
        CHANGE `enc_bank_id` `enc_bank_id` varchar(255) NULL COMMENT 'encrypted bank id' AFTER `bank_city`");
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
