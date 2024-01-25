<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadBankNomin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('invdb')->create('lead_moninee_bank_tax', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->text('nominee_details')->nullable()->comment('Nominee Details');
            $table->text('bank_details')->nullable()->comment('Bank Details');
            $table->text('tax_pay_details')->nullable()->comment('Tax detail');
            $table->string('lead_id',200)->nullable()->comment('Lead id');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('created_at');
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
    }
}
