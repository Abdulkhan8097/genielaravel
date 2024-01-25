<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorFolioDetailsFieldStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `investor_folio_details` CHANGE `status` `status` TINYINT(4) NULL DEFAULT '1' COMMENT 'Status: 0=Inactive, 1=Active';");
        DB::statement("UPDATE `investor_folio_details` SET `created_at` = `created_at`, `updated_at` = `updated_at`, `status` = 1 WHERE `status` = 0;");
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
