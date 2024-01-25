<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOtpAddColumnInvestorOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column investor_order_id in MySQL table: otp
        Schema::table('otp', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('investor_account_id');
            $table->index('investor_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column investor_order_id in MySQL table: otp
        Schema::table('otp', function (Blueprint $table) {
            $table->dropColumn('investor_order_id');
        });
    }
}
