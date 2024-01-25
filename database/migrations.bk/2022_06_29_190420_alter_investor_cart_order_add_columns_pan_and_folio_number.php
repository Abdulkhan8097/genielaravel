<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorCartOrderAddColumnsPanAndFolioNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns like PAN & Folio Number
        Schema::connection('invdb')->table('investor_cart_order', function (Blueprint $table) {
            $table->string('pan', 20)->nullable()->comment('PAN card number')->after('folio_investor_detail_id');
            $table->string('folio_number', 20)->nullable()->comment('Folio number')->after('pan');
            $table->index('folio_number');
            $table->index('pan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns like PAN & Folio Number
        Schema::connection('invdb')->table('investor_cart_order', function (Blueprint $table) {
            $table->dropColumn('folio_number');
            $table->dropColumn('pan');
        });
    }
}
