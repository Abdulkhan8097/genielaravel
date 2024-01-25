<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRtaApiExecutionStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rta_api_execution_status', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('rta_refno', 20)->nullable()->comment('RTA reference number');
            $table->tinyInteger('personal_details_saved')->default(0)->nullable()->comment('Personal details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('kyc_details_saved')->default(0)->nullable()->comment('KYC details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('bank_details_saved')->default(0)->nullable()->comment('Bank details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('fatca_details_saved')->default(0)->nullable()->comment('FATCA details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('nominee_details_saved')->default(0)->nullable()->comment('Nominee details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('investment_details_saved')->default(0)->nullable()->comment('Investment details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('payment_details_saved')->default(0)->nullable()->comment('Payment details saved api status: 0=pending, 1=failed, 2=success');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('rta_refno');
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
        Schema::dropIfExists('rta_api_execution_status');
    }
}
