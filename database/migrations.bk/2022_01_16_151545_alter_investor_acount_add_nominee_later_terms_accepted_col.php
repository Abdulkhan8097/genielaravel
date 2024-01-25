<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAcountAddNomineeLaterTermsAcceptedCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_account` ADD `nominee_later_terms_accepted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Nominee later terms accepted 1=YES' AFTER `bank_terms_accepted`");
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
