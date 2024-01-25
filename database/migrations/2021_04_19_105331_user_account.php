<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserAccount extends Migration
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
            $table->string('title',20)->nullable()->comment('title');
            $table->string('name',100)->comment('name');
            $table->string('kra_name',100)->nullable()->comment('kra name fetch');
            $table->string('name_pan_api',100)->nullable()->comment('fetch name from pan api');
            $table->string('email',100)->comment('email id');
            $table->string('kra_email',100)->nullable()->comment('fetch kra email');
            $table->string('mobile',15)->comment('mobile number');
            $table->string('dob',100)->nullable()->comment('date of birth');
            $table->string('verify_hash',100)->nullable()->comment('verify hash');
            $table->string('forgot_hash',100)->nullable()->comment('forgot password hash');
            $table->string('password',100)->nullable()->comment('password');
            $table->string('PAN',50)->nullable()->comment('pan number');
            $table->string('GST',100)->nullable()->comment('GST number');
            $table->string('city',100)->nullable()->comment('city name');
            $table->string('state',100)->nullable()->comment('state name');
            $table->integer('pin_code')->nullable()->comment('pin code');
            $table->text('address')->nullable()->comment('address');
            $table->text('address2')->nullable()->comment('address2');
            $table->text('address3')->nullable()->comment('addres3');
            $table->string('address_type',250)->nullable()->comment('address_type');
            $table->string('ifsc_code',50)->nullable()->comment('bank ifsc code');
            $table->string('bank_name',100)->nullable()->comment('bank name');
            $table->string('branch_add',100)->nullable()->comment('bank address');
            $table->string('acc_no',50)->nullable()->comment('bank account number');
            $table->string('bank_city',50)->nullable()->comment('bank city name');
            $table->string('enc_bank_id',100)->nullable()->comment('encrypted bank id');
            $table->tinyInteger('bank_verify')->default(0)->comment('1=>verify, 0=> not verify');
            $table->tinyInteger('penny_success')->default(0)->comment('penny status 1=>success');
            $table->string('kra_status',20)->nullable()->comment('kra status');
            $table->string('kra_status_code',20)->nullable()->comment('kra status code');
            $table->text('kra_fetch_time')->nullable()->comment('kra fetch time');
            $table->tinyInteger('kra_document_fetch')->default(0)->comment('1=>success');
            $table->tinyInteger('convert_non_kra')->default(0)->comment('1=>success');
            $table->tinyInteger('email_verify')->default(0)->comment('1=>success');
            $table->tinyInteger('mobile_verify')->default(0)->comment('1=>success');
            $table->tinyInteger('pan_verify')->default(0)->comment('1=>success');
            $table->tinyInteger('is_gst')->default(0)->comment('1=>YES, 0=>NO');
            $table->tinyInteger('is_nominee')->default(0)->comment('1=>YES, 0=>NO');
            $table->string('nominee_name',100)->nullable()->comment('nominee name');
            $table->string('nominee_relationship',100)->nullable()->comment('nominee relationship');
            $table->text('nominee_address')->nullable()->comment('nominee address');
            $table->tinyInteger('is_nominee_minor')->default(0)->comment('1=>YES, 0=>NO');
            $table->text('minor_dob')->nullable()->comment('minor dob');
            $table->string('nominee_guardian_name',100)->nullable()->comment('nominee guardian name');
            $table->text('nominee_minor_address')->nullable()->comment('nominee minor address');
            $table->string('from_site',50)->default('samcomf')->comment('from_site');
            $table->tinyInteger('form_status')->default(1)->comment('1=>pan, 2=>personal, 3=>communication, 4=>mobile verification, 5=>email verification, 6=>bank, 7=>arn, 8=>upload');
            $table->tinyInteger('status')->default(0)->comment('0=>created,1=> approved 2=>Activate, 3=>Deactivate');
            $table->tinyInteger('terms_samcomf')->nullable()->comment('1=accepted');
            $table->string('user_unique_code',100)->nullable()->comment('user unique code');
            $table->timestamps();
            // $table->index(['status', 'form_status','from_site','date_created','email','mobile','PAN','']);
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
