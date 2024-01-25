<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRtaApiLogsAddFieldInvestorOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field investor_order_id
        Schema::table('rta_api_logs', function (Blueprint $table) {
            $table->string('rta_refno', 20)->nullable()->comment('RTA reference number')->after('investor_account_id');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('rta_refno');
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
        Schema::table('rta_api_logs', function (Blueprint $table) {
            $table->dropForeign('rta_api_logs_investor_account_id_foreign');
            $table->dropForeign('rta_api_logs_investor_order_id_foreign');
            $table->dropColumn(['investor_order_id', 'rta_refno']);
        });
    }
}
