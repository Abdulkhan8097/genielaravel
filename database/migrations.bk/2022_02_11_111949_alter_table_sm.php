<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `scheme_master_details` ADD `about_objective` TEXT NULL DEFAULT NULL COMMENT 'about objective for detail scheme ' AFTER `short_objective`;");
        DB::statement("UPDATE `scheme_master_details` SET `about_objective` = 'Samco Flexi Cap Fund will invest in 25 stress tested efficient companies from India & across the globe at an efficient price, maintaining an efficient portfolio turnover & cost to generate superior risk-adjusted return for investors over long term.' WHERE `scheme_master_details`.`RTA_Scheme_Code` = 'FCRG';");
        DB::statement("UPDATE `scheme_master_details` SET `about_objective` = 'Samco Flexi Cap Fund will invest in 25 stress tested efficient companies from India & across the globe at an efficient price, maintaining an efficient portfolio turnover & cost to generate superior risk-adjusted return for investors over long term.' WHERE `scheme_master_details`.`RTA_Scheme_Code` = 'FCDG';");
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
