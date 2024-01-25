<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterApiUsersListAddColumnShowErrorsFieldwiseAndOrderOtpConfirmationRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns show_fieldwise_errors & order_otp_confirmation_required in MySQL table: api_users_list
        Schema::table('api_users_list', function (Blueprint $table) {
            $table->tinyInteger('show_fieldwise_errors')->nullable()->default(0)->comment('Show fieldwise errors in API response.0=No,1=Yes')->after('name');
            $table->tinyInteger('order_otp_confirmation_required')->nullable()->default(1)->comment('OTP confirmation required for an order.0=No,1=Yes')->after('show_fieldwise_errors');
        });
        // Adding an API user for RANKMF
        DB::statement("INSERT INTO api_users_list(name, show_fieldwise_errors, order_otp_confirmation_required, status, created_at) VALUES('RANKMF', 1, 0, 1, now());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns show_fieldwise_errors & order_otp_confirmation_required from MySQL table: api_users_list
        Schema::table('api_users_list', function (Blueprint $table) {
            $table->dropColumn('show_fieldwise_errors');
            $table->dropColumn('order_otp_confirmation_required');
        });
    }
}
