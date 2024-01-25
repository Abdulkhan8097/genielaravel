<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmMeetingLoggerAddColumnsCustomerResponseReceivedDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add columns customer response received datetime
        Schema::table('drm_meeting_logger', function (Blueprint $table) {
        DB::statement("ALTER TABLE `drm_meeting_logger` ADD `customer_response_received_datetime` DATETIME NULL DEFAULT NULL COMMENT 'Customer response received datetime' AFTER `customer_response_source`;");
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
