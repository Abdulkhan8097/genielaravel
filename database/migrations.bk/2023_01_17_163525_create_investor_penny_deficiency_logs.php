<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorPennyDeficiencyLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_penny_deficiency_logs', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('investor_bank_account_id')->nullable()->comment('Foreign key references id field from investor_account_bank_details');
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references id field from investor_account table');
            $table->string('pan',20)->nullable()->comment('Investor account PAN Number');
            $table->tinyInteger('upload_status')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = accept, 2 = rejected');
            $table->string('rejection_reason', 255)->nullable()->comment('Rejection reason, if upload_status = 2');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Record created datetime');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Record updated datetime');
            $table->index('investor_bank_account_id');
            $table->index('investor_account_id');
            $table->index('pan');
            $table->index('upload_status');
            $table->foreign('investor_bank_account_id')->references('id')->on('investor_account_bank_details')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('investor_account_id')->references('id')->on('investor_account')->cascadeOnUpdate()->cascadeOnDelete();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investor_penny_deficiency_logs');
    }
}
