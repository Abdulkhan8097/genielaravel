<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTblStepupSipSchemeMasterAddColApplicableForFrequency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `stepup_sip_scheme_master` ADD `applicable_for_frequency` VARCHAR(50) NOT NULL DEFAULT 'ALL' COMMENT 'Show this steup up frequency in case of base frequency mentioned here' AFTER `sip_frequency`;");

        DB::statement("UPDATE `stepup_sip_scheme_master` SET `applicable_for_frequency` = 'MONTHLY' WHERE `sip_frequency` = 'HALF YEARLY';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
