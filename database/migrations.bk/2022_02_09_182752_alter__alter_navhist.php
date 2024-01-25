<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAlterNavhist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `nav_history` ADD `pre_nav_date` DATE NULL DEFAULT NULL COMMENT 'previous nav date ' AFTER `NAV_Date`;");
        DB::statement("ALTER TABLE `nav_history` ADD `pre_nav` DECIMAL(10,4) NULL DEFAULT NULL COMMENT 'previous nav' AFTER `NAV_Date`;");
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
