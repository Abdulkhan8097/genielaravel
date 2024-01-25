<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSmsVendorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_vendor_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('name', 255)->comment('Vendor Name');
            $table->string('api_url', 255)->comment('API endpoint url');
            $table->string('api_key_for_general_sms', 255)->comment('API key for general SMS');
            $table->string('api_key_for_otp', 255)->comment('API key for OTP');
            $table->tinyInteger('send')->nullable()->default(0)->comment('Send SMS functionality will be using this vendor: 0 = No, 1 = Yes');
            $table->tinyInteger('resend')->nullable()->default(0)->comment('Re-send SMS functionality will be using this vendor: 0 = No, 1 = Yes');
            $table->tinyInteger('order')->nullable()->default(0)->comment('Order transactional SMS functionality will be using this vendor: 0 = No, 1 = Yes');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
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
        Schema::dropIfExists('sms_vendor_master');
    }
}
