<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNomineeVerifiedInFolioInvestorDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE `folio_investor_detail` ADD `nominee_verified` tinyint(1) DEFAULT 0 COMMENT 'Nominee verified: 0 = No, 1 = Yes' AFTER `mobile_verified`");
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
