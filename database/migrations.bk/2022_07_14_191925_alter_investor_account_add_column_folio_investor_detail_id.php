<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAccountAddColumnFolioInvestorDetailId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field folio_investor_detail_id into MySQL table: investor_account & investor_account_bkp_records
        Schema::table('investor_account', function (Blueprint $table) {
            $table->unsignedBigInteger('folio_investor_detail_id')->nullable()->comment('Foreign key references id field from folio_investor_detail table')->after('investor_id');
            $table->index('folio_investor_detail_id');
            $table->foreign('folio_investor_detail_id')->references('id')->on('folio_investor_detail')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::table('investor_account_bkp_records', function (Blueprint $table) {
            $table->unsignedBigInteger('folio_investor_detail_id')->nullable()->comment('Foreign key references id field from folio_investor_detail table')->after('investor_id');
            $table->index('folio_investor_detail_id');
            $table->foreign('folio_investor_detail_id')->references('id')->on('folio_investor_detail')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field folio_investor_detail_id from MySQL table: investor_account & investor_account_bkp_records
        Schema::table('investor_account', function (Blueprint $table) {
            $table->dropForeign('investor_account_folio_investor_detail_id_foreign');
            $table->dropColumn('folio_investor_detail_id');
        });

        Schema::table('investor_account_bkp_records', function (Blueprint $table) {
            $table->dropForeign('investor_account_bkp_records_folio_investor_detail_id_foreign');
            $table->dropColumn('folio_investor_detail_id');
        });
    }
}
