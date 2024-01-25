<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmUploadedArnAverageAumTotalCommissionDataTableV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column partner_code
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data', function (Blueprint $table) {
            $table->string('partner_code')->nullable()->default(null)->before('created_at');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->string('partner_code')->nullable()->default(null)->before('created_at');;
    }
}
