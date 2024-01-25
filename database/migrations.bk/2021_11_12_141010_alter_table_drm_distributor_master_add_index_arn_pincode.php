<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmDistributorMasterAddIndexArnPincode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding index for field arn_pincode
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->index('arn_pincode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added index for field arn_pincode
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropIndex('arn_pincode');
        });
    }
}
