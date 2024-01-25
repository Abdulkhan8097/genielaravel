<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmMeetingLogger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_meeting_logger', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('user_id')->comment('User ID belongs to users table');
            $table->string('meeting_mode', 191)->comment('Mode: Phone Call/VC/In Person Meeting');
            $table->string('contact_person_name', 255)->comment('Contact person name');
            $table->string('contact_person_mobile', 255)->comment('Contact person mobile');
            $table->string('contact_person_email', 255)->comment('Contact person email');
            $table->dateTime('start_datetime')->comment('Meeting start date & time');
            $table->dateTime('end_datetime')->comment('Meeting end date & time');
            $table->text('meeting_remarks')->nullable()->comment('Remarks if any');
            $table->tinyInteger('email_sent_to_customer')->default(0)->nullable()->comment('Email sent to customer: 0 = No, 1 = Yes');
            $table->text('email_sent_response')->nullable()->comment('Email sent error if received any');
            $table->tinyInteger('sms_sent_to_customer')->default(0)->nullable()->comment('SMS sent to customer: 0 = No, 1 = Yes');
            $table->text('sms_sent_to_response')->nullable()->comment('SMS sent error if received any');
            $table->tinyInteger('customer_response_received')->default(0)->nullable()->comment('Customer response received: 0 = No, 1 = Yes');
            $table->tinyInteger('customer_response_source')->default(0)->nullable()->comment('Customer response received from source: 1 = Email, 2 = SMS');
            $table->tinyInteger('customer_given_rating')->default(0)->nullable()->comment('Rating given by customer: 1 to 5');
            $table->text('customer_remarks')->nullable()->comment('Remarks given by customer');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('meeting_mode');
            $table->index('user_id');
            $table->index('email_sent_to_customer');
            $table->index('sms_sent_to_customer');
            $table->index('customer_response_received');
            $table->index('customer_response_source');
            $table->index('customer_given_rating');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('drm_meeting_logger');
    }
}
