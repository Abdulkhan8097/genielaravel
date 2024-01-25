<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmailSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::connection('mfdb')->statement("ALTER TABLE `email_subscriptions` ADD `name` varchar(100) NULL COMMENT 'name' AFTER `id`, ADD `mobile` varchar(20) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'mobile' AFTER `from_site`, ADD `city` varchar(100) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'city' AFTER `mobile`;");

        
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
