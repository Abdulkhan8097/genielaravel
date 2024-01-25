<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorFolioDetailsAddFieldPurchaseSaveConfirmation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field purchase_save_confirmation in MySQL table: investor_folio_details
        Schema::table('investor_folio_details', function (Blueprint $table) {
            $table->text('purchase_save_confirmation')->nullable()->comment('Payment save confirmation details')->after('payment_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field purchase_save_confirmation in MySQL table: investor_folio_details
        Schema::table('investor_folio_details', function (Blueprint $table) {
            $table->dropColumn(['purchase_save_confirmation']);
        });
    }
}
