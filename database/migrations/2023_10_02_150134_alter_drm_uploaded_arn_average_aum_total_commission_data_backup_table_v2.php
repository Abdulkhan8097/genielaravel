<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmUploadedArnAverageAumTotalCommissionDataBackupTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data_backup', function (Blueprint $table) {
            $table->renameColumn('year', 'aum_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data_backup', function (Blueprint $table) {
            $table->renameColumn('year', 'aum_year');
        });
    }
}
