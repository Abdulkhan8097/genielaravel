<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmDistributorMasterBackupAddColumnPincodeCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column for field pincode_city
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('pincode_city', 100)->nullable()->comment('City as per pincode')->after('arn_city');
            $table->index('arn_pincode');
            $table->index('arn_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column for field pincode_city
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['pincode_city']);
            $table->dropIndex('arn_pincode');
            $table->dropIndex('arn_city');
        });
    }
}
