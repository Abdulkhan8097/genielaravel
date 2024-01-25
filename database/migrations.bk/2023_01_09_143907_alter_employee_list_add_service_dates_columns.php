<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmployeeListAddServiceDatesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns for service start & end dates in MySQL table: employee_list
        Schema::connection('invdb')->table('employee_list', function (Blueprint $table) {
            $table->date('service_start_date')->nullable()->comment('Service Started Date')->after('pan');
            $table->date('service_end_date')->nullable()->comment('Service End Date')->after('service_start_date');
            $table->tinyInteger('is_brokerage_authorised_person')->nullable()->default(0)->comment('Is this employee to be considered as a Partner Brokerage/Commission Authorised Person')->after('service_end_date');
            $table->index('service_start_date');
            $table->index('service_end_date');
            $table->index('is_brokerage_authorised_person');
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
        // removing earlier added columns for service start & end dates from MySQL table: employee_list
        Schema::connection('invdb')->table('employee_list', function (Blueprint $table) {
            $table->dropColumn('service_start_date');
            $table->dropColumn('service_end_date');
            $table->dropColumn('is_brokerage_authorised_person');
            $table->dropIndex('employee_list_status_index');
        });
    }
}
