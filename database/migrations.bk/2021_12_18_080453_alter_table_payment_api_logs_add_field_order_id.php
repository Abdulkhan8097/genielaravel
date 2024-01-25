<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentApiLogsAddFieldOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field investor_order_id
        Schema::table('payment_api_logs', function (Blueprint $table) {
            //$table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('investor_account_id');
            $table->string('merchant_transaction_id', 50)->nullable()->comment('Unique merchant transaction id')->after('investor_order_id');
            $table->index('merchant_transaction_id');
            $table->foreign('investor_account_id')->references('id')->on('investor_account');
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
        // removing earlier added field investor_order_id
        Schema::table('payment_api_logs', function (Blueprint $table) {
            $table->dropForeign('payment_api_logs_investor_account_id_foreign');
            $table->dropForeign('payment_api_logs_investor_order_id_foreign');
            $table->dropColumn(['investor_order_id', 'merchant_transaction_id']);
        });
    }
}
