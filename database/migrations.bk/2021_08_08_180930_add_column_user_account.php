<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account`
        ADD `is_deficiency` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = No deficiency 1=Yes, deficiency in some file' AFTER `is_nominee_minor`,
        ADD `distributor_id` varchar(20) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'SMACOMF distributor id' AFTER `user_unique_code`");
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
