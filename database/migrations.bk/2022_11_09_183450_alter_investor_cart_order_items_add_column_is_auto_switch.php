<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorCartOrderItemsAddColumnIsAutoSwitch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding is_auto_switch column in MySQL table: investor_cart_order_items
        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->tinyInteger('is_auto_switch')->nullable()->comment('Used when order_type is Switch. In case of Auto Switch transaction date goes of Target Scheme NFO End Date if its in NFO period')->after('min_redeem');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column is_auto_switch column from MySQL table: investor_cart_order_items
        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->dropColumn('is_auto_switch');
        });
    }
}
