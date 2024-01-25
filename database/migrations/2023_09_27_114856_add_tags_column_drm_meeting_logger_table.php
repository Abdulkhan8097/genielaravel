<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagsColumnDrmMeetingLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
		Schema::table('drm_meeting_logger', function(Blueprint $table){
			$table->string('tags')->nullable()->default(null)->before('created_at');
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
			$table->dropColumn('tags');
        });
    }
}
