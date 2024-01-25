<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAccountBankDetailsAddFieldBankDetailsSaved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('investor_account_bank_details', function (Blueprint $table) {
            $table->tinyInteger('bank_details_saved')->default(0)->nullable()->comment('Bank details saved api status: 0=pending, 1=failed, 2=success')->after('cdsl_bo_id');
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
        Schema::table('investor_account_bank_details', function (Blueprint $table) {
            $table->dropColumn(['bank_details_saved']);
        });
    }
}
