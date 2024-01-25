<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProjectFocusAmcWiseDetailsAddColumnReportedYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new column reported year and dropping earlier created two different year columns
        Schema::table('project_focus_amc_wise_details', function (Blueprint $table) {
            $table->dropColumn(['avg_aum_for_last_reported_year', 'last_financial_year']);
            $table->year('reported_year')->nullable()->comment('Helps to identify average aum/closing aum etc. mentioned for which year')->after('nature_of_aum');
            $table->index('reported_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column reported year
        Schema::table('project_focus_amc_wise_details', function (Blueprint $table) {
            $table->year('last_reported_year')->nullable()->comment('Helps to identify average aum mentioned for which year');
            $table->year('last_financial_year')->nullable()->comment('Helps to identify closing aum mentioned for which year');
            $table->index('last_reported_year');
            $table->index('last_financial_year');
            $table->dropColumn(['reported_year']);
        });
    }
}
