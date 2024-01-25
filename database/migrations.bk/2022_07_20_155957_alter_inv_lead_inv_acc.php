<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvLeadInvAcc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_lead` ADD `email_belongsto` varchar(100) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'email belongs to - declaration ' AFTER `email`, ADD `mobile_belongsto` varchar(100) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'mobile belongs to - declaration ' AFTER `email_belongsto`;");

        DB::statement("ALTER TABLE `investor_account` ADD `email_belongsto` varchar(100) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'email belongs to - declaration ' AFTER `email`, ADD `mobile_belongsto` varchar(100) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'mobile belongs to - declaration ' AFTER `email_belongsto`;");
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
