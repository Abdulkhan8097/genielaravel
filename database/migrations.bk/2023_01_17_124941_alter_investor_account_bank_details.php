<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAccountBankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investor_account_bank_details', function (Blueprint $table) {
            $table->string('uploaded_filename', 255)->nullable()->comment('Uploaded filename')->after('bank_details_saved');
            $table->string('uploaded_original_filename', 255)->nullable()->comment('Uploaded original filename')->after('uploaded_filename');
            $table->string('upload_type', 255)->nullable()->comment('Possible values : Cancelled Cheque, Bank Statement')->after('uploaded_original_filename');
            $table->tinyInteger('upload_status')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = accept, 2 = rejected')->after('upload_type');
            $table->string('rejection_reason', 255)->nullable()->comment('Rejection reason, if upload_status = 2')->after('upload_status');
            $table->tinyInteger('penny_verified')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = failed, 2 = success')->after('rejection_reason');
            $table->dateTime('penny_created_at')->nullable()->useCurrent()->comment('Penny created datetime')->after('penny_verified');
            $table->dateTime('penny_scrutiny_date')->nullable()->comment('Penny scrutiny done datetime')->after('penny_created_at');
            $table->dateTime('penny_deficiency_date')->nullable()->comment('Penny deficiency mark datetime')->after('penny_scrutiny_date');
            
            $table->index('upload_status');
            $table->index('penny_verified');
            $table->index('penny_created_at');
            $table->index('penny_scrutiny_date');
            $table->index('penny_deficiency_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_account_bank_details', function (Blueprint $table) {
            $table->dropColumn(['uploaded_filename']);
            $table->dropColumn(['uploaded_original_filename']);
            $table->dropColumn(['upload_type']);
            $table->dropColumn(['upload_status']);
            $table->dropColumn(['rejection_reason']);
            $table->dropColumn(['penny_verified']);
        });
    }
}
