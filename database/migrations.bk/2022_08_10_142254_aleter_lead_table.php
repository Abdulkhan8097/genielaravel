<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AleterLeadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->statement("ALTER TABLE `lead_account_detail` ADD `email_belongsto` VARCHAR(100) NULL DEFAULT NULL COMMENT 'email belongs to - declaration' AFTER `email`;");
        DB::connection('invdb')->statement("ALTER TABLE `lead_account_detail` ADD `mobile_belongsto` VARCHAR(100) NULL DEFAULT NULL COMMENT 'mobile belongs to - declaration' AFTER `email`;");
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
