<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserAccountCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('distributor_type')->nullable()->comment('distributor type');
            $table->string('name',100)->nullable()->comment('amfi api name');
            $table->string('business_name',100)->nullable()->comment('business name');
            $table->string('email_amfi',100)->nullable()->comment('Amfi email id');
            $table->string('mobile_amfi',15)->nullable()->comment('amfi api mobile number');
            $table->string('email',100)->nullable()->comment('email');
            $table->string('mobile',15)->nullable()->comment('mobile');
            $table->string('ARN',100)->comment('ARN Number');
            $table->string('PAN',50)->comment('PAN Number');
            $table->string('GST',100)->nullable()->comment('GST Number');
            $table->string('ifsc_code',50)->nullable()->comment('Bank IFSC Code');
            $table->string('bank_name',100)->nullable()->comment('Bank Name');
            $table->string('branch_add',250)->nullable()->comment('Bank Address');
            $table->string('account_type',50)->nullable()->comment('Account Type');
            $table->string('acc_no',50)->nullable()->comment('Bank Account Number');
            $table->string('bank_city',50)->nullable()->comment('BanK City');
            $table->string('bank_state',50)->nullable()->comment('Bank State');
            $table->string('micr_code',50)->nullable()->comment('Bank MICR Code');
            $table->text('address1')->nullable()->comment('Address 1');
            $table->text('address2')->nullable()->comment('Address 2');
            $table->string('city',50)->nullable()->comment('City');
            $table->string('state',50)->nullable()->comment('State');
            $table->string('pin_code',50)->nullable()->comment('Pin Code');
            $table->string('nominee_name',100)->nullable()->comment('Nominee Name');
            $table->string('nominee_relationship',100)->nullable()->comment('nominee relationship ');
            $table->string('nominee_state',50)->nullable()->comment('nominee state');
            $table->string('nominee_city',50)->nullable()->comment('nominee city');
            $table->string('nominee_pin_code',50)->nullable()->comment('nominee pin code');
            $table->string('minor_dob',100)->nullable()->comment('Nominee minor dob');
            $table->string('nominee_guardian_name',100)->nullable()->comment('nominee guardian name');
            $table->text('nominee_minor_address')->nullable()->comment('nominee minor address');
            $table->tinyInteger('is_check_uploaded')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('email_verify')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('pan_verify')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('bank_verify')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('is_gst')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('is_nominee')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('is_nominee_minor')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('form_status')->default(0)->comment('1=>verify, 2=>otp-verification, 3=>bank-details, 4=>Upload, 5=>nominee-details, 6=>thankyou');
            $table->tinyInteger('status')->default(0)->comment('0=>created,1=> approved 2=>Activate, 3=>Deactivate');
            $table->string('from_site',50)->default('samcomf')->comment('from site');
            $table->string('user_unique_code',100)->nullable()->comment('user unique code');
            $table->dateTime('approved_date')->nullable()->comment('Account Approved Date');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_account');
    }
}
