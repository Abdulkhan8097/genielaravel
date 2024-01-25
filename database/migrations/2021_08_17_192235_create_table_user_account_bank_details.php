<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUserAccountBankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_account_bank_details');
        Schema::create('user_account_bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('arn', 100)->nullable()->comment('ARN number');
            $table->string('ifsc_code', 50)->nullable()->comment('Bank IFSC Code');
            $table->string('bank_name', 100)->nullable()->comment('Bank Name');
            $table->string('branch_add', 250)->nullable()->comment('Bank Address');
            $table->string('account_type', 50)->nullable()->comment('Account Type');
            $table->string('acc_no', 50)->nullable()->comment('Bank Account Number');
            $table->string('bank_branch_name', 100)->nullable()->comment('Bank Branch Name (stored only in case of AMFI retrieved data)');
            $table->string('bank_city', 50)->nullable()->comment('Bank City (stored only in case of AMFI retrieved data)');
            $table->string('city', 50)->nullable()->comment('City (stored only in case of AMFI retrieved data)');
            $table->string('state', 50)->nullable()->comment('State (stored only in case of AMFI retrieved data)');
            $table->string('pincode', 10)->nullable()->comment('Pincode (stored only in case of AMFI retrieved data)');
            $table->string('address1', 250)->nullable()->comment('Address 1 (stored only in case of AMFI retrieved data)');
            $table->string('address2', 250)->nullable()->comment('Address 2 (stored only in case of AMFI retrieved data)');
            $table->string('address3', 250)->nullable()->comment('Address 3 (stored only in case of AMFI retrieved data)');
            $table->string('micr_code', 50)->nullable()->comment('MICR Code');
            $table->tinyInteger('amfi_bank_record')->default(0)->nullable()->comment('Is it AMFI retrieved data? 0=No, 1=Yes');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('arn');
            $table->index('amfi_bank_record');
            $table->index('status');
            $table->index('account_type');
            $table->index('ifsc_code');
            $table->index('acc_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_account_bank_details');
    }
}
