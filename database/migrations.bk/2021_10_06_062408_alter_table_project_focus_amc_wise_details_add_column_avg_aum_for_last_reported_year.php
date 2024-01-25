<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProjectFocusAmcWiseDetailsAddColumnAvgAumForLastReportedYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column avg_aum_for_last_reported_year which got deleted mistakenly
        Schema::table('project_focus_amc_wise_details', function (Blueprint $table) {
            $table->decimal('avg_aum_for_last_reported_year', 25, 4)->nullable()->comment('Average AUM for last reported year')->after('net_inflows');
            $table->dropColumn(['last_reported_year']);
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
    }
}
