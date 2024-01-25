<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentNetbankingLogsAddFieldOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field investor_order_id
        DB::statement('ALTER TABLE payment_netbanking_logs DROP COLUMN `investor_account_id`;');
        Schema::table('payment_netbanking_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references investor_id field from investor_account table')->after('id');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('investor_account_id');
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
        Schema::table('payment_netbanking_logs', function (Blueprint $table) {
            $table->dropForeign('payment_netbanking_logs_investor_account_id_foreign');
            $table->dropForeign('payment_netbanking_logs_investor_order_id_foreign');
            $table->dropColumn(['investor_order_id']);
        });
    }
}
