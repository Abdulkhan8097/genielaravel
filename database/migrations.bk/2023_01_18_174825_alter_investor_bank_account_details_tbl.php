<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorBankAccountDetailsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE `investor_account_bank_details` SET `penny_created_at` = NULL WHERE 1");

        DB::statement("ALTER TABLE `investor_account_bank_details` MODIFY `penny_created_at` datetime DEFAULT NULL COMMENT 'Penny created datetime'");
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
