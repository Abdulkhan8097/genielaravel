<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmDistributorMasterAddColumnPincodeCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column for field pincode_city
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('pincode_city', 100)->nullable()->comment('City as per pincode')->after('arn_city');
            $table->index('arn_city');
        });

        // adding index for field city
        Schema::table('drm_uploaded_pincode_city_state', function (Blueprint $table) {
            $table->index('city');
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
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['pincode_city']);
            $table->dropIndex('arn_city');
        });

        // removing earlier added index for field city
        Schema::table('drm_uploaded_pincode_city_state', function (Blueprint $table) {
            $table->dropIndex('city');
        });
    }
}
