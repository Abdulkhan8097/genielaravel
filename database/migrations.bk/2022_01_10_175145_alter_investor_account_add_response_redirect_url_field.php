<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAccountAddResponseRedirectUrlField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding investor_order_id field in MySQL table: distributors_investors_api_logs
        Schema::table('distributors_investors_api_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('investor_account_id');
            $table->tinyInteger('order_status')->default(0)->nullable()->comment('Order status: 0=pending, 1=failed, 2=success, 3=cancelled')->after('status');
            $table->index('investor_order_id');
            $table->index('order_status');
            $table->foreign('investor_order_id')->references('id')->on('investor_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field investor_order_id from MySQL table: distributors_investors_api_logs
        Schema::table('distributors_investors_api_logs', function (Blueprint $table) {
            $table->dropForeign('distributors_investors_api_logs_investor_order_id_foreign');
            $table->dropColumn(['investor_order_id', 'order_status']);
        });
    }
}
