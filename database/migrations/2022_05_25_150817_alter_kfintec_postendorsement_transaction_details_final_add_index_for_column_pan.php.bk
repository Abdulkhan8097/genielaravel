<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterKfintecPostendorsementTransactionDetailsFinalAddIndexForColumnPan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding index for column PAN in MySQL table: kfintec_Postendorsement_TransactionDetails_final
        Schema::table('kfintec_Postendorsement_TransactionDetails_final', function (Blueprint $table) {
            $table->index('pan');
            $table->index('acno');
            $table->index('dmat_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added index for column PAN in MySQL table: kfintec_Postendorsement_TransactionDetails_final
        Schema::table('kfintec_Postendorsement_TransactionDetails_final', function (Blueprint $table) {
            $table->dropIndex('kfintec_postendorsement_transactiondetails_final_pan_index');
            $table->dropIndex('kfintec_postendorsement_transactiondetails_final_acno_index');
            $table->dropIndex('kfintec_postendorsement_transactiondetails_final_dmat_flag_index');
        });
    }
}
