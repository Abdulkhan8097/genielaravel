<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorCartItemsAddColumnFolioNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like Folio Number
        Schema::connection('invdb')->table('investor_cart_items', function (Blueprint $table) {
            $table->string('folio_number', 20)->nullable()->comment('Folio number')->after('scheme_code');
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
        // removing earlier added column like Folio Number
        Schema::connection('invdb')->table('investor_cart_items', function (Blueprint $table) {
            $table->dropColumn('folio_number');
        });
    }
}
