<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderChangeInstallmentAmountDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `investor_order` CHANGE `installment_amount` `installment_amount` DECIMAL(25,4) NULL DEFAULT NULL COMMENT 'SIP installment amount';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE `investor_order` CHANGE `installment_amount` `installment_amount` VARCHAR(15) NULL DEFAULT NULL COMMENT 'SIP installment amount';");
    }
}
