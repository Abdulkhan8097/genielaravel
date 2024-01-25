<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderIncreasingFieldLengthOfReferenceNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `samcomf_investor_db`.`investor_order` CHANGE `unique_transaction_number` `unique_transaction_number` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Unique transaction number sent by us to either Billdesk/UPI gateway';");
        DB::statement("ALTER TABLE `samcomf_investor_db`.`investor_order` CHANGE `transaction_reference_number` `transaction_reference_number` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'transaction reference number sent by Billdesk after redirection from payment gateway';");
        DB::statement("ALTER TABLE `samcomf_investor_db`.`investor_order` CHANGE `bank_reference_number` `bank_reference_number` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Payment reference number';");
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
