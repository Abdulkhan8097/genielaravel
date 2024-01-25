<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertEsignApiLogsAddInvestorAccountUploadId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('esign_api_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_account_upload_id')->nullable()->comment('Foreign key references id field from investor_account_upload_id table')->after('investor_account_id');
            $table->foreign('investor_account_upload_id')->references('id')->on('investor_account_upload')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('esign_api_logs', function (Blueprint $table) {
            $table->dropForeign('esign_api_logs_investor_account_upload_id_foreign');
            $table->dropColumn(['investor_account_upload_id']);
        });
    }
}
