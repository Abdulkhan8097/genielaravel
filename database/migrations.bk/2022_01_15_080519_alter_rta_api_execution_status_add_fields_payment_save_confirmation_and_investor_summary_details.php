<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRtaApiExecutionStatusAddFieldsPaymentSaveConfirmationAndInvestorSummaryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding investor_summary_details & purchase_save_confirmation field in MySQL table: rta_api_execution_status
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->tinyInteger('investor_summary_details')->default(0)->nullable()->comment('Investor summary details status: 0=pending, 1=failed, 2=success')->after('additional_sip_investment_details_saved');
            $table->tinyInteger('purchase_save_confirmation')->default(0)->nullable()->comment('Purchase save confirmation status: 0=pending, 1=failed, 2=success')->after('investor_summary_details');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table')->after('id');
            $table->index('investor_order_id');
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
        // removing earlier added fields like investor_summary_details & purchase_save_confirmation field in MySQL table: rta_api_execution_status
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->dropColumn(['investor_summary_details', 'purchase_save_confirmation', 'investor_order_id']);
        });
    }
}
