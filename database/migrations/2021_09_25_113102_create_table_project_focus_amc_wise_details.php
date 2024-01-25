<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjectFocusAmcWiseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_focus_amc_wise_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->string('amc_name')->comment('AMC name');
            $table->string('amc_code')->nullable()->comment('AMC code');
            $table->decimal('total_commission_expenses_paid', 25, 4)->nullable()->comment('Total commission & expenses paid');
            $table->decimal('gross_inflows', 25, 4)->nullable()->comment('Gross inflows');
            $table->decimal('net_inflows', 25, 4)->nullable()->comment('Net inflows');
            $table->decimal('avg_aum_for_last_reported_year', 25, 4)->nullable()->comment('Average AUM for last reported year');
            $table->year('last_reported_year')->nullable()->comment('Helps to identify average aum mentioned for which year');
            $table->decimal('closing_aum_for_last_financial_year', 25, 4)->nullable()->comment('Closing AUM for last financial year');
            $table->year('last_financial_year')->nullable()->comment('Helps to identify closing aum mentioned for which year');
            $table->decimal('effective_yield', 25, 4)->nullable()->comment('Effective yield');
            $table->string('nature_of_aum', 100)->nullable()->comment('Dealing majorly in which segment: Possible values are like EQUITY, DEBT, EQUITY & DEBT etc.');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->foreign('ARN')->references('ARN')->on('distributor_master')->onUpdate('cascade')->onDelete('cascade');
            $table->index('last_reported_year');
            $table->index('last_financial_year');
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
        Schema::dropIfExists('project_focus_amc_wise_details');
    }
}
