<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAddColumnInvestorCartOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like Folio Number
        Schema::connection('invdb')->table('investor_order', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_cart_order_id')->nullable()->comment('Foreign key references id field from investor_cart_order table')->after('investor_account_id');
            $table->foreign('investor_cart_order_id')->references('id')->on('investor_cart_order')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::connection('invdb')->table('investor_order', function (Blueprint $table) {
            $table->dropForeign('investor_order_investor_cart_order_id_foreign');
            $table->dropColumn('investor_cart_order_id');
        });
    }
}
