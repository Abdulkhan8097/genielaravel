<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBrokaragePartnerList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brokarage_partner_list', function (Blueprint $table) {
            DB::statement("ALTER TABLE `brokarage_partner_list` ADD `special_additional_first_year_trail` VARCHAR(250) NULL DEFAULT '0' COMMENT 'Special Additional 1st Year Trial' AFTER `b30`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` ADD `special_additional_first_year_trail_for_b30` VARCHAR(250) NULL DEFAULT '0' COMMENT 'Special Additional 1st Year Trial for b30' AFTER `special_additional_first_year_trail`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` ADD `month` VARCHAR(250) NULL DEFAULT '0' COMMENT 'Month' AFTER `special_additional_first_year_trail_for_b30`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` ADD `year` VARCHAR(250) NULL DEFAULT '0' COMMENT 'year' AFTER `month`;");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brokarage_partner_list', function (Blueprint $table) {
            DB::statement("ALTER TABLE `brokarage_partner_list` DROP `special_additional_first_year_trail`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` DROP `special_additional_first_year_trail_for_b30`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` DROP `month`;");
            DB::statement("ALTER TABLE `brokarage_partner_list` DROP `year`;");
        });    
    }
}
