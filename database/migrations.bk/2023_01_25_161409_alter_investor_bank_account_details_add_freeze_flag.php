<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorBankAccountDetailsAddFreezeFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_account_bank_details` MODIFY `bank_verified` tinyint DEFAULT 0 COMMENT 'Possible values: 0 = pending, 1 = In progress, 2 = accepted, 3 = rejected'");

        DB::statement("ALTER TABLE `investor_penny_deficiency_logs` MODIFY `bank_verified` tinyint DEFAULT 0 COMMENT 'Possible values: 0 = pending, 1 = In progress, 2 = accepted, 3 = rejected'");

        Schema::table('investor_penny_deficiency_logs', function (Blueprint $table) {

            $table->tinyInteger('freeze_flag')->default(0)->nullable()->comment('Possible values: 0 = Not Freezed, 1 = Freezed')->after('bank_verified');
        });

        Schema::table('investor_account_bank_details', function (Blueprint $table) {

            $table->tinyInteger('freeze_flag')->default(0)->nullable()->comment('Possible values: 0 = Not Freezed, 1 = Freezed')->after('bank_verified');
        });
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
