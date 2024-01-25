<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEmailSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `email_subscriptions` ADD `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=>unsubscribed, 1=>subscribed' AFTER `from_site`, ADD INDEX `email_subscriptions_status_index`(`status`);");
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
