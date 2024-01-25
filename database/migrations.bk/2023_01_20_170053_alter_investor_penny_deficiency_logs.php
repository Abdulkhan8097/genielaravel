<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorPennyDeficiencyLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('investor_penny_deficiency_logs', function (Blueprint $table) {
            $table->string('uploaded_filename', 255)->nullable()->comment('Uploaded filename')->after('pan');
            $table->string('uploaded_original_filename', 255)->nullable()->comment('Uploaded original filename')->after('uploaded_filename');
            $table->string('upload_type', 255)->nullable()->comment('Possible values : Cancelled Cheque, Bank Statement')->after('uploaded_original_filename');
            $table->tinyInteger('bank_verified')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = In progress, 2 = success, 3 = failed')->after('rejection_reason');
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_penny_deficiency_logs', function (Blueprint $table) {
            $table->dropColumn(['uploaded_filename']);
            $table->dropColumn(['uploaded_original_filename']);
            $table->dropColumn(['upload_type']);
        });
    }
}
