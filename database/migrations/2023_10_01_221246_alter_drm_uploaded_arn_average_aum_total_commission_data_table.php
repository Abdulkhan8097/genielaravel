<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmUploadedArnAverageAumTotalCommissionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column appointment_link
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data', function (Blueprint $table) {
            $table->integer('year')->default(0)->nullable()->comment('AUM YEAR')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column appointment_link
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data', function (Blueprint $table) {
            $table->dropColumn(['year']);
        });
    }
}
