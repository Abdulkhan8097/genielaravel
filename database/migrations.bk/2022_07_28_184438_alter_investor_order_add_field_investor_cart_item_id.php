<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAddFieldInvestorCartItemId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like investor_cart_order_item_id
        Schema::table('investor_order', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_cart_order_item_id')->nullable()->comment('Foreign key references id field from investor_cart_order_items table')->after('investor_cart_order_id');
            $table->foreign('investor_cart_order_item_id')->references('id')->on('investor_cart_order_items')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column like investor_cart_order_item_id
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropForeign('investor_order_investor_cart_order_item_id_foreign');
            $table->dropColumn('investor_cart_order_item_id');
        });
    }
}
