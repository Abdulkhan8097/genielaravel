<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AltertableschememasterportfoliocolumnHoldPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `scheme_master_portfolio` CHANGE `Holdpercentage` `Holdpercentage` DECIMAL(25,2) NULL DEFAULT NULL COMMENT 'Hold percentage';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `scheme_master_portfolio` CHANGE `Holdpercentage` `Holdpercentage` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Hold percentage';");
    }
}
