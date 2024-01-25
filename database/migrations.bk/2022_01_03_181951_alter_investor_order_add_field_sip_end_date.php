<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAddFieldSipEndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field sip_end_date
        Schema::table('investor_order', function (Blueprint $table) {
            $table->date('sip_end_date')->nullable()->comment('SIP end date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field sip_end_date
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn(['sip_end_date']);
        });
    }
}
