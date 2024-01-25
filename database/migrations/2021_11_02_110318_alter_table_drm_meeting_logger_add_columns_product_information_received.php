<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmMeetingLoggerAddColumnsProductInformationReceived extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding product_information_received column
        Schema::table('drm_meeting_logger', function (Blueprint $table) {
            $table->tinyInteger('product_information_received')->default(0)->nullable()->comment('product information received: 0 = No, 1 = Yes')->after('customer_remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added ARN column
        Schema::table('drm_meeting_logger', function (Blueprint $table) {
            $table->dropColumn(['product_information_received']);
        });
    }
}
