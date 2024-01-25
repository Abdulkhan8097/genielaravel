<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBdmZonalColDrmMeetingLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('drm_meeting_logger', function(Blueprint $table){
			$table->string('bdm_data')->nullable()->default(null);
			$table->string('zonal_Head')->nullable()->default(null);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('drm_meeting_logger', function(Blueprint $table){
			$table->dropColumn('bdm_data');
			$table->dropColumn('zonal_Head');
		});
    }
}
