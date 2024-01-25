<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTblInvestorBankAccountDetailsAndPennyDropLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE `investor_account_bank_details` MODIFY `bank_verified` tinyint DEFAULT 0 COMMENT 'Possible values: 0 = pending, 1 = In progress, 2 = success, 3 = failed, 4 = freeze'");
        
        DB::statement("ALTER TABLE `investor_penny_deficiency_logs` MODIFY `bank_verified` tinyint DEFAULT 0 COMMENT 'Possible values: 0 = pending, 1 = In progress, 2 = success, 3 = failed, 4 = freeze'");
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
