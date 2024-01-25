<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmUploadedArnAverageAumTotalCommissionDataBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('drm_uploaded_arn_average_aum_total_commission_data_backup', function (Blueprint $table) {
            $table->integer('year')->default(0)->nullable()->comment('AUM YEAR')->after('status');;
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
            $table->dropColumn(['year']);
        });
    }
}
