<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorFolioDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_folio_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references id field from investor_account table');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table');
            $table->string('rta_refno', 20)->nullable()->comment('RTA reference number');
            $table->string('folio_name', 50)->nullable()->comment('Folio name/label/tag');
            $table->string('folio_number', 20)->nullable()->comment('Folio number');
            $table->string('pan', 20)->nullable()->comment('PAN card number');
            $table->string('name')->nullable()->comment('Investor name');
            $table->string('gender', 20)->nullable()->comment('Gender');
            $table->date('dob')->nullable()->comment('Date of birth');
            $table->string('birth_place')->nullable()->comment('Place of birth');
            $table->string('email')->nullable()->comment('Email ID');
            $table->string('mobile', 20)->nullable()->comment('Mobile');
            $table->text('kyc_details')->nullable()->comment('KYC details');
            $table->text('nominee_details')->nullable()->comment('Nominee details');
            $table->text('fatca_details')->nullable()->comment('Fatca details');
            $table->text('bank_details')->nullable()->comment('Bank details');
            $table->text('investment_details')->nullable()->comment('Investment details');
            $table->text('payment_details')->nullable()->comment('Payment details');
            $table->tinyInteger('status')->default(0)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('investor_account_id');
            $table->index('investor_order_id');
            $table->foreign('investor_account_id')->references('id')->on('investor_account');
            $table->foreign('investor_order_id')->references('id')->on('investor_order');
            $table->index('rta_refno');
            $table->index('folio_name');
            $table->index('folio_number');
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
        Schema::dropIfExists('investor_folio_details');
    }
}
