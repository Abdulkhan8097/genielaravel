<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFromsiteInvestorAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_lead` DROP INDEX `investor_lead_from_site_index`;");

        DB::statement("ALTER TABLE `investor_lead` CHANGE `from_site` `from_site` text COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'from_site' AFTER `pan`;");

        DB::statement("ALTER TABLE `investor_account` CHANGE `from_site` `from_site` text COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'from_site' AFTER `broker_id`;");

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
