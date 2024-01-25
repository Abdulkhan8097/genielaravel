<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsersDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('user_id')->comment('User ID belongs to users table');
            $table->string('employee_code', 20)->comment('Employee code');
            $table->string('mobile_number', 10)->nullable()->comment('Mobile number');
            $table->tinyInteger('cadre_of_employee')->nullable()->comment('Rating of an employee');
            $table->string('state', 100)->nullable()->comment('State');
            $table->string('city', 100)->nullable()->comment('City');
            $table->text('serviceable_pincode')->nullable()->comment('Serviceable pincode by this employee, multiple values are separated by comma');
            $table->unsignedBigInteger('reporting_to')->nullable()->comment('Reporting person of this employee');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->index('reporting_to');
            $table->foreign('reporting_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('users_details');
    }
}
