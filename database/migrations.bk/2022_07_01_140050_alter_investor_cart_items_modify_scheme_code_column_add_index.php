<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorCartItemsModifySchemeCodeColumnAddIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding an index to column scheme_code
        Schema::connection('invdb')->table('investor_cart_items', function (Blueprint $table) {
            $table->dropForeign('investor_cart_items_cart_id_foreign');
            $table->foreign('cart_id')->references('id')->on('investor_cart_order')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('scheme_code', 50)->nullable()->comment('RTA scheme code')->change();
            $table->foreign('scheme_code')->references('RTA_Scheme_Code')->on('scheme_master')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added index from column scheme_code
        Schema::connection('invdb')->table('investor_cart_items', function (Blueprint $table) {
            $table->dropForeign('investor_cart_items_cart_id_foreign');
            $table->foreign('cart_id')->references('id')->on('investor_cart_order');
            $table->dropForeign('investor_cart_items_scheme_code_foreign');
        });
    }
}
