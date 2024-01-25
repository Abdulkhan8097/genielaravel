<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorCartOrderItemsModifyStatusFieldComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // changing status field comment
        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->nullable()->comment("Status: 0=Inactive, 1=Active, 2=Converted to order")->after('euin_declaration_terms');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // keeping status field comment like the one which is before updating
        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active')->after('euin_declaration_terms');
            $table->index('status');
        });
    }
}
