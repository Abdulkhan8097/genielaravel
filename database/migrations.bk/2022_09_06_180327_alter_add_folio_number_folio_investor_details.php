<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddFolioNumberFolioInvestorDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like folio_number in MySQL table: folio_investor_detail
        Schema::table('folio_investor_detail', function (Blueprint $table) {
            $table->string('folio_number', 20)->nullable()->comment('Folio number')->after('investor_id');
            $table->index('folio_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column like folio_number from MySQL table: folio_investor_detail
        Schema::table('folio_investor_detail', function (Blueprint $table) {
            $table->dropColumn('folio_number');
        });
    }
}
