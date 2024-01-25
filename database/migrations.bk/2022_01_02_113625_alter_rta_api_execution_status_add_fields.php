<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRtaApiExecutionStatusAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->string('folio_number', 20)->nullable()->comment('Folio number')->after('rta_refno');
            $table->tinyInteger('sip_enach_saved')->default(0)->nullable()->comment('SIP ENach saved api status: 0=pending, 1=failed, 2=success')->after('payment_details_saved');
            $table->tinyInteger('additional_lumpsum_investment_details_saved')->default(0)->nullable()->comment('Additional purchase lumpsum investment details saved api status: 0=pending, 1=failed, 2=success')->after('sip_enach_saved');
            $table->tinyInteger('additional_sip_investment_details_saved')->default(0)->nullable()->comment('Additional purchase SIP investment details saved api status: 0=pending, 1=failed, 2=success')->after('additional_lumpsum_investment_details_saved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->dropColumn(['folio_number', 'sip_enach_saved', 'additional_lumpsum_investment_details_saved', 'additional_sip_investment_details_saved']);
        });
    }
}
