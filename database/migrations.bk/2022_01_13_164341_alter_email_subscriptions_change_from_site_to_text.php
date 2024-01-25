<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmailSubscriptionsChangeFromSiteToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 
        DB::statement("ALTER TABLE `samcomf`.`email_subscriptions` DROP INDEX `email_subscriptions_from_site_index`;");
        DB::statement("ALTER TABLE `samcomf`.`email_subscriptions` CHANGE `from_site` `from_site` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'from_site';");
        DB::statement("ALTER TABLE `samcomf`.`email_subscriptions` ADD INDEX `email_subscriptions_from_site_index`(`from_site`(191));");
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
