<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterKfintecMasterSipStpTransactionDetailsAddColumnToSchemeCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns scheme_code_to in MySQL table: kfintec_MasterSipStp_TransactionDetails
        Schema::table('kfintec_MasterSipStp_TransactionDetails', function (Blueprint $table) {
            $table->string('scheme_code_to', 255)->nullable()->comment('to scheme code')->after('to_Plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns scheme_code_to from MySQL table: kfintec_MasterSipStp_TransactionDetails
        Schema::table('kfintec_MasterSipStp_TransactionDetails', function (Blueprint $table) {
            $table->dropColumn('scheme_code_to');
        });
    }
}
